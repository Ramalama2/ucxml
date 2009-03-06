<?php

if ($_SESSION['lang'] == "en") {
	require "language/en.php";
} else {
	$_SESSION['lang'] = "sk";
	require "language/sk.php";
}

?>