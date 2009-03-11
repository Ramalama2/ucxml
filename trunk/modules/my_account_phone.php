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
$tmp_id_phone = defang_input($_SESSION['user_id']);

if (isset($_POST['action']))
{
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit")
	{
		if (isset($_POST['submit_save_phone']))
		{
			// Saving

	   		if (defang_input($_POST['ph_sec']) == "ph_sec")
			{
				$tmp_ph_sec = "yes";
			} else {
				$tmp_ph_sec = "no";
			}

			$tmp_id_phone = defang_input($_POST['id_phone']);
			$tmp_access_lvl = defang_input($_POST['access_lvl']);
			$tmp_memo_ob = defang_input($_POST['memo_ob']);
			$tmp_refresh = defang_input($_POST['refresh']);

			$tmpUpdateSQL = "UPDATE users
						SET memo_ob = '$tmp_memo_ob',
	       				ph_sec = '$tmp_ph_sec'
					WHERE id_user ='$tmp_id_user'";
			mysql_query($tmpUpdateSQL, $db);

			$tmpUpdateSQL2 = "UPDATE phone
					SET access_lvl = '$tmp_access_lvl',
						refresh = '$tmp_refresh'
					WHERE id_phone ='$tmp_id_phone'";

			mysql_query($tmpUpdateSQL2, $db);
			header("Location: index.php?module=my_account_phone");

		} else if (isset($_POST['submit_cancel_phone'])) {
			// Cancel
			header("Location: index.php?module=menu");
		} else {
			// Action, but no valid submit button.
			header("Location: index.php?module=my_account");
		}

	} else {
		// Bad action
		header("Location: index.php?module=my_account");
	}

} else {
	// NO action
	render_HeaderFooter("UCxml web Portal - Edit Phone Registrations");
	output_edit_user($tmp_id_phone, $tmp_id_user);
	render_Footer();
}

//Create page and fill in known data
function output_edit_user ($myID_phone, $myID_user)
{
	include "language/lang.php";
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/my_account_phone.html");
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

	$theSQL = "SELECT id_phone, nick, number, MAC, access_lvl, refresh FROM phone WHERE id_phone='$myID_phone'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_phone",$in['id_phone']);
		$xtpl->assign("nick",$in['nick']);
   		$xtpl->assign("number",$in['number']);
		$xtpl->assign("MAC",$in['MAC']);

		if ($in['access_lvl'] == "Restricted")
		{
			$xtpl->assign("selected_restricted","selected");
			$xtpl->assign("selected_unrestricted","");
			$xtpl->assign("selected_unknown","");

		} else if ($in['access_lvl'] == "Unrestricted"){
			$xtpl->assign("selected_restricted","");
			$xtpl->assign("selected_unrestricted","selected");
			$xtpl->assign("selected_unknown","");

		} else {
			//unknown is selected
			$xtpl->assign("selected_restricted","");
			$xtpl->assign("selected_unrestricted","");
			$xtpl->assign("selected_unknown","selected");
		}
        //TODO: rewrite this section with array and foreach
		if ($in['refresh'] == "60")
		{
			$xtpl->assign("sel_60","selected");
			$xtpl->assign("sel_120","");
			$xtpl->assign("sel_480","");

		} else if ($in['refresh'] == "120")
		{
			$xtpl->assign("sel_60","");
			$xtpl->assign("sel_120","selected");
			$xtpl->assign("sel_480","");
		} else {
			//480 is selected
			$xtpl->assign("sel_60","");
			$xtpl->assign("sel_120","");
			$xtpl->assign("sel_480","selected");
		}

	}

	   	$theSQL = "SELECT ph_sec, memo_ob FROM users WHERE id_user='$myID_user'";
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

   			if ($in['ph_sec'] == "yes")
			{
				$xtpl->assign("ph_sec_check",'CHECKED'); //place check in box
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