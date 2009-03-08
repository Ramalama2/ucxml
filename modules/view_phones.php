<?php
/*
	UCxml web Portal - View phones
	
	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:	
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


$myPref = "primary";

if (isset($_POST['submit_save'])) {
		// Saving
		if (defang_input($_POST['ph_sec']) == "ph_sec")
		{
			$tmp_ph_sec = "Yes";
		} else {
			$tmp_ph_sec = "No";
		}

		$tmpUpdateSQL = "UPDATE phone SET
			ph_sec = '$tmp_ph_sec'
			WHERE preference = '$myPref'";

		mysql_query($tmpUpdateSQL, $db);
		header("Location: index.php?module=view_phones");
}

elseif (isset($_POST['submit_add']))
{
	// add phone
	$tmp_id_phone = create_guid($tmp_id_phone);
	$tmpInitSQL = "INSERT INTO phone (id_phone) VALUES ('$tmp_id_phone')";
	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
	{
		// OK, show editor
		header("Location: index.php?module=edit_phone&id_phone=$tmp_id_phone&new=true");
	} else {
		 // Failure
		 echo "Unable to add phone.";
	}
} else {
	//display phone listings
	render_HeaderFooter("UCxml web Portal - Manage Phone Registrations");
	output_view_phones();
	render_Footer();
}

//
//  FUNCTIONS
//

function output_view_phones ()
{
	include "language/lang.php";
	global $db;
	$xtpl=new XTemplate ("modules/templates/view_phones.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT * FROM phone WHERE preference = 'primary'";
	$theRES = mysql_query($theSQL, $db);

	if ($in = mysql_fetch_assoc($theRES))
	{
		if ($in['ph_sec'] == "Yes")
		{
			$xtpl->assign("ph_sec_check",'CHECKED'); //place check in box
		}

	 else {
		//	echo "Unable to save preferences.";
		}
	}

	//custum order by
	if (isset($_GET['ob']))
	{
		if ($_GET['ob'] == "ob_MAC")
		{
			$ob = "MAC";
		} elseif ($_GET['ob'] == "ob_access_lvl") {
			$ob = "access_lvl";
		} elseif ($_GET['ob'] == "ob_ln") {
			$ob = "lname";
		} elseif ($_GET['ob'] == "ob_num") {
			$ob = "number";
		} else {
			$ob = "MAC";
		}
	} else {
	$ob = "MAC";
	}

	// Content
	$theSQL = "SELECT id_phone,MAC,access_lvl,nick,number FROM phone ORDER BY $ob";
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
		$xtpl->assign("id_phone",$in['id_phone']);
		$xtpl->assign("MAC",$in['MAC']);
		$xtpl->assign("access_lvl",$in['access_lvl']);
		$xtpl->assign("nick",$in['nick']);
		$xtpl->assign("number",$in['number']);

		$xtpl->parse("main.row");
		$oddRow = !$oddRow;
	}

//Create page and fill in known data


	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>