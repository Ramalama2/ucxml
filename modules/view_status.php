<?php
/*
	UCxml web Portal - View contacts
	
	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/


$xtpl=new XTemplate ("WebUI/modules/templates/view_status.html");

$myPref = "primary";
if (isset($_POST['submit_save']))
{
	// Saving
		$tmp_status_view = defang_input($_POST['status_view']);
		$tmpUpdateSQL =
			"UPDATE phone
			SET	status_view = '$tmp_status_view'
			WHERE preference = '$myPref'";

		mysql_query($tmpUpdateSQL, $db);
		header("Location: index.php?module=view_status");

} else {
	//display contacts
	render_HeaderFooter("UCxml web Portal - Phone Status");
	output_view_status();
	render_Footer();
}

//
//  FUNCTIONS
//


function output_view_status ()
{
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/view_status.html");

		$theSQL = "SELECT * FROM phone WHERE preference = 'primary'";
		$theRES = mysql_query($theSQL, $db);
	if ($in = mysql_fetch_assoc($theRES))
	{
		if ($in['status_view'] == "-1")
		{
			$xtpl->assign("selected_all",'selected');
		} elseif ($in['status_view'] == "1") {
			$xtpl->assign("selected_1",'selected');
		} else {
			$xtpl->assign("selected_0",'selected');
		}
	}
	 else {
		echo "Unable to save preferences.";
	}

	$obprefSQL = "SELECT status_view FROM phone WHERE preference = 'primary'";
	$obRES = mysql_query($obprefSQL, $db);
	if ($gl = mysql_fetch_assoc($obRES))
	{
		$status_view = $gl['status_view'];
	} else {
		//sql error
		$status_view = "-1";
	}

          //custom order by
	if (isset($_GET['ob']))
	{
		if ($_GET['ob'] == "ob_ln")
		{
			$ob = "lname";
		} elseif ($_GET['ob'] == "ob_status") {
			$ob = "status";
		} elseif ($_GET['ob'] == "ob_away_msg") {
			$ob = "away_msg";
		}
	} else {
	$ob = "lname";
	}

  	if (isset($_GET['ur']))
	{
		$urMAC = defang_input($_GET['ur']);
		show_status($MAC,$urMAC);

	} elseif (isset($_GET['view_my_status'])) {

		show_status($MAC,$MAC);

	}
    elseif (isset($_GET['others_status'])) {

		$others_status = defang_input($_GET['others_status']);

		if ($others_status == 'all')
		{
            //user wants to view everyones' status
			$loc_sql = "WHERE phone.access_lvl != 'unknown'";
			$xtpl->assign("selected_all",'selected');

		} elseif ($others_status == 'out') {
			//user wants to view people unavailable, status
			$loc_sql = "WHERE phone.status = 0 AND phone.access_lvl != 'unknown'";
			$xtpl->assign("selected_0",'selected');

		} elseif ($others_status == 'in') {
			//user wants to view people in the available, status
			$loc_sql = "WHERE phone.status = 1 AND phone.access_lvl != 'unknown'";
			$xtpl->assign("selected_1",'selected');
			$xtpl->assign("in",$others_status);

		}
		else{

		}

	$xtpl->parse("main.column");//show columns
		//user has submited a search, show the contacts

	$theSQL = "SELECT id,access_lvl,fname,lname,away_msg, status FROM phone $loc_sql ";
	$theRES = mysql_query($theSQL, $db);

	$oddRow = true;
	while ($in = mysql_fetch_assoc($theRES))
	{
		//Generate data rows
		if ($oddRow)
		{
			$xtpl->assign("bg","#EFEFEF");
		} else {
			$xtpl->assign("bg","#DFDFDF");
		}
		$xtpl->assign("id",$in['id']);
		$xtpl->assign("status",$in['status']);
		$xtpl->assign("name",$in['lname'].", ".$in['fname']);
		$xtpl->assign("away_msg",$in['away_msg']);
		$xtpl->assign("access_lvl",$in['access_lvl']);


		$xtpl->parse("main.row");
		$oddRow = !$oddRow;

	}

	// Output
	$xtpl->parse("main");
	$xtpl->out("main");
} elseif (isset($_GET['my_status'])) {
		//User has requested to change their status
		if ($registered == "TRUE")
		{
			$xtpl=new XTemplate ("templates/my_status.xml");
			$statusqry = "SELECT
				phone.status AS status
				FROM phone
				WHERE MAC = '$MAC'";

			$theCountRES = mysql_query($statusqry, $db);

			//Fetch phone availablility
			if ($in = mysql_fetch_assoc($theCountRES))
			{
				if ($in['status'] == '1')
				{
					$xtpl->assign("available",'*');
					$xtpl->assign("unavailable",'');
				} else {
					$xtpl->assign("available",'');
					$xtpl->assign("unavailable",'*');
				}
			}
			//show prompt to select in office or out of office
			$xtpl->assign("MAC",$MAC);
			$xtpl->assign("url_base",$URLBase);
			$xtpl->parse("main");
			$xtpl->out("main");
		} else {
			//User must have a registered MAC to set a status, display error page
			require_once "templates/img_sec_breach.php";
		}
	} else {
	$xtpl->parse("main");
	$xtpl->out("main");
	}

}
function show_status ($MAC,$urMAC)
{
	/*
		The user has selected the message she wishes to view, the MAC address
		is used to select the corresponding fields from the db
		the "away_msg" if using the preprogrammed is a number, that corresponds to a msg
		written in the php in the function.  If user has a custom message, the message
		is written in text in the away_msg field
	*/
	global $db;
	global $URLBase;

	$browseQuery = "SELECT
		phone.number AS number,
		phone.fname AS fname,
		phone.lname AS lname,
		phone.away_msg AS away_msg,
		phone.date AS date,
		phone.status AS status
		FROM phone
		WHERE phone.MAC = '$urMAC'";

		$theContactRES = mysql_query($browseQuery, $db);

	if ($in = mysql_fetch_assoc($theContactRES))
	{
		//Assign user msg info to the screen
		$tmp_unixtime = $in2['date'];
		$displaydate = date("n/d g:ia" ,$tmp_unixtime);

        $xtpl=new XTemplate ("templates/status_detail.xml");

		if ($MAC == $urMAC)
		{
			$xtpl=new XTemplate ("templates/view_my_status.xml");
			$xtpl->assign("url_base",$URLBase);
			$xtpl->assign("MAC",$MAC);
			$tmp_display = num2txt($in['away_msg']);
			$xtpl->assign("msg",$tmp_display);
		} else {
			$xtpl=new XTemplate ("templates/status_detail.xml");
			$xtpl->assign("msg",$in['lname'].",".$in['fname']);

		}

		$curphone = parse_phone($in['number']);
		$number = return_dial($curphone);

		$tmp_display = num2txt($in['away_msg']);

		$tmp_location = $in['status'];

		if ($tmp_location == '1')
		{
			$display_away = "Available since ".$displaydate;
			$tmp_your = "Available";
		} elseif ($tmp_location == '0') {
			$display_away = "Unavailable since ".$displaydate;
			$tmp_your = "Unavailable";
		} else {
			$display_away = "Availability Unknown since ".$displaydate;
			$tmp_your = "Status Unknown";
		}

		$xtpl->assign("prompt",$tmp_display);
		$xtpl->assign("tmpyour",$tmp_your);
		$xtpl->assign("tmpTitle",$display_away);
		$xtpl->assign("number",$number);
		$xtpl->parse("main");
		$xtpl->out("main");
	}
}

?>