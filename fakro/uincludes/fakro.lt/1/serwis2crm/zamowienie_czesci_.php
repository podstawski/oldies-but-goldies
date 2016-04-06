<?

function search()
{
	global $smarty;

	foreach ($_SESSION as $k => $v)
	{
		unset($_SESSION[$k]);
	}
	$smarty->assign('title', 'Zamówienie czê¶ci');
	return $smarty->fetch('bp-search.tpl');
}

function results()
{

	global $smarty;

    $smarty->assign('title', 'Zamówienie cze¶ci');
	$smarty->assign('zglaszajacy_caption','Dane do faktury');
	$smarty->assign('adres_zamontowania_caption','Adres dostarczenia przesy³ki');

	$year_for_query = substr($_POST['nazwa_nr2'], 1, 1) . substr($_POST['nazwa_nr2'], 0, 1);
	$week_for_query = substr($_POST['nazwa_nr2'], 3, 1) . substr($_POST['nazwa_nr2'], 2, 1);

	$date_for_query = '(
		YEARWEEK(data_poczatek) <= "20' . $year_for_query . $week_for_query .
						'" AND data_koniec = "0000-00-00"
		OR
		YEARWEEK(data_koniec) > "20' . $year_for_query. $week_for_query .
						'" AND YEARWEEK(data_poczatek) <= "20' . $year_for_query. $week_for_query . '")';

	$query_text = 'SELECT id_bp_okna FROM bp_okna ' .
				'WHERE indeks = "' . $_POST['nazwa_nr'] . '" ' .
					'AND dostepnosc = 1 ' .
					'AND' . $date_for_query . ' ' .
				'ORDER BY data_poczatek DESC';

	$id_bp_okna = fetch_single($query_text);

	if ($id_bp_okna)
	{
		$id_bp_konstrukcje = fetch_single('SELECT id_bp_konstrukcje ' .
					'FROM bp_okna ' .
					'WHERE id_bp_okna = ' . $id_bp_okna);

		if ($id_bp_konstrukcje)
		{
    		$query_text = 'SELECT bp_explozyjne.id_bp_explozyjne ' .
							'FROM bp_konstrukcje_explozyjne ' .
							'LEFT JOIN bp_explozyjne USING (id_bp_explozyjne) ' .
							'WHERE id_bp_konstrukcje = ' . $id_bp_konstrukcje . ' ' .
							'AND bp_explozyjne.niedostepny = 0 ' .
							//'AND ' . $date_for_query . ' ' .
							'ORDER BY bp_konstrukcje_explozyjne.data_poczatek DESC';
    		$id_bp_explozyjne = fetch_single($query_text);

			if ($id_bp_explozyjne)
			{
    			$files = fetch_array(
    			        'SELECT a.id_bp_explozyjne_pliki, b.opis ' .
						'FROM bp_explozyjne_pliki a ' .
						'LEFT OUTER JOIN bp_explozyjne_pliki__jezyki b ON ((a.id_bp_explozyjne_pliki = b.id_bp_explozyjne_pliki OR b.id_bp_explozyjne_pliki IS NULL) AND id_jezyki = ' . _LANG . ') ' .
						'WHERE a.id_bp_explozyjne = ' . $id_bp_explozyjne . ' ' .
							'AND mime LIKE "%image%"');

       			if (is_array($files) && count($files) > 0)
				{
    				$query_text = '
						SELECT
							c.id_bp_czesci,
							c.indeks,
							if (cn.nazwa!="",cn.nazwa,c.nazwa) as nazwa,
							if (co.opis!="",co.opis,c.opis) as opis,
							oc.nr_explozyjny,
							c.serwis as access_www,
							bc.cena,
							oc.ilosc
						FROM bp_okna__bp_czesci oc
						LEFT JOIN bp_czesci c USING (id_bp_czesci)
						LEFT JOIN bp_jde j ON c.id_bp_jde2 = j.id_bp_jde
						LEFT JOIN bp_jde_ceny jc ON jc.id_bp_jde = j.id_bp_jde
						LEFT JOIN bp_ceny bc ON jc.id_bp_ceny = bc.id_bp_ceny ' .
						'LEFT OUTER JOIN bp_czesci_nazwa cn ON ((c.id_bp_czesci = cn.id_bp_czesci OR cn.id_bp_czesci IS NULL) AND cn.id_jezyki = ' . _LANG . ') ' .
						'LEFT OUTER JOIN bp_czesci_opis co ON ((c.id_bp_czesci = co.id_bp_czesci OR co.id_bp_czesci IS NULL) AND co.id_jezyki = ' . _LANG . ') ' .
						'WHERE oc.id_bp_okna = ' . $id_bp_okna . '
							AND
							bc.id_firmy = ' . _ID_SERWIS . '
							AND
							bc.id_bp_waluty = ' . _ID_WALUTY . '
							AND ' . str_replace('data', 'oc.data', $date_for_query);
					$czesci = fetch_array($query_text);

    				if (is_array($czesci) && count($czesci) > 0)
					{
						$smarty->assign('czesci', $czesci);
						foreach ($czesci as $czesc)
						{
							if (strlen($czesc['nr_explozyjny']) > 0)
							{
    							$nr_explozyjny[] = $czesc['nr_explozyjny'];
						    }
						}
						foreach ($files as $file)
						{

							$smarty->assign('id_bp_explozyjne_pliki', $file['id_bp_explozyjne_pliki']);
							$smarty->assign('opis', $file['opis']);
							$images .= $smarty->fetch('bp-images.tpl');

							if (is_array($nr_explozyjny) && count($nr_explozyjny) > 0)
							{
								$map = fetch_array('
									SELECT
										ed.coords,
										ed.nr_explozyjny,
										c.indeks,
										c.nazwa,
										oc.access_www
									FROM
										bp_explozyjne_data ed
									LEFT JOIN bp_okna__bp_czesci oc USING (nr_explozyjny)
									LEFT JOIN bp_czesci c USING (id_bp_czesci)
									WHERE
										ed.id_bp_explozyjne_pliki = ' . $file['id_bp_explozyjne_pliki'] . '
										AND
										ed.nr_explozyjny IN ("' . implode('","', $nr_explozyjny) . '")
										AND
										oc.id_bp_okna = ' . $id_bp_okna . '
										AND ' . str_replace('data', 'oc.data', $date_for_query));


								if (is_array($map) && count ($map) > 0)
								{
									foreach ($map as $k => $v)
									{
    									$map[$k]['access_www'] = 1;
										$coords = explode(',', $v['coords']);
										unset($coords[(count($coords) - 1)]);
										unset($coords[0]);
										$map[$k]['coords'] = implode(',', array_keys_recount($coords));
									}

									$smarty->assign('map', $map);
									$maps .= $smarty->fetch('bp-maps.tpl');

									$smarty->assign('kraje', fetch_list('SELECT id_kraje, nazwa FROM kraje WHERE _is_adding = 0 ORDER BY nazwa'));
									$smarty->assign('dostawcy', fetch_array('SELECT id_zgloszenie_serwisowe_dostawcy, nazwa, uwagi FROM zgloszenie_serwisowe_dostawcy WHERE _is_adding = 0 ORDER BY sequence ASC'));
									$smarty->assign('produkty', fetch_list('SELECT id_bp_produkty, opis FROM bp_produkty WHERE _is_adding = 0 ORDER BY nazwa'));
									$smarty->assign('maps', $maps);
									$smarty->assign('images', '<table cellpadding=5><tr>' . $images . '</tr></table>');
								}
							}
						}
						$_SESSION['nazwa_nr'] = $_POST['nazwa_nr'];
						$_SESSION['nazwa_nr2'] = $_POST['nazwa_nr2'];
						$smarty->assign('showInfoDiv', true);
						return $smarty->fetch('bp-results.tpl');
					}
				}
			}
		}
	}

	$smarty->assign('showInfoDiv', false);
	$content = 'Nie znaleziono okna o podanym numerze tabliczki. Prosimy o kontakt telefoniczny. ';
	$content .= '<a href="?module=bp&mode=search">Powrót</a>';

	$smarty->assign('content', $content);

	return $smarty->fetch('bp-results.tpl');
}

function details()
{
	$firmy = array(
		'nazwa' => $_POST['nazwa'],
		'nazwa_skrocona' => $_POST['nazwa'],
		'ulica' => $_POST['ulica'],
		'nr_domu' => $_POST['nr_domu'],
		'nr_mieszkania' => $_POST['nr_mieszkania'],
		'kod_pocztowy' => $_POST['kod_pocztowy'],
		'miasto' => $_POST['miasto'],
		't1_kraj' => $_POST['t1_kraj'],
		't1_miasto' => $_POST['t1_miasto'],
		't1_numer' => $_POST['t1_numer'],
		't1_wewnetrzny' => $_POST['t1_wewnetrzny'],
		'id_kraj' => $_POST['id_kraje'],
		'email' => $_POST['email'],
	);
	$id_firmy = insert_firmy($firmy);

	$nr_ewidencyjny = '';

	$adres_okno = $_POST['okno_miasto'] != '' && $_POST['okno_ulica'] != '';
	$zgloszenie_serwisowe = array(
		'id_zgloszenie_serwisowe_typ' => '8',
		'id_zgloszenie_serwisowe_status' => '1',
		'data_przyjecia_zgloszenia' => date('Y-m-d'),
		'id_dodal_zgloszenie' => _ID_OSOBY,
		'nr_ewidencyjny' => $nr_ewidencyjny,
		'id_nabywca' => $id_firmy,
		'okno_ulica' => $adres_okno == true ? $_POST['okno_ulica'] : $_POST['ulica'],
		'okno_nr_domu' => $adres_okno == true ? $_POST['okno_nr_domu'] : $_POST['nr_domu'],
		'okno_nr_mieszkania' => $adres_okno == true ? $_POST['okno_nr_mieszkania'] : $_POST['nr_mieszkania'],
		'okno_kod_pocztowy' => $adres_okno == true ? $_POST['okno_kod_pocztowy'] : $_POST['kod_pocztowy'],
		'okno_miasto' => $adres_okno == true ? $_POST['okno_miasto'] : $_POST['miasto'],
		'okno_id_kraje' => $adres_okno == true ? $_POST['okno_id_kraje'] : $_POST['id_kraje'],
		'powod_zgloszenia_reklamacji' => $_POST['powod_zgloszenia_reklamacji'],
		'pora_dnia_przyjazdu_serwisu' => $_POST['pora_dnia_przyjazdu_serwisu'],
		'uwagi' => ($_POST['termin_dostawy']!='' ? 'Proponowany termin dostawy: ' . $_POST['termin_dostawy'] . '. ':'') . 'Wybrany dostawca: ' . $_POST['nazwa_dostawcy'] . '. Ca³kowity koszt zamówienia: ' . $_POST['suma'] . ' PLN.',
	);
	$id_zgloszenie_serwisowe = insert_array($zgloszenie_serwisowe, 'zgloszenie_serwisowe');

	// nr dla klienta
	$nr_ewidencyjny_klient = date('ymd') .  $id_zgloszenie_serwisowe;
	query('UPDATE zgloszenie_serwisowe SET nr_ewidencyjny_klient = \''.$nr_ewidencyjny_klient.'\' ' .
			'WHERE id_zgloszenie_serwisowe = \''.$id_zgloszenie_serwisowe.'\'');

    //towar
    foreach ($_POST['produkt_nazwa'] as $k => $v)
	{
		$zgloszenie_serwisowe_towary = array(
			'nazwa' => $_POST['produkt_nazwa'][$k],
			'nazwa_nr' => $_POST['nazwa_nr'][$k],
			'nazwa_nr2' => $_POST['nazwa_nr2'][$k],
			'data_nabycia_towaru' => $_POST['data_nabycia_towaru'][$k],
			'data_montazu' => $_POST['data_montazu'][$k],
			'montaz' => $_POST['montaz'][$k],
			'pokrycie_dachowe' => $_POST['pokrycie_dachowe'][$k],
			'rozmiar' => $_POST['rozmiar'][$k],
			'wersja' => $_POST['wersja'][$k],
			'ilosc' => $_POST['ilosc_produktow'][$k],
			'id_grupy_produktow' => $_POST['id_bp_produkty'][$k],
			'komentarz' => $_POST['komentarz'][$k],
		);
		$id_zgloszenie_serwisowe_towary = insert_array($zgloszenie_serwisowe_towary, 'zgloszenie_serwisowe_towary');

		$id = insert('INSERT INTO zgloszenie_serwisowe__towary (id_zgloszenie_serwisowe, id_zgloszenie_serwisowe_towary) VALUES (' . $id_zgloszenie_serwisowe . ', ' . $id_zgloszenie_serwisowe_towary . ')');
	}


	unset($_SESSION['czesci']);
	unset($_SESSION['ceny']);
	unset($_SESSION['ilosci']);
    unset($_SESSION['suma']);
    unset($_SESSION['dostawa']);

	// dodatkowy komentarz do zgloszenia
	$dodatkowyKomentarz = '';

    foreach ($_POST['ilosc'] as $id_bp_czesci => $ilosc)
	{
		if ($ilosc > 0)
		{
			$zgloszenie_serwisowe_zlecenie_zamowienie = array(
				'id_zgloszenie_serwisowe' => $id_zgloszenie_serwisowe,
				'id_bp_czesci' => $id_bp_czesci,
				'ilosc' => $ilosc,
				'cena' => $_POST['cena'][$id_bp_czesci],
				'nazwa' => $_POST['czesci'][$id_bp_czesci],
			);

			$dodatkowyKomentarz .= ' '.$_POST['czesci'][$id_bp_czesci] . ' (Ilo¶æ: '.$ilosc.', ' .
					'cena dla klienta: '.$_POST['cena'][$id_bp_czesci].')';

			$_SESSION['czesci'][$id_bp_czesci] = $_POST['czesci'][$id_bp_czesci];
			$_SESSION['ceny'][$id_bp_czesci] = $_POST['cena'][$id_bp_czesci];
			$_SESSION['ilosci'][$id_bp_czesci] = $ilosc;

			$id_zgloszenie_serwisowe_zlecenie_zamowienie = insert_array($zgloszenie_serwisowe_zlecenie_zamowienie, 'zgloszenie_serwisowe_zlecenie_zamowienie');
		}
	}
	if($dodatkowyKomentarz != '')
	{
		$dodatkowyKomentarz = ' Zamówione czê¶ci:' . $dodatkowyKomentarz;
		$query_text = 'UPDATE zgloszenie_serwisowe ' .
					'SET uwagi = CONCAT(uwagi, "'.$dodatkowyKomentarz.'") ' .
				'WHERE id_zgloszenie_serwisowe = ' . $id_zgloszenie_serwisowe;
		query($query_text);
	}

	$_SESSION['suma'] = $_POST['suma'];
	$_SESSION['dostawa'] = 'Dostawca: ' .$_POST['nazwa_dostawcy'] . ', cena dostawy: ' . $_POST['cena_dostawca'] . ' PLN';
	header('Location: ./index.php?module=bp&mode=done');
}

function done()
{
	global $smarty;
	$smarty->assign('title', 'Zamówienie czê¶ci');
	return $smarty->fetch('bp-done.tpl');
}

?>