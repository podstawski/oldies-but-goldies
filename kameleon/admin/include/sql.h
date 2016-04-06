<?
	include ("include/users.h");


	$href=$SCRIPT_NAME;
	$back=$href;

	$usun=$href."?action=delsql";

	if (!strlen($pe_sess_id))
	{
		$query="SELECT 'pe_sess_id' AS link_name,pe_sess_id AS link_value,
				pe_sess_id,min(pe_data) AS pe_sql,max(pe_data) AS pe_data,
				sum(pe_czas) AS pe_czas, count(*) AS pe_count
				FROM kameleon_performance
				WHERE pe_parent IS NULL
				GROUP BY pe_sess_id
				ORDER BY max(pe_data) DESC";
		$url_sign="?";
	}
	if (strlen($pe_sess_id))
	{
		$query="SELECT 'pe_parent' AS link_name,pe_id AS link_value,
				pe_sql,pe_czas,pe_count
				FROM kameleon_performance
				WHERE pe_sess_id='$pe_sess_id' AND pe_parent IS NULL
				ORDER BY pe_id DESC";
		
		$href.='?pe_sess_id='.$pe_sess_id;
		$url_sign="&";
		$usun=$href."&action=delsql";
	}


	if (strlen($pe_parent))
	{
		$query="SELECT 'pe_id' AS link_name,pe_id AS link_value,
				pe_sql,pe_czas,pe_count
				FROM kameleon_performance
				WHERE pe_parent = $pe_parent
				ORDER BY pe_id";

		
		$back=$href;
		$href.='&pe_parent='.$pe_parent;
		$url_sign="&";
		$usun=$href."&action=delsql";
	}

	if (strlen($pe_id))
	{
		$query="SELECT pe_sql,pe_czas,pe_count,pe_result
				FROM kameleon_performance
				WHERE pe_id = $pe_id
				";

		$back=$href;
		$href.='&pe_parent='.$pe_parent;
		$url_sign="&";
		$usun="";
	}




//	$adodb->debug=1;
	$res=$adodb->Execute($query);


	
	
	echo "<br><a href=\"$back\" class=\"k_td\">".label("Return")."</a>
		<table border=1 align=center width=\"100%\">";
		
	if ($res->RecordCount())
	{
		echo "<tr class=\"k_formtitle\">
			<td align=\"center\" width=\"80%\">".label("SQL")."</td>
			<td align=\"right\">".label("Time")." [s]</td>
			<td align=\"right\">".label("Results")."</td></tr>\n";
	}
	
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$ser_bgcolor="bgcolor=\"#D0D0D0\"";
		if (($i&1)==0) $ser_bgcolor="bgcolor=\"#E0E0E0\"";

		if ($pe_count<0) $ser_bgcolor="bgcolor=\"#F0F0A0\"";
		
		if (!strlen($_REQUEST["pe_sess_id"])) 
			$pe_sql=date('d-m-Y H:i',$pe_sql).' - '.date('H:i',$pe_data);

		$shittag="";
		$link=$href.$url_sign.$link_name.'='.$link_value;
		if (!strlen($link_name)) $shittag="skldjf";
		
		$pe_sql=nl2br(stripslashes($pe_sql));
		if (strstr($pe_sql,'QS=')) $pe_sql=chunk_split($pe_sql);

		if (strlen($_REQUEST["pe_id"])) 
		{
			$pe_sql.="<hr size=1>";
			foreach (explode('&',$pe_result) AS $pair)
			{
				$p=explode('=',$pair);
				if (!strlen($p[0])) continue;
				$pe_sql.='<b>'.$p[0].'</b> = '.urldecode($p[1]).'<br>';
			}
		}
		$pe_czas=number_format($pe_czas,4,",","");
		echo "<tr class=k_td $ser_bgcolor>\n";
		echo "<td><a$shittag href=\"$link\">$pe_sql</a$shittag>";
		echo "<td align=\"right\"><a$shittag href=\"$link\">$pe_czas</a$shittag>";
		echo "<td align=\"right\"><a$shittag href=\"$link\">$pe_count</a$shittag>";
	}
	
	echo "</table>";

	if (strlen($usun)) echo "<a href=\"$usun\" class=\"k_td\">".label("Delete")."</a>";
?>