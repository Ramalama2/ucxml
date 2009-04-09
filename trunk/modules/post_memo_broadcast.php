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
	$tmp_receiver = defang_input($_SESSION['user_name']);
}


if (isset($_POST['action']) || isset($_GET['submit_delete']) || isset($_GET['read_memo_broadcast']))
{
	//User wants to save, cancel, or delete memo
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" || $_GET['submit_delete'] == 'yes' || $_GET['read_memo_broadcast'] =='yes')
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_id_memo = defang_input($_POST['id_memo']);
			$tmp_title = defang_input($_POST['title']);
			$tmp_access = defang_input($_POST['access']);
			$tmp_msg = defang_input($_POST['msg']);

			$tmpUpdateSQL = "UPDATE memos SET
				title = '$tmp_title',
				msg = '$tmp_msg',
				access = '$tmp_access'
				WHERE id_memo ='$tmp_id_memo'";

			if (mysql_query($tmpUpdateSQL, $db))
			{
				header("Location: index.php?module=view_memos_broadcast");
			} else {
				echo "Unable to save memo.";
			}

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			if ($_GET['new'] == 'true')
			{
				delete_memo($tmp_id_memo);
			}
			header("Location: index.php?module=view_memos_broadcast");

		} else if (isset($_POST['submit_delete']) || $_GET['submit_delete'] == 'yes') {
				// Deleting
				if ($tmp_id_memo != '0') //prevent user from deleting main container
				{
					delete_memo($tmp_id_memo);
					header("Location: index.php?module=view_memos_broadcast");
				} else {
					header("Location: index.php?module=delete_error");
				}

		}elseif (isset($_POST['read_memo_broadcast']) || $_GET['read_memo_broadcast'] == 'yes' )
		{

  			if ($_SESSION['account_type'] == "Admin")
			{
				output_view_memo($tmp_id_memo);
			}
            else
			{
				$theSQL = "SELECT memos_read.receiver FROM memos_read WHERE memos_read.receiver != '' AND memos_read.id_memo = '$tmp_id_memo'";
				$theRES = mysql_query($theSQL, $db);
				if ($in = mysql_fetch_assoc($theRES))
				{
	                if ($in['receiver'] == $tmp_receiver)
					{
						output_view_memo($tmp_id_memo);
					}
					else
					{
	            	$tmp_id_memo_read = create_guid($tmp_id_memo_read);
					$tmpInitSQL = "INSERT INTO memos_read (id_memo_read, id_memo)
									VALUES ('$tmp_id_memo_read', '$tmp_id_memo')";

		           	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
					{
						$tmpUpdateSQL = "UPDATE memos_read SET
										memos_read.receiver = '$tmp_receiver',
	                            		memos_read.read = '1',
										memos_read.new = '0'
										WHERE id_memo_read ='$tmp_id_memo_read'";

						if (mysql_query($tmpUpdateSQL, $db))
						{
							output_view_memo($tmp_id_memo);
						}
						else
						{
							echo "error";
						}
					}
	              	}
                }
			}
		} else {
			// Action, but no valid submit button.
			header("Location: index.php?module=view_memos_broadcast");
		}
	} else {
		// Bad action
		header("Location: index.php?module=view_memos_broadcast");
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

   	$sql2 = "DELETE FROM memos_read WHERE id_memo='$tmp_id_memo'";
	$result2 = mysql_query($sql2);
}


//Create page and fill in known data
function output_edit_memo ($myID_memo)
{

	include "language/lang.php";
	global $db;
	$xtpl=new XTemplate ("modules/templates/post_memo_broadcast.html");
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

function output_view_memo ($myID_memo)
{
	include "language/lang.php";

	global $db;
	$xtpl=new XTemplate ("modules/templates/post_memo_broadcast.html");

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