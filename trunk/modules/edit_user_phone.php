<?php
/*
	UCxml web Portal - Edit user phone

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

//Checks if id is known, stores in variable
$tmp_id_phone = defang_input($_GET['id_user']);
$tmp_id_user = defang_input($_GET['id_user']);

if (isset($_POST['action']))
{
	//User wants to save, cancel, or delete object
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit")
	{
		if (isset($_POST['submit_save_phone']))
		{
			// Saving
			$tmp_id_phone = defang_input($_POST['id_phone']);

			$tmp_MAC = defang_input($_POST['MAC']);
			$tmp_access_lvl = defang_input($_POST['access_lvl']);
			$tmp_number = defang_input($_POST['number']);

               $tmpUpdateSQL = "UPDATE phone SET
					MAC = '$tmp_MAC',
					access_lvl = '$tmp_access_lvl',
					number = '$tmp_number'
					WHERE id_phone ='$tmp_id_phone'";

		mysql_query($tmpUpdateSQL, $db);
		header("Location: index.php?module=edit_user_phone&id_user=$tmp_id_phone");

		} else if (isset($_POST['submit_cancel_phone'])) {
			// Cancel
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
	output_edit_user($tmp_id_user, $tmp_id_phone);
	render_Footer();
}

//Create page and fill in known data
function output_edit_user ($myID_user, $myID_phone)
{
	include "language/lang.php";
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/edit_user_phone.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT id_user FROM users WHERE id_user='$myID_user'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_user",$in['id_user']);
	}

	$theSQL = "SELECT id_phone, nick, MAC, access_lvl, number FROM phone WHERE id_phone='$myID_phone'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_phone",$in['id_phone']);
		$xtpl->assign("MAC",$in['MAC']);
		$xtpl->assign("nick",$in['nick']);
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
		$xtpl->assign("number",$in['number']);

	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>