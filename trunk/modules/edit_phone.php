<?php
/*
	UCxml web Portal - Edit phone

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


//Checks if id is known, stores in variable
if (isset($_GET['id_phone'])) $tmp_id_phone = defang_input($_GET['id_phone']);

if (isset($_POST['action']) || isset($_GET['submit_delete']))
{
	//User wants to save, cancel, or delete object

	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" || $_GET['submit_delete'] == yes)
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_id_phone = defang_input($_POST['id_phone']);
			$tmp_MAC = defang_input($_POST['MAC']);
			$tmp_access_lvl = defang_input($_POST['access_lvl']);
			$tmp_number = defang_input($_POST['number']);
			$tmp_nick = defang_input($_POST['nick']);

			$tmpUpdateSQL = "UPDATE phone SET
				MAC = '$tmp_MAC',
				number = '$tmp_number',
				nick = '$tmp_nick',
				access_lvl = '$tmp_access_lvl'
				WHERE id_phone ='$tmp_id_phone'";

			if (mysql_query($tmpUpdateSQL, $db))
			{
				header("Location: index.php?module=view_phones");
			} else {
				echo "Unable to edit phone.";
			}

		} else if (isset($_POST['submit_delete']) || $_GET['submit_delete'] == 'yes') {
			// Deleting
			$tmp_id_phone = defang_input($tmp_id_phone);
			delete_phone($tmp_id_phone);
			header("Location: index.php?module=view_phones");

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			if ($_GET['new'] == "true")
			{
				
				delete_phone($tmp_id_phone);
			}
			header("Location: index.php?module=view_phones");
		} else {
			// Action, but no valid submit button.
			header("Location: index.php?module=view_phone");
		}
		
	} else {
		// Bad action
		header("Location: index.php?module=view_phone");
	}
	
} else {
	// NO action
	render_HeaderFooter("UCxml web Portal - Edit Phone Registrations");
	output_edit_phone($tmp_id_phone);
	render_Footer();
}

function delete_phone ($tmp_id_phone)
{
	$sql = "DELETE FROM phone WHERE id_phone='$tmp_id_phone'";
	$result = mysql_query($sql);
}

//Create page and fill in known data
function output_edit_phone ($myID_phone)
{
	include "language/lang.php";
	global $db;
	$xtpl=new XTemplate ("modules/templates/edit_phone.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT * FROM phone WHERE id_phone='$myID_phone'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_phone",$in['id_phone']);
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
		$xtpl->assign("number",$in['number']);
		$xtpl->assign("nick",$in['nick']);

	}
	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}


?>