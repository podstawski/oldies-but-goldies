<?
	
	function makeList($arr)
	{
		if (!is_array($arr)) return;
		$res = ",";
		for ($i=0; $i < count($arr); $i++)
		{
			if (is_array($arr[$i]))
				$res.=makeList($arr[$i]);
			else
				$res.="'".$arr[$i]."',";
		}
		$res = substr($res,1,-1);
		$res = ereg_replace("''","','",$res);
		return $res;
	}

	$lista_katalogow = system_list_dir($UFILES,0,1);
	$lista_nazw =  system_list_dir($UFILES,1,1);

	$lista_sql = makeList($lista_nazw);

	$sql = "DELETE FROM system_obiekt WHERE so_server = $SERVER_ID
			AND so_klucz NOT IN ($lista_sql) AND so_klucz LIKE 'a_%'";

	$lista_sql = explode(",",$lista_sql);
	$projdb->debug=0;	
	for ($i=0; $i < count($lista_sql); $i++)
	{
		$query = "SELECT COUNT(*) AS jest FROM system_obiekt WHERE so_klucz = $lista_sql[$i] AND so_server = $SERVER_ID";
		parse_str(ado_query2url($query));
		if (!$jest)
			$sql.= ";INSERT INTO system_obiekt (so_klucz,so_server) VALUES (".$lista_sql[$i].",$SERVER_ID)";
	}
	

	$projdb->execute($sql);
	$projdb->debug=0;
	global $sg_id;
	$lista_praw = array();
	if (strlen($sg_id))
	{
		$sql = "SELECT sao_klucz FROM system_acl_obiekt WHERE sao_grupa_id = $sg_id";
		$res = $projdb->execute($sql);
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res, $i));
			$lista_praw[] = $sao_klucz;
		}
	}

	$pliki  = system_print_dirlist($lista_katalogow,$lista_nazw,$lista_praw);

	echo "
	<FORM METHOD=POST ACTION=\"$next\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszPrawaPliki\">
	<INPUT TYPE=\"hidden\" name=\"form[sg_id]\" value=\"$sg_id\">
	<TABLE>
	<TR>
		<TD>$pliki</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"button\" value=\"Anuluj\" class=\"sys_button\" onClick=\"history.back()\"></TD>
		<TD><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"sys_button\"></TD>
	</TR>
	</TABLE>
	</FORM>
	";


?>