<?php
/*
	UCxml web Portal - Main menu
	
	Zoli Toth, FEI TUKE
	Unified Communications solution in open source - UCxml

	original idea:		
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/

render_HeaderFooter("UCxml web Portal - Home");

$xtpl=new XTemplate ("modules/templates/menu.html");

if ($_SESSION['account_type'] == 'Admin')
{
	$xtpl->parse("main.admin_section1");
	$xtpl->parse("main.admin_section2");
}
$xtpl->parse("main");
$xtpl->out("main");

$xtpl=new XTemplate ("footer.html");

if ($_SESSION['account_type'] == 'Admin')
{
	$xtpl->parse("main.admin_section");
}
$xtpl->parse("main");
$xtpl->out("main");
?>
