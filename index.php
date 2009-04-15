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
require_once "lib/xtpl/xtemplate.class.php";

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

		} elseif ($ModuleName == "chat") {
			require_once "modules/chat/index.php";

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

		} elseif ($ModuleName == "view_memos_posted"){
			require_once "modules/view_memos_posted.php";

		} elseif ($ModuleName == "view_memos_broadcast"){
			require_once "modules/view_memos_broadcast.php";

		} elseif ($ModuleName == "post_memo"){
			require_once "modules/post_memo.php";

		} elseif ($ModuleName == "post_memo_broadcast"){
			require_once "modules/post_memo_broadcast.php";

		} elseif ($ModuleName == "post_memo_private"){
			require_once "modules/post_memo_private.php";

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

		} elseif ($ModuleName == "edit_user_contact"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/edit_user_contact.php";
			} else {
				require_once "modules/not_admin.php";
			}

		} elseif ($ModuleName == "edit_user_phone"){
			if ($_SESSION['account_type'] == 'Admin')
			{
				require_once "modules/edit_user_phone.php";
			} else {
				require_once "modules/not_admin.php";
			}

		} elseif ($ModuleName == "my_account"){
			require_once "modules/my_account.php";

		} elseif ($ModuleName == "my_account_contact"){
			require_once "modules/my_account_contact.php";

		} elseif ($ModuleName == "my_account_phone"){
			require_once "modules/my_account_phone.php";

		} elseif ($ModuleName == "my_account_avatar"){
			require_once "modules/my_account_avatar.php";

		} elseif ($ModuleName == "my_account_lang"){
			require_once "modules/my_account_lang.php";

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
function render_HeaderFooter ($mytitle)
{
	global $db;
	include "language/lang.php";
	$xtpl=new XTemplate ("header.html");
	$xtpl->assign( 'LANG', $lang );
	$target = "images/avatars/";

	// if the user has a custom avatar, show their avatar, else show default avatar
	$xtpl->assign("av", $target . ($_SESSION['av']? $_SESSION['user_id'] .'.'. $_SESSION['av'] : 'default.png'));

    $tmp_my_nick = defang_input($_SESSION['user_name']);
	// if the user have a new memo, show the count

    if ($_SESSION['account_type'] == 'Admin')
	{
        $checkSQL ="SELECT count(*) AS newmemo FROM memos
		WHERE memos.receiver='$tmp_my_nick' AND memos.read = '0' AND memos.new = '1'";
	}

	else
	{
		$checkSQL ="SELECT count(*),
   			CASE
			WHEN (memos.receiver = '' AND memos.id_memo = memos_read.id_memo
			 AND memos_read.receiver = '$tmp_my_nick')
			THEN 0 ELSE 1
			END AS newmemo
			FROM memos LEFT JOIN memos_read ON memos.id_memo = memos_read.id_memo
			WHERE memos.receiver IN ('$tmp_my_nick','')";
	}

		$checkRES = mysql_query($checkSQL, $db);

		if($in2 = mysql_fetch_assoc($checkRES))
		{
	   		$newmemo = $in2['newmemo'];
		}

		if ($newmemo > '0')
		{
	        $xtpl->assign("memo_count",$newmemo);
   			$xtpl->parse("main.new_memo");
		}

	$xtpl->assign("page_title",$mytitle);
	$xtpl->assign("current",$_SESSION['user_name']);
	$xtpl->assign("user_id",$_SESSION['user_id']);
	$xtpl->assign("status_view",$_SESSION['status_view']);

	if ($_SESSION['account_type'] == 'Admin')
	{
		$xtpl->parse("main.admin_section");
	}

	$xtpl->parse("main");
	$xtpl->out("main");
}

function render_Footer()
{
	include "language/lang.php";
	$xtpl=new XTemplate ("footer.html");
	$xtpl->assign( 'LANG', $lang );

	if ($_SESSION['account_type'] == 'Admin')
	{
		$xtpl->parse("main.admin_section");
	}
	$xtpl->parse("main");
	$xtpl->out("main");
}

?>