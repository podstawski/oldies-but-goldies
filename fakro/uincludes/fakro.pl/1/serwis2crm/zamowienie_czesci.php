<?
$config = array(
	'_FILES_URL' => '/serwis7/',
	'_ID_OSOBY' => '29',
	'_ID_SERWIS' => '990',
	'_ID_WALUTY' => '1',
	'_LANG' => '5',
	);

foreach($config as $key => $value) {
	define($key, $value);
	}

# ustawienia MySql`a
$CFG['host']	= "213.25.72.188";
$CFG['user']	= "fakro_www";
$CFG['pass']	= "www_fakro";
$CFG['db']		= "fakro_crm_prod";

include("$INCLUDE_PATH/serwis2crm/idb_mysql.php");
include("$INCLUDE_PATH/serwis2crm/common.php");

$idb = new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);
?>

<script src="<?=$INCLUDE_PATH?>/serwis2crm/common.js" type="text/javascript"></script>
<script src="<?=$INCLUDE_PATH?>/serwis2crm/numer_tabliczki.js" type="text/javascript"></script>

<style type="text/css">
<!--
		.opis
		{
			text-align: right;
			padding-right: 4px;
		}

		input, select, textarea
		{
		  font-family: Verdana,sans-serif;
		  font-size: 10px;
		  background-color: #eafafd;
		  color: black;
		  margin: 0px;
		  padding: 0px;
		  border: 1px outset #0066cc;
		}

		small
		{
			font-size: 9px;
		}
-->
</style>

<?
if($_POST['mode'] == "details") {
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
	$id_firmy = insert_array($firmy, 'firmy');
	
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
		'uwagi' => ($_POST['termin_dostawy']!='' ? 'Proponowany termin dostawy: ' . $_POST['termin_dostawy'] . '. ':'') . 'Wybrany dostawca: ' . $_POST['nazwa_dostawcy'] . '. Całkowity koszt zamówienia: ' . $_POST['suma'] . ' PLN.',
		);
	
	$id_zgloszenie_serwisowe = insert_array($zgloszenie_serwisowe, 'zgloszenie_serwisowe');
	
	// nr dla klienta
	$nr_ewidencyjny_klient = date('ymd').$id_zgloszenie_serwisowe;
	$idb->sqlaction('update','zgloszenie_serwisowe',' id_zgloszenie_serwisowe = "'.$id_zgloszenie_serwisowe.'"',array('nr_ewidencyjny_klient'),array($nr_ewidencyjny_klient));
	
    //towar
    foreach ($_POST['produkt_nazwa'] as $k => $v) {
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
		
		$idb->sqlaction('insert','zgloszenie_serwisowe__towary',' ',array('id_zgloszenie_serwisowe','id_zgloszenie_serwisowe_towary'),array($id_zgloszenie_serwisowe,$id_zgloszenie_serwisowe_towary));
		}
	
	unset($_SESSION['czesci']);
	unset($_SESSION['ceny']);
	unset($_SESSION['ilosci']);
    unset($_SESSION['suma']);
    unset($_SESSION['dostawa']);
	
	// dodatkowy komentarz do zgloszenia
	$dodatkowyKomentarz = '';
	foreach($_POST['ilosc'] as $id_bp_czesci => $ilosc) {
		if($ilosc > 0) {
			$zgloszenie_serwisowe_zlecenie_zamowienie = array(
				'id_zgloszenie_serwisowe' => $id_zgloszenie_serwisowe,
				'id_bp_czesci' => $id_bp_czesci,
				'ilosc' => $ilosc,
				'cena' => $_POST['cena'][$id_bp_czesci],
				'nazwa' => $_POST['czesci'][$id_bp_czesci],
				);
			
			$dodatkowyKomentarz .= ' '.$_POST['czesci'][$id_bp_czesci].' (Ilość: '.$ilosc.', cena dla klienta: '.$_POST['cena'][$id_bp_czesci].')';
			
			$_czesci[$id_bp_czesci] = $_POST['czesci'][$id_bp_czesci];
			$_ceny[$id_bp_czesci] = $_POST['cena'][$id_bp_czesci];
			$_ilosci[$id_bp_czesci] = $ilosc;
			
			$id_zgloszenie_serwisowe_zlecenie_zamowienie = insert_array($zgloszenie_serwisowe_zlecenie_zamowienie, 'zgloszenie_serwisowe_zlecenie_zamowienie');
			}
		}
	if($dodatkowyKomentarz != '') {
		$dodatkowyKomentarz = ' Zamówione części:' . $dodatkowyKomentarz;
		#$idb->sqlaction('update','zgloszenie_serwisowe',' id_zgloszenie_serwisowe = "'.$id_zgloszenie_serwisowe.'"',array('uwagi'),array("CONCAT(uwagi,\"".$dodatkowyKomentarz."\")"));
		$idb->sqlaction('sql','zgloszenie_serwisowe',' id_zgloszenie_serwisowe = "'.$id_zgloszenie_serwisowe.'"',array("uwagi=CONCAT(uwagi,\"".$dodatkowyKomentarz."\")"));
		
		/*
		$query_text = 'UPDATE zgloszenie_serwisowe ' .
					'SET uwagi = CONCAT(uwagi, "'.$dodatkowyKomentarz.'") ' .
				'WHERE id_zgloszenie_serwisowe = ' . $id_zgloszenie_serwisowe;
		*/
		}

	$_dostawa = 'Dostawca: ' .$_POST['nazwa_dostawcy'] . ', cena dostawy: ' . $_POST['cena_dostawca'] . ' PLN';
