<?
if($_GET['user_action'] == 'summary' && isset($_GET['id'])) {
	$_tmp_element = explode(".",$_GET['id']);
	
	$_SESSION['bp_form_data'][$_tmp_element[0].'-'.$_tmp_element[1]]['cena'][$_tmp_element[2]] = '';
	$_SESSION['bp_form_data'][$_tmp_element[0].'-'.$_tmp_element[1]]['ilosc'][$_tmp_element[2]] = 0;
	
	$_tmp_ilosc = $_SESSION['bp_form_data'][$_tmp_element[0].'-'.$_tmp_element[1]]['ilosc'];
	
	$_tmp = 0;
	foreach($_tmp_ilosc as $v) $_tmp .= $v;
	
	if($_tmp == 0) {
		unset($_SESSION['done']);
		unset($_SESSION['bp_form_data'][$_tmp_element[0].'-'.$_tmp_element[1]]);
		}
	
	$_count_bp_form_data = count($_SESSION['bp_form_data']);
	
	if($_count_bp_form_data == 0) {
?>
<SCRIPT LANGUAGE="JavaScript">
setTimeout('checkBrowser()', 0);

function checkBrowser() {
	window.location.href="<?=$_action;?>";
	}
</SCRIPT>
<?
		}
	}
?>


<script type="text/javascript">
function sum_cena()
{
	var inputs = document.getElementsByTagName('INPUT');

	var suma = 0;
	if(inputs.length > 0)
	{
		for(var i = 0; i < inputs.length; i++)
		{
			var element = inputs[i];
			var reg = new RegExp("produkty\\\[(.+)\\\]\\\[cena\\\]\\\[(\\d+)\\\]", "g");
			var res = reg.exec(element.name);

			if(res)
			{
				var cena = element.value;
				var nrTabliczki = res[1];
				var id_bp_czesci = res[2];

				var id = 'produkty[' + nrTabliczki + '][ilosc][' + id_bp_czesci + ']';
				var ilosc = document.getElementById(id).value;

				var id_cj = 'produkty[' + nrTabliczki + '][cena_jednostkowa][' + id_bp_czesci + ']';
				var cena_jednostkowa = document.getElementById(id_cj).value;

				if(is_number(ilosc))
					suma += ilosc * cena_jednostkowa;
				else
					return;
			}
		}
	}
	
	el('suma').value = suma;
}

function change_uwagi_dostawca()
{
    option = el('select_dostawca');
	<? for($i = 0; $i < count($summary['dostawcy']); $i++) { ?>
        if (option[option.selectedIndex].value=='<? echo $summary['id_bp_czesci'][$i]['id_zgloszenie_serwisowe_dostawcy']; ?>') {
            el('uwagi_dostawca').innerHTML = '<? echo $summary['id_bp_czesci'][$i]['uwagi']; ?>';
            el('hidden_uwagi_dostawca').value = '<? echo $summary['id_bp_czesci'][$i]['uwagi']; ?>';
        }
	<? } ?>
    if (option[option.selectedIndex].value==0) {
        el('uwagi_dostawca').value = 0;
        el('hidden_uwagi_dostawca').value = 0;
    }
    el('nazwa_dostawcy').value = option[option.selectedIndex].text;
    //sum_cena();
}
</script>

Proszъ zweryfikowaц poniПszБ listъ czъЖci i dokonaц ewentulanych modyfikacji:
<br />
<form method="POST" action="<?=$_action;?>">
<input type="hidden" name="mode" value="done">
<input type="hidden" name="user_action" value="save">

