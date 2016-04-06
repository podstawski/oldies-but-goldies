<?
$id = $_SESSION['rezerwacja']['id'];

$kontakt = $_POST["kontakt"];

$re = $idb->query("INSERT INTO osoby (id, imie, nazwisko, ulica, miasto, kod, telefon, mail, preferencje, id_sesji) VALUES ('','$kontakt[name]','$kontakt[surname]', '$kontakt[ulica]', '$kontakt[miasto]', '$kontakt[kod]', '$kontakt[telefon]', '$kontakt[mail]', '$kontakt[preferencje]', '$id')");
$insertid = $idb->insertid($re);

$result_daty = $idb->getvalues($idb->query("SELECT * FROM daty WHERE id='".$id."'"));
$od = $result_daty["od"];
$do = $result_daty["do"];

$result_osoby_select = $idb->getvalues($idb->query("SELECT * FROM osoby WHERE id_sesji='".$id."'"));
$id_osoby = $result_osoby_select["id"];

$result = $idb->fetch_assoc($idb->query("SELECT * FROM typy_pokoi WHERE id_pytania = '".$id."'"));
$licznik = "0";
foreach($result as $key => $value) {
	
	$typ_pokoju = $value['typ_pokoju'];
	$id_tp = $value['id_tp'];
	$licznik=$licznik+'100';
	$ilosc = $_POST[$licznik];
	
	if($ilosc != 0) {
		$re = $idb->query("INSERT INTO rezerwacje(id, id_osoby, od, do, id_tp, typ_pokoju, ilosc, data_rezerwacji, czas_rezerwacji) VALUES ('', '$id_osoby', '$od', '$do', '$id_tp', '$typ_pokoju', '$ilosc', now(), now())");
		$insertid = $idb->insertid($re);
		}
	}

echo '<br><br><div align="center"><strong>Dziekujemy za dokonanie rezerwacji</strong></div>';
unset($_SESSION['rezerwacja']['id']);
?>
