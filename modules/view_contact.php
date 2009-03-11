
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


	output_edit_contact($tmp_id_contact);

//Create page and fill in known data
function output_edit_contact ($myID_contact)
{

	include "language/lang.php";

	global $db, $xtpl;

	$xtpl=new XTemplate ("modules/templates/view_contact.html");
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


        }
	$xtpl->parse('main');
	$xtpl->out("main");
}
?>