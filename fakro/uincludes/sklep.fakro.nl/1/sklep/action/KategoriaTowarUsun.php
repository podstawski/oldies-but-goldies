<?
	$killId = $LIST[killKat];

	if (strlen($killId))
	{
		$sql = "SELECT * FROM towar_kategoria WHERE tk_id = $killId";
		parse_str(ado_query2url($sql));
		$sql = "DELETE FROM towar_kategoria WHERE tk_id = $killId;
		UPDATE towar SET to_ka_c = wIluKatTow(to_id) WHERE to_id = $tk_to_id;
		UPDATE kategorie SET ka_to_c = ileTowWKat(ka_id) WHERE ka_id = $tk_ka_id;";
		$adodb->execute($sql);
		$action_id = $killId;
	}

?>
