<?
	$KATEGORIA = $CIACHO[admin_ka_id];

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
				parse_str(ado_query2url($query));

				$K[$ka_id][c]=$c;
				
			}
			else
				$c=$K[$ka_id][c];

			$t="";
			
			$style="color:#000000";
			$value=$t?$ka_id:0;
			$sel=($selid==$ka_id)?" selected":"";

			if (!$t && !$c) $ka_kod.=" (id=$ka_id)"; 
	
			if ($ka_id != $userid)
				$wynik.="\n<option$sel value=\"$ka_id\" style=\"$style\">$tab$ka_nazwa</option>";

			if ($c && ($ka_id != $userid))
				$wynik.=towar_select_options($adodb,$page,$ka_id,$tab."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$selid,$userid);
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

	$options=towar_select_options($projdb,"","","",$ka_parent,$KATEGORIA);

	$kategorie = "
	<select name=\"form[parent_id]\" class=\"formselect\" style=\"width:380px\">
	<option value=\"\">Brak</option>
	$options
	</select>";
//	<option value=\"0\">Przyznane rabaty</option>

	$foto_add = "
		<TR>
			<TD class=\"c2\">Foto ma³e:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto NAME=\"form[ka_foto_m]\" value=\"$ka_foto_m\" style=\"width:380px\">
					<img src=$SKLEP_IMAGES/sb/i_image_n.gif align=absmiddle 
						onClick=\"galeria_input='foto';kartoteka_popup('$next${next_char}form[img]='+document.all[galeria_input].value,'galeria')\" 
						style=\"cursor:hand;\" 
						onmouseover=\"this.src='$SKLEP_IMAGES/sb/i_image_a.gif'\" 
						onmouseout=\"this.src='$SKLEP_IMAGES/sb/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>
		<TR>
			<TD class=\"c2\">Foto du¿e:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto2 NAME=\"form[ka_foto_d]\" value=\"$ka_foto_d\" style=\"width:380px\">
					<img src=$SKLEP_IMAGES/sb/i_image_n.gif align=absmiddle 
						onClick=\"galeria_input='foto2';kartoteka_popup('$next${next_char}form[img]='+document.all[galeria_input].value,'galeria')\" style=\"cursor:hand;\" 
						onmouseover=\"this.src='$SKLEP_IMAGES/sb/i_image_a.gif'\" 
						onmouseout=\"this.src='$SKLEP_IMAGES/sb/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>";

	eval("\$opis=stripslashes(\$ka_opis_m_$lang);");

	$table = "
	<table class=\"list_table\" cellspacing=0 cellpadding=0 border=0 width=80%>
	<TR>
		<Td class=\"c2\">Nazwa</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[ka_nazwa]\" value=\"$ka_nazwa\"></Td>
	</TR>
	<TR>
		<Td class=\"c2\">Kategoria nadrzêdna</Td>
		<Td class=\"c4\">$kategorie</Td>
	</TR>	
	$foto_add
	<TR>
		<TD class=\"c4\" colspan=2>Krótki opis:<br>
		<textarea NAME=\"form[ka_opis_m_$lang]\" style=\"width:100%; height:60px\">$opis</textarea></TD>
	</TR>
	<TR>
		<TD class=\"c4\" colspan=2>
		<INPUT TYPE=\"submit\" value=\"Zapisz\">
		</TD>
	</TR>
	</table>
	";

	echo "
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszKategorie\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"$ka_id\">
	$table
	</FORM>";

?>
<script>

var galeria_input="";

</script>
