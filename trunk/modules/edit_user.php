<?php
/*
	UCxml web Portal - Edit user

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


//Checks if id is known, stores in variable
if (isset($_GET['id_user'])) $tmp_id_user = defang_input($_GET['id_user']);

$user = "good"; //defaults user to good before chances of it beging invalid
$domain = "zt.voip.cnl.tuke.sk";

if (isset($_POST['action']) || isset($_GET['submit_delete']))
{
	//User wants to save, cancel, or delete object
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" || $_GET['submit_delete'] == yes)
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_id_user = defang_input($_POST['id_user']);

			$tmp_username = defang_input($_POST['username']);
			$unique_user_sql = "SELECT username,id_user FROM `users` WHERE username = '$tmp_username' AND id_user != '$tmp_id_user'";
			$other_usernames = mysql_query($unique_user_sql, $db);

			$tmp_raw_password = defang_input($_POST['password0']);
			$tmp_password = md5($tmp_raw_password);
			$tmp_email = defang_input($_POST['email']);
			$tmp_account_type = defang_input($_POST['account_type']);

			if ($tmp_raw_password != "password_is_saved1")
			{
				//password was changed, save
				$password_sql = "password = '$tmp_password',";
			} else {
				//password was not changed, dont save
				$password_sql = "";
			}

			if ($un = mysql_fetch_assoc($other_usernames))
			{
				//There is already a user with this name
				render_HeaderFooter("UCxml web Portal - User Edit");
				$user = "bad";
				output_edit_user($tmp_id_user,$user);
				render_Footer();

			} else {
				if ($unique['username'] != $tmp_username)
				{

					$tmpUpdateSQL = "UPDATE users SET
						username = '$tmp_username',
						$password_sql
						email = '$tmp_email',
						account_type = '$tmp_account_type'
						WHERE id_user ='$tmp_id_user'";
					mysql_query($tmpUpdateSQL, $db);

					$tmpUpdateSQL2 = "UPDATE contacts SET
						nick = '$tmp_username'
						WHERE id_contact ='$tmp_id_user'";
					mysql_query($tmpUpdateSQL2, $db);

                    $tmpUpdateSQL3 = "UPDATE phone SET
						nick = '$tmp_username'
						WHERE id_phone ='$tmp_id_user'";
					mysql_query($tmpUpdateSQL3, $db);

                    $tmpUpdateSQL4 = "UPDATE phone SET
						nick = '$tmp_username'
						WHERE id_phone ='$tmp_id_user'";
					mysql_query($tmpUpdateSQL3, $db);

					header("Location: index.php?module=edit_user&id_user='$tmp_id_user'");
				}
			}
		} else if (isset($_POST['submit_delete']) || $_GET['submit_delete'] == 'yes') {
			// Deleting
			$tmp_id_user = defang_input($tmp_id_user);
			if ($tmp_id_user != '0' && $tmp_id_user != $_SESSION['user_id'])
			{
				delete_user($tmp_id_user);
			} else {
				//deleting of admin account is not allowed
				header("Location: index.php?module=view_users");
			}
			header("Location: index.php?module=view_users");

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			if ($_GET['new'] == "true")
			{
				delete_user($tmp_id_user);
			}
			header("Location: index.php?module=view_users");

		} else {
			// Action, but no valid submit button.
			header("Location: index.php?module=view_users");
		}

	} else {
		// Bad action
		header("Location: index.php?module=view_users");
	}

} else {
	// NO action
	render_HeaderFooter("UCxml web Portal - User Edit");
	output_edit_user($tmp_id_user, $user);
	render_Footer();
}

function delete_user ($tmp_id_user)
{
	$sql = "DELETE FROM users WHERE id_user='$tmp_id_user'";
	$result = mysql_query($sql);

	$sql2 = "DELETE FROM phone WHERE id_phone='$tmp_id_user'";
	$result2 = mysql_query($sql2);

	$sql3 = "DELETE FROM contacts WHERE id_contact='$tmp_id_user'";
	$result3 = mysql_query($sql3);
}

//Create page and fill in known data
function output_edit_user ($myID_user,$user)
{
	include "language/lang.php";
	global $db;
	$xtpl=new XTemplate ("modules/templates/edit_user.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT * FROM users WHERE id_user='$myID_user'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_user",$in['id_user']);
		$xtpl->assign("fake_password","password_is_saved1");//do not output real password.
		$xtpl->assign("email",$in['email']);
		$xtpl->assign("username",$in['username']);

		if ($in['account_type'] == "Admin")
		{
			$xtpl->assign("account_type","Admin");
			$xtpl->assign("var_account_type","User");
		} else {
			$xtpl->assign("account_type","User");
			$xtpl->assign("var_account_type","Admin");
		}
	}

		if ($_SESSION['user_id'] == $in['id_user'] || $in['id_user'] == '0')
		{
			//dont show delete
		} else {
			$xtpl->parse("main.notuser");
		}

	if ($user == "bad")
	{
		$xtpl->parse("main.bad_username");
		$xtpl->assign("email",defang_input($_POST['email']));
		$xtpl->assign("username",defang_input($_POST['username']));
		$xtpl->assign("password",defang_input($_POST['password']));

		if (defang_input($_POST['account_type']) == "Admin")
		{
			$xtpl->assign("account_type","Admin");
			$xtpl->assign("var_account_type","User");
		} else {
			$xtpl->assign("account_type","User");
			$xtpl->assign("var_account_type","Admin");
		}
	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>