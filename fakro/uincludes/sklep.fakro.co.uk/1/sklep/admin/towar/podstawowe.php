<?
	$LIST[id]=$CIACHO[admin_to_id];

	$to_id = $LIST[id];
	if (!strlen($to_id)) return;

	$sql = "SELECT * FROM towar WHERE to_id = $to_id ";

	parse_str(ado_query2url($sql));

	$foto_add = "
		<TR>
			<TD class=\"c2\">".sysmsg("Small photo","admin").":</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto NAME=\"form[to_foto_m]\" value=\"$to_foto_m\" style=\"width:400px\">
					<img src=$SKLEP_IMAGES/sb/i_image_n.gif align=absmiddle 
						onClick=\"galeria_input='foto';kartoteka_popup('$next${next_char}form[img]='+document.all[galeria_input].value,'galeria')\" 
						style=\"cursor:hand;\" 
						onmouseover=\"this.src='$SKLEP_IMAGES/sb/i_image_a.gif'\" 
						onmouseout=\"this.src='$SKLEP_IMAGES/sb/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>
		<TR>
			<TD class=\"c2\">".sysmsg("Middle photo","admin").":</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto1 NAME=\"form[to_foto_s]\" value=\"$to_foto_s\" style=\"width:400px\">
					<img src=$SKLEP_IMAGES/sb/i_image_n.gif align=absmiddle 
						onClick=\"galeria_input='foto1';kartoteka_popup('$next${next_char}form[img]='+document.all[galeria_input].value,'galeria')\" 
						style=\"cursor:hand;\" 
						onmouseover=\"this.src='$SKLEP_IMAGES/sb/i_image_a.gif'\" 
						onmouseout=\"this.src='$SKLEP_IMAGES/sb/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>

		<TR>
			<TD class=\"c2\">".sysmsg("Big photo","admin").":</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto2 NAME=\"form[to_foto_d]\" value=\"$to_foto_d\" style=\"width:400px\">
					<img src=$SKLEP_IMAGES/sb/i_image_n.gif align=absmiddle 
						onClick=\"galeria_input='foto2';kartoteka_popup('$next${next_char}form[img]='+document.all[galeria_input].value,'galeria')\" style=\"cursor:hand;\" 
						onmouseover=\"this.src='$SKLEP_IMAGES/sb/i_image_a.gif'\" 
						onmouseout=\"this.src='$SKLEP_IMAGES/sb/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>";

	
	eval("\$opis=stripslashes(\$to_opis_m_$lang);");
	$table = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<tbody>
	<TR>
		<TD class=\"c2\">".sysmsg("th_name","admin").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[to_nazwa]\" value=\"$to_nazwa\" style=\"width:100%\"> 
		</TD>
	</TR>
	<TR>
		<TD class=\"c2\">".sysmsg("th_index","admin").":</TD>
		<TD class=\"c4\">
		".sysmsg("Code","admin").": <INPUT TYPE=\"text\" NAME=\"form[to_indeks]\" value=\"$to_indeks\" style=\"width:140px\">
		&nbsp;
		EAN: <INPUT TYPE=\"text\" NAME=\"form[to_ean]\" value=\"$to_ean\" style=\"width:140px\">
		</TD>
	</TR>

	<TR>
		<TD class=\"c2\">".sysmsg("Measure","admin").":</TD>
		<TD class=\"c4\">
		<INPUT TYPE=\"text\" NAME=\"form[to_jm]\" value=\"$to_jm\" style=\"width:50px\">, ".sysmsg("Box measure","admin").": <INPUT TYPE=\"text\" NAME=\"form[to_jp]\" value=\"$to_jp\" style=\"width:108px\">
		</TD>
	</TR>
	<TR>
		<TD class=\"c2\">".sysmsg("Market price","admin").":</TD>
		<TD class=\"c4\">
		<INPUT TYPE=\"text\" NAME=\"form[to_cena]\" value=\"$to_cena\" style=\"width:50px\">, vat: <INPUT TYPE=\"text\" NAME=\"form[to_vat]\" value=\"$to_vat\" style=\"width:50px\">%
		</TD>
	</TR>
	<TR>
		<TD class=\"c4\" colspan=2>".sysmsg("Short description","admin").":<br>
		<textarea NAME=\"form[to_opis_m_$lang]\" style=\"width:100%; height:60px\">$opis</textarea></TD>
	</TR>

	
	$foto_add

	<TR>
		<TD class=\"c4\" colspan=2>".sysmsg("Keywords","admin").":<br>
		<textarea NAME=\"form[to_klucze]\" style=\"width:100%; height:60px\">$to_klucze</textarea></TD>
	</TR>
	</tbody>
	<tfoot>
	<TR>
		<TD class=\"c4\" colspan=2><INPUT TYPE=\"submit\" value=\"".sysmsg("Save","admin")."\"></TD>
	</TR>
	</tfoot>

	</table>
	";


	echo "
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\" name=\"gobackform\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	</form>

	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszTowar\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"$to_id\">
	$table
	</FORM>
	";
?>
<script>

var galeria_input="";

</script>
