<?php
/*
	UCxml web Portal - My Account

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/

//Checks if id is known, stores in variable
$tmp_id_user = defang_input($_SESSION['user_id']);

if (isset($_POST['action']))
{
	//User wants to save, cancel, or delete object
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" )
	{
      if (isset($_POST['submit_lang']))
		{
			// Saving
			$tmp_lang = defang_input($_POST['lang']);
			$tmpUpdateSQL ="UPDATE users
							SET	lang = '$tmp_lang'
							WHERE id_user ='$tmp_id_user'";
			mysql_query($tmpUpdateSQL, $db);

	        $checkSQL = "SELECT lang FROM users WHERE id_user = '$tmp_id_user'";
			$checkRES = mysql_query($checkSQL, $db);
			if ($in = mysql_fetch_assoc($checkRES))
			{
				if( $in['lang'] )
				{
					$_SESSION['lang'] = $in['lang'];
				}
		    }
			if (isset($_GET['lang']))
			{
				require_once "language/lang.php";
		    }

			header("Location: index.php?module=my_account_lang");
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
	output_edit_user($tmp_id_user);
	render_Footer();
}

//Create page and fill in known data
function output_edit_user ($myID_user)
{

	include "language/lang.php";
	global $db, $xtpl;
	$target = "images/avatars/";
	$xtpl=new XTemplate ("modules/templates/my_account_lang.html");
	$xtpl->assign( 'LANG', $lang );

   	$obprefSQL = "SELECT lang FROM users WHERE id_user='$myID_user'";
	$obRES = mysql_query($obprefSQL, $db);
	if ($gl = mysql_fetch_assoc($obRES))
	{
		$lang = $gl['lang'];
	} else {
		//sql error
		$lang = "en";
	}

	$theSQL = "SELECT * FROM users WHERE id_user='$myID_user'";
    $theRES = mysql_query($theSQL, $db);
    if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_user",$in['id_user']);
		$xtpl->assign("lang",$in['lang']);


   		if ($in['lang'] == "sk")
		{
			$xtpl->assign("sel_sk", "selected");
			$xtpl->assign("sel_en", "");
		}
        elseif ($in['lang'] == "en")
		{
			$xtpl->assign("sel_sk", "");
			$xtpl->assign("sel_en", "selected");
		}
	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}


?>