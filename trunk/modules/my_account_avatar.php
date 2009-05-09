<?php
/*
	UCxml web Portal - My Account avatar

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com

*/

//Checks if id is known, stores in variable
$tmp_id_user = defang_input($_SESSION['user_id']);
global $errMsg;

if (isset($_POST['action']))
{
	//User wants to save, cancel, or delete object
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" )
	{
	  if(isset($_POST['submit_av'])) {
      avatar_submit();
//	  header("Location: index.php?module=my_account_avatar");
		}

	} else {
		// Bad action
	header("Location: index.php?module=my_account");
	}

} else {
	// NO action
	render_HeaderFooter("UCxml web Portal - User Edit");
	output_edit_user($tmp_id_user, $errMsg);
	render_Footer();
}

//Create page and fill in known data
function output_edit_user ($myID_user, $errMsg)
{
	include "language/lang.php";
	global $db, $xtpl, $delete;
	$target = "images/avatars/";
	$xtpl=new XTemplate ("modules/templates/my_account_avatar.html");
	$xtpl->assign( 'LANG', $lang );

	if( $errMsg )
	{
		$xtpl->assign("error_msg",$errMsg);
	 	$xtpl->parse("main.error");
	}

	$theSQL = "SELECT id_user,av FROM users WHERE id_user='$myID_user'";
    $theRES = mysql_query($theSQL, $db);
    if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_user",$in['id_user']);
		if( $in['av'] )
		{
			$_SESSION['av'] = $in['av'];
		}

		// if the user does not have an avatar, echo the default avatar
		// if the user has an avatar echo the path to it (whether it is just uploaded, or is already saved)
		$xtpl->assign("current_av",$_SESSION['ucp_av_ext']? 'index.php?tmpimg&amp;'.'.'. $_SESSION['ucp_av_ext'] : $target . ($_SESSION['av']? $_SESSION['user_id'] .'.'. $in['av'] : 'default.png'));
		$xtpl->assign("default_av",$_SESSION['ucp_av_ext']? ' (unsaved)' : '');

	// if the user has an avatar, or just uploaded one
		if( $in['av'] || $_SESSION['ucp_av_ext'] )
		{
			// show a delete checkbox
		$xtpl->assign ("delete", $delete? ' checked="checked"' : '');
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
				$errMsg = 'The file you supplied for avatar was invalid.';
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
	render_HeaderFooter("UCxml web Portal - User Edit");
	output_edit_user($tmp_id_user, $errMsg);
	render_Footer();
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