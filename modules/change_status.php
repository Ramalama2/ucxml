<?php
/*
	UCxml web Portal - Change Status

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/

	$tmp_username = defang_input($_SESSION['user_name']);
	$tmp_server_address = "5.5.6.12";
 	$tmp_server_port = "5060";
	$serverURL = "zt.voip.cnl.tuke.sk";	

if (isset($_POST['save']))
{
	// Saving
//	$tmp_status = defang_input($_POST['status']);
// 	$tmp_CSeq = getCSeq();	
 	$tmp_CSeq = $CSeq;	

	$tmp_note = defang_input($_POST['my_note']);

//	global $CallID;
	global $CSeq;

function logs($zprava)
{
	$f = fopen( "/log/testing.log", "a" );
	fwrite( $f, $zprava );
	fclose( $f );
}

	function getCSeq(){
		return $CSeq;
	}

	function __construct(){
		//include ("lib/SIP_UA.php");
		$CSeq = 0;
		$CSeq=(int)$_POST['CSeq'];
	}

	function countMsg($msg){
		$contentLength= strlen($msg);
	}

		function sendReg(){
			$msg=
				"REGISTER sip:".$serverURL." SIP/2.0\r\n".
				"Via: SIP/2.0/TCP 5.5.6.12:5060;branch=z9hG4bK7C4A1A301DA751B11DC3\r\n".
				"From: <sip:".$tmp_username."@".$serverURL.">;tag=1557326777;epid=1234567890\r\n".
				"To: <sip:".$tmp_username."@".$serverURL.">\r\n".
				"Max-Forwards: 10\r\n".
				"CSeq: ".$tmp_CSeq." REGISTER\r\n".
				"User-Agent: UCXML PUA/0.1\r\n".
				"Call-ID: 63C6g2F84a4740i6B61m569Bt2332b093Bx162Ax\r\n".
				'Contact: <sip:'.$tmp_username.'@'.$serverURL.':'.$serverPort.';transport=tcp>;methods="MESSAGE,SUBSCRIBE,NOTIFY"'."\r\n".
				"Expires: 60\r\n".
				"Content-Length: 0\r\n".
				"\r\n";

			sendMsg($msg);
//	SEND_REGISTER( $msg );
	
	logs( "sent SEND_REGISTER packet: " . $msg ); // toto pridas na konec funkci -- predane zpravy se zaloguji, mame informace o prubehu skriptu

		}

	function sendPub(){
		$msgBody=
			'<?xml version="1.0" encoding="UTF-8" ?>
			<presence entity="sip:'.$tmp_username.'@'.$serverURL.'" xmlns="urn:ietf:params:xml:ns:pidf" xmlns:dm="urn:ietf:params:xml:ns:pidf:data-model" xmlns:rpid="urn:ietf:params:xml:ns:pidf:rpid">
			<tuple id="bs35r9f"><status><basic>open</basic></status><note>'.$tmp_note.'</note></tuple></presence>'.
 			"\r\n\r\n";
		$msgLen = strlen($msgBody);
//		$msgLen=$msgLen+1;
		$msgHeader=
			"PUBLISH sip:".$tmp_username."@".$serverURL." SIP/2.0\r\n".
 			"Via: SIP/2.0/TCP 5.5.6.12:5060;branch=z9hG4bK16C5187E4A80692C7049\r\n".
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
//	SEND_PUBLISH( $msg );
	
	logs( "sent PUBLISH packet: " . $msg ); // toto pridas na konec funkci -- predane zpravy se zaloguji, mame informace o prubehu skriptu

	}

	function sendMsg($msg){
		$CSeq++;
 		$sock=fsockopen("5.5.6.12",5060,$errorno,$errorstr,10);
 		if($sock){
   			fwrite($sock,$msg);
			$stat = socket_get_status($sock);

			if ($stat["timed_out"])
			{ echo "timeout"; }
			fclose($sock);
		}else{
			echo "Can't connect to a Server";
		}
	logs( "sent PUBLISH packet: " . $msg ); // toto pridas na konec funkci -- predane zpravy se zaloguji, mame informace o prubehu skriptu


	}

	header("Location: index.php?module=view_status&status_view={status_view}");

} else {
	//display form
	output_change_status($tmp_username, $tmp_server_address, $tmp_server_port);
}

function output_change_status ($myUsername, $ServerAddress, $ServerPort)
{
	include "language/lang.php";
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/change_status.html");
	$xtpl->assign( 'LANG', $lang );

//	$xtpl->assign ("CSeq", getCSeq());
	$xtpl->assign ("Server_Address", $ServerAddress);
	$xtpl->assign ("Server_Port", $ServerPort);

 		if(($sock=fsockopen("5.5.6.12",3306,$errorno,$errorstr,60)))
 		{
			$theSQL2 = "SELECT username, body FROM opensips.presentity WHERE username='$myUsername'";
			$theRES2 = mysql_query($theSQL2, $db);

				while($in2=mysql_fetch_object($theRES2))
				{
					$xml = simplexml_load_string($in2->body);
					if($xml->tuple->status->basic=="open")
				{
					$xtpl->assign ("username", $in2->username);
					//$xtpl->assign ("my_status", $in2['.$xml->tuple->im:im.']);
					$xtpl->assign ("my_note", "".$xml->tuple->note."");
				}
			}

		// Output
        $xtpl->parse("change_status");
        $xtpl->out("change_status");

		fclose($sock);
		}
}

?>