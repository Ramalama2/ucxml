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

if (isset($_POST['action']))
{
	//User wants to save, cancel, or delete memo
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" || $_GET['submit_delete'] == 'yes')
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_id_memo = defang_input($_POST['id_memo']);
			$tmp_title = defang_input($_POST['title']);
			$tmp_access = defang_input($_POST['access']);
			$tmp_msg = defang_input($_POST['msg']);
			$tmp_receiver = defang_input($_POST['receiver']);

			$tmpUpdateSQL = "UPDATE memos SET
				title = '$tmp_title',
				msg = '$tmp_msg',
				access = '$tmp_access',
   				receiver = '$tmp_receiver'
				WHERE id_memo ='$tmp_id_memo'";

			if (mysql_query($tmpUpdateSQL, $db))
			{
				header("Location: index.php?module=view_memos");
			} else {
				echo "Unable to save memo.";
			}

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			if ($_GET['new'] == 'true')
			{
				delete_memo($tmp_id_memo);
			}
			header("Location: index.php?module=view_memos");

		} else if (isset($_POST['submit_delete']) || $_GET['submit_delete'] == 'yes') {
				// Deleting
				if ($tmp_id_memo != '0') //prevent user from deleting main container
				{
					delete_memo($tmp_id_memo);
					header("Location: index.php?module=view_memos");
				} else {
					header("Location: index.php?module=delete_error");
				}
		} else {
			// Action, but no valid submit button.
			header("Location: index.php?module=view_memos");
		}
	} else {
		// Bad action
		header("Location: index.php?module=view_memos");
	}
} else {
	// No action
	render_HeaderFooter("UCxml web Portal - Edit Memo");
	output_edit_memo($tmp_id_memo);
	render_Footer();
}

//
//  FUNCTIONS
//

function delete_memo ($tmp_id_memo)
{
	$sql = "DELETE FROM memos WHERE id_memo='$tmp_id_memo'";
	$result = mysql_query($sql);
}

//Create page and fill in known data
function output_edit_memo ($myID_memo)
{

	include "language/lang.php";
	global $db;
	$xtpl=new XTemplate ("modules/templates/post_memo_private.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT nick FROM phone";
	$theRES = mysql_query($theSQL, $db);
	while ($row = mysql_fetch_row($theRES))
	{
		foreach ($row as $value)
		{
		$xtpl->assign ("receiver", $value);
  		$xtpl->parse('main.TO');
		}
	}

	$theSQL = "SELECT * FROM memos WHERE id_memo='$myID_memo'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$tmp_unixtime = $in['date'];
		$displaydate = date("l, F d, Y h:i" ,$tmp_unixtime);

		$xtpl->assign("id_memo",$in['id_memo']);
		$xtpl->assign("date",$displaydate);
		$xtpl->assign("title",$in['title']);
		$xtpl->assign("msg",$in['msg']);
		$xtpl->assign("from",$in['sender']);

		if ($in['access'] == "Private")
		{
			$xtpl->assign("access","Private");
			$xtpl->assign("var_access","Public");

		} else {
			$xtpl->assign("access","Public");
			$xtpl->assign("var_access","Private");
		}
	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>