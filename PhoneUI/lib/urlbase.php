<?php

$url_end = 'phone/'; //Whatever folder this may be in besides the root 
 
 
$URLBase = 'http://'.$_SERVER['HTTP_HOST'].'/'.$url_end; 
 
//here are the headers 
header("Content-type: text/xml"); 
header("Connection: close"); 
header("Expires: -1"); 
?>