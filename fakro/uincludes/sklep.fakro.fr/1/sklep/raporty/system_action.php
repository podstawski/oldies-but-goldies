<?
	$id = $LIST[id];

	$sql = "SELECT * FROM system_action_log, system_user
			WHERE sal_id = $id AND sal_user_id = su_id";

	parse_str(ado_query2url($sql));

	$sql = "SELECT * FROM system_log
			WHERE sl_tin <= $sal_data AND sl_tout >= $sal_data
			AND $sal_user_id = sl_user_id AND sl_server = $SERVER_ID LIMIT 1";

	parse_str(ado_query2url($sql));	
	$adodb->debug=0;

	$sql = "SELECT su_nazwisko AS su_nazwa FROM system_user WHERE su_id = $su_parent";
	parse_str(ado_query2url($sql));

	echo "
	<TABLE>
	<TR>
		<TD>Firma:</TD>
		<TD>$su_nazwa</TD>
	</TR>
	<TR>
		<TD>Osoba</TD>
		<TD>$su_imiona $su_nazwisko ($su_login)</TD>
	</TR>
	<TR>
		<TD>Ip:</TD>
		<TD>$sl_ip</TD>
	</TR>
	<TR>
		<TD>Nazwa akcji:</TD>
		<TD>$sal_action</TD>
	</TR>
	<TR>
		<TD>Czas akcji:</TD>
		<TD>".date("d-m-Y H:i:s",$sal_data)."</TD>
	</TR>
	<TR>
		<TD>Czas zalogowania:</TD>
		<TD>".date("d-m-Y H:i:s",$sl_tin)."</TD>
	</TR>
	<TR>
		<TD>Czas wylogowania:</TD>
		<TD>".date("d-m-Y H:i:s",$sl_tout)."</TD>
	</TR>
	<TR>
		<TD>Klucz:</TD>
		<TD>$sal_klucz</TD>
	</TR>
	<TR>
		<TD valign=\"top\">Opis:</TD>
		<TD valign=\"top\">
		<pre style='font-size:14px'>$sal_opis</pre>
		</TD>
	</TR>
	</TABLE>
	";
?>
