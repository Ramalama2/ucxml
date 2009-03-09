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
			$tmp_id_phone = defang_input($_POST['id_phone']);
			$tmp_access_lvl = defang_input($_POST['access_lvl']);

			$tmpUpdateSQL = "UPDATE phone SET
				access_lvl = '$tmp_access_lvl'
				WHERE id_phone ='$tmp_id_phone'";

			if(mysql_query($tmpUpdateSQL, $db))
			{
				header("Location: index.php?module=my_account_phone");
            } else {
				echo "Unable to edit phone.";
			}

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
	output_edit_user($tmp_id_phone);
	render_Footer();
}

//Create page and fill in known data
function output_edit_user ($myID_phone)
{
	include "language/lang.php";
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/my_account_phone.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT id_phone, nick, number, MAC, access_lvl FROM phone WHERE id_phone='$myID_phone'";
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

	}
	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>