<?
$_from_1 = $_SESSION['bp_form_data']; if(!is_array($_from_1) && !is_object($_from_1)) { settype($_from_1, 'array'); } if(count($_from_1)) {
foreach($_from_1 as $produkt) {
?>
<table class="list_table" id="zamowienie_form">
<col width="342">
<col width="40">
<col width="90">
<col width="40">
<thead>
<tr>
	<td colspan="4"><strong>Nazwa produktu:</strong> <? echo $produkt['nazwa_nr']; ?> - <? echo $produkt['nazwa_nr2']; ?>
	<? if(strlen($produkt['nazwa_nr3']) || strlen($produkt['nazwa_nr4'])) echo "- ".$produkt['nazwa_nr3']." - ".$produkt['nazwa_nr4']; ?>
	</td>
</tr>
<tr>
	<td><b>CzъЖц</b></td>
	<td><b>Cena</b></td>
	<td colspan="2"><b>IloЖц</b></td>
</tr>
</thead>
<tbody>
<?
unset($suma);
$_from_2 = $produkt['ilosc']; if(!is_array($_from_2) && !is_object($_from_2)) { settype($_from_2, 'array'); } if(count($_from_2)) {
foreach($_from_2 as $id_bp_czesci => $ilosc) {
?>
<? if($ilosc > 0) { ?>
<tr>
	<td id="nazwa_col"><? echo $produkt['czesci'][$id_bp_czesci]; ?></td>
	<td>
	<input type="text" disabled="disabled" size="8" value="<? echo $produkt['cena'][$id_bp_czesci]; ?>">
	<input type="hidden" name="produkty[<? echo $produkt['nazwa_nr']; ?>-<? echo $produkt['nazwa_nr2']; ?>][cena][<? echo $id_bp_czesci; ?>]" value="<? echo $produkt['cena'][$id_bp_czesci]; ?>">
	<input type="hidden" name="produkty[<? echo $produkt['nazwa_nr']; ?>-<? echo $produkt['nazwa_nr2']; ?>][cena_jednostkowa][<? echo $id_bp_czesci; ?>]" value="<? echo $produkt['cena_jednostkowa'][$id_bp_czesci]; ?>" id="produkty[<? echo $produkt['nazwa_nr']; ?>-<? echo $produkt['nazwa_nr2']; ?>][cena_jednostkowa][<? echo $id_bp_czesci; ?>]">
	</td>
	<td>
	<input type="text" size="8" value="<? echo $ilosc; ?>" name="produkty[<? echo $produkt['nazwa_nr']; ?>-<? echo $produkt['nazwa_nr2']; ?>][ilosc][<? echo $id_bp_czesci; ?>]" id="produkty[<? echo $produkt['nazwa_nr']; ?>-<? echo $produkt['nazwa_nr2']; ?>][ilosc][<? echo $id_bp_czesci; ?>]">
	<input type="hidden" name="produkty[<? echo $produkt['nazwa_nr']; ?>-<? echo $produkt['nazwa_nr2']; ?>][czesci][<? echo $id_bp_czesci; ?>]" value="<? echo $produkt['czesci'][$id_bp_czesci]; ?>">
	</td>
	<td align="center"><a href="<?=$_action;?>?user_action=summary&id=<?=$produkt['nazwa_nr'].'.'.$produkt['nazwa_nr2'].'.'.$id_bp_czesci; ?>">X</a></td>
</tr>
<?
$suma += $produkt['cena'][$id_bp_czesci];
?>

<? } ?>
<? } ?>
<tr>
	<td colspan="2" align="right"><strong>Suma:</strong></td>
	<td><!--- <input type="text" readonly="readonly" size="8" id="suma" name="suma" /> --->
	<input type="text" readonly="readonly" value="<?=$suma;?>" size="8"></td>
	<td>PLN</td>
</tr>
<? $suma_calosc += $suma; ?>
<? } ?>
</tbody>
</table>
<br />
<? } ?>
<? } ?>

<table class="list_table">
<col width="342">
<col width="40">
<col width="90">
<col width="40">
<?
if($suma_calosc <= 150) {
	$suma_calosc = ($suma_calosc+18);
?>
<tr>
	<td colspan="2" align="right">Sposѓb dostawy: przesyГka kurierska</td>
	<td>18,00</td>
	<td>PLN</td>
</tr>
<? } ?>
<tr>
	<td colspan="2" align="right"><strong>CaГkowita wartoЖц zamѓwienia</strong></td>
	<td><strong><?=number_format($suma_calosc,2);?></strong></td>
	<td><strong>PLN</strong></td>
</tr>
</table>
<input type="hidden" name="suma" value="<?=$suma_calosc;?>">

<br><br>
<table width="100%">
<tbody>
<tr>
	<td align="right">Wszystkie podane ceny sБ cenami netto.</td>
</tr>
<tr>
	<td><br><br>
	<ul>
		<li>JeЖli wartoЖц zamѓwienia przekracza <strong><font color="#e9832f">150 zГ</font></strong>  - dostawa jest realizowana <strong><font color="#e9832f">bezpГatnie </font></strong></li>
		<li>Dla zamѓwieё o wartoЖci <strong><font color="#e9832f">poniПej 150 zГ</font></strong> koszty dostawy wynoszБ <font color="#e9832f"><strong>18 zГ.</strong></font></li>
	</ul>
	</td>
