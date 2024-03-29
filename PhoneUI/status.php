<?php
/*
	UCxml PhoneUI - view status

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

require_once "../lib/xtpl/xtemplate.class.php";
require_once "../lib/utils.php";
require_once "../lib/mysql.php";

require_once "lib/urlbase.php";
require_once "lib/security.php";//grab mac address info, along with global preferences
require_once "lib/headers.php";
require_once "lib/refresh.php";

if ($ph_sec == 'yes' && $registered == 'FALSE')
{
	//Security to stop unregistered users from going any further if 'Phone Security' is on.  XML images cannot be templated with XTPL.
	require_once "templates/img_sec_breach.php";
} else {
	if (isset($_GET['ur']))
	{
		$urMAC = defang_input($_GET['ur']);
		show_status($MAC,$urMAC);

	} elseif (isset($_GET['view_my_status'])) {

		show_status($MAC,$MAC);

	} elseif (isset($_GET['others_status'])) {

		//User has inputed all information needed
		//A list of the users in a certain location
		//according to their status is now displayed

		$others_status = defang_input($_GET['others_status']);

		$per_page = 31;//number of phones displayed on each page

		if (isset($_GET['start']))
		{
			$start = defang_input($_GET['start']);
			$limitstart = 'LIMIT '.$start.','.$per_page;

		} else {
			$start = 0;
			$limitstart = 'LIMIT 0,'.$per_page;
		}
		//Number of phones to be displayed per page
		$countQuery = "SELECT
			COUNT(opensips.presentity.id) AS total
			FROM opensips.presentity
			$loc_sql";
		$theCountRES = mysql_query($countQuery, $db);

		//Fetch total phones
		if ($in = mysql_fetch_assoc($theCountRES))
		{
			$totalCount = defang_input($in['total']);
		}

		//Calc remaining rows
		$remainingRows = ($totalCount - $start);

		$browseQuery = "SELECT opensips.presentity.username, 
				ucxml.phone.nick AS nick,
				ucxml.phone.number AS number, 
				ucxml.phone.MAC AS urMAC
				FROM opensips.presentity
				LEFT JOIN ucxml.phone ON ucxml.phone.nick = opensips.presentity.username
				WHERE ucxml.phone.MAC !=  '$urMAC'
				ORDER BY opensips.presentity.username
				$limitstart";
		$theBrowseRES = mysql_query($browseQuery, $db);

		$xtpl=new XTemplate ("templates/status_listing.xml");

		if ($remainingRows <= $per_page)
		{
			$prompt = ($start + 1) ." to ". ($start + $remainingRows) ." of ". $totalCount.".";
		} else {
			$prompt = ($start + 1) ." to ". ($start + $per_page) ." of ". $totalCount.".";
		}

		while ($in2 = mysql_fetch_assoc($theBrowseRES))
		{
			//assign users to listing of status
			$tmpTitle = $in2['username'];

			$title = substr($tmpTitle,0,27);

			$urMAC= $in2['urMAC'];

			$xtpl->assign("title",$title);
			$xtpl->assign("url_base",$URLBase);
			$xtpl->assign("MAC",$MAC);
			$xtpl->assign("ID_phone",$urMAC);
			$xtpl->parse("main.contact_menu");
		}
/*
		if ($remainingRows > $per_page)
		{
			// There are more entries, show More
			$start = $start + $per_page;

			$xtpl->assign("start","$start");
			$xtpl->assign("title","More");
			$xtpl->assign("url_base",$URLBase);
			$xtpl->assign("MAC",$MAC);
			$xtpl->assign("others_status",$others_status);
			$xtpl->assign("start",$start);
			$xtpl->parse("main.contact_more");
		}

		if ($others_status == 'in')
		{
			$xtpl->assign("heading","Available (Currently ".$totalCount.")");
//		} elseif ($others_status == 'out') {
//			$xtpl->assign("heading","Unavailable (Currently ".$totalCount.")");
		} else {
			$xtpl->assign("heading","Show All (Currently ".$totalCount.")");
		}
*/
		$xtpl->assign("prompt",$prompt);
		$xtpl->parse("main");
		$xtpl->out("main");

	}

	elseif (isset($_GET['status_others_index'])) {
		//user has selected to view others' status
		//display screen with more specific options with who to show
		$xtpl=new XTemplate ("templates/status_others_index.xml");
		$num_in = count_qry("WHERE extractvalue(body, '//note') IN ('available', '') AND phone.access_lvl != 'unknown'");
//		$num_out = count_qry("WHERE $xml->tuple->note = 'unavailable' AND phone.access_lvl != 'unknown'");
		$num_all = count_qry("WHERE phone.access_lvl != 'unknown'");

		$xtpl->assign("num_in",$num_in);
//		$xtpl->assign("num_out",$num_out);
		$xtpl->assign("all",$num_all);
		$xtpl->assign("MAC",$MAC);
		$xtpl->assign("url_base",$URLBase);
		$xtpl->parse("main");
		$xtpl->out("main");

	} else {
	//User is requesting the main status page,
	//where they choose to view others' status, or set their own
	$xtpl=new XTemplate ("templates/status_index.xml");
	$xtpl->assign("MAC",$MAC);
	$xtpl->assign("url_base",$URLBase);
	$xtpl->parse("main");
	$xtpl->out("main");
	}
}


