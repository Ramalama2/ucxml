<?php
/*
	UCxml web Portal - Edit contact

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


$xtpl=new XTemplate ("modules/templates/edit_contact.html");

//Checks if id is known, stores in variable
if (isset($_GET['id']))
	$tmp_id = defang_input($_GET['id']);


if (isset($_POST['action']) || isset($_GET['submit_delete']))
{
	//User wants to save, cancel, or delete object

	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit" || $_GET['submit_delete'] == 'yes')
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_id = defang_input($_POST['id']);
			$tmp_member_of = defang_input($_POST['member_of']);
			$tmp_lname = defang_input($_POST['lname']);
			$tmp_fname = defang_input($_POST['fname']);
			$tmp_company = defang_input($_POST['company']);
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
				if ($tmp_company != '')
				{
					$tmpTitle = $tmpTitle.' - '.$tmp_company;
				}
			} elseif ($tmp_company != '') {
				//lname,fname is not specified, display company
				$tmpTitle = $tmp_company;
			} else {
				$tmpTitle = $tmp_company;
			}

			$tmpUpdateSQL = "UPDATE contacts SET
				member_of='$tmp_member_of',
				display_name= '$tmpTitle',
				fname='$tmp_fname',
				lname='$tmp_lname',
				company='$tmp_company',
				title='$tmp_title',
				office_phone='$tmp_office_phone',
				home_phone='$tmp_home_phone',
				custom_phone='$tmp_custom_phone',
				custom_number='$tmp_custom_number',
				cell_phone='$tmp_cell_phone',
				other_phone='$tmp_other_phone'
				WHERE id='$tmp_id'";

				mysql_query($tmpUpdateSQL, $db);

			header("Location: index.php?module=view_contacts");

		} else if (isset($_POST['submit_delete']) || $_GET['submit_delete'] == 'yes') {
			// Deleting
			$tmp_id = defang_input($tmp_id);
			delete_contact($tmp_id);
			header("Location: index.php?module=view_contacts&mbr_of=".$_GET['mbr_of']);

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			if ($_GET['new'] == "true")
			{
				delete_contact($tmp_id);
			}
			//delete_contact($tmp_id);
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
	output_edit_contact($tmp_id);
	render_Footer();
}

function delete_contact ($tmp_id)
{
	$sql = "DELETE FROM contacts WHERE id='$tmp_id'";
    $result = mysql_query($sql);
}

//Create page and fill in known data
function output_edit_contact ($myId)
{
	global $db, $xtpl;

		//look up info specific to contact
	$theSQL = "SELECT * FROM contacts WHERE id='$myId'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("id",$in['id']);
		$xtpl->assign("member_of",$in['member_of']);
		$xtpl->assign("lname",$in['lname']);
		$xtpl->assign("fname",$in['fname']);
		$xtpl->assign("company",$in['company']);
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
	$xtpl->parse('main.cat_exist');
	$xtpl->parse('main');
	$xtpl->out("main");
}
?>