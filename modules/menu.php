<?php
/*
	UCxml web Portal - Main menu
	
	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml

	original idea:		
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

include "language/lang.php";

render_HeaderFooter("UCxml web Portal - Home");

$xtpl=new XTemplate ("modules/templates/menu.html");
$xtpl->assign( 'LANG', $lang );

if ($_SESSION['account_type'] == 'Admin')
{
	$xtpl->parse("main.admin_section1");
	$xtpl->parse("main.admin_section2");
}
$xtpl->parse("main");
$xtpl->out("main");

render_Footer();
$xtpl=new XTemplate ("footer.html");

if ($_SESSION['account_type'] == 'Admin')
{
	$xtpl->parse("main.admin_section");
}

$xtpl->parse("main");
$xtpl->out("main");
?>
