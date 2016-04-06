<?
$year_for_query = substr($_POST['nazwa_nr2'], 1, 1) . substr($_POST['nazwa_nr2'], 0, 1);
$week_for_query = substr($_POST['nazwa_nr2'], 3, 1) . substr($_POST['nazwa_nr2'], 2, 1);

$date_for_query = '(YEARWEEK(data_poczatek) <= "20'.$year_for_query.$week_for_query.'" AND data_koniec = "0000-00-00" OR YEARWEEK(data_koniec) > "20'.$year_for_query.$week_for_query.'" AND YEARWEEK(data_poczatek) <= "20'.$year_for_query.$week_for_query.'")';

$query_text = 'SELECT id_bp_okna FROM bp_okna WHERE indeks = "'.$_POST['nazwa_nr'].'" AND dostepnosc = 1 AND '.$date_for_query.' ORDER BY data_poczatek DESC';

$id_bp_okna = fetch_single($query_text);

if($id_bp_okna) {
	$query_text = 'SELECT ' .
						'bp_okna.id_bp_konstrukcje, ' .
						'CONCAT(bp_okna.indeks, \' \', bp_okna.nazwa) as `produkt`, ' .
						'CONCAT(IF(bp_okna.s IS NOT NULL, bp_okna.s, \'..\'), \' / \', IF(bp_okna.w IS NOT NULL, bp_okna.w, \'..\')) AS `rozmiar`, ' .
						'CONCAT(bp_numery.nazwa, \' - \', bp_numery.opis) AS `wersja`, ' .
						'bp_numery.id_bp_numery ' .
					'FROM bp_okna ' .
					'LEFT JOIN bp_numery USING(id_bp_numery) ' .
					'WHERE id_bp_okna = ' . $id_bp_okna . ' ' .
					'LIMIT 1';
	$dane = fetch_array($query_text);
	$id_bp_konstrukcje = $dane[0]['id_bp_konstrukcje'];
	
	if($j != 0) {
		$query_text = 'SELECT nazwa ' .
							'FROM bp_okna_nazwa ' .
							'WHERE id_bp_okna = ' . $id_bp_okna . ' ' .
								'AND id_jezyki = ' . $j;
		$tlum = fetch_single($query_text);
		if(strlen($tlum) > 0) {
			$dane[0]['produkt'] = $tlum;
			}
		}
	
	$nazwa_produktu = $dane[0]['produkt'];
	$wersja = $dane[0]['wersja'];
	$id_bp_numery = $dane[0]['id_bp_numery'];
	$rozmiar = $dane[0]['rozmiar'];
	
	if($id_bp_konstrukcje) {
		$query_text = 'SELECT bp_explozyjne.id_bp_explozyjne ' .
							'FROM bp_konstrukcje_explozyjne ' .
							'LEFT JOIN bp_explozyjne USING (id_bp_explozyjne) ' .
							'WHERE id_bp_konstrukcje = ' . $id_bp_konstrukcje . ' ' .
							'AND bp_explozyjne.niedostepny = 0 ' .
							'ORDER BY bp_konstrukcje_explozyjne.data_poczatek DESC';
		$id_bp_explozyjne = fetch_single($query_text);
		
		if($id_bp_explozyjne) {
			$files = fetch_array(
    			        'SELECT a.id_bp_explozyjne_pliki, b.opis ' .
						'FROM bp_explozyjne_pliki a ' .
						'LEFT OUTER JOIN bp_explozyjne_pliki__jezyki b ON ((a.id_bp_explozyjne_pliki = b.id_bp_explozyjne_pliki OR b.id_bp_explozyjne_pliki IS NULL) AND id_jezyki = ' . _LANG . ') ' .
						'WHERE a.id_bp_explozyjne = ' . $id_bp_explozyjne . ' ' .
							'AND mime LIKE "%image%"');
			/*
			echo '<pre>';
			print_r($files).'<br><br>';
			echo '</pre>';
			*/
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
						'WHERE oc.id_bp_okna = ' . $id_bp_okna . '
							AND
							bc.id_firmy = ' . _ID_SERWIS . '
							AND
							bc.id_bp_waluty = ' . _ID_WALUTY . '
							AND ' . str_replace('data', 'oc.data', $date_for_query);
				#echo $query_text.'<br><br>';
				
				$czesci = fetch_array($query_text);
				
				if(is_array($czesci) && count($czesci) > 0) {
					foreach($czesci as $czesciK => $czesc) {
						if($j != 0) {
							// nazwa
							$query_text = 'SELECT nazwa ' .
										'FROM bp_czesci_nazwa ' .
										'WHERE id_bp_czesci = ' . $czesc['id_bp_czesci'] . ' ' .
												'AND id_jezyki = ' . $j;
							$tlum = fetch_single($query_text);
							if(strlen($tlum) > 0) {
								$czesci[$czesciK]['nazwa'] = $tlum;
								}
							// opis
							$query_text = 'SELECT opis ' .
										'FROM bp_czesci_opis ' .
										'WHERE id_bp_czesci = ' . $czesc['id_bp_czesci'] . ' ' .
												'AND id_jezyki = ' . $j;
							$tlum = fetch_single($query_text);
							if(strlen($tlum) > 0) {
								$czesci[$czesciK]['opis'] = $tlum;
								}
							}
						if(strlen($czesc['nr_explozyjny']) > 0) {
							$nr_explozyjny[] = $czesc['nr_explozyjny'];
							}
						}
					
					$czesci = $czesci;
					
					foreach($files as $file) {
						$id_bp_explozyjne_pliki = $file['id_bp_explozyjne_pliki'];
						$opis = $file['opis'];
						
						$images .= '<td style="text-align:center"><img style="height:100px;cursor:pointer;" class="ramka" onclick="show_image('.$id_bp_explozyjne_pliki.')" src="'._FILES_URL.'bp_explozyjne_pliki_'.$id_bp_explozyjne_pliki.'" border="0" hspace="15" vspace="2" /><br>'.$opis.'</td>';
						
						if(is_array($nr_explozyjny) && count($nr_explozyjny) > 0) {
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
								
								if(is_array($map) && count ($map) > 0) {
									
									$maps .= "\n\n<map name='map_".$id_bp_explozyjne_pliki."' id='map_".$id_bp_explozyjne_pliki."'>\n";
									
									foreach ($map as $k => $v) {
										$map_access_www = 1;
										$coords = explode(',', $v['coords']);
										unset($coords[(count($coords) - 1)]);
										unset($coords[0]);
										$map_coords = implode(',', array_keys_recount($coords));
										
										
										$maps .= '<area shape="poly" coords="'.$map_coords.'" href="javascript:void(0);"';
										if($map_access_www == '1') {
											#$maps .= ' onclick="return display(\''.$v['nr_explozyjny'].'\')"';
											
											$_czesci = $czesci;
											if(!is_array($_czesci) && !is_object($_czesci)) { settype($_czesci, 'array'); }
											
											
											foreach($_czesci as $_czesci_1) {
												if($_czesci_1['nr_explozyjny'] == $v['nr_explozyjny']) {
													$maps .= ' onmouseout="return nd();" onclick="return overlib(\''.$_czesci_1['indeks'].' - '.$_czesci_1['nazwa'].'<br>';
													$maps .= '<strong>Cena:</strong> '.$_czesci_1['cena'].' PLN<br>';
													#$maps .= "<a href=\'javascript:void(0);\' onClick=\'add(".$v['nr_explozyjny'].");\' style=\'color:#000000;\'><strong>Zamów część</strong></a>";
													$maps .= "<a href=\'javascript:void(0);\' onClick=add(\'".$v['nr_explozyjny']."\');cClick();show_lista(); style=\'color:#000000;\'><strong>Zamów część</strong></a>";
													$maps .= '\',STICKY,CAPTION,\'\',BGCOLOR,\'#ffffff\',FGCOLOR,\'#D8D8D8\',TEXTCOLOR,\'#636363\',WIDTH,350,CAPICON,\''.$UIMAGES.'/COMMON/layout/logo/fakro/okna_fakro_male.gif\', CAPTION, \'CAPICON Example\',CAPTION,\'&nbsp;\',CLOSETEXT,\'zamknij\',CENTER);" href="javascript:void(0);"';
													$maps .= ' alt="'.$_czesci_1['nazwa'].'"';
													}
												}
											}else{
											$maps .= ' onclick="alert(\'Część niedostępna. Prosimy o kontakt telefoniczny.\');"';
											}
										$maps .= ">\n";
										}
									
									$maps .= "</map>\n";
								
								$kraje = fetch_list('SELECT id_kraje, nazwa FROM kraje WHERE _is_adding = 0 ORDER BY nazwa');
								$dostawcy = fetch_array('SELECT id_zgloszenie_serwisowe_dostawcy, nazwa, uwagi FROM zgloszenie_serwisowe_dostawcy WHERE _is_adding = 0 ORDER BY nazwa ASC');
								#$produkty = fetch_list('SELECT id_bp_produkty, opis FROM bp_produkty WHERE _is_adding = 0 ORDER BY nazwa');
								$maps = $maps;
								$images_all = '<table cellpadding=5><tr>'.$images.'</tr></table>';
								}
							}
						}
					$_SESSION['nazwa_nr'] = $_POST['nazwa_nr'];
					$_SESSION['nazwa_nr2'] = $_POST['nazwa_nr2'];
					$_SESSION['nazwa_nr3'] = $_POST['nazwa_nr3'];
					$_SESSION['nazwa_nr4'] = $_POST['nazwa_nr4'];
					$showInfoDiv = true;
					}
				}
			}
		}
	}else{
	$showInfoDiv = false;
	$content = 'Przepraszamy produkt niedostępny.<br>Prosimy o kontakt z działem serwisu <a href="mailto:serwis@fakro.pl">serwis@fakro.pl</a>';
	$content .= '<br><br><a href="'.$_action.'"><strong>cofnij</strong></a>';
	
	unset($_SESSION['bp_form_data'][$_POST['nazwa_nr'].'-'.$_POST['nazwa_nr2']]);
	unset($_SESSION['nazwa_nr']);
	unset($_SESSION['nazwa_nr2']);
	
	/*
	if(isset($_SESSION['bp_form_data'])) $content .= '&action=continue';
	
	$content .= '">Powrót</a>';

	$content = $content;
	*/
	}
