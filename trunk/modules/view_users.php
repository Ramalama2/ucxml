<?php
/*
	UCxml web Portal - View users

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


if (isset($_POST['submit_add']))
{
	// add user
	$tmp_id_user = create_guid($tmp_id_user);

	$tmpInitSQL = "INSERT INTO users (id_user) VALUES ('$tmp_id_user')";
  mysql_query("INSERT INTO contacts (id_contact) VALUES ('".$tmp_id_user."')");

	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
	{

	// OK, show editor
		header("Location: index.php?module=edit_user&id_user=$tmp_id_user&new=true");
	} else {
	 // Failure
	 echo "Unable to create user.";
	}
} else {
	//display user listings
	render_HeaderFooter("UCxml web Portal - User View");
	output_view_users();
	render_Footer();
}

//
//  FUNCTIONS
//

function output_view_users ()
{
	global $db;
	$xtpl=new XTemplate ("modules/templates/view_users.html");

	// Content

	//custom order by
	if (isset($_GET['ob']))
	{
		if ($_GET['ob'] == "ob_username")
		{
			$ob = "username";
		} elseif ($_GET['ob'] == "ob_email") {
			$ob = "email";
		} elseif ($_GET['ob'] == "ob_account_type") {
			$ob = "account_type";
		}
	} else {
	$ob = "username";
	}


	$theSQL = "SELECT id_user,username,email,account_type FROM users ORDER BY $ob";
	$theRES = mysql_query($theSQL, $db);
	$oddRow = true;
	while ($in = mysql_fetch_assoc($theRES))
	{
		//Generate data rows
		if ($oddRow)
		{
			$xtpl->assign("bg","#EFEFEF");
		} else {
			$xtpl->assign("bg","#DFDFDF");
		}
		$xtpl->assign("id_user",$in['id_user']);
		$xtpl->assign("username",$in['username']);
		$xtpl->assign("email",$in['email']);
		$xtpl->assign("account_type",$in['account_type']);
		if ($_SESSION['user_id'] == $in['id_user'] || $in['id_user'] == '0')
		{
			$xtpl->assign("delete","");
		} else {
			$xtpl->assign("delete","delete");
		}

		$xtpl->parse("main.row");
		$oddRow = !$oddRow;
	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>