//
//
// Functions
//
//

function count_qry($location)
{
	global $db;
	$countQuery = "SELECT
			COUNT(opensips.presentity.id) AS total
			FROM opensips.presentity
			$loc_sql";
	$theCountRES = mysql_query($countQuery, $db);
	if ($in = mysql_fetch_assoc($theCountRES))
	{
		//display # in category, beforegoing into it
		return $in['total'];
	}
}

function show_status ($MAC,$urMAC)
{
	/*
		The user has selected the message she wishes to view, the MAC address
		is used to select the corresponding fields from the db
		the "away_msg" if using the preprogrammed is a number, that corresponds to a msg
		written in the php in the function.  If user has a custom message, the message
		is written in text in the away_msg field
	*/
	global $db;
	global $URLBase;

	$browseQuery = "SELECT presentity.username AS username,
			extractvalue(body, '//note') AS note,
			extractvalue(body, '//basic') AS basic,
			ucxml.phone.number AS number
			FROM opensips.presentity
			LEFT JOIN ucxml.phone ON ucxml.phone.nick=opensips.presentity.username
			WHERE phone.MAC ='$urMAC'";

		$theContactRES = mysql_query($browseQuery, $db);

if (mysql_num_rows($theContactRES) == 0)
	{
		$xtpl=new XTemplate ("templates/status_detail.xml");
		if ($MAC == $urMAC)
		{
			$xtpl=new XTemplate ("templates/view_my_status.xml");
			$xtpl->assign("url_base",$URLBase);
			$xtpl->assign("MAC",$MAC);
			$xtpl->assign("msg",$in['note']);
		} else {
			$xtpl=new XTemplate ("templates/status_detail.xml");
			$xtpl->assign("msg",$in['username']);
		}

		$tmp_your = "Offline";

		$xtpl->assign("tmpyour",$tmp_your);
		$xtpl->assign("tmpTitle",$display_away);
		$xtpl->assign("number",$number);
		$xtpl->parse("main");
		$xtpl->out("main");

	}
	elseif ($in = mysql_fetch_assoc($theContactRES))
	{

	if($in['basic'] == 'open')
	{
		//Assign user msg info to the screen
//		$tmp_unixtime = $in2['date'];
//		$displaydate = date("n/d g:ia" ,$tmp_unixtime);

		$xtpl=new XTemplate ("templates/status_detail.xml");
		if ($MAC == $urMAC)
		{
			$xtpl=new XTemplate ("templates/view_my_status.xml");
			$xtpl->assign("url_base",$URLBase);
			$xtpl->assign("MAC",$MAC);
			$xtpl->assign("msg",$in['note']);
//			$tmp_display = num2txt($in['away_msg']);
//			$xtpl->assign("msg",$tmp_display);
		} else {
			$xtpl=new XTemplate ("templates/status_detail.xml");
			$xtpl->assign("msg",$in['username']);
		}

		$curphone = parse_phone($in['number']);
		$number = return_dial($curphone);

//		$tmp_display = num2txt($in['away_msg']);

		$tmp_location = $in['note'];

		if ($tmp_location == 'available')
		{
			$display_away = "Available since ".$displaydate;
			$tmp_your = "Available";
		} else {
			$display_away = "Availability Unknown since ".$displaydate;
			if ($in['note'] == '')
			{
			$tmp_your = "Online";
			}
			else
			{
			$tmp_your = $in['note'];
			}
		}

//		$xtpl->assign("prompt",$tmp_display);
		$xtpl->assign("tmpyour",$tmp_your);
		$xtpl->assign("tmpTitle",$display_away);
		$xtpl->assign("number",$number);
		$xtpl->parse("main");
		$xtpl->out("main");
	}

	}
}


function parse_phone ($in_phone)
{
	//remove extraneous characters from phone number
	$chk_phone = trim($in_phone);
	$chkExp = "(\-)|(\.)|(\()|(\))|(\ )";
	$out_phone = trim(eregi_replace($chkExp, "", $chk_phone));
	return $out_phone;
}


function return_dial($phone)
{
	/*
		The user is able to hit dial from this screen
	*/

	$number = $phone;
	return $number;
}
?>