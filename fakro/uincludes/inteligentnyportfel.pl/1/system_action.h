<?
	return;

	global $WEBPAGE;
	$tree=$WEBPAGE->tree."$page:";

	if (!$WEBPAGE->id) return;
	

	$db = $tui_db;
        $query="SELECT so_id,so_nazwa,so_parent
                FROM system_obiekt WHERE so_server=$SERVER_ID 
		AND so_klucz='p_$page'";
        parse_str(query2url($query));

	$query="UPDATE system_obiekt SET so_tree='$tree' WHERE so_id=$so_id";
	if ($so_id) pg_exec($db,$query);

?>
