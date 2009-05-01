<?php

/*
	MySQL Authorization Information
	Establish DB Connection
	Entered: 01/31/2009
*/

	$installed = 'true'; //to be able to reinstall, change this to false

	$db = mysql_connect('localhost', 'root', 'root');
	mysql_select_db('ucxml', $db);

?>