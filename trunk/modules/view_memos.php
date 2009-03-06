<?php
/*
	UCxml web Portal - View memos
	
	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:	
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/



$myPref = "primary";
if (isset($_POST['submit_save'])) 
{
	// Saving
		$tmp_memo_ob = defang_input($_POST['memo_ob']);
		$tmpUpdateSQL = 
			"UPDATE memos 
			SET	memo_ob = '$tmp_memo_ob'
			WHERE preference = '$myPref'";

		mysql_query($tmpUpdateSQL, $db);
		header("Location: index.php?module=view_memos");
} 

elseif (isset($_POST['submit_add']))
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
		header("Location: index.php?module=edit_memos&id_memo=$tmp_id_memo&new=true");
	} else {
	 // Failure
	echo "Unable to create memo.";
	}
} else {
	//display memo listings
	render_HeaderFooter("UCxml web Portal - Memo View");
	output_view_memos();
	render_Footer();
}

//
//  FUNCTIONS
//

function output_view_memos()
{
	include "language/lang.php";
	global $db;
	$xtpl=new XTemplate ("modules/templates/view_memos.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT * FROM memos WHERE preference = 'primary'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		if ($in['memo_ob'] == "Sender")
		{
			$xtpl->assign("selected_sender",'selected');
		} elseif ($in['memo_ob'] == "Date") {
			$xtpl->assign("selected_date",'selected');
		} else {
			$xtpl->assign("selected_Title",'selected');
		}
	}
	 else {
		echo "Unable to save preferences.";
	}

	$obprefSQL = "SELECT memo_ob FROM memos WHERE preference = 'primary'";
	$obRES = mysql_query($obprefSQL, $db);
	if ($gl = mysql_fetch_assoc($obRES))
	{
		$memo_ob = $gl['memo_ob'];
	} else {
		//sql error
		$memo_ob = "Date";
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

	$theSQL = "SELECT id_memo,title,access,sender,date FROM memos ORDER BY $ob";
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
		$xtpl->assign("from",$in['sender']);

		$xtpl->parse("main.row");
		//alternate bg color
		$oddRow = !$oddRow;
	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>