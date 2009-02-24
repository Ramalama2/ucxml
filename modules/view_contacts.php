<?php
/*
	UCxml web Portal - View contacts

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

$xtpl=new XTemplate ("modules/templates/view_contacts.html");

if (isset($_POST['submit_add']))
{
	// add contact
	$tmp_id = create_guid($tmp_id);
	$tmp_owner = $_SESSION['user_id'];
	$tmpInitSQL = "INSERT INTO contacts (id) VALUES ('$tmp_id')";
	if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
	{
		// show editor
		header("Location: index.php?module=edit_contact&id=$tmp_id&new=true");
	} else {
		 // Failure
		 echo "Unable to create contact";
	}
} elseif (isset($_POST['submit_import'])) {
		header("Location: index.php?module=import_contacts");

}
 elseif (isset($_POST['submit_export'])) {
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
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/view_contacts.html");


    if (isset($_GET['view_member_of'])) {

		$view_member_of = defang_input($_GET['view_member_of']);

		if ($view_member_of == 'all')
		{
            //user wants to view everyones' status
			$loc_sql = "";
			$xtpl->assign("sel_all",'selected');

		} elseif ($view_member_of == 'b512') {
			//user wants to view people unavailable, status
			$loc_sql = "WHERE contacts.member_of = 'b512'";
			$xtpl->assign("sel_b512",'selected');

		} elseif ($view_member_of == 'b521') {
			//user wants to view people unavailable, status
			$loc_sql = "WHERE contacts.member_of = 'b521'";
			$xtpl->assign("sel_b521",'selected');

		} elseif ($view_member_of == 'emergency') {
			//user wants to view people unavailable, status
			$loc_sql = "WHERE contacts.member_of = 'emergency'";
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
		} elseif ($_GET['ob'] == "ob_company") {
			$ob = "company";
		}
	} else {
	$ob = "lname";
	}

		$xtpl->parse("main.column");//show columns
		//user has submited a search, show the contacts
		$theSQL = "SELECT id,fname,lname,company,title,member_of FROM contacts $loc_sql ORDER BY $ob";
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
			$xtpl->assign("id",$in['id']);
			$xtpl->assign("fname",$in['fname']);
			$xtpl->assign("lname",$in['lname']);
			$xtpl->assign("company",$in['company']);
			$xtpl->assign("title",$in['title']);
			$xtpl->assign("office_phone",$in['office_phone']);
			$xtpl->assign("home_phone",$in['home_phone']);
			$xtpl->assign("cell_phone",$in['cell_phone']);
			$xtpl->assign("other_phone",$in['other_phone']);
			$xtpl->assign("member_of",$in['member_of']);

			$xtpl->parse("main.row");
			$oddRow = !$oddRow;

			if ($in['fname'] == '' && $in['lname'] == '' && $in['company'] == '' && $in['title'] == '' && $in['office_phone'] == '' && $in['home_phone'] == '' && $in['cell_phone'] == '' && $in['cell_phone'] == '' && $in['other_phone'] == '')
			{
				//contacts has no information, delete the entry
				$tmp_delete_id = $in['id'];
				$sql = "DELETE FROM contacts WHERE id='$tmp_delete_id'";
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