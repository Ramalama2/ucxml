<?php

// Get Global Preferences
// otestovat

$secQuery = "SELECT
users.ph_sec AS ph_sec,
users.account_type AS account_type,
users.memo_ob AS memo_ob
FROM users LEFT JOIN phone ON users.id_user = phone.id_phone
WHERE phone.MAC = '$MAC'";

$thesecRES = mysql_query($secQuery, $db);

if ($sy = mysql_fetch_assoc($thesecRES))
{
	//set global preferences
	$ph_sec = $sy['ph_sec'];
	$memo_ob = $sy['memo_ob'];
	$account_type = $sy['account_type'];
} else {
	//security defaults to 'yes'
	$ph_sec = 'Yes';
}

if (isset($_GET['name']))
{
	// Get MAC Address
//	$MAC = defang_input($_GET['name']);
	$MAC = $HTTP_SERVER_VARS['REMOTE_ADDR'];

	// SQL to check MAC
	$macQuery = "SELECT
	phone.id_phone AS id_phone,
	phone.access_lvl AS access_lvl,
	phone.nick AS nick,
	phone.refresh AS refresh
	FROM phone
	WHERE phone.MAC = '$MAC'
	AND phone.access_lvl != 'unknown'";

	$themacRES = mysql_query($macQuery, $db);

	if ($mc = mysql_fetch_assoc($themacRES))
	{

		if ($mc['access_lvl'] != "")
		{
			//MAC was found as a registered phone
			$registered = 'TRUE';
			$access_lvl = $mc['access_lvl'];
			$my_nick = $mc['nick'];
			$refresh = $mc['refresh'];
		} else {
			//Access Level of MAC was not defined
			$registered = 'FALSE';
			$access_lvl = "Restricted";
		}
	} else {
		//Qry Fail
		$registered = 'FALSE';
		$access_lvl = "Restricted";
	}
} else {
	//MAC not found in database
	$registered = 'FALSE';
	$access_lvl = "Restricted";
}

?>