?>

<script type="text/javascript">
<!--
function show_lista() {
	document.location.href="#lista";
	}

function validate_zamowienie() {
<? for($i = 0; $i < count($czesci); $i++) { ?>
	count = el('ilosc[<? echo $czesci[$i]['id_bp_czesci']; ?>]');
	bool = el('zamowienie_<? echo $czesci[$i]['nr_explozyjny']; ?>').value;
	if(bool == '1' && !is_number(count.value)) {
		alert('Musisz podać poprawną ilość zamawianej częsci!');
		count.focus();
		return false;
		}
<? } ?>
	return true;
	}

function show_image(id) {
	img = el('image');
	img.style.display = '';
	img.src = '<?=_FILES_URL;?>bp_explozyjne_pliki_' + id;

	if(current) {
		c_map = el('map');
		map.name = 'map_' + current;
		map.id = 'map_' + current;
		}

	map = el('map_' + id);
	map.name = 'map';
	map.id = 'map';
	current = id;

	if(explozyjny) el('infoczesci_' + explozyjny).style.display = 'none';
    //el('wstep_info').style.display = '';
	}

function add(id) {
	if (el('czesci_' + id).style.display != '') form_count++;
	
	el('czesci_' + id).style.display = '';
	<? for($i = 0; $i < count($czesci); $i++) { ?>
		if (id=="<? echo $czesci[$i]['nr_explozyjny']; ?>") el('czesci_ilosc_' + id).value = '<? echo $czesci[$i]['ilosc']; ?>';
	<? } ?>
	
	el('zamowienie_' + id).value = 1;
	if(form_count > 0) {
		el('submit_buttons').style.display = '';
		el('czesci_header').style.display = '';
		el('czesci_suma').style.display = '';
		el('zakladka1').style.display = '';
		el('zakladka3').style.display = '';
		el('produkt_form').style.display = '';
		}
	zmiana_ilosci(id);
	return false;
	}

