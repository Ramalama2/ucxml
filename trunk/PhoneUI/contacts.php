<?php
/*
	UCxml PhoneUI - browse menu items

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

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
	//Security to stop unregistered users from going any further if 'Phone Security' is on.
	//XML images cannot be templated with XTPL.
	require_once "templates/img_sec_breach.php";

} elseif (isset($_GET['ur'])) {
	//Specific contact has been selected (seperate style)
	$urID_contact = defang_input($_GET['ur']);
	$browseQuery = "SELECT
		contacts.id_contact AS id_contact,
		contacts.lname AS lname,
		contacts.fname AS fname,
		contacts.nick AS nick,
		contacts.office_phone AS office_phone,
		contacts.home_phone AS home_phone,
		contacts.cell_phone AS cell_phone,
		contacts.other_phone AS other_phone,
		contacts.custom_phone AS custom_phone,
		contacts.custom_number AS custom_number,
		contacts.title AS title
		FROM contacts
		WHERE contacts.id_contact = '$urID_contact'";

	$theContactRES = mysql_query($browseQuery, $db);

	if ($in = mysql_fetch_assoc($theContactRES))
	{
		$tmpTitle = $in['fname'] ." ". $in['lname'];
		if ($in['title'] != '' && $in['nick'] != '')
		{
			//show with '-' beacuse both exist
			$tmpNick = $in['title'] ." - ". $in['nick'];
		} else {
			//show without dash, 1 or less exist
			$tmpNick = $in['title'].$in['nick'];
		}

		$xtpl=new XTemplate ("templates/contact_detail.xml");

		$xtpl->assign("tmpTitle",$tmpTitle);
		$xtpl->assign("nick",$tmpNick);

		$name = ''; //$name is blank when inside a contact

		//Display phone numbers under current contact
		if (trim($in['office_phone']) != "")
		{
			$curphone = parse_phone($in['office_phone']);
			$number = return_dial($curphone);
			$xtpl->assign("type",'Office');
			$xtpl->assign("number",$number);
			$xtpl->parse("main.dir_entry");
		}
		if (trim($in['home_phone']) != "")
		{
			$curphone = parse_phone($in['home_phone']);
			$number = return_dial($curphone);
			$xtpl->assign("type",'Home');
			$xtpl->assign("number",$number);
			$xtpl->parse("main.dir_entry");
		}
		if (trim($in['cell_phone']) != "")
		{
			$curphone = parse_phone($in['cell_phone']);
			$number = return_dial($curphone);
			$xtpl->assign("type",'Cell');
			$xtpl->assign("number",$number);
			$xtpl->parse("main.dir_entry");
		}
		if (trim($in['custom_number']) != "")
		{
			$curphone = parse_phone($in['custom_number']);
			$number = return_dial($curphone);
			$xtpl->assign("type",$in['custom_phone']);
			$xtpl->assign("number",$number);
			$xtpl->parse("main.dir_entry");
		}
		if (trim($in['other_phone']) != "")
		{
			$curphone = parse_phone($in['other_phone']);
			$number = return_dial($curphone);
			$xtpl->assign("type",'Other');
			$xtpl->assign("number",$number);
			$xtpl->parse("main.dir_entry");
		}
	}
	$xtpl->parse("main");
	$xtpl->out("main");

} else {
list_contacts ($in['title'],$MAC);
}


//
//
// Functions
//

//
function list_contacts ($member_cat,$MAC)
{
/*
	This function lists the contacts in specifed category:
		-$per_page is set to limit the number contacts to each page.
		-a count query is used to count how many contacts will be displayed.
		 If number of contacts is greater than 0 then the title information and ID for each contact is fetched.
		 If total contacts == 0, then display prompt to add contacts
	*/
	global $db;
	global $URLBase;

		$per_page = 31;//number of contacts displayed on each page

		if (isset($_GET['start']))
		{
			$start = defang_input($_GET['start']);
			$limitstart = 'LIMIT '.$start.','.$per_page;

		} else {
			$start = 0;
			$limitstart = 'LIMIT 0,'.$per_page;
		}

	//Number of contacts to be displayed per page
	$countQuery = "SELECT
		COUNT(contacts.id_contact) AS total
		FROM contacts";
	$theCountRES = mysql_query($countQuery, $db);

	//Fetch total items
	if ($in = mysql_fetch_assoc($theCountRES))
	{
		$totalCount = $in['total'];
	}

	//Calc remaining rows
	$remainingRows = ($totalCount - $start);

	if ($totalCount != "0")
	{
		//Items that match creteria exist, fetch data
		//
		$browseQuery = "SELECT
			contacts.id_contact AS id_contact,
			contacts.display_name AS display_name,
			contacts.lname AS lname,
			contacts.fname AS fname,
			contacts.nick AS nick,
			contacts.office_phone AS office_phone,
			contacts.home_phone AS home_phone,
			contacts.cell_phone AS cell_phone,
			contacts.custom_phone AS custom_phone,
			contacts.custom_number AS custom_number,
			contacts.other_phone AS other_phone,
			contacts.title AS title
			FROM contacts
			ORDER BY contacts.display_name
			$limitstart";
		$theBrowseRES = mysql_query($browseQuery, $db);

			//category is set to display phone numbers on a seperate screen
			$xtpl=new XTemplate ("templates/listcontacts_sep.xml");

			if ($remainingRows <= $per_page)
				{
					$prompt = ($start + 1) ." to ". ($start + $remainingRows) ." of ". $totalCount.".";
				} else {
					$prompt = ($start + 1) ." to ". ($start + $per_page) ." of ". $totalCount.".";
				}

		while ($in2 = mysql_fetch_assoc($theBrowseRES))
		{
			$tmpTitle = $in2['display_name'];

			$title = substr($tmpTitle,0,25);

			$ID_contact = $in2['id_contact'];

			$xtpl->assign("title",$title);
			$xtpl->assign("url_base",$URLBase);
			$xtpl->assign("MAC",$MAC);
			$xtpl->assign("ID_contact",$ID_contact);
			$xtpl->parse("main.contact_menu");
		}
				// If there are more entries, show Next
			if ($remainingRows > $per_page)
			{
				$start = $start + $per_page;

				$xtpl->assign("title","More");
				$xtpl->assign("url_base",$URLBase);
				$xtpl->assign("MAC",$MAC);
				$xtpl->assign("start",$start);
				$xtpl->parse("main.contact_more");
			}

			$xtpl->assign("heading",$member_cat);
			$xtpl->assign("prompt",$prompt);
			$xtpl->parse("main");
			$xtpl->out("main");

		}
	 else {
		//No contacts in container
		require_once "templates/img_empty_cat.php";
	}
}

function parse_phone ($in_phone)
{
	//remove extraneous characters from phone number
	$chk_phone = trim($in_phone);
	$chkExp = "(\-)|(\()|(\))|(\ )";
	$out_phone = trim(eregi_replace($chkExp, "", $chk_phone));
	return $out_phone;
}

function return_dial($phone)
{
/*
	This function is used to display the number and for each of the contacts different phones,
	The user is able to hit dial from this screen
*/

	$number = $phone;
	return $number;
}
?>