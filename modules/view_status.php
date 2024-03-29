<?php
/*
	UCxml web Portal - View status

	Zoli Toth, FEI TUKE
	Unified Communications solution with Open Source applications - UCxml
	source code: http://ucxml.googlecode.com
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

} 
elseif (isset($_POST['submit_add']))
{
	header("Location: index.php?module=change_status");
}

else {
	//display contacts
	render_HeaderFooter("UCxml web Portal - Phone Status");
	output_view_status($tmp_id_user, $tmp_username);
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
                $loc_sql = "AND extractvalue(body, '//note') IN ('available', '')";
                $xtpl->assign("sel_all","");
                $xtpl->assign("sel_in","selected");
                $xtpl->assign("sel_out","");
			}
				$xtpl->parse("main.column");//show columns

				if(($sock=fsockopen("5.5.6.12",3306,$errorno,$errorstr,60)))
				{
				$theSQL = "SELECT username, extractvalue(body, '//note') AS note FROM opensips.presentity WHERE username !='$myUsername' 
		AND extractvalue(body, '//basic')='open' $loc_sql";
				$theRES = mysql_query($theSQL, $db);

				$oddRow = true;
	
	            	while($in=mysql_fetch_assoc($theRES))
				{
                    if ($oddRow)
                    {
                            $xtpl->assign("bg","#EFEFEF");
                    } else {
                            $xtpl->assign("bg","#DFDFDF");
                    }

					$xtpl->assign ("username", $in['username']);
					$xtpl->assign ("status", $in['note']);

					$xtpl->parse("main.row");
					$oddRow = !$oddRow;

				}

			$theSQL2 = "SELECT username, extractvalue(body, '//note') AS note FROM opensips.presentity WHERE username='$myUsername' 
			AND extractvalue(body, '//basic')='open'";
				$theRES2 = mysql_query($theSQL2, $db);

		            	if($in2=mysql_fetch_assoc($theRES2))
				{
					$xtpl->assign ("my_username", $in2['username']);
					if ($in2['note'] == '')
					{
					$xtpl->assign ("my_status", "Online");
					}
					else
					{
					$xtpl->assign ("my_status", $in2['note']);					}
					}

                // Output
                $xtpl->parse("main");
                $xtpl->out("main");

				fclose($sock);
				}
		else
		{
			echo "Server seems Down";
		}
	}
}
?>