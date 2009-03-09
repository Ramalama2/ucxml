<?php
/*
	UCxml web Portal - View memos

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

$tmp_id_user = defang_input($_SESSION['user_id']);
if (isset($_POST['submit_save']))
{
	// Saving
		$tmp_memo_ob = defang_input($_POST['memo_ob']);
		$tmpUpdateSQL =
			"UPDATE users
			SET	memo_ob = '$tmp_memo_ob'
			WHERE id_user ='$tmp_id_user'";
		mysql_query($tmpUpdateSQL, $db);

		header("Location: index.php?module=view_memos");
}

elseif (isset($_POST['submit_post']))
{
	//add new memo
	$tmp_id_memo = create_guid($tmp_id_memo);
	$tmpInitSQL = "INSERT INTO memos (id_memo) VALUES ('$tmp_id_memo')";
	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
	{
	//memo has been created
		$tmp_date = time();
		$tmp_from = $_SESSION['user_name'];

		$tmpUpdateSQL =
			"UPDATE memos SET
			date = '$tmp_date',
			sender = '$tmp_from'
			WHERE id_memo ='$tmp_id_memo'";
		mysql_query($tmpUpdateSQL, $db);
		// show editor
		header("Location: index.php?module=post_memo&id_memo=$tmp_id_memo&new=true");
	}
}
    elseif (isset($_POST['submit_post_private']))
	{
	//add new memo
	$tmp_id_memo = create_guid($tmp_id_memo);
	$tmpInitSQL = "INSERT INTO memos (id_memo) VALUES ('$tmp_id_memo')";
	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
	{
	//memo has been created
		$tmp_date = time();
		$tmp_from = $_SESSION['user_name'];

		$tmpUpdateSQL =
			"UPDATE memos SET
			date = '$tmp_date',
			sender = '$tmp_from'
			WHERE id_memo ='$tmp_id_memo'";
		mysql_query($tmpUpdateSQL, $db);
		// show editor
		header("Location: index.php?module=post_memo_private&id_memo=$tmp_id_memo&new=true");
	} else {
	 // Failure
	echo "Unable to create memo.";
	}
} else {
	//display memo listings
	render_HeaderFooter("UCxml web Portal - Memo View");
	output_view_memos($tmp_id_user);
	render_Footer();
}

//
//  FUNCTIONS
//

function output_view_memos($myID_user)
{
	include "language/lang.php";
	global $db;
	$xtpl=new XTemplate ("modules/templates/view_memos_posted.html");
	$xtpl->assign( 'LANG', $lang );

	$obprefSQL = "SELECT memo_ob FROM users WHERE id_user='$myID_user'";
	$obRES = mysql_query($obprefSQL, $db);
	if ($gl = mysql_fetch_assoc($obRES))
	{
		$memo_ob = $gl['memo_ob'];
	} else {
		//sql error
		$memo_ob = "date";
	}
	//custum order by
	if (isset($_GET['ob']))
	{
		if ($_GET['ob'] == "ob_date")
		{
			$ob = "date DESC";
		} elseif ($_GET['ob'] == "ob_title") {
			$ob = "title";
		} elseif ($_GET['ob'] == "ob_access") {
			$ob = "access";
		} elseif ($_GET['ob'] == "ob_sender") {
			$ob = "sender";
   		} elseif ($_GET['ob'] == "ob_msg") {
			$ob = "msg";
		}
	} else {
		if ($memo_ob == "Date")
		{
			//global says to order by date, make order DESC, to show the oldest first
			$ob = $memo_ob." DESC";
		} else {
			//global says to order by sender or title, dont need to order by DESC
			$ob = $memo_ob;
		}
	}

    if ($_SESSION['account_type'] == 'Admin')
		{
			$xtpl -> parse ("main.admin_broadcast");
		}

	$theSQL = "SELECT id_memo,title,access,receiver,date,msg FROM memos ORDER BY $ob";
	$theRES = mysql_query($theSQL, $db);
	$oddRow = true;
	while ($in = mysql_fetch_assoc($theRES))
	{
		//Generate data rows
		if ($oddRow)
		{
			$xtpl->assign("bg","#F6F6F6");
		} else {
			$xtpl->assign("bg","#EFEFEF");
		}

		$tmp_unixtime = $in['date'];
		$displaydate = date("n/d, h:i A" ,$tmp_unixtime);

		$xtpl->assign("id_memo",$in['id_memo']);
		$xtpl->assign("title",$in['title']);
		$xtpl->assign("date",$displaydate);
		$xtpl->assign("access",$in['access']);
		$xtpl->assign("to",$in['receiver']);
		$xtpl->assign("msg",$in['msg']);

		$xtpl->parse("main.row_send");
		//alternate bg color
		$oddRow = !$oddRow;

        if ($in['title'] == '' && $in['msg'] == '' && $in['access'] == '')
			{
				//contacts has no information, delete the entry
				$tmp_delete_id_memo = $in['id_memo'];
				$sql = "DELETE FROM memos WHERE id_memo='$tmp_delete_id_memo'";
				$result = mysql_query($sql);
			}

	}

   	$theSQL = "SELECT memo_ob FROM users WHERE id_user='$myID_user'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("memo_ob",$in['memo_ob']);

		if ($in['memo_ob'] == "sender")
		{
			$xtpl->assign("sel_sender","selected");
		} elseif ($in['memo_ob'] == "date") {
			$xtpl->assign("sel_date","selected");
		} else {
			$xtpl->assign("sel_title","selected");
		}
	}
     else {
		echo "Unable to save preferences.";
	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>