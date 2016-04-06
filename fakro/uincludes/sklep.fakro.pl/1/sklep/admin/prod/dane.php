<?
	$pr_id = $CIACHO[admin_pr_id];

	$sql = "SELECT * FROM producent WHERE pr_id = $pr_id";

	parse_str(ado_query2url($sql));

	$table = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
		<tbody>
		<TR>
			<TD class=\"c2\">Nazwa:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[pr_nazwa]\" value=\"$pr_nazwa\" style=\"width:350px\"></TD>
		</TR>
		<TR>
			<TD class=\"c2\">Stona WWW:</TD>
			<TD class=\"c4\">http://<INPUT TYPE=\"text\" NAME=\"form[pr_www]\" value=\"$pr_www\" style=\"width:314px\"></TD>
		</TR>
		<TR>
			<TD class=\"c2\">Kraj:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[pr_kraj]\" value=\"$pr_kraj\" style=\"width:350px\"></TD>
		</TR>
		<TR>
			<TD class=\"c2\">Logo małe:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto NAME=\"form[pr_logo_m]\" value=\"$to_logo_m\" style=\"width:400px\">
					<img src=$SKLEP_IMAGES/sb/i_image_n.gif align=absmiddle 
						onClick=\"galeria_input='foto';kartoteka_popup('$next${next_char}form[img]='+document.all[galeria_input].value,'galeria')\" 
						style=\"cursor:hand;\" 
						onmouseover=\"this.src='$SKLEP_IMAGES/sb/i_image_a.gif'\" 
						onmouseout=\"this.src='$SKLEP_IMAGES/sb/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>
		<TR>
			<TD class=\"c2\">Logo duże:</TD>
			<TD class=\"c4\"><INPUT TYPE=\"text\" id=foto2 NAME=\"form[pr_logo_d]\" value=\"$to_logo_d\" style=\"width:400px\">
					<img src=$SKLEP_IMAGES/sb/i_image_n.gif align=absmiddle 
						onClick=\"galeria_input='foto2';kartoteka_popup('$next${next_char}form[img]='+document.all[galeria_input].value,'galeria')\" style=\"cursor:hand;\" 
						onmouseover=\"this.src='$SKLEP_IMAGES/sb/i_image_a.gif'\" 
						onmouseout=\"this.src='$SKLEP_IMAGES/sb/i_image_n.gif'\" 
						border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>			
			</TD>
		</TR>
		</tbody>
		<tfoot>
		<TR>
			<TD class=\"c4\" colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD>
		</TR>
		</tfoot>
	</TABLE>
	";

	echo "
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ProducentZapisz\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"$pr_id\">
	$table
	</FORM>
	";

?>
<script>

var galeria_input="";

</script>
