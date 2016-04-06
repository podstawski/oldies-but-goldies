<?
	global $KATEGORIA;
	$cena = $FORM[cena];
	$kat = $KATEGORIA;
	$sklep = $FORM[sklep];
	$cena = ereg_replace(",",".",$cena);
	$cena = ereg_replace("[^0-9\.-]","",$cena);

	if (!strlen($cena) || !strlen($kat) || !strlen($sklep)) return;
	
	function getAllKat($adodb,$id)
	{
		if (!strlen($id)) return;
		$sql = "SELECT ka_id FROM kategorie WHERE ka_parent = $id";
		$res = $adodb->execute($sql);
		if (!$res->RecordCount()) return;
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$wynik.=$ka_id.",".getAllKat($adodb,$ka_id);
		}
		return $wynik;
	}

	$lista = $kat.",".getAllKat($adodb,$kat);
	$lista = substr($lista,0,-1);

	$sql = "UPDATE towar_sklep
			SET ts_cena = $cena 
			WHERE ts_sk_id = $sklep 
			AND ts_to_id 
			IN (SELECT tk_to_id FROM towar_kategoria WHERE 
				tk_to_id=towar_sklep.ts_to_id AND tk_ka_id IN ($lista))";

	$adodb->execute($sql);
	$action_id=$kat;
?>
