<?
$data_od=$_POST['rezerwacja_date_od'];
$data_do=$_POST['rezerwacja_date_do'];

$re = $idb->query("INSERT INTO daty(id, od, do, data_pytania, czas_pytania) VALUES ('','$data_od','$data_do', now(), now())");
$insertid = $idb->insertid($re);

if($insertid) { //jesli sie udal zapis
	
	$row = $idb->getvalues($idb->query("SELECT id FROM daty WHERE daty.od = '".$data_od."' AND daty.do = '".$data_do."' AND daty.id = '".$insertid."'"));
	$id = $row["id"];
	
	$_SESSION['rezerwacja']['id'] = $id; //zmienna sesyjna
	
	$ilosc_rekordow='0';
	$_id = 0;
	while($ilosc_rekordow=='0') { //sprawdzanie czy splynal rekord z Pensionnaire do tabeli typy_pokoi
		$idb->query("SELECT * FROM typy_pokoi where id_pytania = '".$id."'"); //odczyt z typy_pokoi($id_pytania) po $id z daty
		$ilosc_rekordow = $idb->rowcount();
		
		if($_id > 1000000) return;
		$_id++;
		}
	
	if($ilosc_rekordow <> '0') {
?>
	<SCRIPT LANGUAGE="JAVASCRIPT">
		window.location.href = '<?=$_action;?>?m=akcja';
	</SCRIPT>
<?
		}
	}else{
	echo'Chwilowo nie mozna odpytc bazy. Prosimy o kontakt!'. mysql_error();
	}
?>
