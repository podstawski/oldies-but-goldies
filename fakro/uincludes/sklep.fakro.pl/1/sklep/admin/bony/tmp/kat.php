<?
	$LIST[id]=$CIACHO[admin_to_id];

	$to_id = $LIST[id];

	if (!strlen($to_id)) return;
	
	function towar_select_options($adodb,$page,$id="",$tab="")
	{
		global $projdb;
		global $SKLEP_SESSION;
		static $K;

	
		if (!$id) $K=$SKLEP_SESSION["KAT"];

		$ID=$id?"=$id":" IS NULL";
		$query="SELECT * FROM kategorie WHERE ka_parent$ID ORDER BY ka_nazwa";
		$result=$adodb->Execute($query);
		for ($i=0;$i<$result->RecordCount();$i++)
		{	
			parse_str(ado_ExplodeName($result,$i));

			if (!is_array($K[$ka_id]))
			{
				$query="SELECT count(*) AS c FROM kategorie WHERE ka_parent=$ka_id";
				parse_str(ado_query2url($query));
				$query="SELECT count(*) AS t FROM towar_kategoria WHERE tk_ka_id=$ka_id";
				parse_str(ado_query2url($query));

				$K[$ka_id][t]=$t;
				$K[$ka_id][c]=$c;
				
			}
			else
			{
				$c=$K[$ka_id][c];
				$t=$K[$ka_id][t];
			}

			//$style=$t?"color:#000000":"color:#a0a0a0";
			$value=$t?$ka_id:0;
			$value=$ka_id;
			//$sel=("$page"==$ka_kod)?" selected":"";

			if ($t) $ka_nazwa.=" ($t szt)";
			if (strlen($ka_kod)) $ka_kod=" ....... ".sysmsg("page","admin")." $ka_kod";
			//if (!$t && !$c) $ka_kod.=" (id=$ka_id)"; 
			$wynik.="\n<option$sel value=\"$value\" style=\"$style\">$tab$ka_nazwa $ka_kod</option>";
			
			if ($c)
				$wynik.=towar_select_options($adodb,$page,$ka_id,$tab."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		}
		

		if (!$id) 
		{
			$SKLEP_SESSION["KAT"]=$K;
			$KAT=$K;
			@session_register("KAT");// co ciekawe - bez tego tego sesyja nie działa :(
		}		
		return $wynik;
	}

	$options=towar_select_options($projdb,"");
	$options = "<option value=\"\">".sysmsg("Choose from list","admin")."</option>$options";

	if (!function_exists("getFullPath"))
	{
		function getFullPath($id)
		{		
			$sql = "SELECT ka_nazwa, ka_parent FROM kategorie WHERE ka_id = $id";
			parse_str(ado_query2url($sql));
			if ($ka_parent)
				return "$ka_nazwa;".getFullPath($ka_parent);
			else
				return "$ka_nazwa";
		}
	}

	$sql = "SELECT tk_id, tk_ka_id FROM towar_kategoria WHERE tk_to_id = $to_id";
	$res = $adodb->execute($sql);
	

	$katform = "
	<thead>
	<TR>
		<Th class=\"c2\" colspan=\"2\">".sysmsg("Categories","admin")."</Th>
	</TR>	
	</thead>
	<tbody>
	";
	

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$path = getFullPath($tk_ka_id);
		$path = explode(";",$path);
		$path = array_reverse($path);
		$path = implode("->",$path); 		
		$katform.="	
		<TR>
			<Td class=\"c2\">$path</Td>
			<Td class=\"c4\"><img src=\"$SKLEP_IMAGES/del.gif\" onClick=\"usunKat('$tk_id')\" style=\"cursor:hand\"></Td>
		</TR>";
				
	}

	$sql = "SELECT * FROM producent ORDER BY pr_nazwa";
	$res = $projdb->execute($sql);

	$prod = "<option value=\"\">Brak producenta</option>";
	$query = "SELECT to_pr_id FROM towar WHERE to_id = $to_id";
	parse_str(ado_query2url($query));
	$sel[$to_pr_id] = "selected";
	$prod_count = $res->RecordCount();
	for ($i = 0; $i < $prod_count; $i++)
	{
		parse_str(ado_explodename($res,$i));
		$prod.= "<option value=\"$pr_id\" ".$sel[$pr_id].">".stripslashes(htmlspecialchars($pr_nazwa))."</option>\n";
	}

	$producent = "
	<SELECT NAME=\"form[producent]\">
	$prod
	</SELECT>
	";
	
	$prodform = "
	<FORM METHOD=POST ACTION=\"$self\" name=\"ProdForm\">	
	<INPUT TYPE=\"hidden\" NAME=\"action\" value=\"TowarDodajProd\">
	<INPUT TYPE=\"hidden\" NAME=\"form[towar]\" value=\"$to_id\">
	<INPUT TYPE=\"hidden\" NAME=\"list[id]\" value=\"$to_id\">
	<TR>
		<Th class=\"c2\" colspan=\"2\">".sysmsg("Producer","admin")."</Th>
	</TR>	
	<TR>
		<Td class=\"c2\">$producent</Td>
		<Td class=\"c4\"><img src=\"$SKLEP_IMAGES/save.gif\" onClick=\"submit()\" style=\"cursor:hand\"></Td>
	</TR>	
	</tbody>	
	</FORM>";
	
	if (!$prod_count)
			$prodform = "";

	$katform .= "
	<TR>
		<Td class=\"c2\"><SELECT NAME=\"form[nowa_kategoria]\">$options</SELECT></Td>
		<Td class=\"c4\"><img src=\"$SKLEP_IMAGES/save.gif\" onClick=\"submit()\" style=\"cursor:hand\"></Td>
	</TR>	
	";

	echo "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KategoriaTowarZapisz\">
	<INPUT TYPE=\"hidden\" name=\"list[id]\" value=\"$to_id\">
	$katform
	</form>
	$prodform
	</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\" name=\"deleteKat\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KategoriaTowarUsun\">
	<INPUT TYPE=\"hidden\" name=\"list[id]\" value=\"$to_id\">
	<INPUT TYPE=\"hidden\" name=\"list[killKat]\" id=\"killKat\" value=\"\">
	</form>
	";

?>
<script>
	function usunKat(id)
	{
		if (confirm('Na pewno usunąć towar z podanej kategorii ?'))
		{
			document.deleteKat.killKat.value = id;
			document.deleteKat.submit();
		}
	}

</script>
