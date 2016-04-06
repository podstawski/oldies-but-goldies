<?
setlocale(LC_ALL, 'pl_PL.ISO8859-2');

require 'DB.php';
// Connect to the database

$dba = &DB::connect('mysql://fakro_www:www_fakro@213.25.72.188/fakro_crm_prod_kadry');

$inne = $_POST['inne'];
$podstawowe = $_POST['podstawowe'];
$umiejetnosci = $_POST['umiejetnosci'];
$informacje = $_POST['informacje'];
$ZGODA = $_POST['ZGODA'];

if(PEAR::isError($dba)) {
	$komunikat = 'Przepraszamy serwis jest chwilowo nieczynny.';
	}else{

	$dba->query('SET NAMES latin2');
	$dba->query('SET CHARSET latin2');

	$re = $dba->query("INSERT INTO www2crm_kadry_kandydat (
					imie,nazwisko,miasto,kod_pocztowy,ulica,
					nr_domu,nr_mieszkania,id_kraje,email,t1_kraj,
					t1_miasto,t1_numer,t1_wewnetrzny,wyksztalcenie,kierunek_studiow,
					szkola,zawod_wykonywany,znajomosc_angielski,znajomosc_niemiecki,znajomosc_rosyjski,
					znajomosc_francuski,inny_jezyk,znajomosc_inny_jezyk,doswiadczenie_zawodowe,wymagania_finansowe_od,
					wymagania_finansowe_do,umiejetnosci,_created,urodziny,kom_kraj,
					kom_operator,kom_numer,id_wojewodztwa,id_fakro,id_nabor)
					VALUES (
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?)",
					array(
					strtoupper($podstawowe['imie']),strtoupper($podstawowe['nazwisko']),strtoupper($podstawowe['miasto']),strtoupper($podstawowe['kod']),strtoupper($podstawowe['ulica']),
					strtoupper($podstawowe['ulica_nr']),strtoupper($podstawowe['ulica_nr_m']),'167',strtolower($podstawowe['email']),strtoupper($podstawowe['tel_kraj']),
					strtoupper($podstawowe['tel_kier']),strtoupper($podstawowe['tel']),strtoupper($podstawowe['tel_wew']),strtoupper($umiejetnosci['wyksztalcenie']),strtoupper($umiejetnosci['kierunek']),
					strtoupper($umiejetnosci['szkola']),strtoupper($umiejetnosci['zawod']),strtoupper($umiejetnosci['angielski']),strtoupper($umiejetnosci['niemiecki']),strtoupper($umiejetnosci['rosyjski']),
					strtoupper($umiejetnosci['francuski']),strtoupper($umiejetnosci['inny_jezyk']),strtoupper($umiejetnosci['znajomoscinnegojezyka']),strtoupper($informacje['doswiadczenie']),strtoupper($informacje['od']),
					strtoupper($informacje['do']),strtoupper($informacje['uprawnieniakursy']),date("Y-m-d H:i:s"),strtoupper($podstawowe['data_urodzenia']),strtoupper($podstawowe['tel_kom_kraj']),
					strtoupper($podstawowe['tel_kom_op']),strtoupper($podstawowe['tel_kom']),strtoupper($podstawowe['wojewodztwo']),$inne['typ'],138
					)
					);

	if(DB::isError($re)) {
		echo $re->getMessage();
		$komunikat = 'Przepraszamy serwis jest chwilowo nieczynny.';
		}else{
		$komunikat = 'Dziękujemy za wypełnienie formularza.';
		}
	}

echo '<br><br><div align="center"><strong>'.$komunikat.'</strong></div>';
/********************************************************************************/
?>