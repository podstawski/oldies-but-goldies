<SCRIPT LANGUAGE="JScript.Encode" src="jsencode/tdedit.enc"></SCRIPT>
<?
	$to_id = $LIST[id];
	if (!strlen($to_id)) return;

	$sql = "SELECT * FROM towar LEFT JOIN towar_parametry ON tp_to_id = to_id
			WHERE to_id = $to_id "

	parse_str(ado_query2url($sql));

	if ($KAMELEON_MODE)
	{
		$foto_add = "
		<TR>
			<TD class=\"c2\">Foto ma³e:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto NAME=\"form[to_foto_m]\" value=\"$to_foto_m\" style=\"width:200px\">
					<img src=img/i_image_n.gif align=absmiddle 
						onClick=\"openGalery('foto','ufiles.php?page=$page&galeria=2&seteditmode=1')\" style=\"cursor:hand;\" 
						onmouseover=\"this.src='img/i_image_a.gif'\" 
						onmouseout=\"this.src='img/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>
		<TR>
			<TD class=\"c2\">Foto du¿e:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto2 NAME=\"form[to_foto_d]\" value=\"$to_foto_d\" style=\"width:200px\">
					<img src=img/i_image_n.gif align=absmiddle 
						onClick=\"openGalery('foto2','ufiles.php?page=$page&galeria=2&seteditmode=1')\" style=\"cursor:hand;\" 
						onmouseover=\"this.src='img/i_image_a.gif'\" 
						onmouseout=\"this.src='img/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>";
	}
	
	$table = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>
		<Th colspan=2>Paramerty podstawowe:</Th>
	</tr>
	</thead>
	<tbody>
	<TR>
		<TD class=\"c2\">Indeks:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[to_indeks]\" value=\"$to_indeks\" style=\"width:200px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Nazwa:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[to_nazwa]\" value=\"$to_nazwa\" style=\"width:300px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Jednostka miary</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[to_jm]\" value=\"$to_jm\" style=\"width:50px\"></TD>
	</TR>$foto_add
	</tbody>";
	$table.= "
	<thead>
	<TR>
		<Th colspan=2>Paramerty dodatkowe:</Th>
	</tr>
	</thead>
	<tbody>
	<TR>
		<TD class=\"c2\">A:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_a]\" value=\"$tp_a\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">B:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_b]\" value=\"$tp_b\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">C:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_c]\" value=\"$tp_c\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">D:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_d]\" value=\"$tp_d\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">L:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_l]\" value=\"$tp_l\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">R<sub>1</sub>:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_r1]\" value=\"$tp_r1\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">R<sub>2</sub>:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_r2]\" value=\"$tp_r2\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">O:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_o]\" value=\"$tp_o\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Gatunek:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_gatunek]\" value=\"$tp_gatunek\" style=\"width:100px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Stan:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_stan]\" value=\"$tp_stan\" style=\"width:100px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Masa metra:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_m]\" value=\"$tp_m_m\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Masa metra kwadratowego:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_m2]\" value=\"$tp_m_m2\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Masa sztuki:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_szt]\" value=\"$tp_m_szt\" style=\"width:50px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">Masa jednostki miary:</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_jm]\" value=\"$tp_m_jm\" style=\"width:50px\"></TD>
	</TR>
	</tbody>
	<tfoot>
	<TR>
		<TD class=\"c2\"><INPUT TYPE=\"button\" value=\"Anuluj\" onClick=\"document.gobackform.submit()\"></TD>
		<TD class=\"c4\"><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD>
	</TR>
	</tfoot>
	</table>";

	echo "
	<FORM METHOD=POST ACTION=\"$next\" method=\"POST\" name=\"gobackform\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	".sort_navi_options($LIST)."
	</form>
	<FORM METHOD=POST ACTION=\"$next\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszTowar\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"$to_id\">
	$table
	".sort_navi_options($LIST)."
	</FORM>
	";
?>
