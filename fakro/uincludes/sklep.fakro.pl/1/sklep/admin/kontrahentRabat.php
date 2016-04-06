<?
	$LIST[id]=$CIACHO[admin_su_id];

	global $KATEGORIA,$FROM;

	$minimum = $FORM[minimum];
	$procent = $FORM[procent];
	$rabat = $FORM[rabat_id];

	if (strlen($procent) && strlen($LIST[id]))
	{
		$procent = ereg_replace(",",".",$procent);
		$procent = ereg_replace("[^0-9\.-]","",$procent);
		if (strlen($rabat))
			$sql = "UPDATE rabat_kontrahenta SET 
					rk_procent = $procent
					WHERE rk_id = $rabat";
		else
		{

			$sql = "SELECT ks_id AS ksid FROM kontrahent_sklep WHERE 
					ks_sk_id = $SKLEP_ID AND ks_su_id = ".$LIST[id];
			parse_str(ado_query2url($sql));

			$sql = "INSERT INTO rabat_kontrahenta (rk_ks_id,rk_ka_id,rk_procent)
					VALUES ($ksid, $KATEGORIA, $procent)";
		}
		$adodb->execute($sql);
	}

//	if ($WEBTD->page_id==$page) return;
	function towar_select_options($adodb,$page,$id="",$tab="",$selid="",$userid="")
	{
		global $projdb;
		global $SKLEP_SESSION, $SKLEP_ID;
		static $K;

	
		//if (!$id) $K=$SKLEP_SESSION["KAT"];

		$ID=$id?"=$id":" IS NULL";
		$query="SELECT * FROM kategorie WHERE ka_parent$ID ORDER BY ka_nazwa";
		$result=$adodb->Execute($query);
		for ($i=0;$i<$result->RecordCount();$i++)
		{	
			parse_str(ado_ExplodeName($result,$i));
			
			if (!is_array($K[$ka_id]))
			{
				$query="SELECT count(*) AS c FROM kategorie WHERE ka_parent=$ka_id";
				parse_str(ado_query2url($query,true));

				$K[$ka_id][c]=$c;
				
			}
			else
				$c=$K[$ka_id][c];

			$t="";
			$query="SELECT rk_procent AS t FROM rabat_kontrahenta, kontrahent_sklep 
					WHERE rk_ka_id = $ka_id
					AND rk_ks_id = ks_id
					AND ks_sk_id = $SKLEP_ID
					AND ks_su_id = $userid LIMIT 1";

			parse_str(ado_query2url($query,true));

			$style="color:#000000";
			$value=$t?$ka_id:0;
			$sel=($selid==$ka_id)?" selected":"";

			if (strlen($t))
			{
				$tt=100-$t;
				$ka_nazwa.=" (przyznany rabat: $tt%)";
			}

			//if (strlen($ka_kod)) $ka_kod=" ....... strona $ka_kod";
			if (!$t && !$c) $ka_kod.=" (id=$ka_id)"; 
			$wynik.="\n<option$sel value=\"$ka_id\" style=\"$style\">$tab$ka_nazwa</option>";
			
			if ($c)
				$wynik.=towar_select_options($adodb,$page,$ka_id,$tab."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$selid,$userid);
		}
		

		if (!$id) 
		{
			//$SKLEP_SESSION["KAT"]=$K;
			$KAT=$K;
			//session_register("KAT");// co ciekawe - bez tego tego sesyja nie działa :(
		}		
		return $wynik;
	}

	//$ka_kod = $page;
	$sql = "SELECT * FROM kategorie WHERE ka_id = $KATEGORIA";
	if (strlen($KATEGORIA)) parse_str(ado_query2url($sql));

	$options=towar_select_options($projdb,"","","",$KATEGORIA,$LIST[id]);

?>
<form method="post" action="<?echo $self?>" >
<INPUT TYPE="hidden" name="list[id]" value="<? echo $LIST[id] ?>">
<p>
<select name="KATEGORIA" class="formselect" onChange="submit()">
<option value="0">Przyznane rabaty</option>
<? echo $options ?>
</select>
</p>
</form>
<?
	if (!strlen($KATEGORIA) || !$KATEGORIA) return;

	$sql = "SELECT * FROM kategorie WHERE ka_id = $KATEGORIA";
	parse_str(ado_query2url($sql));
	$s_path = explode(";",getFullPath($KATEGORIA));
	$s_path = array_reverse($s_path);
	$s_path = implode("->",$s_path);
	echo "
	<table class=\"list_table\" cellspacing=0 cellpadding=0 border=0 width=80%>
	<TR>
		<Th colspan=\"2\">Nazwa</Th>
	</TR>
	<TR>
		<Td colspan=\"2\">$s_path</Td>
	</TR>
	<TR>
		<th>Procent ceny</th>
		<th>&nbsp;</th>
	</TR>
	";
	
	$sql = "SELECT rk_id, rk_procent, rk_ks_id FROM rabat_kontrahenta, kontrahent_sklep 
			WHERE rk_ka_id = $KATEGORIA
			AND rk_ks_id = ks_id
			AND ks_sk_id = $SKLEP_ID
			AND ks_su_id = ".$LIST[id]." LIMIT 1";

	parse_str(ado_query2url($sql));

	$add = "
	<TR>
		<FORM METHOD=POST ACTION=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
		<INPUT TYPE=\"hidden\" name=\"form[rabat_id]\" value=\"$rk_id\">
		<INPUT TYPE=\"hidden\" name=\"list[id]\" value=\"$LIST[id]\">
		<Td><INPUT TYPE=\"text\" NAME=\"form[procent]\" value=\"$rk_procent\">%</Td>
		<Td><img src=\"$SKLEP_IMAGES/save.gif\" style=\"cursor:hand\" onClick=\"submit()\">
		<img src=\"$SKLEP_IMAGES/del.gif\" style=\"cursor:hand\" onClick=\"usunRabat('$rk_id')\">			
		</Td>
		</FORM>
	</TR>
	";

	echo "$add
	</table>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killRabat\">
	<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
	<INPUT TYPE=\"hidden\" name=\"list[id]\" value=\"$LIST[id]\">
	<INPUT TYPE=\"hidden\" value=\"KontrahentRabatUsun\" name=\"action\">
	<INPUT TYPE=\"hidden\" id=\"killRabatId\" name=\"form[killRabatId]\">
	</FORM>
	";
?>
<script>
	
	function usunRabat(id)
	{
		if (confirm('Na pewno usunąć ten rabat ?'))
		{
			document.killRabat.killRabatId.value = id;
			document.killRabat.submit();
		}
	}

</script>
