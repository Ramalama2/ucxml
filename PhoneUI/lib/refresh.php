<?php

/*
	UCxml PhoneUI - refresh url pages

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/

require_once "urlbase.php";
require_once "headers.php";

// Set refresh
function SetRefresh($time, $page)
{
global $refresh;
global $URLBase;

static $done;

if($done)	// ak sa uz raz nastavil refresh tak sa uz viackrat nenastavuje
return ;
$done = 1;

if($page == "")
	$page = $URLBase;
	// Refresh: 3; url=

if($time == -1)
		header("Refresh: $refresh; url=$page");
	else
		header("Refresh: $time; url=$page");
}

?>