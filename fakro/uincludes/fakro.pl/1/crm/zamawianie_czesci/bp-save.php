<?php
function summary()
{
	put2session();
	$row['kraje'] = fetch_list('SELECT id_kraje, nazwa FROM kraje WHERE _is_adding = 0 ORDER BY nazwa');
	$row['dostawcy'] = fetch_array('SELECT id_zgloszenie_serwisowe_dostawcy, nazwa, uwagi FROM zgloszenie_serwisowe_dostawcy WHERE _is_adding = 0 ORDER BY nazwa ASC');
	$row['title'] = 'Zamówienie części - podsumowanie';
	$row['zglaszajacy_caption'] = 'Dane zamawiającego';
	return $row;
}

function proceed() {
	put2session();
	$mode = '';
}

function put2session()
{
	#$nazwa_nr = $_POST['nazwa_nr'][0] != '' ? $_POST['nazwa_nr'][0] : '';
	#$nazwa_nr2 = $_POST['nazwa_nr2'][0] != '' ? $_POST['nazwa_nr2'][0] : '';
	
	$nazwa_nr = $_POST['nazwa_nr'];
	$nazwa_nr2 = $_POST['nazwa_nr2'];
	
	if($nazwa_nr != '' && $nazwa_nr2 != '')
	{
		$_SESSION['bp_form_data'][$nazwa_nr . '-' . $nazwa_nr2] = $_POST;
	}
}