?>
<div align="center">

<table>
<tr>
	<td>Nr z tabliczki znamionowej okna: <b><?=$_POST['nazwa_nr'][0];?> <?=$_POST['nazwa_nr2'][0];?></b><br><br></td>
</tr>
<tr>
	<td><strong>Zamówione elementy:</strong><br><br>
	
	<table padding: 10px; font-size:9px; width:510; height: 100%;">
	<tr>
		<td style="text-align:left; width:450;"><strong>Opis</strong></td>
		<td style="text-align:left; width:30;"><strong>Ilośc:</strong></td>
		<td style="text-align:left; width:30;"><strong>Cena:</strong></td>
	</tr>
<?
$id = 1;
foreach($_czesci as $key => $value) {
?>
	<tr>
		<td style="text-align:left;"><?=$_czesci[$key];?></td>
		<td style="text-align:right;"><?=$_ilosci[$key];?></td>
		<td style="text-align:right;"><?=$_ceny[$key];?>&nbsp;PLN</td>
	</tr>
<?
	$id++;
	}
?>
	<tr>
		<td colspan="2" style="text-align:right;"><b>Suma zamówienia:</b></td>
		<td style="text-align:left;"><?=$_POST['suma'];?>&nbsp;PLN</td>
	</tr>
	</table>
	
	</td>
</tr>
</table>

<br><br>
Twoje zamówienie zostało złożone i oczekuje na akceptację.
Prosimy czekać na potwierdzenie pozytywnej weryfikacji zamówienia ? zostanie ono wysłane pocztą email.
<br><br>
</div>
<?
	}

