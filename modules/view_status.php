<?php
/*
	UCxml web Portal - View contacts

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/

$tmp_id_user = defang_input($_SESSION['user_id']);

if (isset($_POST['save_view']))
{
	// Saving
	$tmp_status_view = defang_input($_POST['status_view']);
	$tmpUpdateSQL = "UPDATE users
					SET	status_view = '$tmp_status_view'
					WHERE id_user = '$tmp_id_user'";
	mysql_query($tmpUpdateSQL, $db);

	header("Location: index.php?module=view_status");

} else {
	//display contacts
	render_HeaderFooter("UCxml web Portal - Phone Status");
	output_view_status($tmp_id_user);
	render_Footer();
}

//
//  FUNCTIONS
//

function output_view_status ($myID_user)
{
	include "language/lang.php";
	global $db, $xtpl;
	$xtpl=new XTemplate ("modules/templates/view_status.html");
	$xtpl->assign( 'LANG', $lang );


  	$checkSQL = "SELECT status_view FROM users WHERE id_user='$myID_user'";
	$checkRES = mysql_query($checkSQL, $db);
	if ($in = mysql_fetch_assoc($checkRES))
		{
			if( $in['status_view'] )
			{
				$_SESSION['status_view'] = $in['status_view'];
			}
//          		$xtpl->assign("status_view",$in['status_view']);

       		if ($in['status_view'] == "all")
			{
				$xtpl->assign("sel_all", "selected");
				$xtpl->assign("sel_in", "");
				$xtpl->assign("sel_out", "");
			}
	        elseif ($in['status_view'] == "in")
			{
				$xtpl->assign("sel_all", "");
				$xtpl->assign("sel_in", "selected");
				$xtpl->assign("sel_out", "");
			}
	        else
			{
				$xtpl->assign("sel_all", "");
				$xtpl->assign("sel_in", "");
				$xtpl->assign("sel_out", "selected");
			}

	    }

	$obprefSQL = "SELECT status_view FROM users WHERE id_user='$myID_user'";
	$obRES = mysql_query($obprefSQL, $db);
	if ($gl = mysql_fetch_assoc($obRES))
	{
		$status_view = $gl['status_view'];
	} else {
		//sql error
		$status_view = "all";
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

       if (isset($_GET['status_view']))
	   {
       		$status_view = defang_input($_GET['status_view']);

			if ($in['status_view'] == "all")
			{
			//user wants to view everyones' status
				$loc_sql = "";
				$xtpl->assign("sel_all",'selected');

			} elseif ($in['status_view'] == "out") {
				//user wants to view people unavailable, status
				$loc_sql = "WHERE phone.status = 0 AND phone.access_lvl != 'unknown'";
				$xtpl->assign("sel_out",'selected');

			} elseif ($in['status_view'] == "in") {
				//user wants to view people in the available, status
				$loc_sql = "WHERE phone.status = 1 AND phone.access_lvl != 'unknown'";
				$xtpl->assign("sel_in",'selected');
				$xtpl->assign("in",$status_view);
			}

        }
			$xtpl->parse("main.column");//show columns

			$theSQL = "SELECT id_phone,status,nick,away_msg,access_lvl FROM phone $loc_sql";
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
				$xtpl->assign("id_phone",$in['id_phone']);
				$xtpl->assign("status",$in['status']);
				$xtpl->assign("nick");
				$xtpl->assign("away_msg",$in['away_msg']);
				$xtpl->assign("access_lvl",$in['access_lvl']);

				$xtpl->parse("main.row");
				$oddRow = !$oddRow;

			}
      // Output
			$xtpl->parse("main");
			$xtpl->out("main");


/*  	if (isset($_GET['ur']))
		{
		$urMAC = defang_input($_GET['ur']);
		show_status($MAC,$urMAC);

		} elseif (isset($_GET['view_my_status'])) {

		show_status($MAC,$MAC);
		}
*/
}
function my_status()
{
	if (isset($_GET['my_status'])) {
		//User has requested to change their status
		if ($registered == "TRUE")
		{
			$xtpl=new XTemplate ("templates/my_status.xml");

			$statusqry = "SELECT phone.status AS status FROM phone WHERE MAC = '$MAC'";
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

	include "language/lang.php";
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
			$tmp_display = $in['away_msg'];
			$xtpl->assign("msg",$tmp_display);
		}
		else {
			$xtpl=new XTemplate ("templates/status_detail.xml");
			$xtpl->assign("msg",$in['lname'].",".$in['fname']);
		}

		$tmp_display = $in['away_msg'];

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
		$xtpl->parse("main");
		$xtpl->out("main");
	}
}
?>