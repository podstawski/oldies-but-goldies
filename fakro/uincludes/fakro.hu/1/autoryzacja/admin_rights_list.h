<?
	//synchronizacji dziękujemy
	return;

	if (!$KAMELEON_MODE) return;	
	include ("$INCLUDE_PATH/autoryzacja/config.inc.php");

	global $doUpdate;
	echo "
	<TABLE width=\"100%\">
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"doUpdate\" value=\"1\">
	<TR>
		<TD align=\"center\"><INPUT TYPE=\"submit\" class=\"formbutton\" value=\"Synchronizuj tabelę z prawami\" style=\"width:300px\"></TD>
	</TR>
	</FORM>	
	</TABLE>
	";
	
	if (!$doUpdate) return;

	if ($_AUTH_TREE_ROOT > 0)
	{
		function getChildList($plist)
		{	
			global $SERVER_ID, $ver, $lang, $adodb;
			
			if (!strlen($plist)) return;

			$sql = "SELECT id FROM webpage WHERE
					server = $SERVER_ID 
					AND ver = $ver 
					AND lang = '$lang'
					AND prev IN ($plist)";
//			$adodb->debug=1;
			$res = $adodb->execute($sql);
//			$adodb->debug=0;
			$lista = "";
			for ($i=0; $i < $res->RecordCount(); $i++)
			{
				parse_str(ado_explodename($res,$i));
				$lista.= ",$id";
			}
			$lista = substr($lista,1);
			return $lista;
		}

		$lista_stron = "";
		$lista = $_AUTH_TREE_ROOT;
		while (strlen($lista))
		{
			$lista = getChildList($lista);
			if (strlen(trim($lista)))
				$lista_stron.= ",".$lista;
		}
		$lista_stron = substr($lista_stron,1);
		$add_sql = " AND id IN ($lista_stron) ";
//		echo $add_sql."<br>";
	}

	$sql = "DELETE FROM system_obiekt";
	pg_exec($db,$sql);

	$sql = "SELECT title, id, prev FROM webpage
			WHERE server = $SERVER_ID
			 AND ver = $ver AND lang = '$lang'
			$add_sql
			ORDER BY id";

//	$adodb->debug = 1;
	$res = $adodb->execute($sql);
//	$adodb->debug = 0;

	for ($i=0;$i<$res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		echo "Dodano klucz : p_$id - $title<br>";
		if (!strlen($prev)) $prev = "NULL";
		$sql = "INSERT INTO system_obiekt 
				(so_server,so_klucz,so_nazwa,so_parent)
				VALUES
				($SERVER_ID,'p_$id','$title',$prev)";
		pg_exec($db,$sql);
	}
?>