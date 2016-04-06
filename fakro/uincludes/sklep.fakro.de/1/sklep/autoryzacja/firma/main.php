<?
	$query="SELECT su_id,su_nazwisko AS nazwa,su_miasto AS miasto,su_login AS login 
		FROM system_user WHERE su_id=$CIACHO[admin_su_id]";
	parse_str(quoteUrlEnc(ado_query2url($query)));

	echo "<b><a href=\"$next\">".strtoupper($login).".".strtoupper($miasto)." ($su_id)</a></b><br>$nazwa";

?>