</tr>
</tbody>
</table>

<!--- 
<table width="100%">
<tbody>
<tr>
	<td>
	<select name="select_dostawca" id='select_dostawca' onChange='change_uwagi_dostawca()'>
	<? for($i = 0; $i < count($summary['dostawcy']); $i++) { ?>
		<option value="<? echo $summary['dostawcy'][$i]['id_zgloszenie_serwisowe_dostawcy']; ?>"><? echo $summary['dostawcy'][$i]['nazwa']; ?></option>
	<? } ?>
	</select>
	</td>
	<td align="right"><strong>Suma:</strong><input type="text" readonly="readonly" size="8" id="suma" name="suma" /> PLN</td>
</tr>
</tbody>
</table>
 --->
<br><br>

<div id="uwagi_dostawca" style="width:450px;"></div>
<input type="hidden" name="uwagi_dostawca" id="hidden_uwagi_dostawca" value="0">
<input type="hidden" value="PrzesyГka kurierska" name="nazwa_dostawcy" id="nazwa_dostawcy">

<table width="100%" id="zamowienie_dane">
<col width="180">
<col>
<tbody>
<!---  --->
<tr>
	<td colspan="2" align="left"><b>Dane zamawiajБcego</b>:<br /><br /></td>
</tr>
<tr>
	<td id="nazwa_label" class="opis">Imiъ i nazwisko / Nazwa firmy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="nazwa" id="nazwa" style="width:150px" /></td>
</tr>
<tr>
	<td id="ulica_label" class="opis">Ulica <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="ulica" id="ulica" style="width:150px" onchange="kopiuj_zmiany('ulica')" /></td>
</tr>
<tr>
	<td id="nr_domu_label" class="opis">Nr budynku <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="nr_domu" id="nr_domu" style="width:30px" onchange="kopiuj_zmiany('nr_domu')" /></td>
</tr>
<tr>
	<td class="opis">Nr mieszkania:</td>
	<td align="left"><input type="text" name="nr_mieszkania" id="nr_mieszkania" style="width:30px" onchange="kopiuj_zmiany('nr_mieszkania')" /></td>
</tr>
<tr>
	<td class="opis" id="kod_pocztowy_label">Kod pocztowy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="kod_pocztowy" id="kod_pocztowy" style="width:150px" onchange="kopiuj_zmiany('kod_pocztowy')" /></td>
</tr>
<tr>
	<td id="miasto_label" class="opis">Miasto <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="miasto" id="miasto" style="width:150px" onchange="kopiuj_zmiany('miasto')" /></td>
</tr>
<input type="hidden" value="167" name="id_kraje" id="id_kraje">
<!--- 
<tr>
	<td class="opis">Kraj <span style="color:Red;">*</span>:</td>
	<td align="left">
	<select name="id_kraje" id="id_kraje" style="width:150px" onchange="kopiuj_zmiany('id_kraje')" >
	<? foreach ($summary['kraje'] as $key => $value) { ?>
		<option value="<? echo $key; ?>" label="<? echo $summary['kraje'][$key]; ?>"><? echo $summary['kraje'][$key]; ?></option>
	<? } ?>
	</select>
	</td>
</tr>
--->
<tr>
	<td id="t1_label" class="opis">Numer telefonu <span style="color:Red;">*</span>:</td>
	<td align="left" style="white-space:nowrap">
	<input type="text" name="t1_kraj" value="48" id="t1_kraj" style="width:20px" />
	<input type="text" name="t1_miasto" id="t1_miasto" style="width:20px" />
	<input type="text" name="t1_numer" id="t1_numer" style="width:78px" />
	<input type="text" name="t1_wewnetrzny" id="t1_wewnetrzny" style="width:20px" />
	<br />
	<small>	kraj - miasto - numer - wewnъtrzny</small></td>
</tr>
<tr>
	<td id="email_label" class="opis">E-mail <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="email" id="email" style="width:150px" /></td>
</tr>
<tr>
	<td class="opis">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="opis">Osoba kontaktowa:</td>
	<td align="left"><input type="text" name="osoba_kontaktowa" style="width:150px"  /></td>
