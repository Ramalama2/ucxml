<?php
/*
	UCxml web Portal - Change Status

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/

include ("lib/SIP_UA.php");
$tmp_id_user = defang_input($_SESSION['user_id']);
$tmp_username = defang_input($_SESSION['user_name']);

$changeStatus = new Publisher((string)$tmp_username,(string)$tmp_server_address,(int)$tmp_server_port);

$changeStatus = sendReg();
$changeStatus = sendPub();

if (isset($_POST['save']))
{
	// Saving
	$tmp_server_address = defang_input($_POST['Server_Address']);
	$tmp_server_port = defang_input($_POST['Server_Port']);
	$tmp_CSeg = defang_input($_POST['CSeg']);
	$tmp_status = defang_input($_POST['status']);
	$tmp_note = defang_input($_POST['note']);

//		global $CallID;

		function getCSeq(){
			return $CSeq;
		}

		function countMsg($msg){
			$contentLength= strlen($msg);
		}

		function sendReg(){
			$msg=
				"REGISTER sip:".$serverURL." SIP/2.0\r\n".
				"Via: SIP/2.0/TCP xxx.xxx.xxx.xxx:5060;branch=z9hG4bK7C4A1A301DA751B11DC3\r\n".
				"From: <sip:".$tmp_username."@".$serverURL.">;tag=1557326777;epid=1234567890\r\n".
				"To: <sip:".$tmp_username."@".$serverURL.">\r\n".
				"Max-Forwards: 10\r\n".
				"CSeq: ".$tmp_CSeq." REGISTER\r\n".
				"User-Agent: ".$UA."/".$UA_VERSION."\r\n".
				"Call-ID: 63C6g2F84a4740i6B61m569Bt2332b093Bx162Ax\r\n".
				'Contact: <sip:'.$tmp_username.'@'.$serverURL.':'.$serverPort.';transport=tcp>;methods="MESSAGE,SUBSCRIBE,NOTIFY"'."\r\n".
				"Expires: 60\r\n".
				"Content-Length: 0\r\n".
				"\r\n";

			sendMsg($msg);
		}

	function sendPub(){
		$msgBody=
			'<?xml version="1.0" encoding="UTF-8" ?>
			<presence xmlns="urn:ietf:params:xml:ns:pidf"
			xmlns:im="urn:ietf:params:xml:ns:pidf:im
			xmlns:rpid="urn:ietf:params:xml:ns:pidf:rpid" entity="sip:'.$tmp_username.'@'.$serverURL.'">
			<tuple id="bs35r9f"><status><basic>open</basic><im:im>'.$tmp_status.'</im:im></status><note>'.$tmp_note.'</note></tuple></presence>'.
 			"\r\n\r\n";
		$msgLen = strlen($msgBody);
//		$msgLen=$msgLen+1;
		$msgHeader=
			"PUBLISH sip:".$tmp_username."@".$serverURL." SIP/2.0\r\n".
 			"Via: SIP/2.0/TCP xxx.xxx.xxx.xxx:5060;branch=z9hG4bK16C5187E4A80692C7049\r\n".
 			"From: <sip:".$tmp_username."@".$serverURL.">;tag=1557326777;epid=1234567890\r\n".
 			"To: <sip:".$tmp_username."@".$serverURL.">\r\n".
 			"Max-Forwards: 10\r\n".
 			"CSeq: ".$tmp_CSeq." PUBLISH\r\n".
 			"User-Agent: UCXML PUA/0.1\r\n".
 			"Call-ID: 139Dg5772a7BB9i0902m3699t26CAb58B0x73DAx\r\n".
 			'SIP-If-None-Match: a.1230130174.2857.118.1\r\n'.
 			"Expires: 30\r\n".
 			"Event: presence\r\n".
 			"Content-Type: application/rpid+xml\r\n".
 			"Content-Length: ".$msgLen."\r\n\r\n";

		$msg=$msgHeader.$msgBody;

		sendMsg($msg);
	}

	function sendMsg($msg){
		CSeq++;
 		$sock=fsockopen($serverURL,$serverPort,$errorno,$errorstr,10);
 		if($sock){
   			fwrite($sock,$msg);
			$stat = socket_get_status($sock);

			if ($stat["timed_out"])
			{ echo "timeout"; }
//			while (!feof($sock)) {
//     				echo fgets ($sock);
//  			}
			fclose($sock);
		}else{
			echo "Can't connect to a Server";
		}
	}

	header("Location: index.php?module=view_status");

} else {
	//display contacts
	output_view_status($tmp_id_user, $tmp_username);
}

function output_view_status ($myID_user, $myUsername)
{
	include "language/lang.php";
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/change_status.html");
	$xtpl->assign( 'LANG', $lang );

 		if(($sock=fsockopen("xxx.xxx.xxx.xxx",3306,$errorno,$errorstr,60)))
 		{
 			$db2 = mysql_connect("xxx.xxx.xxx.xxx","watcher","presence");
			mysql_select_db("opensips",$db2);

			$theSQL2 = "SELECT username,body FROM presentity WHERE username='$myUsername'";
			$theRES2 = mysql_query($theSQL2, $db2);

          	while($in2=mysql_fetch_object($theSQL2))
			{
				$xtpl->assign ("user_id", $tmp_username);
				$xtpl->assign ("server_address", $tmp_server_address);
				$xtpl->assign ("server_port", $tmp_server_port);

				$xml = simplexml_load_string($in2['body']);
				if($xml->tuple->status->basic=="open")
				{
					$xtpl->assign ("my_username", $in2['username']);
					$xtpl->assign ("my_status", $in2['.$xml->tuple->im:im.']);
					$xtpl->assign ("my_note", $in2['.$xml->tuple->note.']);
				}
			}

		// Output
        $xtpl->parse("change_status");
        $xtpl->out("change_status");

		mysql_close($db2);
		fclose($sock);
		}
}

?>