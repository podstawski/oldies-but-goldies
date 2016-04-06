<?
/*
		$sql = "ALTER TABLE system_log ADD sl_tin int4;
				ALTER TABLE system_log ADD sl_tout int4";
		$res = 	pg_exec($db,$sql);
*/
//		$sql = "DELETE FROM system_log";
//		pg_exec($db,$sql);		
	
		include("$INCLUDE_PATH/navifun.h");
		global $limit, $offset, $ile, $start, $navi, $szukaj;

		echo SID;		

		$szukaj[user] = urldecode($szukaj[user]);

		$limit = 10;
		if (strlen($size) && $size!=0) 	$limit = $size;

		$addsql = "";

		if (strlen($szukaj[data_od]))
			$addsql.= " AND sl_tin >= ".strtotime(FormatujDateSql($szukaj[data_od]))." ";
		if (strlen($szukaj[data_do]))
			$addsql.= " AND sl_tout <= ".strtotime(FormatujDateSql($szukaj[data_do]))." ";
		if (strlen($szukaj[user]))
		{
			$addsql.= " AND sl_user = '".$szukaj[user]."' ";

			$sql = "SELECT SUM(sl_tout - sl_tin) AS suma FROM system_log 
					WHERE sl_server = $SERVER_ID $addsql";
//			$adodb->debug=1;
			parse_str(ado_query2url($sql));
//			$adodb->debug=0;
			$total_info = "Łączny czas dla: ".$szukaj[user]." - ".date("H:i:s",$suma - 3600)."<br>";
		}
		
		if (!strlen($szukaj[data_od])) $szukaj[data_od] = date("01-m-Y");
		if (!strlen($szukaj[data_do])) $szukaj[data_do] = date("d-m-Y");
	

		if (!$ile)
		{
			$sql = "SELECT * FROM system_log WHERE	
					sl_server = $SERVER_ID 
					$addsql	
					ORDER BY sl_tin";

			$res = $adodb->execute($sql);		
			$ile = $res->RecordCount();
			$start=0;
		}

		$offset=$start;			

		$sql = "SELECT DISTINCT(sl_user) FROM system_log WHERE	
				sl_server = $SERVER_ID 
				AND sl_user != '' 
				ORDER BY sl_user";
		$res = $adodb->execute($sql);				
		$cont= "<option value=\"\" $sel>wybierz z listy</option>";
		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_explodename($res,$i));
			if ($szukaj[user] == $sl_user)
				$sel = "selected";
			else
				$sel = "";
			$cont.= "<option value=\"$sl_user\" $sel>$sl_user</option>";
		}

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
			<td class=\"tabletd\">Użytkownik:</td>
			<td class=\"tabletd\"><select name=\"szukaj[user]\" class=\"forminput\">$cont</select></td>
		</TR>
		<TR class=\"tabletr\" bgcolor=\"#EAEAF4\">
			<td align=\"center\" colspan=\"2\" class=\"tabletd\"><INPUT TYPE=\"submit\" value=\"Szukaj\" class=\"formbutton\"></td>
		</TR>
		</table><br>
		$total_info
		<br>
		</FORM>
		";		

		echo "Znalezionych wpisów: ".$ile." ";
		
		$href="$self${next_char}szukaj[data_od]=$szukaj[data_od]&szukaj[data_do]=$szukaj[data_do]&szukaj[user]=".urlencode($szukaj[user]);		
		$nawigacja=naviIndex($href,$start,$offset,$ile,$limit);
		echo "<table width=100%><tr><td>$nawigacja</td></tr></table>";

		$sql = "SELECT * FROM system_log WHERE	
				sl_server = $SERVER_ID
				$addsql
				ORDER BY sl_tin LIMIT $limit OFFSET $offset";

//		$adodb->debug=1;
		$res = $adodb->execute($sql);
//		$adodb->debug=0;		
		echo "
		<TABLE border=\"1\" cellspacing=\"0\" cellpading=\"0\" class=\"tabletable\" width=\"100%\">
		<TR class=\"tabletr\" bgcolor=\"#EAEAF4\">
			<TH class=\"tabletd\">Nr</TH>
			<TH class=\"tabletd\">Użytkownik</TH>
			<TH class=\"tabletd\">Logowanie</TH>
			<TH class=\"tabletd\">Wylogowanie</TH>
			<TH class=\"tabletd\">Czas pobytu</TH>
			<TH class=\"tabletd\">IP</TH>
			<TH class=\"tabletd\">Ostatnia strona</TH>
		</TR>";

		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			if (!strlen($sl_user)) $sl_user = "&nbsp;";
			
			if ($i%2) 
				$bgcolor = "bgcolor=\"#FFFFFF\"";
			else
				$bgcolor = "bgcolor=\"#F2F2F2\"";
			echo "
				<TR class=\"tabletr\" $bgcolor>
					<TD class=\"tabletd\">".($i+1)."</TD>
					<TD class=\"tabletd\" nowrap>$sl_user</TD>
					<TD class=\"tabletd\" nowrap>".date("d.m.Y H:i:s",$sl_tin)."</TD>
					<TD class=\"tabletd\" nowrap>".date("d.m.Y H:i:s",$sl_tout)."</TD>
					<TD class=\"tabletd\" nowrap>".date("H:i:s",$sl_tout - $sl_tin - 3600)."</TD>
					<TD class=\"tabletd\">$sl_ip</TD>
					<TD class=\"tabletd\">$sl_lastpage</TD>
				</TR>";
		}
		echo "</TABLE>";
?>