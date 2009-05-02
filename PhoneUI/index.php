<?php
require_once "../lib/xtpl/xtemplate.class.php";
require_once "../lib/utils.php";
require_once "../lib/mysql.php";

require_once "lib/urlbase.php";
require_once "lib/security.php";//grab mac address info, along with global preferences
require_once "lib/headers.php";
require_once "lib/refresh.php";


if ($ph_sec == 'yes' && $registered == 'FALSE')
{
	//Security to stop unregistered users from going any further if 'Phone Security' is on.
	require_once "templates/img_sec_breach.php";

	//check to see if mac is already stored in database
	$macQuery = "SELECT
	count(phone.id_phone) as total
	FROM phone
	WHERE phone.MAC = '$MAC'";
	$themacRES = mysql_query($macQuery, $db);

	if ($nm = mysql_fetch_assoc($themacRES))
	{
		if ($nm['total'] == '0')
		{
			
//				MAC is not in database or it is not a valid MAC
//				add MAC address to database, but do not give privileges, label as unknown
			
			$tmp_id_phone = create_guid($tmp_id_phone);
			$tmpInitSQL = "INSERT INTO phone (id_phone,MAC,access_lvl) VALUES ('$tmp_id_phone','$MAC','unknown')";

			if ($tmpInitRES = mysql_query($tmpInitSQL, $db))
			{
				// OK added
			} else {
				 // Fail to add
			}
		} else {
			//MAC is already registered in database as 'unknown'
		}
	}
} else {
	//Display picture menu
	require_once "templates/img_menu.php";
}

function newMemo()
{
    global $refresh;
	global $URLBase;

	// prehrat zvuk a presmerovat na URL obrazovky SMSiek
	SetRefresh(1, $URLBase."?show=1&readmemo=1" );
	header("Content-Type: audio/basic");
	readfile("lib/sound.raw");
//	$this->db->Close();
	exit(0);
}

?>