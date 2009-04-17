<?php
/*
	UCxml web Portal - View contacts

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
*/

$tmp_id_user = defang_input($_SESSION['user_id']);
$tmp_username = defang_input($_SESSION['user_name']);

if (isset($_POST['save_view']))
{
	// Saving
	$tmp_status_view = defang_input($_POST['status_view']);
	$tmpUpdateSQL = "UPDATE users
					SET status_view = '$tmp_status_view'
					WHERE id_user = '$tmp_id_user'";
	mysql_query($tmpUpdateSQL, $db);

	header("Location: index.php?module=view_status&status_view=$tmp_status_view");

} else {
	//display contacts
	render_HeaderFooter("UCxml web Portal - Phone Status");
	output_view_status($tmp_id_user);
	render_Footer();
}

//
//  FUNCTIONS
//

function output_view_status ($myID_user, $myUsername)
{
	include "language/lang.php";
	global $db, $xtpl,$xml;
	$xtpl=new XTemplate ("modules/templates/view_status.html");
	$xtpl->assign( 'LANG', $lang );


  	$checkSQL = "SELECT status_view FROM users WHERE id_user='$myID_user'";
	$checkRES = mysql_query($checkSQL, $db);
	if ($in = mysql_fetch_assoc($checkRES))
		{
       		if ($in['status_view'] == "all")
			{
				$xtpl->assign("sel_all", "selected");
			}
	        elseif ($in['status_view'] == "in")
			{
				$xtpl->assign("sel_in", "selected");
			}
	        else
			{
				$xtpl->assign("sel_out", "selected");
			}
	    }


        $obprefSQL = "SELECT status_view FROM users WHERE id_user = '$myID_user'";
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
		if ($_GET['ob'] == "ob_n")
		{
			$ob = "nick";
		} elseif ($_GET['ob'] == "ob_status") {
			$ob = "status";
		} elseif ($_GET['ob'] == "ob_access_lvl") {
			$ob = "access_lvl";
		}
	} else {
	$ob = "nick";
	}

		if (isset($_GET['status_view']))
		{

			$status_view = defang_input($_GET['status_view']);
			if ($status_view == 'all')
            {
                  //user wants to view everyones' status
                $loc_sql = "";
                $xtpl->assign("sel_all","selected");
                $xtpl->assign("sel_in","");
                $xtpl->assign("sel_out","");
			}
			elseif ($status_view == 'in')
			{
                //user wants to view people in the available, status
                $loc_sql = "WHERE $xml->tuple->note = 'available'";
                $xtpl->assign("sel_all","");
                $xtpl->assign("sel_in","selected");
                $xtpl->assign("sel_out","");
			}
			elseif ($status_view == 'out')
			{
                //user wants to view people unavailable, status
                $loc_sql = "WHERE $xml->tuple->note = 'unavailable'";
                $xtpl->assign("sel_all","");
                $xtpl->assign("sel_in","");
				$xtpl->assign("sel_out","selected");
	        }

				$xtpl->parse("main.column");//show columns

				if(($sock=fsockopen("xxx.xxx.xxx.xxx",3306,$errorno,$errorstr,60)))
				{
					$db2 = mysql_connect("xxx.xxx.xxx.xxx","watcher","presence");
					mysql_select_db("opensips",$db2);

				$theSQL = "SELECT username,body FROM presentity $loc_sql";
				$theRES = mysql_query($theSQL, $db2);

				$oddRow = true;
            	while($in=mysql_fetch_object($theSQL))
				{
                    if ($oddRow)
                    {
                            $xtpl->assign("bg","#EFEFEF");
                    } else {
                            $xtpl->assign("bg","#DFDFDF");
                    }

					$time = date("Y/m/d H:i:s");
                    $xtpl->assign ("time", $time);

					$xml = simplexml_load_string($in['body']);
					if($xml->tuple->status->basic=="open")
					{
						$xtpl->assign ("username", $in['username']);
						$xtpl->assign ("status", $in2['.$xml->tuple->im:im.']);
						$xtpl->assign ("note", $in['.$xml->tuple->note.']);
					}
					$xtpl->assign ("body", $in['body']);

					$xtpl->parse("main.row");
					$oddRow = !$oddRow;

				}

				$theSQL2 = "SELECT username,body FROM presentity WHERE username='$myUsername'";
				$theRES2 = mysql_query($theSQL2, $db2);

            	while($in2=mysql_fetch_object($theSQL2))
				{

					$xml = simplexml_load_string($in2['body']);
					if($xml->tuple->status->basic=="open")
					{
						$xtpl->assign ("my_username", $in2['username']);
						$xtpl->assign ("my_status", $in2['.$xml->tuple->im:im.']);
						$xtpl->assign ("my_note", $in2['.$xml->tuple->note.']);
					}
				}

                // Output
                $xtpl->parse("main");
                $xtpl->out("main");

				mysql_close($db2);
				fclose($sock);
				}
		else
		{
			echo "Servre seems Down";
		}
	}
}
?>