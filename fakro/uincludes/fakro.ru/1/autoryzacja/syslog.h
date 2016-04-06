<?	
		global $limit, $offset, $ile, $start, $navi, $szukaj;
		$szukaj[user] = urldecode($szukaj[user]);
		
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
			$total_info = "ЃБczny czas pobytu dla uПytkownika: ".date("H:i:s",$suma - 3600)."<br>";
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
/* nie wywalam bo moПe siъ przyda

			$sql = "SELECT DISTINCT(sl_user_id) FROM system_log WHERE	
				sl_server = $SERVER_ID 
				ORDER BY sl_user_id";
			$res = $ofekdb->execute($sql);				
			$users = array();
			for ($i=0;$i<$res->RecordCount();$i++)
			{
				parse_str(ado_explodename($res,$i));
				$sql = "SELECT su_imiona, su_nazwisko FROM system_user WHERE su_id = $sl_user_id";
					parse_str(ado_query2url($sql));			
				$users[$sl_user_id]	= $su_imiona." ".$su_nazwisko;
			}
*/

		echo "
		<FORM METHOD=POST ACTION=\"$self\">		
		<TABLE border=\"1\" cellspacing=\"0\" cellpading=\"0\" class=\"tabletable\" width=\"100%\">
		<TR class=\"tabletr\" bgcolor=\"#EAEAF4\">
			<td class=\"tabletd\">Data od:</td>
			<td class=\"tabletd\"><INPUT TYPE=\"text\" class=\"forminput\" NAME=\"szukaj[data_od]\" value=\"".$szukaj[data_od]."\"></td>
		</TR>
		<TR class=\"tabletr\" bgcolor=\"#EAEAF4\">
			<td class=\"tabletd\">Data do:</td>
			<td class=\"tabletd\"><INPUT TYPE=\"text\" class=\"forminput\" NAME=\"szukaj[data_do]\" value=\"".$szukaj[data_do]."\"></td>
		</TR>
		<TR class=\"tabletr\" bgcolor=\"#EAEAF4\">
			<td class=\"tabletd\">Login:</td>
			<td class=\"tabletd\"><INPUT TYPE=\"text\" class=\"forminput\" NAME=\"szukaj[user]\" value=\"".$szukaj[user]."\"></td>
		</TR>
		<TR class=\"tabletr\" bgcolor=\"#EAEAF4\">
			<td align=\"center\" colspan=\"2\" class=\"tabletd\"><INPUT TYPE=\"submit\" value=\"Szukaj\" class=\"sys_button\"></td>
		</TR>
		</table><br>
		$total_info
		<br>
		</FORM>
		";		
		
		if (!strlen($szukaj[user])) return;

		echo "Znalezionych wpisѓw: ".$ile." ";
		
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
		<TABLE border=\"1\" cellspacing=\"0\" cellpading=\"0\" class=\"tabletable\" width=\"100%\">
		<TR class=\"tabletr\" bgcolor=\"#EAEAF4\">
			<TH class=\"tabletd\">Nr</TH>
			<TH class=\"tabletd\">UПytkownik</TH>
			<TH class=\"tabletd\">Logowanie</TH>
			<TH class=\"tabletd\">Wylogowanie</TH>
			<TH class=\"tabletd\">Czas pobytu</TH>
			<TH class=\"tabletd\">IP</TH>
			<TH class=\"tabletd\">Ostatnia strona</TH>
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
				<TR class=\"tabletr\" $bgcolor>
					<TD class=\"tabletd\">".($i+1)."</TD>
					<TD class=\"tabletd\" nowrap>".$su_imiona." ".$su_nazwisko."</TD>
					<TD class=\"tabletd\" nowrap>".date("d.m.Y H:i:s",$sl_tin)."</TD>
					<TD class=\"tabletd\" nowrap>".date("d.m.Y H:i:s",$sl_tout)."</TD>
					<TD class=\"tabletd\" nowrap>".date("H:i:s",$sl_tout - $sl_tin - 3600)."</TD>
					<TD class=\"tabletd\">$sl_ip</TD>
					<TD class=\"tabletd\">$sl_lastpage</TD>
				</TR>";
		}		
		echo "</TABLE>";
?>