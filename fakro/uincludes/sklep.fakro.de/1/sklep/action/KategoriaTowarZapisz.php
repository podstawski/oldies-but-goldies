<?
	$kat_id = $FORM[nowa_kategoria];
	$to_id = $LIST[id];

	if (!strlen($kat_id) || !strlen($to_id)) return;

	$action_id="$to_id";

	$sql = "SELECT COUNT(*) AS jest FROM towar_kategoria
		WHERE tk_to_id = $to_id
		AND tk_ka_id = $kat_id";	
	parse_str(ado_query2url($sql));
		
	if (!$jest)
	{
		$sql = "INSERT INTO towar_kategoria (tk_to_id, tk_ka_id)
				VALUES ($to_id, $kat_id); 
				UPDATE towar SET to_ka_c = wIluKatTow(to_id) WHERE to_id = $to_id;
				UPDATE kategorie SET ka_to_c = ileTowWKat(ka_id) WHERE ka_id = $kat_id;";
		$adodb->execute($sql);
	}
	$sql = "";
?>
