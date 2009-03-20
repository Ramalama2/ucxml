<?php

/*
	UCxml web Portal - Login

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

session_start();

require_once "lib/mysql.php";
require_once "lib/utils.php";
require_once "lib/xtpl/xtemplate.class.php";

global $errMsg;
// Check Login
if(isset($_POST['Login']))
{
	$remoteInfo = "(".$_SERVER['HTTP_USER_AGENT'].")";
	$username = defang_input($_POST['username']);
	$password = defang_input($_POST['password']);
	$crypt_pass = md5($password);
	$checkSQL = "SELECT id_user,username,account_type,av,lang,status_view FROM users
				WHERE username='$username' AND password='$crypt_pass' ";
	$checkRES = mysql_query($checkSQL, $db);

	if ($in = mysql_fetch_assoc($checkRES))
	{
		$_SESSION['user_id'] = $in['id_user'];
		$_SESSION['user_name'] = $in['username'];
		$_SESSION['account_type'] = $in['account_type'];
		$_SESSION['lang'] = $in['lang'];
		$_SESSION['status_view'] = $in['status_view'];
       	$_SESSION['av'] = $in['av'];

		header("Location: index.php?module=menu");

	} else {
		//fail to login
		session_destroy();
		session_unset();
		$errMsg = "Bad Username or Password";
	}
}

// Log Out
if (isset($_GET['module']) && $_POST['username'] == "")
{
	$ModuleName = defang_input($_GET['module']);
	if ($ModuleName == "logout")
	{
		session_destroy();
		session_unset();
		$logout = "You have been successfully logged out!";
	}
}

if ($installed == 'false')
{
	//not installed
} else {
	$installed == 'true';
}


if (defang_input($_GET['newuser']) == "true")
{
	$errMsg = "It recommended that you change your password after logging in";
}

// Produce the login page
output_login_page($errMsg,$logout,$installed);

//
//  FUNCTIONS
//

function output_login_page ($errMsg,$logout,$installed)
{

	$xtpl=new XTemplate ("login.html");
	if ($installed == 'false')
	{
		$xtpl->parse("main.install");
	} else {

		$xtpl->parse("main.installed");
		$xtpl->assign("error_msg",$errMsg);
		$xtpl->assign("logout",$logout);

    }
	$xtpl->parse("main");
	$xtpl->out("main");
}

?>