function save()
{
	global $platnosci_page_1, $platnosci_page_2;
	
	if(!isset($_POST['produkty']) || count($_POST['produkty']) == 0)
	{
		// info ze nic nie dodano
	}
	unset($_SESSION['done']);
	$_SESSION['done']['produkty'] = 'Produkty, dla których zamówiono cześci:';

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
	
	if($_POST['sposob_platnosci'] == 1) $sposob_platnosci = 'Przy odbiorze';
	if($_POST['sposob_platnosci'] == 2) $sposob_platnosci = 'Płatności elektroniczne (karta płatnicza, szybki przelew, przelew bankowy)';
	
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
		'uwagi' => 'Wybrany dostawca: ' . $_POST['nazwa_dostawcy'] . '.--BR--Całkowity koszt zamówienia: ' . $_POST['suma'] . ' PLN.--BR--'.$sposob_platnosci.'--BR--',
		'osoba_kontaktowa' => $_POST['osoba_kontaktowa'],
		'telefon_kontaktowy_kraj' => $_POST['telefon_kontaktowy_kraj'],
		'telefon_kontaktowy_miasto' => $_POST['telefon_kontaktowy_miasto'],
		'telefon_kontaktowy_numer' => $_POST['telefon_kontaktowy_numer'],
		'telefon_kontaktowy_wewnetrzny' => $_POST['telefon_kontaktowy_wewnetrzny'],
	);
	$id_zgloszenie_serwisowe = insert_array($zgloszenie_serwisowe, 'zgloszenie_serwisowe');
	
	// nr dla klienta
	$nr_ewidencyjny_klient = date('ymd') .  $id_zgloszenie_serwisowe;
	query('UPDATE zgloszenie_serwisowe SET nr_ewidencyjny_klient = \''.$nr_ewidencyjny_klient.'\' WHERE id_zgloszenie_serwisowe = \''.$id_zgloszenie_serwisowe.'\'');
	$_SESSION['done']['nr_ewidencyjny_klient'] = $nr_ewidencyjny_klient;
	
	// usuniecie najpierw z bazy poprzednich wpisow towarow w tej sesji
	if(array_key_exists('id_zgloszenie_serwisowe_towary', $_SESSION))
	{
		if(count($_SESSION['id_zgloszenie_serwisowe_towary']) > 0)
		{
			$query_text = 'DELETE FROM zgloszenie_serwisowe_towary WHERE id_zgloszenie_serwisowe_towary IN ('.implode(',', $_SESSION['id_zgloszenie_serwisowe_towary']).')';
			query($query_text);
		}
	}
	if(array_key_exists('id_zgloszenie_serwisowe__towary', $_SESSION))
	{
		if(count($_SESSION['id_zgloszenie_serwisowe__towary']) > 0)
		{
			$query_text = 'DELETE FROM zgloszenie_serwisowe_towary WHERE zgloszenie_serwisowe__towary IN ('.implode(',', $_SESSION['id_zgloszenie_serwisowe__towary']).')';
			query($query_text);
		}
	}
	
	foreach($_POST['produkty'] as $tabliczka => $array)
	{
	    //towar
	    // pobranie z sesji informacji o produkcie
	    $_SESSION['done']['produkty'] .= '<strong>' . $tabliczka . '</strong>';

		$zgloszenie_serwisowe_towary = array(
			'nazwa' => $_SESSION['bp_form_data'][$tabliczka]['produkt_nazwa'][0],
			'nazwa_nr' => $_SESSION['bp_form_data'][$tabliczka]['nazwa_nr'],
			'nazwa_nr2' => $_SESSION['bp_form_data'][$tabliczka]['nazwa_nr2'],
			'data_nabycia_towaru' => $_SESSION['bp_form_data'][$tabliczka]['data_nabycia_towaru'][0],
			'data_montazu' => $_SESSION['bp_form_data'][$tabliczka]['data_montazu'][0],
			'montaz' => $_SESSION['bp_form_data'][$tabliczka]['montaz'][0],
			'pokrycie_dachowe' => $_SESSION['bp_form_data'][$tabliczka]['pokrycie_dachowe'][0],
			'rozmiar' => $_SESSION['bp_form_data'][$tabliczka]['rozmiar'][0],
			'id_bp_numery' => $_SESSION['bp_form_data'][$tabliczka]['id_bp_numery'][0],
			'ilosc' => $_SESSION['bp_form_data'][$tabliczka]['ilosc_produktow'][0],
			'id_grupy_produktow' => $_SESSION['bp_form_data'][$tabliczka]['id_bp_produkty'][0],
			'komentarz' => $_SESSION['bp_form_data'][$tabliczka]['komentarz'][0],
			'nazwa_nr3' => $_SESSION['bp_form_data'][$tabliczka]['nazwa_nr3'],
			'nazwa_nr4' => $_SESSION['bp_form_data'][$tabliczka]['nazwa_nr4'],
		);
		$id_zgloszenie_serwisowe_towary = insert_array($zgloszenie_serwisowe_towary, 'zgloszenie_serwisowe_towary');
		$id_zgloszenie_serwisowe__towary = insert('INSERT INTO zgloszenie_serwisowe__towary (id_zgloszenie_serwisowe, id_zgloszenie_serwisowe_towary) VALUES (' . $id_zgloszenie_serwisowe . ', ' . $id_zgloszenie_serwisowe_towary . ')');
		$_SESSION['id_zgloszenie_serwisowe_towary'][] = $id_zgloszenie_serwisowe_towary;
		$_SESSION['id_zgloszenie_serwisowe__towary'][] = $id_zgloszenie_serwisowe__towary;
		
		// dodatkowy komentarz do zgloszenia
		$dodatkowyKomentarz = '--BR--';
	    foreach ($array['ilosc'] as $id_bp_czesci => $ilosc)
		{
			if ($ilosc > 0)
			{
				$zgloszenie_serwisowe_zlecenie_zamowienie = array(
					'id_zgloszenie_serwisowe' => $id_zgloszenie_serwisowe,
					'id_bp_czesci' => $id_bp_czesci,
					'ilosc' => $ilosc,
					'cena' => $array['cena'][$id_bp_czesci],
					'nazwa' => $array['czesci'][$id_bp_czesci],
				);

				$dodatkowyKomentarz .= '--BR--'.$array['czesci'][$id_bp_czesci] . ' (Ilość: '.$ilosc.', ' .
						'cena dla klienta: '.$array['cena'][$id_bp_czesci].')';

				$_SESSION['done']['czesci'][$id_bp_czesci] = $array['czesci'][$id_bp_czesci];
				$_SESSION['done']['ceny'][$id_bp_czesci] = $array['cena'][$id_bp_czesci];
				$_SESSION['done']['ilosci'][$id_bp_czesci] = $ilosc;
				$id_zgloszenie_serwisowe_zlecenie_zamowienie = insert_array($zgloszenie_serwisowe_zlecenie_zamowienie, 'zgloszenie_serwisowe_zlecenie_zamowienie');
			}
		}
		if($dodatkowyKomentarz != '')
		{
			$dodatkowyKomentarz = ' Zamówione części:--BR--' . $dodatkowyKomentarz . '--BR--';
			$query_text = 'UPDATE zgloszenie_serwisowe ' .
						'SET uwagi = CONCAT(uwagi, "'.$dodatkowyKomentarz.'") ' .
					'WHERE id_zgloszenie_serwisowe = ' . $id_zgloszenie_serwisowe;
			query($query_text);
		}
		$_SESSION['done']['suma'] = $_POST['suma'];
		$_SESSION['done']['sposob_platnosci'] = $_POST['sposob_platnosci'];
	}

	foreach ($_SESSION as $k => $v)
	{
		if($k == 'done')
			continue;
		unset($_SESSION[$k]);
	}
	foreach ($_POST as $k => $v)
	{
		unset($_POST[$k]);
	}
}
?>