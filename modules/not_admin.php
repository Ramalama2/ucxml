<?php
/*
	UCxml web Portal - if not admin
	
	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com

	original idea:	
	Joe Hopkins <joe@csma.biz>
	Copyright (c) 2005, McFadden Associates.  All rights reserved.
*/
	
render_HeaderFooter("UCxml web Portal - Error");
$xtpl=new XTemplate ("modules/templates/not_admin.html");
// Output
$xtpl->parse("main");
$xtpl->out("main");


render_Footer();

?>