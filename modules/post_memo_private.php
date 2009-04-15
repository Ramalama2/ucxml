<?php
/*
	UCxml web Portal - Edit memos (IM)

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/


//Checks if id is known, stores in variable
if (isset($_GET['id_memo']))
{
	$tmp_id_memo = defang_input($_GET['id_memo']);
}
$tmp_sender = defang_input($_SESSION['user_name']);

if (isset($_POST['action']) || isset($_GET['submit_delete']) || isset($_GET['delete_memo_sender']) || isset($_GET['read_memo']))
{
	//User wants to save, cancel, or delete memo
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" || $_GET['submit_delete'] == 'yes' || $_GET['delete_memo_sender'] == 'yes' || $_GET['read_memo'] == 'yes')
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_id_memo = defang_input($_POST['id_memo']);
			$tmp_title = defang_input($_POST['title']);
			$tmp_msg = defang_input($_POST['msg']);
			$tmp_receiver = defang_input($_POST['receiver']);

			$tmpUpdateSQL = "UPDATE memos SET
				title = '$tmp_title',
				msg = '$tmp_msg',
   				receiver = '$tmp_receiver'
				WHERE id_memo ='$tmp_id_memo'";

			if (mysql_query($tmpUpdateSQL, $db))
			{
				header("Location: index.php?module=view_memos_posted");
			} else {
				echo "Unable to save memo.";
			}

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			if ($_GET['new'] == 'true')
			{
				delete_memo($tmp_id_memo);
			}
			header("Location: index.php?module=view_memos_posted");

		} else if (isset($_POST['submit_delete']) || $_GET['submit_delete'] == 'yes') {
				// Deleting
				if ($tmp_id_memo != '0') //prevent user from deleting main container
				{
					delete_view_memo($tmp_id_memo);
					header("Location: index.php?module=view_memos_posted");
				} else {
					header("Location: index.php?module=delete_error");
				}
		} else if (isset($_POST['delete_memo_sender']) || $_GET['delete_memo_sender'] == 'yes') {
				// Deleting
			$tmpUpdateSQL = "UPDATE memos SET memos.del_sender ='1' WHERE memos.sender='$tmp_sender' AND memos.id_memo ='$tmp_id_memo'";
			if (mysql_query($tmpUpdateSQL, $db))
			{
				header("Location: index.php?module=view_memos_posted");
			}

		}elseif ($_GET['read_memo'] == 'yes')
		{
				output_view_memo($tmp_id_memo);

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
		$sender_av = $in['sender'];

		$xtpl->assign("id_memo",$in['id_memo']);
		$xtpl->assign("date",$displaydate);
		$xtpl->assign("title",$in['title']);
		$xtpl->assign("msg",$in['msg']);
		$xtpl->assign("from",$in['sender']);

	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}

function output_view_memo ($myID_memo)
{
	include "language/lang.php";

	global $db;
	$xtpl=new XTemplate ("modules/templates/post_memo_private.html");

	$xtpl->assign( 'LANG', $lang );

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
		$xtpl->assign("to",$in['receiver']);
	}

	$theSQL2 = "SELECT id_user,av FROM users WHERE username='$sender_av'";
	$theRES2 = mysql_query($theSQL2, $db);
	if ($in = mysql_fetch_assoc($theRES2))
	{
	     $target="images/avatars/";

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