function sum_cena() {
	suma = '0';
	<? for($i = 0; $i < count($czesci); $i++) { ?>
		<? if($czesci[$i]['access_www'] == 1) { ?>
		if(el('czesci_<? echo $czesci[$i]['nr_explozyjny']; ?>').style.display == '') {
			suma = 1 * (suma + (el('czesci_cena_<? echo $czesci[$i]['nr_explozyjny']; ?>').value * 1));
			}
		<? } ?>
	<? } ?>
	
	el('suma').value = suma;
	el('sumavalue').value = suma;
	}

function change_uwagi_dostawca() {
    option = el('select_dostawca');
	<? for($i = 0; $i < count($dostawcy); $i++) { ?>
		if(option[option.selectedIndex].value=='<? echo $dostawcy[$i]['id_zgloszenie_serwisowe_dostawcy']; ?>') {
			el('uwagi_dostawca').innerHTML = '<? echo $dostawcy[$i]['uwagi']; ?>';
			el('hidden_uwagi_dostawca').value = '<? echo $dostawcy[$i]['uwagi']; ?>';
			}
	<? } ?>
    if(option[option.selectedIndex].value==0) {
		el('uwagi_dostawca').value = 0;
		el('hidden_uwagi_dostawca').value = 0;
		}
	el('nazwa_dostawcy').value = option[option.selectedIndex].text;
	sum_cena();
	}
