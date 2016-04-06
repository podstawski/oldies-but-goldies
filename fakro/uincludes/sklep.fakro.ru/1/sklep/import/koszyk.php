<?
	$pole = $obj->magazyn->identyfikator;
	while (list($_key,$_v)=each($obj->magazyn->koszyk))
		while (list($key,$v)=each($_v))
		{
			$towar = $v->identyfikator;
			$sql = "SELECT to_id FROM towar WHERE $pole = '$towar' LIMIT 1";
			parse_str(ado_query2url($sql));
			$FORM[towar_id] = $to_id;
			$FORM[quantity] = $v->ilosc;			
			include($SKLEP_INCLUDE_PATH."/action/KoszykDodaj.h");
		}
?>
