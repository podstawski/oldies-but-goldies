<?
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
	
	$dba->query('SET NAMES utf8');
	$dba->query('SET CHARSET utf8');
	
	$re = $dba->query("INSERT INTO www2crm_kadry_kandydat (
					imie,nazwisko,miasto,kod_pocztowy,ulica,
					nr_domu,nr_mieszkania,id_kraje,email,t1_kraj,
					t1_miasto,t1_numer,t1_wewnetrzny,wyksztalcenie,kierunek_studiow,
					szkola,zawod_wykonywany,znajomosc_angielski,znajomosc_niemiecki,znajomosc_rosyjski,
					znajomosc_francuski,inny_jezyk,znajomosc_inny_jezyk,doswiadczenie_zawodowe,wymagania_finansowe_od,
					wymagania_finansowe_do,umiejetnosci,_created,urodziny,kom_kraj,
					kom_operator,kom_numer,id_wojewodztwa,id_fakro)
					VALUES (
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?,?,
					?,?,?,?)",
					array(
					mb_convert_case($podstawowe['imie'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['nazwisko'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['miasto'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['kod'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['ulica'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['ulica_nr'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['ulica_nr_m'], MB_CASE_UPPER, "UTF-8"),
					'167',
					mb_convert_case($podstawowe['email'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['tel_kraj'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['tel_kier'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['tel'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['tel_wew'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['wyksztalcenie'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['kierunek'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['szkola'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['zawod'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['angielski'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['niemiecki'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['rosyjski'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['francuski'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['inny_jezyk'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($umiejetnosci['znajomoscinnegojezyka'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($informacje['doswiadczenie'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($informacje['od'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($informacje['do'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($informacje['uprawnieniakursy'], MB_CASE_UPPER, "UTF-8"),
					date("Y-m-d H:i:s"),
					mb_convert_case($podstawowe['data_urodzenia'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['tel_kom_kraj'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['tel_kom_op'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['tel_kom'], MB_CASE_UPPER, "UTF-8"),
					mb_convert_case($podstawowe['wojewodztwo'], MB_CASE_UPPER, "UTF-8"),
					$inne['typ']
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