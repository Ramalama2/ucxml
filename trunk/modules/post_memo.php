<?php
/*
	UCxml web Portal - Edit memos (IM)

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


//Checks if id is known, stores in variable
if (isset($_GET['id_memo']))
{
	$tmp_id_memo = defang_input($_GET['id_memo']);
}

$tmp_receiver = defang_input($_SESSION['user_name']);

output_view_memo($tmp_id_memo);

function output_view_memo ($myID_memo)
{
	include "language/lang.php";

	global $db;
	$xtpl=new XTemplate ("modules/templates/post_memo.html");

	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT * FROM memos WHERE id_memo='$myID_memo'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$tmp_unixtime = $in['date'];
		$displaydate = date("l, F d, Y h:i" ,$tmp_unixtime);
	        $sender_av = $in['sender'];

		$xtpl->assign("id_memo",$in['id_memo']);
		$xtpl->assign("date",$displaydate);
		$xtpl->assign("title",$in['title']);
		$xtpl->assign("msg",$in['msg']);
		$xtpl->assign("from",$in['sender']);
	}

	$theSQL2 = "SELECT id_user,av FROM users WHERE username='$sender_av'";
	$theRES2 = mysql_query($theSQL2, $db);
	if ($in = mysql_fetch_assoc($theRES2))
	{
		$target = "images/avatars/";
		// if the user has a custom avatar, show their avatar, else show default avatar
		if($in['av'])
		{

		$xtpl->assign("av", $target . ($in['av']? $in['id_user'] .'.'. $in['av'] : 'default.png'));
	}
           }
	// Output
	$xtpl->parse ("view_memo");
	$xtpl->out("view_memo");
}
?>