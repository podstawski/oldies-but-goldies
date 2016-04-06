<?
	global $KATEGORIA;
	$vat = $FORM[vat];
	$kat = $KATEGORIA;
	$vat = ereg_replace(",",".",$vat);
	$vat = ereg_replace("[^0-9\.-]","",$vat);

	if (!strlen($vat) || !strlen($kat)) return;
	
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

	$sql = "UPDATE towar
			SET to_vat = $vat 
			WHERE to_id 
			IN (SELECT tk_to_id FROM towar_kategoria WHERE 
				tk_to_id=towar_sklep.ts_to_id AND tk_ka_id IN ($lista))";

	$adodb->execute($sql);
	$action_id=$kat;
?>
