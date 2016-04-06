<?
	$query="SELECT su_nazwisko AS nazwisko,su_imiona AS im,su_login AS login 
		FROM system_user WHERE su_id=$LIST[id]";
	parse_str(ado_query2url($query));

	echo " &raquo; $im $nazwisko";

	$szukaj[user]=$login;

?>