-->
</script>

<?
if($showInfoDiv != false) {
?>

Kliknij na rysunku, aby wybrać części do zamówienia:

<? } ?>

<br />
<div align="center">
<? echo $content; ?>
<br>
<? echo $images_all; ?>
<br>
<? echo $maps; ?>
</div>

<br>

<div align="center">
<img id="image" usemap="#map" style="display:none" border="0" />

<table width="100%" class="list_table" id="zakladka1" style="display:none">
<form action="<?=$_action;?>" method="post">
<col>
<col width="40">
<col width="90">
<col width="40">
<thead>
<tr>
	<td colspan="4"><a name="lista"></a></td>
</tr>
<tr id="czesci_header" style="display:none;">
	<td><b>Część</b></td>
	<td><b>Cena</b></td>
	<td><b>Ilość</b></td>
	<td>&nbsp;</td>
</tr>
</thead>
<tbody>
<? for($i = 0; $i < count($czesci); $i++) { ?>
	<? if($czesci[$i]['access_www'] == 1) { ?>
<tr id="czesci_<? echo $czesci[$i]['nr_explozyjny']; ?>" style="display:none;">
	<td id="nazwa_col"><? echo $czesci[$i]['indeks'].' - '.$czesci[$i]['nazwa']; ?><br /></td>
	<td>
	<input type="hidden" value="<? echo $czesci[$i]['cena']; ?>" id="czesci_cenaszt_<? echo $czesci[$i]['nr_explozyjny']; ?>">
	<input type="text" disabled size="8" value="<? echo $czesci[$i]['cena']; ?>" id="czesci_cena_<? echo $czesci[$i]['nr_explozyjny']; ?>">
	<input type="hidden" id="czesci_cenavalue_<? echo $czesci[$i]['nr_explozyjny']; ?>" name="cena[<? echo $czesci[$i]['id_bp_czesci']; ?>]">
	<input type="hidden" name="cena_jednostkowa[<? echo $czesci[$i]['id_bp_czesci']; ?>]" value="<? echo $czesci[$i]['cena']; ?>">
	</td>
	<td>
	<input type="text" size="8" value="0" id="czesci_ilosc_<? echo $czesci[$i]['nr_explozyjny']; ?>" name="ilosc[<? echo $czesci[$i]['id_bp_czesci']; ?>]" onKeyUp="zmiana_ilosci('<? echo $czesci[$i]['nr_explozyjny']; ?>')">
	<input type="hidden" name="czesci[<? echo $czesci[$i]['id_bp_czesci']; ?>]" value="<? echo $czesci[$i]['indeks']; ?> - <? echo $czesci[$i]['nazwa']; ?>">
	<input type="hidden" id="zamowienie_<? echo $czesci[$i]['nr_explozyjny']; ?>" value="0" />
	</td>
	<td align="center"><a href="#" onclick="return remove('<? echo $czesci[$i]['nr_explozyjny']; ?>')">X</a></td>
</tr>
	<? } ?>
<? } ?>
<tr id="czesci_suma" style="display:none;">
	<td colspan="2" style="text-align: right;">Suma</td>
	<td><input disabled type="text" size="8" id="suma" /><input type="hidden" id="sumavalue" name="suma"/></td>
	<td>PLN</td>
</tr>
</tbody>
</table>

<center>
<table width="100%" id="zakladka3" style="display:none">
<tr>
	<td align="left"><b>Informacje o produkcie</b></td>
</tr>
</table>
</center>

<div id="produkt_form" style="display:none">
<table>
<col width="180">
<col>
<tbody>
<tr>
	<td valign="top">Nazwa produktu:</td>
	<td><? echo $nazwa_produktu; ?> <input type="hidden" value="<? echo $nazwa_produktu; ?>" name="produkt_nazwa[]" style="width:200px"></td>
