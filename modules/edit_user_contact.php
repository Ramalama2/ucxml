<?php
/*
	UCxml web Portal - Edit user contact

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


$tmp_id_contact = defang_input($_GET['id_user']);
$tmp_id_user = defang_input($_GET['id_user']);

if (isset($_POST['action']))
{
	//User wants to save, cancel, or delete object
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" )
	{
  		if (isset($_POST['submit_save_contact']))
		{

			$tmp_id_contact = defang_input($_POST['id_contact']);
			$tmp_member_of = defang_input($_POST['member_of']);
			$tmp_lname = defang_input($_POST['lname']);
			$tmp_fname = defang_input($_POST['fname']);
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

   			if ($tmp_custom_phone != "Create Custom")
			{
				//custom_phone changed, save
				$custom_phone_sql = "custom_phone = '$tmp_custom_phone',";
			} else {
				//custom_phone was not changed, dont save
				$custom_phone_sql = "";
			}

			$tmpUpdateSQL = "UPDATE contacts SET
				member_of='$tmp_member_of',
				display_name= '$tmpTitle',
				fname='$tmp_fname',
				lname='$tmp_lname',
				title='$tmp_title',
				office_phone='$tmp_office_phone',
				home_phone='$tmp_home_phone',
				$custom_phone_sql
				custom_number='$tmp_custom_number',
				cell_phone='$tmp_cell_phone',
				other_phone='$tmp_other_phone'
				WHERE id_contact='$tmp_id_contact'";

				if (mysql_query($tmpUpdateSQL, $db))
				header("Location: index.php?module=edit_user_contact&id_user=$tmp_id_contact"); // Saving
	}
	   else if (isset($_POST['submit_cancel_contact'])) {
			// Cancel
			header("Location: index.php?module=view_users");
		}

	else {
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
	output_edit_user($tmp_id_user, $tmp_id_contact);
	render_Footer();
}

//Create page and fill in known data
function output_edit_user ($myID_user, $myID_contact)
{
	include "language/lang.php";
	global $db, $xtp;
	$xtpl=new XTemplate ("modules/templates/edit_user_contact.html");
	$xtpl->assign( 'LANG', $lang );


	$theSQL = "SELECT id_user FROM users WHERE id_user='$myID_user'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id_user",$in['id_user']);
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
?>