<?
$config = array(
	'_ID_OSOBY' => '29',
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

<style type="text/css">
<!--
a
		{
	color: #222222;
	font-weight: bold;
}

a:hover
		{
	color: #777777;
}

input, select, textarea
		{
	background-color: #EAFAFD;
	border: 1px outset #0066CC;
	color: #000000;
	font-family: Verdana,sans-serif;
	font-size: 10px;
	margin: 0px;
	padding: 0px;
}

small
		{
	font-size: 9px;
}

.opis
		{
	padding-right: 4px;
	text-align: right;
}
-->
</style>

<script src="<?=$INCLUDE_PATH?>/serwis2crm/common.js" type="text/javascript"></script>
<script src="<?=$INCLUDE_PATH?>/serwis2crm/zgloszenie.js" type="text/javascript"></script>

<?
if($_POST['mode'] != 'save') {
?>
<div align="center">
<table border="0" cellspacing="0" cellpadding="0">
<form method="post" action="<?=$self;?>" enctype="multipart/form-data">
<tr>
	<td colspan="2" align="left"><b>Dane zgГaszajБcego</b>:<br/><br/></td>
</tr>
<tr>
	<td id="nazwa_label" class="opis">Imiъ i nazwisko / Nazwa firmy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[nazwa]" id="nazwa" style="width:150px"></td>
</tr>
<tr>
	<td id="ulica_label" class="opis">Ulica <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[ulica]" id="ulica" style="width:150px" onchange="kopiuj_zmiany('ulica')"></td>
</tr>
<tr>
	<td id="nr_domu_label" class="opis">Nr budynku <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[nr_domu]" id="nr_domu" style="width:30px" onchange="kopiuj_zmiany('nr_domu')"></td>
</tr>
<tr>
	<td class="opis">Nr mieszkania:</td>
	<td align="left"><input type="text" name="zgloszenie[nr_mieszkania]" id="nr_mieszkania" style="width:30px" onchange="kopiuj_zmiany('nr_mieszkania')"></td>
</tr>
<tr>
	<td class="opis" id="kod_pocztowy_label">Kod pocztowy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[kod_pocztowy]" id="kod_pocztowy" style="width:150px" onchange="kopiuj_zmiany('kod_pocztowy')"></td>
</tr>
<tr>
	<td id="miasto_label" class="opis">Miasto <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[miasto]" id="miasto" style="width:150px" onchange="kopiuj_zmiany('miasto')"></td>
</tr>
<tr>
	<td class="opis">Kraj <span style="color:Red;">*</span>:</td>
	<td align="left">
	<select name="zgloszenie[id_kraje]" id="id_kraje" style="width:150px" onchange="kopiuj_zmiany('id_kraje')" >
<?
$idb->query("SELECT id_kraje, nazwa FROM kraje WHERE _is_adding = 0 ORDER BY nazwa");
for($i = 0; $i < $idb->rowcount(); $i++) {
	$idb->getvalues();
	
	echo "<option label=\"".$idb->row['nazwa']."\" value=\"".$idb->row['id_kraje']."\" ";
	
	if($idb->row['id_kraje'] == 167) echo "selected";
	
	echo ">".$idb->row['nazwa']."</option>";
	}
?>				
	</select>
	</td>
</tr>
<tr>
	<td id="t1_label" class="opis">Numer telefonu <span style="color:Red;">*</span>:</td>
	<td align="left" style="white-space:nowrap">
	<input type="text" name="zgloszenie[t1_kraj]" value="48" id="t1_kraj" style="width:20px">
	<input type="text" name="zgloszenie[t1_miasto]" id="t1_miasto" style="width:20px">
	<input type="text" name="zgloszenie[t1_numer]" id="t1_numer" style="width:78px">
	<input type="text" name="zgloszenie[t1_wewnetrzny]" id="t1_wewnetrzny" style="width:20px">
	<br>
	<small>kraj - miasto - numer - wewnъtrzny</small></td>
</tr>
<tr>
	<td id="email_label" class="opis">E-mail <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[email]" id="email" style="width:150px"></td>
</tr>
<tr>
	<td class="opis">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="opis">Osoba kontaktowa:</td>
	<td align="left"><input type="text" name="zgloszenie[osoba_kontaktowa]" style="width:150px"></td>
</tr>
<tr>
	<td id="telefon_kontaktowy_label" class="opis">Telefon kontaktowy:</td>
	<td style="white-space:nowrap" align="left">
	<input type="text" name="zgloszenie[telefon_kontaktowy_kraj]" value="48" id="telefon_kontaktowy_kraj" style="width:20px">
	<input type="text" name="zgloszenie[telefon_kontaktowy_miasto]" id="telefon_kontaktowy_miasto" style="width:20px">
	<input type="text" name="zgloszenie[telefon_kontaktowy_numer]" id="telefon_kontaktowy_numer" style="width:78px">
	<input type="text" name="zgloszenie[telefon_kontaktowy_wewnetrzny]" id="telefon_kontaktowy_wewnetrzny" style="width:20px">
	<br>
	<small>kraj - miasto - numer - wewnъtrzny</small></td>
</tr>
<tr>
	<td id="regulamin_label" class="opis">Akceptacja regulaminu?:</td>
	<td style="white-space:nowrap" align="left"><input type="checkbox" name="zgloszenie[regulamin]" id="regulamin">zgadzam siъ</td>
</tr>
<tr>
	<td colspan="2" align="left"><br><b>Adres zamontowania produktu</b>:<br><br></td>
</tr>
<tr>
	<td class="opis"><input type="checkbox" name="kopiuj" id="kopiuj" onclick="kopiuj_adres()"></td>
	<td align="left"><a href="javascript:void(0);" onclick="kopiuj_adres_link()" >Dane jak wyПej</a></td>
</tr>
<tr>
	<td class="opis" id="okno_ulica_label">Ulica <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[okno_ulica]" id="okno_ulica" style="width:150px"></td>
</tr>
<tr>
	<td class="opis" id="okno_nr_domu_label">Nr budynku <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[okno_nr_domu]" id="okno_nr_domu" style="width:30px"></td>
</tr>
<tr>
	<td class="opis">Nr mieszkania:</td>
	<td align="left"><input type="text" name="zgloszenie[okno_nr_mieszkania]" id="okno_nr_mieszkania" style="width:30px"></td>
</tr>
<tr>
	<td class="opis" id="okno_kod_pocztowy_label">Kod pocztowy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[okno_kod_pocztowy]" id="okno_kod_pocztowy" style="width:150px"></td>
</tr>
<tr>
	<td class="opis" id="okno_miasto_label">Miasto <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="zgloszenie[okno_miasto]" id="okno_miasto" style="width:150px"></td>
</tr>
<tr>
	<td class="opis">Kraj:</td>
	<td align="left">
	<select name="zgloszenie[okno_id_kraje]" id="okno_id_kraje" style="width:150px" >
<?
$idb->query("SELECT id_kraje, nazwa FROM kraje WHERE _is_adding = 0 ORDER BY nazwa");
for($i = 0; $i < $idb->rowcount(); $i++) {
	$idb->getvalues();
	
	echo "<option label=\"".$idb->row['nazwa']."\" value=\"".$idb->row['id_kraje']."\" ";
	
	if($idb->row['id_kraje'] == 167) echo "selected";
	
	echo ">".$idb->row['nazwa']."</option>";
	}
?>	
	</select>
	</td>
</tr>
<tr>
	<td colspan="2" align="left"><br><b>Informacje</b>:<br><br></td>
</tr>
<tr>
	<td class="opis">ZaГБczniki:<br>max. wielkoЖц pliku 1,44 MB</td>
	<td align="left"><div id='files'><input name="userfile1" type="file" id="file1"><br></div>
	<input type='button' onClick='add_file()' value='Dodaj wiъcej zaГБcznikѓw'/> </td>
</tr>
<tr>
	<td id="powod_zgloszenia_reklamacji_label" class="opis">Powѓd zgГoszenia reklamacji <span style="color:Red;">*</span>:</td>
	<td align="left"><textarea rows="5" cols="56" name="zgloszenie[powod_zgloszenia_reklamacji]" id="powod_zgloszenia_reklamacji"></textarea></td>
</tr>
<tr>
	<td class="opis">Pora dnia w jakiej moПe przyjechaц serwis:</td>
	<td align="left"><input type="text" name="zgloszenie[pora_dnia_przyjazdu_serwisu]" style="width:300px"></td>
</tr>
<tr>
	<td class="opis">Produkt reklamowany <span style="color:Red;">*</span>:</td>
	<td align="left"><a href="?" onclick="return add_product();"><img src="<?=$UIMAGES;?>/action_add.gif" style="border-style:none" alt="Dodaj"></a>
	<div id="produkt"></div>
	</td>
</tr>
<tr>
	<td class="opis"></td>
	<td align="left"><input type="hidden" name="mode" value="save"><input type="submit" value="WyЖlij" onclick="return validate();"></td>
</tr>
</form>
</table>
<!--- <input type="submit" value="WyЖlij" onclick="return validate();"> --->
<br>

<b>W przypadku nieuzasadnionego zgГoszenia serwisowego klient ponosi zryczaГtowany<br>koszt dojazdu zaleПny od iloЖci kilometrѓw.</b>
</div>

<div id="produkt_form" style="display:none">
<a href="?" onclick="return remove_product(this)"><img src="<?=$UIMAGES;?>/action_remove.gif" style="border-style:none" alt="Usuё"></a>
<table>
<tr>
	<td class="opis">Nazwa produktu <span style="color:Red;">*</span>:</td>
	<td><input type="text" name="zgloszenie[produkt_nazwa][]" style="width:200px"></td>
</tr>
<tr>
	<td id="product_tabliczka_label" class="opis">Numer tabliczki znamionowej <span style="color:Red;">*</span>:</td>
	<td>
	<input type="text" name="zgloszenie[nazwa_nr][]" style="width:73px">
	<input type="text" name="zgloszenie[nazwa_nr2][]" style="width:73px">
	</td>
</tr>
<tr>
	<td class="opis">Data nabycia produktu <span style="color:Red;">*</span>:</td>
	<td><input type="text" name="zgloszenie[data_nabycia_towaru][]" style="width:73px"><br>
	RRRR-MM-DD (np. 2004-05-31)</td>
</tr>
<tr>
	<td class="opis">Data montaПu produktu <span id="data_montazu_gwiazdka" style="color:Red;">*</span>:</td>
	<td><input type="text" name="zgloszenie[data_montazu][]" style="width:73px"><br>
	RRRR-MM-DD (np. 2004-05-31)<br>
	lub proszъ wybraц w polu 'Kto montowaГ' wartoЖц 'Brak montaПu'</td>
</tr>
<tr>
	<td class="opis">Kto montowaГ:</td>
	<td>
	<select name="zgloszenie[montaz][]" style="width:150px" onchange="ustawDateMontazu(this, '');">
		<option value="1">MontaП wГasny</option>
		<option value="2">Firma dekarska</option>
		<option value="3">Brak montaПu</option>
	</select>
	</td>
</tr>
<tr>
	<td class="opis">Pokrycie dachowe:</td>
	<td>
	<select name="zgloszenie[pokrycie_dachowe][]" style="width:150px">
		<option value="1">dachѓwka</option>
		<option value="2">blacha</option>
		<option value="3">inne</option>
	</select>
	</td>
</tr>
<tr>
	<td class="opis">Rozmiar:</td>
	<td><input type="text" name="zgloszenie[rozmiar][]" style="width:73px"></td>
</tr>
<tr>
	<td class="opis">Wersja:</td>
	<td><input type="text" name="zgloszenie[wersja][]" style="width:73px"></td>
</tr>
<tr>
	<td class="opis">IloЖц <span style="color:Red;">*</span>:</td>
	<td><input type="text" name="zgloszenie[ilosc][]" style="width:73px"></td>
</tr>
<tr>
	<td class="opis">Komentarz:</td>
	<td><textarea name="zgloszenie[komentarz][]" cols="30" rows="3"></textarea></td>
</tr>
</table>
</div>

<?
	}else{
	$zgloszenie = $_POST['zgloszenie'];
	
	$firmy = array(
				'nazwa' => $zgloszenie['nazwa'],
				'nazwa_skrocona' => $zgloszenie['nazwa'],
				'ulica' => $zgloszenie['ulica'],
				'nr_domu' => $zgloszenie['nr_domu'],
				'nr_mieszkania' => $zgloszenie['nr_mieszkania'],
				'kod_pocztowy' => $zgloszenie['kod_pocztowy'],
				'miasto' => $zgloszenie['miasto'],
				't1_kraj' => $zgloszenie['t1_kraj'],
				't1_miasto' => $zgloszenie['t1_miasto'],
				't1_numer' => $zgloszenie['t1_numer'],
				't1_wewnetrzny' => $zgloszenie['t1_wewnetrzny'],
				'id_kraj' => $zgloszenie['id_kraje'],
				'email' => $zgloszenie['email'],
				'data_utworzenia' => date('Y-m-d')
				);
	$id_nabywca = insert_array($firmy, 'firmy');
	#$id_nabywca = insert_firmy($firmy);
	$adres_okno = (isset($zgloszenie['okno_miasto']) && $zgloszenie['okno_miasto'] != '' && isset($zgloszenie['okno_ulica']) && $zgloszenie['okno_ulica'] != '');
	
	// na razie nr nie jest zapisywany tylko bedzie dodawany na etapie przenoszenia
	// zgloszenia do wlasciwej bazy!
	// $nr_ewidencyjny = get_nr_ewidencyjny();
	$nr_ewidencyjny = '';
	
	$zgloszenie_serwisowe = array(
				'id_zgloszenie_serwisowe_typ' => '1',
				'id_zgloszenie_serwisowe_status' => '1',
				'data_przyjecia_zgloszenia' => date('Y-m-d'),
				'id_dodal_zgloszenie' => _ID_OSOBY,
				'nr_ewidencyjny' => $nr_ewidencyjny,
				'id_nabywca' => $id_nabywca,
				'okno_ulica' => $adres_okno == true ? $zgloszenie['okno_ulica'] : $zgloszenie['ulica'],
				'okno_nr_domu' => $adres_okno == true ? $zgloszenie['okno_nr_domu'] : $zgloszenie['nr_domu'],
				'okno_nr_mieszkania' => $adres_okno == true ? $zgloszenie['okno_nr_mieszkania'] : $zgloszenie['nr_mieszkania'],
				'okno_kod_pocztowy' => $adres_okno == true ? $zgloszenie['okno_kod_pocztowy'] : $zgloszenie['kod_pocztowy'],
				'okno_miasto' => $adres_okno == true ? $zgloszenie['okno_miasto'] : $zgloszenie['miasto'],
				'okno_id_kraje' => $adres_okno == true ? $zgloszenie['okno_id_kraje'] : $zgloszenie['id_kraje'],
				'powod_zgloszenia_reklamacji' => $zgloszenie['powod_zgloszenia_reklamacji'],
				'pora_dnia_przyjazdu_serwisu' => $zgloszenie['pora_dnia_przyjazdu_serwisu'],
				);
	$id_zgloszenie_serwisowe = insert_array($zgloszenie_serwisowe, 'zgloszenie_serwisowe');
	
	/** obsluga plikow */
	foreach ($_FILES as $file) {
		if(strlen($file['name']) > 0) {
			$idb->sqlaction('insert','external_zgloszenie_serwisowe_pliki',' ',array('id_zgloszenie_serwisowe','nazwa','mime','size'),array($id_zgloszenie_serwisowe,$file['name'],$file['type'],$file['size']));
			$id_file = $idb->_insertid;
			
			if(!$fh = fopen($file['tmp_name'], "r")) {
				echo "<BR>NIE DODAЃEM: (".$file[tmp_name].")";
				}else{
				$file = addslashes(fread($fh,$file['size']));
				$idb->sqlaction('insert','tmp_serwisowe_pliki',' ',array('id_external_zgloszenie_serwisowe_pliki','file'),array($id_file,$file));
				}
			
			/*
			if(!move_uploaded_file($file['tmp_name'], $uploadFile)) {
				echo("<BR>NIE DODAЃEM: ($file[tmp_name]) " . $uploadFile);
				}
			*/
			}
		}
	
	// nr dla klienta
	$nr_ewidencyjny_klient = date('ymd').$id_zgloszenie_serwisowe;
	$idb->sqlaction('update','zgloszenie_serwisowe',' id_zgloszenie_serwisowe = "'.$id_zgloszenie_serwisowe.'"',array('nr_ewidencyjny_klient'),array($nr_ewidencyjny_klient));
	
	/* Funkcjonalnosc zbedna ze wzgledu na zewnetrzne umiejscowienie tych danych
	$zgloszenie_serwisowe_status_dni = array(
					'id_zgloszenie_serwisowe' => $id_zgloszenie_serwisowe,
					'id_zgloszenie_serwisowe_status' => '1',
					'poczatek' => date('Y-m-d'),
					);

	$id_zgloszenie_serwisowe_status_dni = insert_array($zgloszenie_serwisowe_status_dni, 'zgloszenie_serwisowe_status_dni');
	*/
	
	
	foreach($zgloszenie['produkt_nazwa'] as $k => $v) {
		$zgloszenie_serwisowe_towary = array(
						'nazwa' => $zgloszenie['produkt_nazwa'][$k],
						'nazwa_nr' => $zgloszenie['nazwa_nr'][$k],
						'nazwa_nr2' => $zgloszenie['nazwa_nr2'][$k],
						'data_nabycia_towaru' => $zgloszenie['data_nabycia_towaru'][$k],
						'data_montazu' => $zgloszenie['data_montazu'][$k],
						'montaz' => $zgloszenie['montaz'][$k],
						'pokrycie_dachowe' => $zgloszenie['pokrycie_dachowe'][$k],
						'rozmiar' => $zgloszenie['rozmiar'][$k],
						'wersja' => $zgloszenie['wersja'][$k],
						'ilosc' => $zgloszenie['ilosc'][$k],
						'komentarz' => $zgloszenie['komentarz'][$k],
						);
		$id_zgloszenie_serwisowe_towary = insert_array($zgloszenie_serwisowe_towary, 'zgloszenie_serwisowe_towary');
		$idb->sqlaction('insert','zgloszenie_serwisowe__towary',' ',array('id_zgloszenie_serwisowe','id_zgloszenie_serwisowe_towary'),array($id_zgloszenie_serwisowe,$id_zgloszenie_serwisowe_towary));
		}
?>

<div align="center">
Twoje zgГoszenie serwisowe zostaГo zГoПone.
<br><br>
Numer ewidencyjny Twojego zgГoszenia to <strong><?=$nr_ewidencyjny_klient;?></strong>.
</div>
<?
	}
?>