</tr>
<tr>
	<td valign="top" id="product_tabliczka_label">Numer tabliczki znamionowej :</td>
	<td>
	<? echo $_SESSION['nazwa_nr']; ?> - <? echo $_SESSION['nazwa_nr2']; ?>
  <? if(strlen($_SESSION['nazwa_nr3'])) echo " - ".$_SESSION['nazwa_nr3']; ?>
	<? if(strlen($_SESSION['nazwa_nr4'])) echo " - ".$_SESSION['nazwa_nr4']; ?>
	<input type="hidden" value="<? echo $_SESSION['nazwa_nr']; ?>" name="nazwa_nr" style="width:73px">
	<input type="hidden" value="<? echo $_SESSION['nazwa_nr2']; ?>" name="nazwa_nr2" style="width:73px">
	<input type="hidden" value="<? echo $_SESSION['nazwa_nr3']; ?>" name="nazwa_nr3" style="width:73px">
	<input type="hidden" value="<? echo $_SESSION['nazwa_nr4']; ?>" name="nazwa_nr4" style="width:73px">
	</td>
</tr>
<tr>
	<td valign="top" id="data_nabycia_towaru_label">Data nabycia produktu <span style="color:Red;">*</span>:</td>
	<td><input type="text" name="data_nabycia_towaru[]" id="data_nabycia_towaru" value="" style="width:100px" readonly>
	<img src="<?=$_path?>js/jscalendar/img.gif" id="data_nabycia_towaru_b" style="cursor: pointer; border: 1px solid green;" title="podglšd kalendarza" onmouseover="this.style.background='green';" onmouseout="this.style.background=''" />
	<script type="text/javascript">
		Calendar.setup({
			inputField	:	"data_nabycia_towaru",				// id of the input field
			ifFormat	:	"%Y-%m-%d",			// format of the input field
			button		:	"data_nabycia_towaru_b",			// trigger for the calendar (button ID)
			singleClick	:	false,					// double-click mode
			firstDay	:	1,						// numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
			step		:	1,						// show all years in drop-down boxes (instead of every other year as default)
			dateStatusFunc : disallowDate
		});
	</script>
	</td>
</tr>
<tr>
	<td valign="top" id="data_montazu_label">Data montażu produktu <span id="data_montazu_gwiazdka" style="color:Red;">*</span>:</td>
	<td><input type="text" name="data_montazu[]" id="data_montazu" value="" style="width:100px" readonly>
	<img src="<?=$_path?>js/jscalendar/img.gif" id="data_montazu_b" style="cursor: pointer; border: 1px solid green;" title="podglšd kalendarza" onmouseover="this.style.background='green';" onmouseout="this.style.background=''" />
	<script type="text/javascript">
		Calendar.setup({
			inputField	:	"data_montazu",				// id of the input field
			ifFormat	:	"%Y-%m-%d",			// format of the input field
			button		:	"data_montazu_b",			// trigger for the calendar (button ID)
			singleClick	:	false,					// double-click mode
			firstDay	:	1,						// numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
			step		:	1,						// show all years in drop-down boxes (instead of every other year as default)
			dateStatusFunc : disallowDate
		});
	</script>
	</td>
</tr>
<tr>
	<td valign="top">Kto montował:</td>
	<td>
	<select name="montaz[]" style="width:150px" onchange="ustawDateMontazu(this, 'data_montazu');">
		<option value="1">Montaż własny</option>
		<option value="2">Firma dekarska</option>
		<option value="3">Brak montażu</option>
	</select>
	</td>
</tr>
<tr>
	<td valign="top">Pokrycie dachowe:</td>
	<td>
	<select name="pokrycie_dachowe[]" style="width:150px">
		<option value=""></option>
		<option value="1">dachówka</option>
		<option value="2">blacha</option>
		<option value="3">inne</option>
	</select>
	</td>
</tr>
<tr>
	<td valign="top">Rozmiar:</td>
	<td><input type="text" name="rozmiar[]" value="<? echo $rozmiar; ?>" style="width:73px"></td>
</tr>
<tr>
	<td valign="top">Wersja:</td>
	<td><? echo $wersja; ?> <input type="hidden" name="id_bp_numery[]" value="<? echo $id_bp_numery; ?>"></td>
</tr>
<input type="hidden" name="user_action" id="userAction" />
<tr>
	<td colspan="2">
	<input type="submit" class="button" value="Kontynuuj zakupy" onclick="el('userAction').value='continue';return validate();" />
	<input type="submit" class="button" value="Przejdź do podsumowania" onclick="el('userAction').value='summary';return validate();" />
	</td>
</tr>
</form>
</tbody>
</table>

</div>
<p id="submit_buttons" style="display:none;"></p>
</div>
