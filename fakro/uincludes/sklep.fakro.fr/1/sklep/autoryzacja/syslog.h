<?	
		global $limit, $offset, $ile, $start, $navi, $szukaj;
		$szukaj[user] = urldecode($szukaj[user]);
		
		$query="SELECT su_data_dodania,su_data_modyfikacji FROM system_user WHERE su_id=".$LIST[id];
		parse_str(ado_query2url($query));
		if ($su_data_dodania) echo "<B>Dodano:</B> ".date("d-m-Y H:i:s",$su_data_dodania)."<br>";
		if ($su_data_modyfikacji) echo "<B>Zmieniono:</B> ".date("d-m-Y H:i:s",$su_data_modyfikacji)."<br>";

		if ($su_data_dodania OR $su_data_modyfikacji) echo "<hr size=1>";

		$limit = 100;
		if (strlen($size) && $size!=0) 	$limit = $size;

		$addsql = "";

		if (!strlen($szukaj[data_od])) $szukaj[data_od] = date("01-m-Y");
		if (!strlen($szukaj[data_do])) $szukaj[data_do] = date("d-m-Y",strtotime("+1 day"));

		$addsql.= " AND sl_tin >= ".strtotime(FormatujDateSql($szukaj[data_od]))." ";
		$addsql.= " AND sl_tout <= ".strtotime(FormatujDateSql($szukaj[data_do])." 23:59:59")." ";

		if (strlen($szukaj[user]))
		{
			$addsql.= " AND sl_user_id = (SELECT su_id FROM system_user 
					WHERE (su_login = '".$szukaj[user]."' OR su_pesel = '".$szukaj[user]."')) ";

			$sql = "SELECT SUM(sl_tout - sl_tin) AS suma FROM system_log 
					WHERE sl_server = $SERVER_ID $addsql";
			parse_str(query2url($sql));
			$total_info = "£±czny czas pobytu dla u¿ytkownika: ".date("H:i:s",$suma - 3600);
		}	

		if (!$ile)
		{
			$sql = "SELECT COUNT(*) AS ile FROM system_log WHERE	
					sl_server = $SERVER_ID 
					$addsql";
			parse_str(query2url($sql));
			$start=0;
		}

		$offset=$start;			

		echo "
		<FORM METHOD=POST ACTION=\"$self\">		
		<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
		<TR>
			<td class=\"c2\" align=right>Data od:</td>
			<td class=\"c4\"><INPUT TYPE=\"text\" class=\"forminput\" NAME=\"szukaj[data_od]\" value=\"".$szukaj[data_od]."\"></td>
			<td class=\"c3\" align=right>Data do:</td>
			<td class=\"c4\"><INPUT TYPE=\"text\" class=\"forminput\" NAME=\"szukaj[data_do]\" value=\"".$szukaj[data_do]."\"></td>
		</TR>
		<TR>
			<td align=\"right\" colspan=\"4\" class=\"tabletd\"><INPUT TYPE=\"submit\" value=\"Szukaj\" class=\"sys\"></td>
		</TR>
		</table><br>
		$total_info
		<br>
		<input type=hidden name=\"list[id]\" value=\"$LIST[id]\">
		</FORM>
		";		
		
		if (!strlen($szukaj[user])) return;

		echo "Znalezionych wpisów: ".$ile." ";
		
		$href="$self${next_char}szukaj[data_od]=$szukaj[data_od]&szukaj[data_do]=$szukaj[data_do]&szukaj[user]=".urlencode($szukaj[user]);		
		$nawigacja=naviIndex($href,$start,$offset,$ile,$limit);
		echo "<table width=100%><tr><td>$nawigacja</td></tr></table>";

		$sql = "SELECT * FROM system_log WHERE	
				sl_server = $SERVER_ID
				$addsql
				ORDER BY sl_tin LIMIT $limit OFFSET $offset";

		$res = pg_exec($db,$sql);		

		if (!$ile) return;

		echo "
		<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
		<TR>
			<TH>Nr</TH>
			<TH>Logowanie</TH>
			<TH>Wylogowanie</TH>
			<TH>Czas pobytu</TH>
			<TH>IP</TH>
			<TH title='Ostatnia strona'>OS</TH>
		</TR>";

		for ($i=0; $i < pg_numrows($res); $i++)
		{
			parse_str(pg_explodename($res,$i));
			if (!strlen($sl_user)) $sl_user = "&nbsp;";
			
			if ($i%2) 
				$bgcolor = "bgcolor=\"#FFFFFF\"";
			else
				$bgcolor = "bgcolor=\"#F2F2F2\"";
			
			$sql = "SELECT su_imiona, su_nazwisko FROM system_user WHERE su_id = $sl_user_id";
			parse_str(query2url($sql));			

			echo "
				<TR $bgcolor>
					<TD class=\"c2\">".($i+1)."</TD>
					<TD class=\"c2\" nowrap>".date("d.m.Y H:i:s",$sl_tin)."</TD>
					<TD class=\"c2\" nowrap>".date("d.m H:i:s",$sl_tout)."</TD>
					<TD class=\"c2\" nowrap>".date("H:i:s",$sl_tout - $sl_tin - 3600)."</TD>
					<TD class=\"c2\">$sl_ip</TD>
					<TD class=\"c4\">$sl_lastpage</TD>
				</TR>";
		}		
		echo "</TABLE>";
?>
