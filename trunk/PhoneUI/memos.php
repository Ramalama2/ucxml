<?php
/*
	UCxml PhoneUI - memos

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
	require_once "templates/img_sec_breach.php";

} else

if (isset($_GET['mem'])) {
	// We are selecting a memo
	$memID = defang_input($_GET['mem']);
	$memQuery = "SELECT
	memos.date AS date,
	memos.id_memo AS id_memo,
	memos.title AS title,
	memos.msg AS msg,
	memos.sender AS sender
	FROM memos WHERE memos.id_memo='$memID'";
	$thememRES = mysql_query($memQuery, $db);

	if ($in = mysql_fetch_assoc($thememRES))
	{
		$xtpl=new XTemplate ("templates/memo_detail.xml");
//		if ($access_lvl == 'Restricted' || $access_lvl == 'Obmedzene')
//		{
			$tmp_unixtime = $in['date'];
			$displaydate = date("n/d, h:ia Y" ,$tmp_unixtime);

			$xtpl->assign("title",$in['title']);
			$xtpl->assign("date",$displaydate);
			$xtpl->assign("sender",$in['sender']);
			$xtpl->assign("msg",$in['msg']);

//		} else {
//			//User did not meet security requirements to view memo
//			$xtpl->assign("msg",'You must be using an Unrestricted phone to view this message');
//		}
		$xtpl->parse("main");
		$xtpl->out("main");
	}
} else {
	list_memos ($MAC);
}

function list_memos ($MAC)
{
	/*
		Set page count to 28, count how many memos are going to be listed.
		List titles of memos, showing their dates by what the global says, and if user
		has chosen an order, order by their custom order
		Create URLs for each memo that will direct user to the text in the memo

	*/
	global $db;
	global $URLBase;
	global $access_lvl;
	global $my_nick;
	global $memo_ob;

	$per_page = 27;//number of memos displayed on each page

		if (isset($_GET['start']))
		{
			$start = defang_input($_GET['start']);
			$limitstart = 'LIMIT '.$start.','.$per_page;

		} else {
			$start = 0;
			$limitstart = 'LIMIT 0,'.$per_page;
		}

	$countQuery = "SELECT
		COUNT(memos.id_memo) AS total
		FROM memos";

	$theCountRES = mysql_query($countQuery, $db);
	//Fetch total items
	if ($in = mysql_fetch_assoc($theCountRES))
	{
		$totalCount = $in['total'];
	}

	//Calc remaining rows
	$remainingRows = ($totalCount - $start);

	//Get order by preferences
	if (isset($_GET['ob']))
	{
		//user is using the settings according to the global preferences
		$ob_saved =  ""; //do not save for 'more' object, user has not selected a custom order
		if ($memo_ob == "date")
		{
			//global says to order by date, make order DESC, to show the oldest first
			$memo_ob_sql = "ORDER BY $memo_ob. DESC";
		} else {
			//global says to order by sender or title, dont need to order by DESC
			$memo_ob_sql = "ORDER BY $memo_ob";
		}
	}

	//Qry
	$browseQuery = "SELECT
		memos.id_memo AS id_memo,
		memos.title AS title,
		memos.date AS date,
		memos.sender AS sender
		FROM memos
		$memo_ob_sql
		$limitstart";

	if ($remainingRows <= $per_page)
		{
			$prompt = ($start + 1) ." to ". ($start + $remainingRows) ." of ". $totalCount.".";
		} else {
			$prompt = ($start + 1) ." to ". ($start + $per_page) ." of ". $totalCount.".";
		}
	if ($totalCount != '0')
	{
		$theBrowseRES = mysql_query($browseQuery, $db);
		$xtpl=new XTemplate ("templates/memo_menu.xml");

			while ($in2 = mysql_fetch_assoc($theBrowseRES))
			{
//				if ($access_lvl == 'Unrestricted')
//				{
					//User is registered
					$tmp_unixtime = $in2['date'];
					$displaydate = date("n/d-" ,$tmp_unixtime);

					$tmpTitle = $displaydate.$in2['title'] ;
					$title = substr($tmpTitle,0,25);

					$xtpl->assign("prompt",$prompt);
					$xtpl->assign("title",$title);
					$xtpl->assign("url_base",$URLBase);
					$xtpl->assign("MAC",$MAC);
					$xtpl->assign("ID_memo",$in2['id_memo']);
					$xtpl->parse("main.memo_menu");
//				}
			}

	
		// If there are more entries, show Next
		if ($remainingRows > $per_page)
		{
			$start = $start + $per_page;
			$xtpl->assign("title","More");
			$xtpl->assign("saved_order",$ob_saved);
			$xtpl->assign("url_base",$URLBase);
			$xtpl->assign("start",$start);
			$xtpl->assign("MAC",$MAC);
			$xtpl->parse("main.memo_more");
		}
		
		//output
		$xtpl->parse("main");
		$xtpl->out("main");

	} else {
		//There are no memos
		require_once "templates/img_empty_cont.php";
	}
}



?>