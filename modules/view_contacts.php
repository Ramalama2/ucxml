<?php
/*
	UCxml web Portal - View contacts

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/


if (isset($_POST['submit_add']))
{
	// add contact
	$tmp_id_contact = create_guid($tmp_id_contact);
    $tmp_owner = $_SESSION['user_id'];
	$tmpInitSQL = "INSERT INTO contacts (id_contact, owner) VALUES ('$tmp_id_contact','$tmp_owner')";
	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
	{
		// show editor
		header("Location: index.php?module=edit_contact&id_contact=$tmp_id_contact&new=true");
	} else {
		 // Failure
		 echo "Unable to create contact";
	}
/*}elseif (isset($_POST['submit_import'])) {
		header("Location: index.php?module=import_contacts");
}*/
 }elseif (isset($_POST['submit_export'])) {
		CSVexport();

}else {
	//display contacts
	render_HeaderFooter("UCxml web Portal - Manage Contacts");
	output_view_contacts();
	render_Footer();
}

//
//  FUNCTIONS
//

function output_view_contacts ()
{
	include "language/lang.php";
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/view_contacts.html");
	$xtpl->assign( 'LANG', $lang );

    if (isset($_GET['view_member_of'])) {

		$view_member_of = defang_input($_GET['view_member_of']);

		if ($view_member_of == 'all')
		{
            //user wants to view everyones' status
			$loc_sql = "";
			$xtpl->assign("sel_all",'selected');

		} elseif ($view_member_of == 'B512') {
			//user wants to view people unavailable, status
			$loc_sql = "WHERE contacts.member_of = 'B512'";
			$xtpl->assign("sel_b512",'selected');

		} elseif ($view_member_of == 'B521') {
			//user wants to view people unavailable, status
			$loc_sql = "WHERE contacts.member_of = 'B521'";
			$xtpl->assign("sel_b521",'selected');

		} elseif ($view_member_of == 'Emergency') {
			//user wants to view people unavailable, status
			$loc_sql = "WHERE contacts.member_of = 'Emergency'";
			$xtpl->assign("sel_emergency",'selected');
		}
    }

	//custum order by
	if (isset($_GET['ob']))
	{
		if ($_GET['ob'] == "ob_ln")
		{
			$ob = "lname";
		} elseif ($_GET['ob'] == "ob_title") {
			$ob = "title";
		} elseif ($_GET['ob'] == "ob_nick") {
			$ob = "nick";
		}
	} else {
	$ob = "lname";
	}

//must query twice because edit_user from edit_user_phone OR edit_user_contact is canceling without new==true
//not ideal solution
	$theSQL = "SELECT id_contact,fname,lname,nick,title FROM ucxml.contacts";
	$theRES = mysql_query($theSQL, $db);
	while ($in = mysql_fetch_assoc($theRES))
	{
       if ($in['fname'] == '' && $in['lname'] == '' && $in['nick'] == '' && $in['title'] == '')
		{
			//contacts has no information, delete the entry
			$tmp_delete_id_contact = $in['id_contact'];
			$sql = "DELETE FROM contacts WHERE id_contact='$tmp_delete_id_contact'";
			$result = mysql_query($sql);
		}
	}

              if ($_SESSION['account_type'] == 'Admin')
			{
				$xtpl -> parse ("main.column.admin_edit_del");
			}

		$xtpl->parse("main.column");//show columns
		//user has submited a search, show the contacts

		$theSQL = "SELECT id_contact,fname,lname,nick,title,member_of FROM contacts $loc_sql ORDER BY $ob";
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
			$xtpl->assign("id_contact",$in['id_contact']);
			$xtpl->assign("fname",$in['fname']);
			$xtpl->assign("lname",$in['lname']);
			$xtpl->assign("nick",$in['nick']);
			$xtpl->assign("title",$in['title']);
			$xtpl->assign("member_of",$in['member_of']);

			if ($_SESSION['account_type'] == 'Admin')
			{
				$xtpl -> parse ("main.row.admin_edit_del");
			}

			$xtpl->parse("main.row");
			$oddRow = !$oddRow;

			if ($in['fname'] == '' && $in['lname'] == '' && $in['nick'] == '' && $in['title'] == '' && $in['office_phone'] == '' && $in['home_phone'] == '' && $in['cell_phone'] == '' && $in['cell_phone'] == '' && $in['other_phone'] == '')
			{
				//contacts has no information, delete the entry
				$tmp_delete_id_contact = $in['id_contact'];
				$sql = "DELETE FROM contacts WHERE id_contact='$tmp_delete_id_contact'";
				$result = mysql_query($sql);
			}
		}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}

function CSVexport()
{
global $db;
$table = 'contacts';
$file = 'export';

$result = mysql_query("SHOW COLUMNS FROM ".$table."");
$i = 0;
if (mysql_num_rows($result) > 0) {
while ($row = mysql_fetch_assoc($result)) {
$csv_output .= $row['Field']."; ";
$i++;
}
}
$csv_output .= "\n";

$values = mysql_query("SELECT * FROM ".$table."");
while ($rowr = mysql_fetch_row($values)) {
for ($j=0;$j<$i;$j++) {
$csv_output .= $rowr[$j]."; ";
}
$csv_output .= "\n";
}

$filename = $file."_".date("Y-m-d_H-i",time());
header("Content-type:text/octect-stream");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header( "Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;

}
?>