if($_POST['mode'] == "results") {

	$year_for_query = substr($_POST['nazwa_nr2'], 1, 1) . substr($_POST['nazwa_nr2'], 0, 1);
	$week_for_query = substr($_POST['nazwa_nr2'], 3, 1) . substr($_POST['nazwa_nr2'], 2, 1);
	
	$date_for_query = '(YEARWEEK(data_poczatek) <= "20'.$year_for_query.$week_for_query.'" AND data_koniec = "0000-00-00" OR YEARWEEK(data_koniec) > "20'.$year_for_query.$week_for_query.'" AND YEARWEEK(data_poczatek) <= "20'.$year_for_query.$week_for_query.'")';
	$query_text = 'SELECT id_bp_okna FROM bp_okna WHERE indeks = "'.$_POST['nazwa_nr'].'" AND dostepnosc = 1 AND '.$date_for_query.' ORDER BY data_poczatek DESC';
	
	$idb->getvalues($idb->query($query_text));
	$id_bp_okna = $idb->row['id_bp_okna'];
	
	if($id_bp_okna) {
		$idb->getvalues($idb->query("SELECT id_bp_konstrukcje FROM bp_okna WHERE id_bp_okna = '".$id_bp_okna."'"));
		$id_bp_konstrukcje = $idb->row['id_bp_konstrukcje'];
		
		if($id_bp_konstrukcje) {
			$query_text = 'SELECT bp_explozyjne.id_bp_explozyjne FROM bp_konstrukcje_explozyjne LEFT JOIN bp_explozyjne USING (id_bp_explozyjne) WHERE id_bp_konstrukcje = '.$id_bp_konstrukcje.' AND bp_explozyjne.niedostepny = 0 ORDER BY bp_konstrukcje_explozyjne.data_poczatek DESC';
			$idb->getvalues($idb->query($query_text));
			$id_bp_explozyjne = $idb->row['id_bp_explozyjne'];
			
			if($id_bp_explozyjne) {
				$files = $idb->fetch_assoc($idb->query("SELECT a.id_bp_explozyjne_pliki, b.opis FROM bp_explozyjne_pliki a LEFT OUTER JOIN bp_explozyjne_pliki__jezyki b ON ((a.id_bp_explozyjne_pliki = b.id_bp_explozyjne_pliki OR b.id_bp_explozyjne_pliki IS NULL) AND id_jezyki = "._LANG.") WHERE a.id_bp_explozyjne = '".$id_bp_explozyjne."' AND mime LIKE '%image%'"));
				
				if(is_array($files) && count($files) > 0) {
					
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
						'WHERE oc.id_bp_okna = '.$id_bp_okna.'
							AND
							bc.id_firmy = '._ID_SERWIS.'
							AND
							bc.id_bp_waluty = '._ID_WALUTY.'
							AND '.str_replace('data', 'oc.data', $date_for_query);
					$czesci = $idb->fetch_assoc($idb->query($query_text));
					
					if(is_array($czesci) && count($czesci) > 0) {
						
						$_czesci = $czesci;
						
						foreach ($czesci as $czesc) {
							if(strlen($czesc['nr_explozyjny']) > 0) {
								$nr_explozyjny[] = $czesc['nr_explozyjny'];
								}
							}
						
						foreach ($files as $file) {
							$id_bp_explozyjne_pliki = $file['id_bp_explozyjne_pliki'];
							$opis = $file['opis'];
							$images .= "<td style='text-align:center'>
											<img style='height:100px;cursor:pointer;' style='cursor: hand;' onclick='show_image(".$id_bp_explozyjne_pliki.")' src='".$UIMAGES._FILES_URL."bp_explozyjne_pliki_".$id_bp_explozyjne_pliki."' border='0'>
										<br>".$opis."</td>";
							
							if(is_array($nr_explozyjny) && count($nr_explozyjny) > 0) {
								
								
								$idb->query('SELECT
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
										ed.id_bp_explozyjne_pliki = '.$file['id_bp_explozyjne_pliki'].'
										AND
										ed.nr_explozyjny IN ("'.implode('","', $nr_explozyjny).'")
										AND
										oc.id_bp_okna = '.$id_bp_okna.'
										AND '.str_replace('data', 'oc.data', $date_for_query));
								
								if($idb->rowcount() > 0) {
									
									$maps .= "\n\n<map name='map_".$id_bp_explozyjne_pliki."' id='map_".$id_bp_explozyjne_pliki."'>\n";
									
									for($i = 0; $i < $idb->rowcount(); $i++) {
										$idb->getvalues();
										
										$map_access_www = 1;
										$coords = explode(',', $idb->row['coords']);
										unset($coords[(count($coords) - 1)]);
										unset($coords[0]);
										$map_coords = implode(',', array_keys_recount($coords));
										
										$maps .= '<area shape="poly" coords="'.$map_coords.'" href="javascript:void(0);"';
										if($map_access_www == '1') {
											$maps .= ' onclick="return display(\''.$idb->row['nr_explozyjny'].'\')"';
											}else{
											$maps .= ' onclick="alert(\'Część niedostępna. Prosimy o kontakt telefoniczny.\');"';
											}
										$maps .= " alt=\"".$idb->row['indeks']." - ".$idb->row['nazwa']."\">\n";
										}
										
									$maps .= "</map>\n";
									}
								}
							}
						$kraje		= $idb->fetch_assoc($idb->query('SELECT id_kraje, nazwa FROM kraje WHERE _is_adding = 0 ORDER BY nazwa'));
						$dostawcy	= $idb->fetch_assoc($idb->query('SELECT id_zgloszenie_serwisowe_dostawcy, nazwa, uwagi FROM zgloszenie_serwisowe_dostawcy WHERE _is_adding = 0 ORDER BY sequence ASC'));
						$produkty	= $idb->fetch_assoc($idb->query('SELECT id_bp_produkty, opis FROM bp_produkty WHERE _is_adding = 0 ORDER BY nazwa'));
						$images		= '<table cellpadding=5><tr>'.$images.'</tr></table>';
						
						$showInfoDiv = true;
						include("$INCLUDE_PATH/serwis/zamowienie_czesci_js.php");
						}
					}
				}
			}
		
		}else{
		$showInfoDiv = false;
		$content = '<div align="center"><br><br>
		<table width="350" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFC1C1">
		<tr>
		<td align="center"><br>Nie znaleziono okna o podanym numerze tabliczki.<br>Prosimy o kontakt telefoniczny.<br><br></td>
		</tr>
		</table></div>
		<SCRIPT LANGUAGE="JavaScript">
			setTimeout(\'checkBrowser()\', 60*60);
			function checkBrowser() {
				window.history.back();
				}
		</SCRIPT>
		';
		}
?>
<br>
<?
if($showInfoDiv == false) {
	echo $content;
	}else{
?>
<div align="right" style="border:1px solid #999999;background-color:#EEEEEE;">
<table style="display:block; padding: 10px; font-size:9px; width:510; height: 100%;" id="wstep_info">
<tr>
	<td>Witamy!</td>
</tr>
<tr>
	<td style="vertical-align:middle;">Prosimy kliknąć na rysunek explozyjny znajdujący się poniżej.</td>
</tr>
</table>
<?
for($i = 0; $i < count($_czesci); $i++) {
	if($_czesci[$i]["access_www"] == 1) {
?>
	<table style="display:none; padding: 10px; font-size:9px; width:510; height: 100%;" id="infoczesci_<?=$_czesci[$i]["nr_explozyjny"];?>">
	<tr>
		<td style="font-size:12px;text-align:left;"><?=$_czesci[$i]["indeks"];?> - <?=$_czesci[$i]["nazwa"];?></td>
	</tr>
	<? if($_czesci[$i]["opis"] != '') { ?><tr><td style="text-align:left;"><b>Opis:</b> <?=$_czesci[$i]["opis"];?></td></tr><? } ?>
	<? if($_czesci[$i]["cena"] != '') { ?><tr><td style="text-align:left;"><b>Cena:</b> <?=$_czesci[$i]["cena"];?>&nbsp;PLN</td></tr><? } ?>
	<tr>
	    <td style='text-align:right;vertical-align:bottom;'><a href='#info' onClick="add('<?=$_czesci[$i]["nr_explozyjny"];?>');">Zamów część</a></td>
	</tr>
	</table>
<?
		}
	}
?>
</div>
<?=$images.'<br><br>';?>
<div align="center">
<img id="image" usemap="#map" style="display:none" border="0">
<?=$maps;?>
<br><br>
<a name="#info"></a>
<table width=510 id='zakladka1' style="display:none">
<tr>
	<td style="text-align:left"><b>Zamówienie</b></td>
</tr>
<tr style="height:1px;background-color:#999999">
	<td></td>
</tr>
</table>
<table align="center" id="zamowienie_form" style="width:500">
<form method="POST" action="<?=$self;?>">
<tr style="display:none;border:1px solid #CCCCCC;" id="czesci_header">
	<td><b>Część</b></td>
	<td><b>Cena</b></td>
	<td colspan="2"><b>Ilość</b></td>
</tr>
<?
for($i = 0; $i < count($_czesci); $i++) {
	if($_czesci[$i]["access_www"] == 1) {
?>
<tr style="display:none;" id="czesci_<?=$_czesci[$i]["nr_explozyjny"];?>">
	<td id="nazwa_col" style="width:420px;border:1px solid #CCCCCC;"><?=$_czesci[$i]["indeks"];?> - <?=$_czesci[$i]["nazwa"];?><br>
	</td>
	<td style="width:30px;border:1px solid #CCCCCC;">
	<input type="hidden" value="<?=$_czesci[$i]["cena"];?>" id="czesci_cenaszt_<?=$_czesci[$i]["nr_explozyjny"];?>">
	<input type="text" disabled size="8" value="<?=$_czesci[$i]["cena"];?>" id="czesci_cena_<?=$_czesci[$i]["nr_explozyjny"];?>" />
	<input type="hidden" id="czesci_cenavalue_<?=$_czesci[$i]["nr_explozyjny"];?>" name="cena[<?=$_czesci[$i]["id_bp_czesci"];?>]" />
	</td>
	<td style="width:30px;">
	<input type="text" size="8" value="0" id="czesci_ilosc_<?=$_czesci[$i]["nr_explozyjny"];?>" name="ilosc[<?=$_czesci[$i]["id_bp_czesci"];?>]" onKeyUp="zmiana_ilosci('<?=$_czesci[$i]["nr_explozyjny"];?>')"/>
	<input type="hidden" name="czesci[<?=$_czesci[$i]["id_bp_czesci"];?>]" value="<?=$_czesci[$i]["indeks"];?> - <?=$_czesci[$i]["nazwa"];?>" />
	<input type="hidden" id="zamowienie_<?=$_czesci[$i]["nr_explozyjny"];?>" value="0" />
	</td>
	<td style="width:20px;"><a href="#" onclick="return remove('<?=$_czesci[$i]["nr_explozyjny"];?>')"><img src="<?=$UIMAGES;?>/action_remove.gif" border="0" /></a></td>
</tr>
<?
		}
	}
?>
<tr style="display:none;border:1px solid #CCCCCC;" id="dostawca">
	<td style='text-align:left;' colspan="4">
	<select name="select_dostawca" id='select_dostawca' onChange='change_uwagi_dostawca()'>
<? for($i = 0; $i < count($dostawcy); $i++) { ?>
		<option value="<?=$dostawcy[$i]["id_zgloszenie_serwisowe_dostawcy"];?>"><?=$dostawcy[$i]["nazwa"];?></option>
<?	} ?>
	</select>
	<br>
	<div id="uwagi_dostawca" style="width:500px"></div>
	<input type="hidden" name="uwagi_dostawca" id="hidden_uwagi_dostawca" value="0" />
	<input type="hidden" value="Odbiór osobisty" name="nazwa_dostawcy" id="nazwa_dostawcy" />
	</td>
</tr>
<tr style="display:none;border:1px solid #CCCCCC;" id="czesci_suma">
	<td style="text-align: right;" colspan="2">Suma</td>
	<td>
	<input disabled type="text" size="8" id="suma" />
	<input type="hidden" id="sumavalue" name="suma"/></td>
	<td>PLN</td>
</tr>
</table>

<center>
<table width=510 id='zakladka3' style="display:none">
<tr>
	<td style="text-align:left"><b>Informacje o produkcie</b></td>
</tr>
<tr style="height:1px;background-color:#999999">
	<td></td>
</tr>
</table>
</center>

<div id="produkt_form" style="display:none">
<table>
<tr>
	<td class="opis">Nazwa produktu:</td>
	<td><?=$_POST['nazwa_nr'];?><?=$_POST['nazwa_nr2'];?>
	<input type="hidden" value="<?=$_POST['nazwa_nr'];?><?=$_POST['nazwa_nr2'];?>" name="produkt_nazwa[]" style="width:200px">
	</td>
</tr>
<tr>
	<td id="product_tabliczka_label" class="opis">Numer tabliczki znamionowej :</td>
	<td><?=$_POST['nazwa_nr'];?><?=$_POST['nazwa_nr2'];?>
	<input type="hidden" value="<?=$_POST['nazwa_nr'];?>" name="nazwa_nr[]" style="width:73px">
	<input type="hidden" value="<?=$_POST['nazwa_nr2'];?>" name="nazwa_nr2[]" style="width:73px">
	</td>
</tr>
<tr>
	<td class="opis" id="data_nabycia_towaru_label">Data nabycia produktu <span style="color:Red;">*</span>:</td>
	<td><input type="text" id="data_nabycia_towaru" name="data_nabycia_towaru[]" style="width:73px"><br>RRRR-MM-DD (np. 2004-05-31)</td>
</tr>
<tr>
	<td class="opis" id="data_montazu_label">Data montażu produktu <span id="data_montazu_gwiazdka" style="color:Red;">*</span>:</td>
	<td><input type="text" id="data_montazu" name="data_montazu[]" style="width:73px"><br>RRRR-MM-DD (np. 2004-05-31)<br>
	lub proszę wybrać w polu 'Kto montował' wartość 'Brak montażu'</td>
</tr>
<tr>
	<td class="opis">Kto montował:</td>
	<td>
	<select name="montaz[]" style="width:150px" onchange="ustawDateMontazu(this, 'data_montazu');">
		<option value="1">Montaż własny</option>
		<option value="2">Firma dekarska</option>
		<option value="3">Brak montażu</option>
	</select>
	</td>
</tr>
<tr>
	<td class="opis">Pokrycie dachowe:</td>
	<td>
	<select name="pokrycie_dachowe[]" style="width:150px">
		<option value="1">dachówka</option>
		<option value="2">blacha</option>
		<option value="3">inne</option>
	</select>
	</td>
</tr>
<tr>
	<td class="opis">Rozmiar:</td>
	<td><input type="text" name="rozmiar[]" style="width:73px"></td>
</tr>
<tr>
	<td class="opis">Wersja:</td>
	<td><input type="text" name="wersja[]" style="width:73px"></td>
</tr>
<tr>
	<td class="opis" id="ilosc_produktow_label">Ilość <span style="color:Red;">*</span>:</td>
	<td><input type="text" id="ilosc_produktow" name="ilosc_produktow[]" style="width:73px"></td>
</tr>
<tr>
	<td class="opis">Grupa produktów:</td>
	<td>
	<select name="id_bp_produkty[]" style="width:150px">
<? for($i = 0; $i < count($produkty); $i++) { ?>
	<option value="<?=$produkty[$i]["id_bp_produkty"];?>"><?=$produkty[$i]["opis"];?></option>
<?	} ?>
	</select>
	</td>
</tr>
</table>

<center>
<table width=510 id='zakladka2' style="display:none">
<tr>
	<td style="text-align:left"><b>Dane kontaktowe</b></td>
</tr>
<tr style="height:1px;background-color:#999999">
	<td></td>
</tr>
</table>
</center>

<table align="center" id="zamowienie_dane" style="display:none">
<tr>
	<td colspan="2" align="left"><b>Dane zgłaszającego</b>:<br><br></td>
</tr>
<tr>
	<td id="nazwa_label" class="opis">Imię i nazwisko / Nazwa firmy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="nazwa" id="nazwa" style="width:150px"></td>
</tr>
<tr>
	<td id="ulica_label" class="opis">Ulica <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="ulica" id="ulica" style="width:150px" onchange="kopiuj_zmiany('ulica')"></td>
</tr>
<tr>
	<td id="nr_domu_label" class="opis">Nr budynku <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="nr_domu" id="nr_domu" style="width:30px" onchange="kopiuj_zmiany('nr_domu')"></td>
</tr>
<tr>
	<td class="opis">Nr mieszkania:</td>
	<td align="left"><input type="text" name="nr_mieszkania" id="nr_mieszkania" style="width:30px" onchange="kopiuj_zmiany('nr_mieszkania')"></td>
</tr>
<tr>
	<td class="opis" id="kod_pocztowy_label">Kod pocztowy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="kod_pocztowy" id="kod_pocztowy" style="width:150px" onchange="kopiuj_zmiany('kod_pocztowy')"></td>
</tr>
<tr>
	<td id="miasto_label" class="opis">Miasto <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="miasto" id="miasto" style="width:150px" onchange="kopiuj_zmiany('miasto')"></td>
</tr>
<tr>
	<td class="opis">Kraj <span style="color:Red;">*</span>:</td>
	<td align="left">
	<select name="id_kraje" id="id_kraje" style="width:150px" onchange="kopiuj_zmiany('id_kraje')" >
<? for($i = 0; $i < count($kraje); $i++) { ?>
	<option value="<?=$kraje[$i]["id_kraje"];?>"><?=$kraje[$i]["nazwa"];?></option>
<?	} ?>
	</select>
	</td>
</tr>
<tr>
	<td id="t1_label" class="opis">Numer telefonu <span style="color:Red;">*</span>:</td>
	<td align="left" style="white-space:nowrap">
	<input type="text" name="t1_kraj" value="48" id="t1_kraj" style="width:20px">
	<input type="text" name="t1_miasto" id="t1_miasto" style="width:20px">
	<input type="text" name="t1_numer" id="t1_numer" style="width:78px">
	<input type="text" name="t1_wewnetrzny" id="t1_wewnetrzny" style="width:20px">
	<br>
	<small>kraj - miasto - numer - wewnętrzny</small></td>
</tr>
<tr>
	<td id="email_label" class="opis">E-mail <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="email" id="email" style="width:150px"></td>
</tr>
<tr>
	<td class="opis">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="opis">Osoba kontaktowa:</td>
	<td align="left"><input type="text" name="osoba_kontaktowa" style="width:150px"></td>
</tr>
<tr>
	<td id="telefon_kontaktowy_label" class="opis">Telefon kontaktowy:</td>
	<td style="white-space:nowrap" align="left">
	<input type="text" name="telefon_kontaktowy_kraj" value="48" id="telefon_kontaktowy_kraj" style="width:20px">
	<input type="text" name="telefon_kontaktowy_miasto" id="telefon_kontaktowy_miasto" style="width:20px">
	<input type="text" name="telefon_kontaktowy_numer" id="telefon_kontaktowy_numer" style="width:78px">
	<input type="text" name="telefon_kontaktowy_wewnetrzny" id="telefon_kontaktowy_wewnetrzny" style="width:20px">
	<br>
	<small>kraj - miasto - numer - wewnętrzny</small></td>
</tr>
<tr>
	<td id="regulamin_label" class="opis">Akceptacja regulaminu?:</td>
	<td style="white-space:nowrap" align="left"><input type="checkbox" name="regulamin" id="regulamin">zgadzam się</td>
</tr>
<tr>
	<td colspan="2" align="left"><br><b>Adres zamontowania produktu</b>:<br><br></td>
</tr>
<tr>
	<td class="opis"><input type="checkbox" name="kopiuj" id="kopiuj" onclick="kopiuj_adres()"></td>
	<td align="left"><a href="javascript:void(0);" onclick="kopiuj_adres_link()" >Dane jak wyżej</a></td>
</tr>
<tr>
	<td class="opis" id="okno_ulica_label">Ulica <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_ulica" id="okno_ulica" style="width:150px"></td>
</tr>
<tr>
	<td class="opis" id="okno_nr_domu_label">Nr budynku <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_nr_domu" id="okno_nr_domu" style="width:30px"></td>
</tr>
<tr>
	<td class="opis">Nr mieszkania:</td>
	<td align="left"><input type="text" name="okno_nr_mieszkania" id="okno_nr_mieszkania" style="width:30px"></td>
</tr>
<tr>
	<td class="opis" id="okno_kod_pocztowy_label">Kod pocztowy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_kod_pocztowy" id="okno_kod_pocztowy" style="width:150px"></td>
</tr>
<tr>
	<td class="opis" id="okno_miasto_label">Miasto <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_miasto" id="okno_miasto" style="width:150px"></td>
</tr>
<tr>
	<td class="opis">Kraj:</td>
	<td align="left">
	<select name="okno_id_kraje" id="okno_id_kraje" style="width:150px">
<? for($i = 0; $i < count($kraje); $i++) { ?>
	<option value="<?=$kraje[$i]["id_kraje"];?>"><?=$kraje[$i]["nazwa"];?></option>
<?	} ?>
	</select>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="opis"><b>Proponowany termin dostawy:</b></td>
	<td align="left"><input type="text" name="termin_dostawy" id="termin_dostawy" style="width:150px"></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="left"><input type="hidden" name="mode" value="details"><input type="submit" class="input" value="Zamów..." onclick="return validate()" id="submit" style="display:none"></td>
</tr>
</form>
</table>
</div>
<?
	}
	}

if(!isset($_POST['mode'])) { ?>
<div align="center">
<table border="0" cellspacing="0" cellpadding="0">
<form method="POST" action="<?=$self;?>" onsubmit="return walidacjaTabliczki();">
<tr>
	<td id="nazwa_label" class="opis">Numer tabliczki znamionowej:</td>
	<td align="left">
	<input type="text" name="nazwa_nr" style="width:73px" id="nazwa_nr">
	<input type="text" name="nazwa_nr2" maxlength="4" size="4"  id="nazwa_nr2">
	</td>
</tr>
<tr>
	<td class="opis"></td>
	<td align="left"><input type="hidden" name="mode" value="results"><input type="submit" value="Szukaj" style="width:150px"></td>
</tr>
</form>
</table>
</div>

<script type="text/javascript">
<!--
document.getElementById('nazwa_nr').focus();
-->
</script>
<? } ?>
