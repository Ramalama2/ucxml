<?php
/*
	UCxml web Portal - Edit contact

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


//Checks if id is known, stores in variable
if (isset($_GET['id_contact']))
	$tmp_id_contact = defang_input($_GET['id_contact']);


if (isset($_POST['action']) || isset($_GET['submit_delete']))
{
	//User wants to save, cancel, or delete object

	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" || $_GET['submit_delete'] == 'yes')
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_id_contact = defang_input($_POST['id_contact']);
			$tmp_member_of = defang_input($_POST['member_of']);
			$tmp_lname = defang_input($_POST['lname']);
			$tmp_fname = defang_input($_POST['fname']);
			$tmp_nick = defang_input($_POST['nick']);
			$tmp_title = defang_input($_POST['title']);
			$tmp_office_phone = defang_input($_POST['office_phone']);
			$tmp_home_phone = defang_input($_POST['home_phone']);
			$tmp_custom_phone = defang_input($_POST['custom_phone']);
			$tmp_custom_number = defang_input($_POST['custom_number']);
			$tmp_cell_phone = defang_input($_POST['cell_phone']);
			$tmp_other_phone = defang_input($_POST['other_phone']);
			$tmp_owner = defang_input($_POST['owner']);

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
				nick='$tmp_nick',
				title='$tmp_title',
				office_phone='$tmp_office_phone',
				home_phone='$tmp_home_phone',
				custom_phone='$tmp_custom_phone',
				custom_number='$tmp_custom_number',
				cell_phone='$tmp_cell_phone',
				owner='$tmp_owner',
				other_phone='$tmp_other_phone'
				WHERE id_contact='$tmp_id_contact'";

				mysql_query($tmpUpdateSQL, $db);

			header("Location: index.php?module=view_contacts");

		} else if (isset($_POST['submit_delete']) || $_GET['submit_delete'] == 'yes') {
			// Deleting
			$tmp_id_contact = defang_input($tmp_id_contact);
			delete_contact($tmp_id_contact);
			header("Location: index.php?module=view_contacts&mbr_of=".$_GET['mbr_of']);

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			if ($_GET['new'] == "true")
			{
				delete_contact($tmp_id_contact);
			}
			//delete_contact($tmp_id_contact);
			header("Location: index.php?module=view_contacts");

		} else {
			// Action, but no valid submit button.
			header("Location: index.php?module=edit_contact");
		}
	} else {
		// Bad action
		header("Location: index.php?module=edit_contact");
	}

} else {
	// No action
	render_HeaderFooter("UCxml web Portal - Editing");
	output_edit_contact($tmp_id_contact);
	render_Footer();
}

function delete_contact ($tmp_id_contact)
{
	$sql = "DELETE FROM contacts WHERE id_contact='$tmp_id_contact'";
	$result = mysql_query($sql);
}

//Create page and fill in known data
function output_edit_contact ($myID_contact)
{

	include "language/lang.php";

	global $db, $xtpl;

	$xtpl=new XTemplate ("modules/templates/edit_contact.html");
	$xtpl->assign( 'LANG', $lang );

	//look up info specific to contact
	$theSQL = "SELECT * FROM contacts WHERE id_contact='$myID_contact'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
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

        if ($_SESSION['account_type'] == 'Admin' || $tmp_owner = $in['owner'] )
		{
			$xtpl -> parse ("main.admin_owner");
		}

        $tmp_owner = $in['owner'];
        $theSQL = "SELECT username FROM users WHERE id_user='$tmp_owner'";
        $theRES = mysql_query($theSQL, $db);
          if ($in2 = mysql_fetch_assoc($theRES))
          {
            $tmp_owner_name = $in2['username'];
          } else {
            $tmp_owner_name = "Owner not found.";
            }
           $xtpl->assign("owner",$tmp_owner_name);
//           $xtpl->parse('main.cat_exist.own_noedit');


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
	$xtpl->parse('main');
	$xtpl->out("main");
}
?>