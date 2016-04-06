<?
  $hidenew = true;
	include ("include/users.h");

	function sec2czas($czas)
	{
		$hours=floor($czas/3600);
		$minutes=floor(($czas%3600)/60);
		$sec=$czas%60;
		return sprintf("%02d:%02d:%02d",$hours,$minutes,$sec);
	}

	function tr_suma($opis,$czas)
	{
		echo " <tr class=\"line_4\">\n";

		echo "  <td><b>".label("Sum of").": $opis</b></td>";
		echo "  <td align=right><b>".sec2czas($czas)."</b></td>";
		echo "  <td>&nbsp;</td>";

		echo " </tr>\n";
	}

	$query="SELECT tin, tout-tin AS czas, ip FROM login_all
		  WHERE username='$login'
		  ORDER BY tin DESC";

	$res=$adodb->Execute($query);
	
	
	echo "<table class=\"tabelka\" cellspacing=\"0\" cellpadding=\"0\">";
		
	if ($res->RecordCount())
	{
		echo "<tr>
			<th colspan=\"3\">".label("Conections for user").": $login</th></tr>
			<tr class=\"line_3\">
			<td>".label("Date")."</td>
			<td>".label("Time")."</td>
			<td>".label("IP number")."</td></tr>\n";
	}
	else
	{
		if ($login)
			echo "<tr class=k_formtitle>
				<td align=center>".label("No conections")."</td></tr>\n";
		else
			echo "<tr class=k_formtitle>
				<td align=center>".label("No user selected")."</td></tr>\n";
	}
	
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		if (!$czas) continue;
	 	$t=getdate($tin);

		if ($last_day && $last_day!=$t["mday"])
		{
			tr_suma(label("the day"),$czas_d);
			$czas_d=0;
		}
		if ($last_mon && $last_mon!=$t["mon"])
		{
			tr_suma(label("the month")." $last_mon",$czas_m);
			$czas_m=0;
		}
		if ($last_year && $last_year!=$t["year"])
		{
			tr_suma(label("the year")." $last_year",$czas_r);
			$czas_r=0;
		}

		$ser_bgcolor=" class=\"line_0\"";
		if (($i&1)==0) $ser_bgcolor=" class=\"line_1\"";
		
		echo "<tr $ser_bgcolor>\n";
		echo "<td>";
 		echo sprintf("%02d-%02d-%04d, %02d:%02d",$t["mday"],$t["mon"],$t["year"],$t["hours"],$t["minutes"]);
		echo "</td>\n";

		echo "  <td align=right>";
		echo sec2czas($czas);
		echo "</td>\n";
	
		echo "  <td align=right>$ip</td>\n";

		echo " </tr>\n";

		$czas_d+=$czas;
		$czas_m+=$czas;
		$czas_r+=$czas;

		$last_day=$t["mday"];
		$last_mon=$t["mon"];
		$last_year=$t["year"];
	}
	
	if ($res->RecordCount())
	{
		tr_suma(label("the day"),$czas_d);
		tr_suma(label("the month")." $last_mon",$czas_m);
		tr_suma(label("the year")." $last_year",$czas_r);
	}
	echo "</table><br>";
?>
