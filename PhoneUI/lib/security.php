<?php

// Get Global Preferences
$secQuery = "SELECT
phone.ph_sec AS ph_sec
FROM phone
WHERE phone.preference = 'primary'";
$thesecRES = mysql_query($secQuery, $db);

if ($sy = mysql_fetch_assoc($thesecRES))
{
	//set global preferences
	$ph_sec = $sy['ph_sec'];
} else {
	//security defaults to 'yes'
	$ph_sec = 'Yes';
}

//TODO: otestovat na phone
//$tmp_memo_ob = defang_input($_POST['memo_ob']);
$secQuery = "SELECT
memos.memo_ob AS memo_ob
FROM memos
WHERE memos.id_user = '$tmp_id_user'";
$thesecRES = mysql_query($secQuery, $db);

if ($sy = mysql_fetch_assoc($thesecRES))
{
	//set global preferences
	$memo_ob = $sy['memo_ob'];
}

if (isset($_GET['name']))
{
	// Get MAC Address
	$MAC = defang_input($_GET['name']);

	// SQL to check MAC
	$macQuery = "SELECT
	phone.id_phone AS phone_id_phone,
	phone.access_lvl AS access_lvl
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