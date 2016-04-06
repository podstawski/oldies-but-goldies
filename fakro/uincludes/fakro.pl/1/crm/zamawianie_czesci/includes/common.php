<?

function debug($var, $stop = false)
{
  echo '<pre style="color:green">dump:[';
  print_r($var);
  echo ']</pre>';

  if($stop)
  	exit;
}

function convert_to_string($string, $length)
{
  while(strlen($string) < $length)
  {
    $string = '0' . $string;
  }
  return $string;
}

function array_keys_recount($array)
{
	foreach ($array as $value)
	{
		$new_array[] = $value;
	}
	return $new_array;
}

function insert_firmy($data)
{
	$data['data_utworzenia'] = date('Y-m-d');
	$id_firmy = insert_array($data, 'firmy');

	if ($id_firmy)
	{
		return $id_firmy;
	}
	return false;
}

function TabliczkaZnamionowa($nazwa_nr, $nazwa_nr2)
{
	$year_for_query = substr($nazwa_nr2, 1, 1) . substr($nazwa_nr2, 0, 1);
	$week_for_query = substr($nazwa_nr2, 3, 1) . substr($nazwa_nr2, 2, 1);

	$date_for_query = '(
		YEARWEEK(bp_okna.data_poczatek) <= "20' . $year_for_query . $week_for_query .
						'" AND bp_okna.data_koniec = "0000-00-00"
		OR
		YEARWEEK(bp_okna.data_koniec) > "20' . $year_for_query. $week_for_query .
						'" AND YEARWEEK(bp_okna.data_poczatek) <= "20' . $year_for_query. $week_for_query . '") ';

	$query_text = 'SELECT bp_okna.id_bp_okna, ' .
						'bp_okna.id_bp_numery, ' .
						'CONCAT(bp_okna.indeks, \' \', bp_okna.nazwa) as `produkt`, ' .
						'CONCAT(bp_numery.nazwa, \' - \', bp_numery.opis) AS `wersja`, ' .
						'CONCAT(IF(bp_okna.s IS NOT NULL, bp_okna.s, \'..\'), \' / \', IF(bp_okna.w IS NOT NULL, bp_okna.w, \'..\')) AS `rozmiar` ' .
					'FROM bp_okna ' .
					'LEFT JOIN bp_numery USING(id_bp_numery) ' .
					'WHERE bp_okna.indeks = "' . $nazwa_nr . '" ' .
						'AND bp_okna.serwis = 1 ' .
						'AND ' . $date_for_query . ' ' .
					'ORDER BY bp_okna.data_poczatek DESC ' .
					'LIMIT 1';
	$okna = fetch_array($query_text);
	if(count($okna) > 0)
	{
		$okna = $okna[0];
	}
	return $okna;
}

?>