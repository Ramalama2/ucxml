<?php

if (isset($_SESSION['lang']) && !preg_match('/\.\./', $_SESSION['lang'])) {
	require ("language/{$_SESSION['lang']}.php");
} else {
	$_SESSION['lang'] = $default_lang;
	require ("language/$default_lang.php");
}
?>