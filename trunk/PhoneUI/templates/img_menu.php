<?php
//require_once ("../lib/refresh.php")

	if ($account_type == 'Admin')
	{
		$checkSQL = "SELECT count(*) AS newmemo FROM memos
		WHERE memos.receiver='$my_nick' AND memos.read = '0'";
	}

	else
	{
		$checkSQL ="SELECT count(*),
   			CASE
			WHEN (memos.receiver = '' AND memos.id_memo = memos_read.id_memo
			AND memos_read.receiver = '$my_nick')
			THEN 0 ELSE 1
			END AS newmemo
			FROM memos LEFT JOIN memos_read ON memos.id_memo = memos_read.id_memo
			WHERE memos.receiver IN ('$my_nick','')";
	}

		$checkRES = mysql_query($checkSQL, $db);

		if($in2 = mysql_fetch_assoc($checkRES))
		{
	   		if ($in2['newmemo'] > '0')
			{
				$newmemo = $in2['newmemo'];
			}
		}


echo "
<CiscoIPPhoneMenu>
<Title>Menu</Title>

  <MenuItem>
  <Name>1. Contacts</Name>
  <URL>".$URLBase."contacts.php?name=".$MAC."</URL>
 </MenuItem>

 <MenuItem>
  <Name>2. Memos ".$newmemo."</Name>
  <URL>".$URLBase."memos.php?name=".$MAC."</URL>
 </MenuItem>

 <MenuItem>
  <Name>3. Status</Name>
  <URL>".$URLBase."status.php?name=".$MAC."</URL>
 </MenuItem>

</CiscoIPPhoneMenu>
";
?>