</tr>
<tr>
	<td id="telefon_kontaktowy_label" class="opis">Telefon kontaktowy:</td>
	<td style="white-space:nowrap" align="left">
	<input type="text" name="telefon_kontaktowy_kraj" value="48" id="telefon_kontaktowy_kraj" style="width:20px" />
	<input type="text" name="telefon_kontaktowy_miasto" id="telefon_kontaktowy_miasto" style="width:20px" />
	<input type="text" name="telefon_kontaktowy_numer" id="telefon_kontaktowy_numer" style="width:78px" />
	<input type="text" name="telefon_kontaktowy_wewnetrzny" id="telefon_kontaktowy_wewnetrzny" style="width:20px" />
	<br />
	<small>kraj - miasto - numer - wewnъtrzny</small>
	</td>
</tr>
<tr>
	<td colspan="2" align="left">
	<br />
	<b>Adres zamontowania produktu</b>:
	<br /><br /></td>
</tr>
<tr>
	<td class="opis"><input type="checkbox" name="kopiuj" id="kopiuj" onclick="kopiuj_adres()" /></td>
	<td align="left"><a href="javascript:void(0);" onclick="kopiuj_adres_link()" >Dane jak wyПej</a></td>
</tr>
<tr>
	<td class="opis" id="okno_ulica_label">Ulica <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_ulica" id="okno_ulica" style="width:150px"  /></td>
</tr>
<tr>
	<td class="opis" id="okno_nr_domu_label">Nr budynku <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_nr_domu" id="okno_nr_domu" style="width:30px"  /></td>
</tr>
<tr>
	<td class="opis">Nr mieszkania:</td>
	<td align="left"><input type="text" name="okno_nr_mieszkania" id="okno_nr_mieszkania" style="width:30px" /></td>
</tr>
<tr>
	<td class="opis" id="okno_kod_pocztowy_label">Kod pocztowy <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_kod_pocztowy" id="okno_kod_pocztowy" style="width:150px" /></td>
</tr>
<tr>
	<td class="opis" id="okno_miasto_label">Miasto <span style="color:Red;">*</span>:</td>
	<td align="left"><input type="text" name="okno_miasto" id="okno_miasto" style="width:150px" /></td>
</tr>
<input type="hidden" value="167" name="okno_id_kraje" id="okno_id_kraje">
<!---
<tr>
	<td class="opis">Kraj:</td>
	<td align="left">
	<select name="okno_id_kraje" id="okno_id_kraje" style="width:150px" >
	<? foreach($summary['kraje'] as $key => $value) { ?>
		<option value="<? echo $key; ?>" label="<? echo $summary['kraje'][$key]; ?>"><? echo $summary['kraje'][$key]; ?></option>
	<? } ?>
	</select>
	</td>
</tr>
--->
<!---  --->


<tr>
	<td colspan="2" align="left">
	<br />
	<b>Sposѓb pГatnoЖci za skГadane zamѓwienie</b>:
	<br /><br /></td>
</tr>
<tr>
	<td colspan="2" align="left">
	<select style="width:100%" id="sposob_platnosci" name="sposob_platnosci">
		<option value="">Wybierz sposѓb pГatnoЖci</option>
		<option value="1">Przy odbiorze</option>
		<option value="2">PГatnoЖci elektroniczne (karta pГatnicza, szybki przelew, przelew bankowy)</option>
	</select>
	</td>
</tr>
<!--- 
<tr>
	<td class="opis"><b>Proponowany termin dostawy:</b></td>
	<td align="left"><input type="text" name="termin_dostawy" id="termin_dostawy" style="width:150px" /></td>
</tr>
 --->
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td id="regulamin_label" class="opis"><a href="<? echo $regulamin; ?>" target="_blank">Akceptacja regulaminu?</a>:</td>
	<td style="white-space:nowrap" align="left"><input type="checkbox" name="regulamin" id="regulamin" />zgadzam siъ</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="left"><input type="submit" class="input" value="Zamѓw..." onclick="return validate()" id="submit" style="display:none" /></td>
</tr>
<tr>
	<td colspan="2" align="right"><p><input class="button" type="submit" value="ZГѓП zamѓwienie" onclick="return validate();" /></p></td>
</tr>
</form>
<tr>
	<td colspan="2" align="center">
	<br><br><a href="http://sklep.fakro.pl/htmli/14.php" target="_blank">Potъga ЖwiatГa regulowana dodatkami do okien dachowych.</a></td>
</tr>
</tbody>
</table>

<script type="text/javascript">
//sum_cena();
</script>