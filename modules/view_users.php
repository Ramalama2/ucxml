<?php
/*
	UCxml web Portal - View users

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

if (isset($_POST['submit_add']))
{
	// add user
	$tmp_id_user = create_guid($tmp_id_user);
	$tmp_owner = $_SESSION['user_id'];
$domain = "zt.voip.cnl.tuke.sk";
$xcap = '<?xml version="1.0" encoding="UTF-8"?>
<cr:ruleset
xmlns="urn:ietf:params:xml:ns:pres-rules"
xmlns:pr="urn:ietf:params:xml:ns:pres-rules"
xmlns:cr="urn:ietf:params:xml:ns:common-policy">'.
'\r\n'.
'<cr:rule id="pres_whitelist">\r\n'.
'<cr:conditions><cr:identity>\r\n'.
'\r\n'.
'<cr:one id="sip:admin@zt.voip.cnl.tuke.sk"/>\r\n'.
'\r\n'.
'</cr:identity></cr:conditions>\r\n'.
'\r\n'.
'<cr:actions>\r\n'.
'<sub-handling>block</sub-handling>\r\n'.
'</cr:actions>\r\n'.
'\r\n'.
'<cr:transformations/>\r\n'.
'</cr:rule></cr:ruleset>\r\n'.
"\r\n";

	$tmpInitSQL = "INSERT INTO users (id_user) VALUES ('".$tmp_id_user."')";
	mysql_query("INSERT INTO contacts (id_contact,owner) VALUES ('".$tmp_id_user."','".$tmp_owner."')");
	mysql_query("INSERT INTO phone (id_phone) VALUES ('".$tmp_id_user."')");
   	mysql_query("INSERT INTO opensips.xcap (domain, doc) VALUES ('".$domain."', '$xcap')");
	//$id = mysql_insert_id();

	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
	{
	// OK, show editor
		header("Location: index.php?module=edit_user&id_user=$tmp_id_user&new=true");
	}
	else {
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
	include "language/lang.php";
	global $db, $db2;
	$xtpl=new XTemplate ("modules/templates/view_users.html");
	$xtpl->assign( 'LANG', $lang );

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

//must query twice because edit_user from edit_user_phone OR edit_user_contact is canceling without new==true
//not ideal solution
	$theSQL = "SELECT id_user,username,email,account_type FROM users";
	$theRES = mysql_query($theSQL, $db);
	while ($in = mysql_fetch_assoc($theRES))
	{
	       if ($in['username'] == '' && $in['email'] == '' && $in['account_type'] == '')
		{
			//contacts has no information, delete the entry
			$tmp_delete_id_user = $in['id_user'];
			$sql = "DELETE FROM users WHERE id_user='$tmp_delete_id_user'";
			$result = mysql_query($sql);
		}
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

		if ($in['id_user'] == '0')
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