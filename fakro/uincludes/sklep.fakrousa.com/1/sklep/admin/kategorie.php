<?
	global $KATEGORIA,$FROM;

	$minimum = $FORM[minimum];
	$procent = $FORM[procent];
	$rabat = $FORM[rabat_id];

	if (strlen($minimum) && strlen($procent))
	{
		$minimum = ereg_replace(",","\.",$minimum);
		$minimum = ereg_replace("[^0-9\.-]","",$minimum);
		$procent = ereg_replace(",","\.",$procent);
		$procent = ereg_replace("[^0-9\.-]","",$procent);

		if (strlen($rabat))
			$sql = "UPDATE rabat_ilosciowy SET 
					ri_minmum = $minimum,
					ri_procent = $procent
					WHERE ri_id = $rabat";
		else
			$sql = "INSERT INTO rabat_ilosciowy (ri_sk_id,ri_ka_id,ri_minmum, ri_procent)
					VALUES ($SKLEP_ID, $KATEGORIA,$minimum,$procent)";
		$adodb->execute($sql);
		
	}

//	if ($WEBTD->page_id==$page) return;
	function towar_select_options($adodb,$page,$id="",$tab="",$selid="")
	{
		global $projdb;
		global $SKLEP_SESSION;
		static $K;

	
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

			$ka_nazwa.=" (grup rabatowych: $t)";
			//if (strlen($ka_kod)) $ka_kod=" ....... strona $ka_kod";
			if (!$t && !$c) $ka_kod.=" (id=$ka_id)"; 
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

	//$ka_kod = $page;
	$sql = "SELECT * FROM kategorie WHERE ka_id = $KATEGORIA";
	if (strlen($KATEGORIA)) parse_str(ado_query2url($sql));

	$options=towar_select_options($projdb,"","","",$KATEGORIA);

?>
<form method="post" action="<?echo $self?>" >
<p>
<select name="KATEGORIA" class="formselect" onChange="submit()">
<option value="0">Wybierz kategorie</option>
<? echo $options ?>
</select>
</p>
</form>
<?
	if (!strlen($KATEGORIA) || !$KATEGORIA) return;

	$sql = "SELECT * FROM kategorie WHERE ka_id = $KATEGORIA";
	parse_str(ado_query2url($sql));

	$sql = "SELECT sk_id, sk_nazwa FROM sklep ORDER BY sk_nazwa";
	$res = $adodb->execute($sql);
	$opcje = "";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$opcje.= "<option value=\"$sk_id\">$sk_nazwa</option>\n";
	}
	
	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"nowaCena\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"CenaKategoriaZapisz\">
	<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>
		<Th></Th>
		<Th>Cena</Th>
		<Th colspan=\"2\"></Th>
	</TR>
	</thead>
	<tbody>
		<TR>
			<Td></Td>
			<Td><INPUT TYPE=\"text\" NAME=\"form[cena]\"></Td>
			<Td colspan=\"2\"><img src=\"$UIMAGES/autoryzacja/i_save_n.gif\" style=\"cursor:hand\" onClick=\"zapiszCene()\"></Td>
		</TR>
		<TR>
			<Td colspan=\"2\" align=\"right\">Sklep:</td>
			<Td colspan=\"2\" align=\"left\"><SELECT NAME=\"form[sklep]\">$opcje</SELECT></td>
		</TR>
	</tbody>
	</FORM>
	<thead>
	<TR>
		<Th colspan=\"2\">Nazwa</Th>
		<Th colspan=\"2\">Kod</Th>
	</TR>
	</thead>
	<tbody>
	<TR>
		<Td colspan=\"2\">$ka_nazwa</Td>
		<Td colspan=\"2\">$ka_kod</Td>
	</TR>
	</tbody>
	<TR>
		<Th colspan=\"4\">Rabaty ilo¶ciowe</Th>
	</TR>
	<TR>
		<th>Lp.</th>
		<th>Minimum</th>
		<th>Procent ceny</th>
		<th></th>
	</TR>
	";
	
	$sql = "SELECT * FROM rabat_ilosciowy 
			WHERE ri_ka_id = $KATEGORIA
			AND ri_sk_id = $SKLEP_ID ORDER BY ri_minmum";

	$res = $adodb->execute($sql);
	$add = "";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$add.= "
		<TR>
			<FORM METHOD=POST ACTION=\"$self\">
			<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
			<INPUT TYPE=\"hidden\" name=\"form[rabat_id]\" value=\"$ri_id\">
			<Td>".($i+1)."</Td>
			<Td><INPUT TYPE=\"text\" NAME=\"form[minimum]\" value=\"$ri_minmum\"></Td>
			<Td><INPUT TYPE=\"text\" NAME=\"form[procent]\" value=\"$ri_procent\">%</Td>
			<Td><img src=\"$UIMAGES/autoryzacja/i_save_n.gif\" style=\"cursor:hand\" onClick=\"submit()\">
			<img src=\"$UIMAGES/autoryzacja/i_delete_n.gif\" style=\"cursor:hand\" onClick=\"usunRabat('$ri_id')\">			
			</Td>
			</FORM>
		</TR>
		";
	}

	echo "
	$add
	<TR>
		<FORM METHOD=POST ACTION=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
		<Td>".($i+1)."</Td>
		<Td><INPUT TYPE=\"text\" NAME=\"form[minimum]\"></Td>
		<Td><INPUT TYPE=\"text\" NAME=\"form[procent]\">%</Td>
		<Td><img src=\"$UIMAGES/autoryzacja/i_save_n.gif\" style=\"cursor:hand\" onClick=\"submit()\"></Td>
		</FORM>
	</TR>
	</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killRabat\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KategoriaRabatIloscUsun\">
	<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
	<INPUT TYPE=\"hidden\" id=\"killRabatId\" name=\"form[killRabatId]\">
	</FORM>
	";
?>
<script>
	
	function usunRabat(id)
	{
		if (confirm('Na pewno usun±æ ten rabat ?'))
		{
			document.killRabat.killRabatId.value = id;
			document.killRabat.submit();
		}
	}

	function zapiszCene()
	{
		if (confirm('<? echo sysmsg('Sure to rewrite this price to all items within this category','admin')?> ?'))
			document.nowaCena.submit();

	}
</script>
