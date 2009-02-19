<?php
/*

	UCxml web Portal - Index

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


session_start();
require_once "lib/utils.php";
require_once "lib/mysql.php";
require_once "lib/xtpl.php";

// Check Login Status
if (isset($_SESSION['user_id']))
{

	// Login OK
	if (isset($_GET['module']))
	{
		// Choose which module to view
		$ModuleName = defang_input($_GET['module']);
		if ($ModuleName == "menu"){
			require_once "modules/menu.php";

		} elseif ($ModuleName == "view_contacts") {
			require_once "modules/view_contacts.php";

		} elseif ($ModuleName == "edit_contact") {
			require_once "modules/edit_contact.php";

		} elseif ($ModuleName == "import_contacts") {
			require_once "modules/import_contacts.php";

		} elseif ($ModuleName == "view_status"){
			require_once "modules/view_status.php";

		} elseif ($ModuleName == "view_memos"){
			require_once "modules/view_memos.php";

		} elseif ($ModuleName == "edit_memos"){
			require_once "modules/edit_memos.php";

		} elseif ($ModuleName == "view_xcap"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/view_xcap.php";
			} else {
				require_once "modules/not_admin.php";
			}
		} elseif ($ModuleName == "edit_xcap"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/edit_xcap.php";
			} else {
				require_once "modules/not_admin.php";
			}

		} elseif ($ModuleName == "view_phones"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/view_phones.php";
			} else {
				require_once "modules/not_admin.php";
			}
		} elseif ($ModuleName == "edit_phone"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/edit_phone.php";
			} else {
				require_once "modules/not_admin.php";
			}

		} elseif ($ModuleName == "view_users"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/view_users.php";
			} else {
				require_once "modules/not_admin.php";
			}
		} elseif ($ModuleName == "edit_user"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/edit_user.php";
			} else {
				require_once "modules/not_admin.php";
			}

		} elseif ($ModuleName == "my_account"){
			require_once "modules/my_account.php";

		} elseif ($ModuleName == "delete_error"){
			require_once "modules/delete_error.php";
			//End Modules Section
		} else {
			//Bad Module was supplied, go to main menu
			require_once "modules/menu.php";
		}
	} else {
		//No module was supplied, go to main menu
		require_once "modules/menu.php";
	}
} else {
	//User is not logged in, direct to login page
	header("Location: login.php");
}
//extra java
require_once "lib/xtra_java.php";

//function to bring header and navigation bar
function render_HeaderFooter ($mytitle) {
	$xtpl=new XTemplate ("header.html");

$xtpl->assign("page_title",$mytitle);
$xtpl->assign("current",$_SESSION['user_name']);

	if ($_SESSION['account_type'] == 'Admin')
	{
		$xtpl->parse("main.admin_section");
	}
	$xtpl->parse("main");
	$xtpl->out("main");
}

function render_Footer() {
	$xtpl=new XTemplate ("footer.html");

	if ($_SESSION['account_type'] == 'Admin')
	{
		$xtpl->parse("main.admin_section");
	}
	$xtpl->parse("main");
	$xtpl->out("main");
}

?>