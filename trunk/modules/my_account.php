<?php
/*
	UCxml web Portal - My Account

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

//Checks if id is known, stores in variable
$tmp_id_user = defang_input($_SESSION['user_id']);
$tmp_id_contact = defang_input($_SESSION['user_id']);

$user = "good"; //defaults user to good before chances of it beging invalid

if (isset($_POST['action']))
{
	//User wants to save, cancel, or delete object
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" )
	{
        if (isset($_POST['submit_save_account']))
		{
        	// Saving
			$tmp_id_user = defang_input($_POST['id_user']);

			$tmp_username = defang_input($_POST['username']);
			$unique_user_sql = "SELECT username,id_user FROM `users` WHERE username = '$tmp_username' AND id_user != '$tmp_id_user'";
			$other_usernames = mysql_query($unique_user_sql, $db);

			$tmp_raw_password = defang_input($_POST['password0']);

			$tmp_password = md5($tmp_raw_password);

			if ($tmp_raw_password != "password_is_saved1")
			{
				//password was changed, save
				$password_sql = "password = '$tmp_password',";
			} else {
				//password was not changed, dont save
				$password_sql = "";
			}

			$tmp_email = defang_input($_POST['email']);


			if ($un = mysql_fetch_assoc($other_usernames))
			{
				//There is already a user with this name
				render_HeaderFooter("UCxml web Portal - User Edit");
				$user = "bad";
				output_edit_user($tmp_id_user,$tmp_id_contact,$user);
				render_Footer();

			} else {
				if ($unique['username'] != $tmp_username)
				{
					$tmpUpdateSQL = "UPDATE users SET
						username = '$tmp_username',
						$password_sql,
						email = '$tmp_email'
						WHERE id_user ='$tmp_id_user'";
					mysql_query($tmpUpdateSQL, $db);
					header("Location: index.php?module=menu");
				}
			}


		} else if (isset($_POST['submit_cancel_account'])) {
			// Cancel
			header("Location: index.php?module=menu");
        }

  		elseif (isset($_POST['submit_save_contact']))
		{

			$tmp_id_contact = defang_input($_POST['id_contact']);
			$tmp_member_of = defang_input($_POST['member_of']);
			$tmp_lname = defang_input($_POST['lname']);
			$tmp_fname = defang_input($_POST['fname']);
			$tmp_username = defang_input($_POST['nick']);
			$tmp_title = defang_input($_POST['title']);
			$tmp_office_phone = defang_input($_POST['office_phone']);
			$tmp_home_phone = defang_input($_POST['home_phone']);
			$tmp_custom_phone = defang_input($_POST['custom_phone']);
			$tmp_custom_number = defang_input($_POST['custom_number']);
			$tmp_cell_phone = defang_input($_POST['cell_phone']);
			$tmp_other_phone = defang_input($_POST['other_phone']);

			//Create clean name for display_name column in contacts table.
			//This is the name used to order and display the contacts on the phone UI
			if ($tmp_lname != '' || $tmp_fname != '')
			{
				if ($tmp_lname != '' && $tmp_fname != '')
				{
					$tmpTitle = $tmp_lname.", ".$tmp_fname;
				} else {
					$tmpTitle = $tmp_lname.$tmp_fname;
				}
				if ($tmp_nick != '')
				{
					$tmpTitle = $tmpTitle.' - '.$tmp_nick;
				}
        	} elseif ($tmp_nick != '') {
				//lname,fname is not specified, display nick
				$tmpTitle = $tmp_nick;
			} else {
				$tmpTitle = $tmp_nick;
			}

			$tmpUpdateSQL = "UPDATE contacts SET
				member_of='$tmp_member_of',
				display_name= '$tmpTitle',
				fname='$tmp_fname',
				lname='$tmp_lname',
				nick='$tmp_username',
				title='$tmp_title',
				office_phone='$tmp_office_phone',
				home_phone='$tmp_home_phone',
				custom_phone='$tmp_custom_phone',
				custom_number='$tmp_custom_number',
				cell_phone='$tmp_cell_phone',
				other_phone='$tmp_other_phone'
				WHERE id_contact='$tmp_id_contact'";

				mysql_query($tmpUpdateSQL, $db);

			header("Location: index.php?module=view_menu");// Saving
			}
	   else if (isset($_POST['submit_cancel_contact'])) {
			// Cancel
			header("Location: index.php?module=menu");
		}
	  elseif(isset($_POST['submit_av'])) {
      avatar_submit();
	  header("Location: index.php?module=my_account");
		}

	   else {
			// Action, but no valid submit button.
			header("Location: index.php?module=my_account");
		}

	} else {
		// Bad action
	header("Location: index.php?module=my_account");
	}

} else {
	// NO action
	render_HeaderFooter("UCxml web Portal - User Edit");
	output_edit_user($tmp_id_user,$tmp_id_contact,$user, $errMsg);
	render_Footer();
}

//Create page and fill in known data
function output_edit_user ($myID_user, $myID_contact, $user, $errMsg)
{
	global $db, $xtpl, $delete;
	$target = "images/avatars/";
	$xtpl=new XTemplate ("modules/templates/my_account.html");

   	if( $errMsg )
	{
	$xtpl->assign("error_msg",$errMsg);
	}

	$theSQL = "SELECT * FROM users WHERE id_user='$myID_user'";
    $theRES = mysql_query($theSQL, $db);
    if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_user",$in['id_user']);
		$xtpl->assign("fake_password","password_is_saved1");//do not output real password.
		$xtpl->assign("email",$in['email']);
		$xtpl->assign("username",$in['username']);
		$xtpl->assign("av",$in['av']);

		// if the user does not have an avatar, echo the default avatar
		// if the user has an avatar echo the path to it (whether it is just uploaded, or is already saved)
		$xtpl->assign("current_av",$_SESSION['ucp_av_ext']? 'index.php?tmpimg&amp;'.'.'. $_SESSION['ucp_av_ext'] : $target . ($_SESSION['av']? $_SESSION['user_id'] .'.'. $in['av'] : 'default.gif'));
		$xtpl->assign("default_av",$_SESSION['ucp_av_ext']? ' (unsaved)' : '');

	// if the user has an avatar, or just uploaded one
		if( $in['av'] || $_SESSION['ucp_av_ext'] )
		{
			// show a delete checkbox
		$xtpl->assign ("delete", $delete? ' checked="checked"' : '');
		}
	}

	if ($user == "bad")
	{
		$xtpl->parse("main.bad_username");
		$xtpl->assign("email",defang_input($_POST['email']));
		$xtpl->assign("username",defang_input($_POST['username']));
		$xtpl->assign("password",defang_input($_POST['password']));
	}

    $theSQL2 = "SELECT * FROM contacts WHERE id_contact='$myID_contact'";
	$theRES2 = mysql_query($theSQL2, $db);
	if ($in = mysql_fetch_assoc($theRES2))
	{
		$xtpl->assign("id_contact",$in['id_contact']);
		$xtpl->assign("member_of",$in['member_of']);
		$xtpl->assign("lname",$in['lname']);
		$xtpl->assign("fname",$in['fname']);
		$xtpl->assign("title",$in['title']);
		$xtpl->assign("office_phone",$in['office_phone']);
		$xtpl->assign("home_phone",$in['home_phone']);

		if ($in['custom_phone'] != '')
		{
			$xtpl->assign("custom_phone",$in['custom_phone']);
		} else {
			$xtpl->assign("custom_phone","Create Custom");
		}

		$xtpl->assign("custom_number",$in['custom_number']);
		$xtpl->assign("cell_phone",$in['cell_phone']);
		$xtpl->assign("other_phone",$in['other_phone']);

		$xtpl->assign("date",$in['date']);


		if ($in['member_of'] == "B512")
		{
			$xtpl->assign("sel_b512", "selected");
			$xtpl->assign("sel_b521", "");
			$xtpl->assign("sel_emergency", "");
			$xtpl->assign("sel_choose", "");
		}
        elseif ($in['member_of'] == "B521")
		{
			$xtpl->assign("sel_b512", "");
			$xtpl->assign("sel_b521", "selected");
			$xtpl->assign("sel_emergency", "");
			$xtpl->assign("sel_choose", "");
		}
        elseif ($in['member_of'] == "Emergency")
		{	$xtpl->assign("sel_b512", "");
			$xtpl->assign("sel_b521", "");
			$xtpl->assign("sel_emergency", "selected");
			$xtpl->assign("sel_choose", "");
		}
        else
		{
			$xtpl->assign("sel_b512", "");
			$xtpl->assign("sel_b521", "");
			$xtpl->assign("sel_emergency", "");
			$xtpl->assign("sel_choose", "selected");
		}

    }

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}

function avatar_submit()
{
   // global so we can use accross functions
   	global $db, $delete;

	$target = "images/avatars/";
    $tmp_id_user = $_SESSION['user_id'];
	$checkSQL = "SELECT av FROM users WHERE id_user = '$tmp_id_user'";
	$checkRES = mysql_query($checkSQL, $db);

	if ($in = mysql_fetch_assoc($checkRES))
	{
		if( $in['av'] )
		{
			$_SESSION['av'] = $in['av'];
		}
    }

	// get the required $_POST data
	$delete = (bool)$_POST['av_delete'];

	// if user did NOT set to delete their avatar
	if( !$delete )
	{
		// if the url is set and is not the default value
		if( is_uploaded_file($_FILES['av_file']['tmp_name']) )
		{
			// check that the filesize is okay
			if( $_FILES['av_file']['size'] > 30720 )
			{
				// show them how far over they went
				$errMsg = 'Max file size for avatar is 30 KB and yours was '. round($_FILES['av_file']['size']/1024, 2) .' KB.';
			}
			// the filesize is okay
			else
			{
				// set $f to the tmp file
				$f = $_FILES['av_file']['tmp_name'];
			}
		}

		// if there was an uploaded (or transloaded) file $f tmp file
		if( $f )
		{
			// get the imagesize on the tmp file
			if( ($ii = getimagesize($f)) )
			{
				// depending on the type
				switch($ii[2])
				{
					case 1:
						$av = 'gif';
						break;
					case 2:
						$av = 'jpg';
						break;
					case 3:
						$av = 'png';
						break;
					default:
						$errMsg = "Invalid image type for avatar.";

						// delete the file
						unlink($f);

						// unset $f so our script knows not to save it
						unset($f);
				}

				// if there is a valid avatar (up|trans)load
				if( $av )
				{
					// check that the image width and height is less than 100
					if( $ii[0] > 100 || $ii[1] > 100 )
					{
						$errMsg = 'Max image dimensions for avatar is 100x100 pixels. Yours was '. $ii[0] .'x'. $ii[1] .' pixels.';

						// delete the file
						unlink($f);

						// make sure our script knows that there wasnt a valid avatar
						unset($av, $f);
					}
				}
			}
			// if getimagesize fails, it was not a valid image
			else
			{
				$errMsg = 'The file you supplied for avatar (location or file upload) was invalid.';
			}
		}
    }
	// otherwise the user checked the box to delete their avatar
	else
	{
		// if the user has an unsaved avatar, delete it
		unset($_SESSION['ucp_av'], $_SESSION['ucp_av_ext']);
	}

	// if any errors during processing
	if( $errMsg )
	{
		// if an avatar was (up|trans)loaded
		if( $av )
		{
			// save it to the user's session
			$_SESSION['ucp_av'] = base64_encode(file_get_contents($f));
			$_SESSION['ucp_av_ext'] = $av;

			// delete the tmp file
			unlink($f);

			// unset the tmp file location
			unset($f);
		}

		// show the form again with the errors
		output_edit_user($tmp_id_user,$tmp_id_contact,$user, $errMsg);
	}
	// otherwise everything went okay
	else
	{
		// if the user just (up|trans)loaded an avatar, or has an unsaved one in their session
		if( $av || $_SESSION['ucp_av_ext'] )
		{
			// if they have a previous avatar
			if( $_SESSION['av'] )
			{
				// delete it
				unlink($target. $_SESSION['user_id'] .'.'. $_SESSION['av']);
			}

			// if the avatar is in a tmp file
			if( $av )
			{
				// move the file to the avatar directory
				rename($f, $target. $_SESSION['user_id'] .'.'. $av);
			}
			// otherwise it is in their session
			else
			{
				// set $av to the file extension so we can save it to our session
				$av = $_SESSION['ucp_av_ext'];

				// save the new avatar
				file_put_contents($target. $_SESSION['user_id'] .'.'. $av, base64_decode($_SESSION['ucp_av']));

				// delete the tmp avatar from the session
				unset($_SESSION['ucp_av_ext'], $_SESSION['ucp_av']);
			}

			// save the new extension
			$_SESSION['av'] = $av;
		}

		// if we just saved an avatar
		if( $av )
		{
			// set to update the av field in the user table to the new extension
			$tmpUpdateSQL = "UPDATE users SET av = '$av' WHERE id_user ='$tmp_id_user'";
			mysql_query($tmpUpdateSQL, $db);
		}

		// otherwise check that they didnt select to delete the avatar
		elseif( $delete && $_SESSION['av'] )
		{
			// update the user table with no avatar
			$tmpUpdateSQL = "UPDATE users SET av = '0' WHERE id_user ='$tmp_id_user'";
			mysql_query($tmpUpdateSQL, $db);

			// delete the avatar
			unlink($target. $_SESSION['user_id'] .'.'. $_SESSION['av']);

			// so the user sees the default avatar now
			unset($_SESSION['av']);
		}
	}
}

?>