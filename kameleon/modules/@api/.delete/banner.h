<?

	$query = "SELECT sid FROM webtd WHERE ver=$ver
				AND server=$SERVER_ID AND lang='$lang'
				AND pri=$pri AND page_id = $page_id";

	parse_str(ado_query2url($query));

	$sql = "DELETE FROM api2_baner WHERE ab_html = $sid AND ab_server = $SERVER_ID";

	if ($adodb->execute($sql)) logquery($sql);

?>