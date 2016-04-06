<?
	global $KATEGORIA;
	if ($WEBTD->page_id==$page) return;

	if (!$SKLEP_ID)
	{
		global $SERVER_NAME;
		$query="INSERT INTO sklep (sk_nazwa,sk_server) VALUES ('$SERVER_NAME',$SERVER_ID);";
		$projdb->Execute($query);
	}


	if ($KATEGORIA && $SKLEP_ID)
	{
		//$projdb->debug=1;

		$query="UPDATE kategorie SET ka_kod='' WHERE ka_kod='$page';
			UPDATE kategorie SET ka_kod='$page' WHERE ka_id=$KATEGORIA";
		$projdb->Execute($query);
		
		$query="INSERT INTO towar_sklep (ts_to_id,ts_sk_id)
				SELECT to_id,$SKLEP_ID FROM towar,towar_kategoria 
					WHERE to_id=tk_to_id AND tk_ka_id=$KATEGORIA
					AND to_id NOT IN (SELECT ts_to_id FROM towar_sklep WHERE ts_sk_id=$SKLEP_ID);";
		$projdb->Execute($query);

	}

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

			$style=$t?"color:#000000":"color:#a0a0a0";
			$value=$t?$ka_id:0;
			$sel=("$page"==$ka_kod)?" selected":"";

			if ($t) $ka_nazwa.=" ($t szt)";
			if (strlen($ka_kod)) $ka_kod=" ....... strona $ka_kod";
			if (!$t && !$c) $ka_kod.=" (id=$ka_id)"; 
			$wynik.="\n<option$sel value=\"$value\" style=\"$style\">$tab$ka_nazwa $ka_kod</option>";
			
			if ($c)
				$wynik.=towar_select_options($adodb,$page,$ka_id,$tab."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		}
		

		if (!$id) 
		{
			$SKLEP_SESSION["KAT"]=$K;
			$KAT=$K;
			session_register("KAT");// co ciekawe - bez tego tego sesyja nie dziaГa :(
		}		
		return $wynik;
	}

	$options=towar_select_options($projdb,$page);


?>
<form method="post" action="<?echo $self?>" >
<p align=right>
<select name="KATEGORIA" class="formselect" >
<option value="0">Wybierz kategorie</option>
<? echo $options ?>
</select>
<input type="submit" value="Zapisz" class="formbutton">
</p>
</form>
<SCRIPT LANGUAGE="JScript.Encode" src="jsencode/tdedit.enc"></SCRIPT>
<?

	$sql = "SELECT ka_id, ka_foto_m, ka_foto_d 
			FROM kategorie WHERE ka_kod = '$page'";

	parse_str(ado_query2url($sql));

	if (!strlen($ka_id)) return;

	if ($FORM[foto_update])
	{
		$sql = "UPDATE kategorie SET
				ka_foto_m = '".$FORM[foto_mini]."',
				ka_foto_d = '".$FORM[foto]."'
				WHERE ka_id = ".$FORM[foto_update];
		$adodb->execute($sql);
		$ka_foto_m = $FORM[foto_mini];
		$ka_foto_d = $FORM[foto];
	}

	echo "
		<FORM METHOD=POST ACTION=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"form[foto_update]\" value=\"$ka_id\">
		<TABLE>
		<TR>
				<TD class=\"tl\">Zdjъcie duПe</TD>
				<TD class=\"ti\"><INPUT TYPE=\"text\" id=\"foto\" NAME=\"form[foto]\" value=\"$ka_foto_d\">
					<img src=img/i_image_n.gif align=absmiddle 
						onClick=\"openGalery('foto','ufiles.php?page=$page&galeria=2&seteditmode=1')\" style=\"cursor:hand;\" 
						onmouseover=\"this.src='img/i_image_a.gif'\" 
						onmouseout=\"this.src='img/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>
				</TD>
		</TR>
		<TR>
				<TD class=\"tl\">Zdjъcie maГe</TD>
				<TD class=\"ti\"><INPUT TYPE=\"text\" id=\"foto_mini\" NAME=\"form[foto_mini]\" value=\"$ka_foto_m\">
					<img src=img/i_image_n.gif align=absmiddle 
						onClick=\"openGalery('foto_mini','ufiles.php?page=$page&galeria=2&seteditmode=1')\" style=\"cursor:hand;\" 
						onmouseover=\"this.src='img/i_image_a.gif'\" 
						onmouseout=\"this.src='img/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>
				</TD>
		</TR>
		</TABLE>
		<P align=\"right\"><input type=\"submit\" value=\"Zapisz\" class=\"formbutton\">
		</FORM>	";
?>
