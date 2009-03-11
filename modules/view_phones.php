<?php
/*
	UCxml web Portal - View phones

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


if (isset($_POST['submit_add']))
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


	//custum order by
	if (isset($_GET['ob']))
	{
		if ($_GET['ob'] == "ob_MAC")
		{
			$ob = "MAC";
		} elseif ($_GET['ob'] == "ob_access_lvl") {
			$ob = "access_lvl";
		} elseif ($_GET['ob'] == "ob_n") {
			$ob = "nick";
		} elseif ($_GET['ob'] == "ob_num") {
			$ob = "number";
		} else {
			$ob = "MAC";
		}
	} else {
	$ob = "MAC";
	}

//must query twice because edit_user from edit_user_phone OR edit_user_contact is canceling without new==true
//not ideal solution
	$theSQL = "SELECT id_phone,MAC,nick,number,access_lvl FROM phone";
	$theRES = mysql_query($theSQL, $db);
	while ($in = mysql_fetch_assoc($theRES))
	{
       if ($in['MAC'] == '' && $in['nick'] == '' && $in['number'] == '' && $in['access_lvl'] == '')
		{
			//contacts has no information, delete the entry
			$tmp_delete_id_phone = $in['id_phone'];
			$sql = "DELETE FROM phone WHERE id_phone='$tmp_delete_id_phone'";
			$result = mysql_query($sql);
		}
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

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}
?>