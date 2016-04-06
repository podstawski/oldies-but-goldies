<?
	$to_id = $LIST[id];
	$killId = $FORM[killKat];

	if (!strlen($to_id)) return;

	if (strlen($killId))
	{
		$sql = "DELETE FROM towar_kategoria WHERE tk_id = $killId";
		$adodb->execute($sql);
	}
	
	$sql = "SELECT towar_sklep.*, sk_nazwa,sk_id FROM towar_sklep,sklep WHERE ts_to_id = $to_id";
	$res = $adodb->execute($sql);

	$table = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<tr>
		<th>Sklep</th>
		<th>Kwant zamówienia</th>
		<th>Czas pobytu w koszyku</th>
		<th>Cena</th>
	</tr>
	</thead>
	<tbody>
	";
	$lst = "";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$lst.= ";$ts_id";
		$table.= "
		<tr>
			<td class=\"c2\">$sk_nazwa</td>
			<td class=\"c2\"><INPUT TYPE=\"text\" NAME=\"SKLEP_KW[$ts_id]\" value=\"$ts_kwant_zam\" style=\"width:100px\"></td>
			<td class=\"c2\"><INPUT TYPE=\"text\" NAME=\"SKLEP_CZ[$ts_id]\" value=\"$ts_czas_koszyk\" style=\"width:100px\"> sek.</td>
			<td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"SKLEP_CE[$ts_id]\" value=\"$ts_cena\" style=\"width:100px\"></td>
		</tr>
		";
	}
	$lst = substr($lst,1);

	$table.= "</tbody>
	<tfoot>
	<tr>
		<td class=\"c2\" colspan=\"2\"><INPUT TYPE=\"button\" value=\"Anuluj\" onClick=\"document.gobackform.submit()\"></td>
		<td class=\"c4\" colspan=\"2\"><INPUT TYPE=\"submit\" value=\"Zapisz\"></td>
	</tr>
	</tfoot></TABLE>
	";

	function towar_select_options($adodb,$page,$id="",$tab="",$selid="")
	{
		global $projdb;
		global $SKLEP_SESSION;
		static $K,$first;

	
		//if (!$id) $K=$SKLEP_SESSION["KAT"];

		$ID=$id?"=$id":" IS NULL";
		$query="SELECT * FROM kategorie WHERE ka_parent$ID ORDER BY ka_nazwa";
		$result=$adodb->Execute($query);
		for ($i=0;$i<$result->RecordCount();$i++)
		{	
			parse_str(ado_ExplodeName($result,$i));
			$t = 0;
			if (!is_array($K[$ka_id]))
			{
				$query="SELECT count(*) AS c FROM kategorie WHERE ka_parent=$ka_id";
				parse_str(ado_query2url($query));
				$query="SELECT count(*) AS t FROM rabat_ilosciowy WHERE ri_ka_id = $ka_id";
				parse_str(ado_query2url($query));

				$K[$ka_id][t]=$t;
				$K[$ka_id][c]=$c;
				
			}
			else
			{
				$c=$K[$ka_id][c];
				$t=$K[$ka_id][t];
			}

			$query="SELECT count(*) AS t FROM rabat_ilosciowy WHERE ri_ka_id = $id AND ri_sk_id = $SKLEP_ID";
			if (strlen($id)) parse_str(ado_query2url($query));

			$style="color:#000000";
			$value=$t?$ka_id:0;
			$sel=($selid==$ka_id)?" selected":"";
			if (!$t && !$c) $ka_kod.=" (id=$ka_id)"; 
			if (!$first) 
				$ka_nazwa = "Wybierz z listy";
			$first = 1;
			$wynik.="\n<option$sel value=\"$ka_id\" style=\"$style\">$tab$ka_nazwa</option>";
			
			if ($c)
				$wynik.=towar_select_options($adodb,$page,$ka_id,$tab."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$selid);
		}
		

		if (!$id) 
		{
			//$SKLEP_SESSION["KAT"]=$K;
			$KAT=$K;
			//session_register("KAT");// co ciekawe - bez tego tego sesyja nie dzia³a :(
		}		
		return $wynik;
	}


	$options=towar_select_options($projdb,"");

	$sql = "SELECT tk_id, tk_ka_id FROM towar_kategoria WHERE tk_to_id = $to_id";
	$res = $adodb->execute($sql);
	

	$katform = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>
		<Th>¦cie¿ka</Th>
		<Th></Th>
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
			<Td class=\"c4\"><img src=\"$UIMAGES/autoryzacja/i_delete_n.gif\" onClick=\"usunKat('$tk_id')\" style=\"cursor:hand\"></Td>
		</TR>";
				
	}

	$katform .= "
	<TR>
		<Td class=\"c2\"><SELECT NAME=\"FORM[nowa_kategoria]\">$options</SELECT></Td>
		<Td class=\"c4\"><img src=\"$UIMAGES/autoryzacja/i_save_n.gif\" onClick=\"submit()\" style=\"cursor:hand\"></Td>
	</TR>	
	</tbody></TABLE>";

	echo "
	<FORM METHOD=POST ACTION=\"$next\" method=\"POST\" name=\"gobackform\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	".sort_navi_options($LIST)."
	</form>
	<FORM METHOD=POST ACTION=\"$next\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"SklepTowarZapisz\">
	<INPUT TYPE=\"hidden\" name=\"SKLEPY\" value=\"$lst\">
	$table
	".sort_navi_options($LIST)."
	</FORM>	
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KategoriaTowarZapisz\">
	<INPUT TYPE=\"hidden\" name=\"FORM[id]\" value=\"$to_id\">
	$katform
	".sort_navi_options($LIST)."
	</form>
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\" name=\"deleteKat\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"list[id]\" value=\"$to_id\">
	<INPUT TYPE=\"hidden\" name=\"form[killKat]\" id=\"killKat\" value=\"\">
	".sort_navi_options($LIST)."
	</form>
	";

?>
<script>
	function usunKat(id)
	{
		if (confirm('Na pewno usun±æ towar z podanej kategorii ?'))
		{
			document.deleteKat.killKat.value = id;
			document.deleteKat.submit();
		}
	}

</script>
