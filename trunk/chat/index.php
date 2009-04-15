<?php

require_once dirname(__FILE__)."/src/phpfreechat.class.php";
$params = array();
$params["title"] = "Multi User Chat";
$params["nick"] = "guest".rand(1,1000);  // setup the intitial nickname
$params["isadmin"] = true; // do not use it on production servers ;)
$params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
//$params["debug"] = true;
$chat = new phpFreeChat( $params );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>UCxml - MUC</title>
  <link rel="stylesheet" title="classic" type="text/css" href="style/style.css" />
 </head>
 <body>

<div class="content">
	<div id="top">
		<div id="icons">
			<a href="/index.html" title="Home page"><img src="style/images/home.gif" alt="Home" /></a>
			<a href="/contact/" title="Contact us"><img src="style/images/contact.gif" alt="Contact" /></a>
		</div>
    <div id="login">
            <?php if (file_exists(dirname(__FILE__)."/checkmd5.php")) { ?>
            <li>
              <a href="checkmd5.php">Check md5</a>
            </li>
            <?php } ?>

            <!--
            <li class="item">
              <a href="admin/">Administration</a>
            </li>
            -->

	</div>

	<h1>Unified Communications solution</h1>
	<h2>Web portal includes some components from UC</h2>
</div>
</div>

<div id="prec">
  <div id="wrap">
  <?php $chat->printChat(); ?>
  <?php if (isset($params["isadmin"]) && $params["isadmin"]) { ?>
    <p style="color:red;font-weight:bold;">Warning: because of "isadmin" parameter, everybody is admin. Please modify this script before using it on production servers !</p>
  <?php } ?>
</div>
</div>
</body></html>
