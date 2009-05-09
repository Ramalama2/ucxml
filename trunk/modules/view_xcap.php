<?php
/*
	UCxml web Portal - View XCAP

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com
*/

	$tmp_username = defang_input($_SESSION['user_name']);

if (isset($_POST['action']))
{
	//User wants to save presence rule
	$myAction = defang_input($_POST['action']);
	if ($myAction == "edit")
	{
		if (isset($_POST['submit_save']))
		{
			// Saving
			$tmp_doc = defang_input($_POST['doc']);

			$tmpUpdateSQL = "UPDATE opensips.xcap SET
				doc = '$tmp_doc'
				WHERE username = '$tmp_username'";

			if (mysql_query($tmpUpdateSQL, $db))
			{
				header("Location: index.php?module=view_xcap");
			} else {
				echo "Unable to save doc.";
			}

		} else if (isset($_POST['submit_cancel'])) {
			// Cancel
			header("Location: index.php?module=menu");

		}
	} else {
		// Bad action
		header("Location: index.php?module=menu");
	}
} else {
	// No action
	render_HeaderFooter("UCxml web Portal - View XCAP");
	output_edit_xcap($tmp_username);
	render_Footer();
}

//
//  FUNCTIONS
//


//Create page and fill in known data
function output_edit_xcap ($myName)
{

	include "language/lang.php";

	global $db;
	$xtpl=new XTemplate ("modules/templates/view_xcap.html");
	$xtpl->assign( 'LANG', $lang );

	$theSQL = "SELECT username, doc, source FROM opensips.xcap WHERE username = '$myName'";
	$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		$xtpl->assign("doc",$in['doc']);
		$xtpl->assign("source",$in['source']);       
		$xtpl->assign("owner",$in['username']);
	}
	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
}

?>