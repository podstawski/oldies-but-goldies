<?
# ustawienia MySql`a
$CFG['host']	= "213.25.72.188";
$CFG['user']	= "fakro_www";
$CFG['pass']	= "www_fakro";
$CFG['db']		= "fakro_crm_prod_kadry";

include("$INCLUDE_PATH/www2crm/idb_mysql.php");

global $idb;
$idb = new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);

$podstawowe = $_POST['podstawowe'];
$umiejetnosci = $_POST['umiejetnosci'];
$informacje = $_POST['informacje'];
$ZGODA = $_POST['ZGODA'];

$idb->sqlaction('insert','www2crm_kadry_kandydat',' ',
	array('imie','nazwisko','miasto','kod_pocztowy',
	'ulica','nr_domu','nr_mieszkania','id_kraje',
	'email','t1_kraj','t1_miasto','t1_numer','t1_wewnetrzny',
	'wyksztalcenie','kierunek_studiow','szkola','zawod_wykonywany',
	'znajomosc_angielski','znajomosc_niemiecki','znajomosc_rosyjski','znajomosc_francuski','inny_jezyk','znajomosc_inny_jezyk',
	'doswiadczenie_zawodowe',
	'wymagania_finansowe_od','wymagania_finansowe_do',
	'umiejetnosci','_created','urodziny',
	'kom_kraj','kom_operator','kom_numer',
	'id_wojewodztwa','id_fakro'),
	array($podstawowe['imie'],$podstawowe['nazwisko'],$podstawowe['miasto'],$podstawowe['kod'],
	$podstawowe['ulica'],$podstawowe['ulica_nr'],$podstawowe['ulica_nr_m'],'167',
	$podstawowe['email'],$podstawowe['tel_kraj'],$podstawowe['tel_kier'],$podstawowe['tel'],$podstawowe['tel_wew'],
	$umiejetnosci['wyksztalcenie'],$umiejetnosci['kierunek'],$umiejetnosci['szkola'],$umiejetnosci['zawod'],
	$umiejetnosci['angielski'],$umiejetnosci['niemiecki'],$umiejetnosci['rosyjski'],$umiejetnosci['francuski'],$umiejetnosci['inny_jezyk'],$umiejetnosci['znajomoscinnegojezyka'],
	$informacje['doswiadczenie'],
	$informacje['od'],$informacje['do'],
	$informacje['uprawnieniakursy'],date("Y-m-d H:i:s"),$podstawowe['data_urodzenia'],
	$podstawowe['tel_kom_kraj'],$podstawowe['tel_kom_op'],$podstawowe['tel_kom'],
	$podstawowe['wojewodztwo'],''));
/*
Array
(
    [Imie] => Michal
    [Nazwisko] => Oginski
    [data urodzenia] => 1976
    [ulica] => Batorego
    [ulica_nr] => 73b
    [ulica_nr_m] => 11
    [kod] => 33-300
    [miasto] => Nowy Sacz
    [tel_kraj] => +48
    [tel_kier] => 18
    [tel] => 4440444
    [tel_wew] => 424
    [tel_kom_kraj] => +48
    [tel_kom_op] => 609
    [tel_kom] => 139090
    [email] => michal@fakro.com.pl
)
Array
(
    [wyksztalcenie] => podstawowe
    [Szkola] => ala-1
    [Zawod] => ala-2
    [Kierunek] => ala-3
    [angielski] => ang[Brak]
    [niemiecki] => niem[Podstawowa]
    [francuski] => franc[Srednia]
    [rosyjski] => ros[Zaawans]
    [Inny_jezyk] => chinski
    [znajomoscInnegoJezyka] => inny[Biegla]
)
Array
(
    [doswiadczenie] => ala-4
    [uprawnieniaKursy] => ala-5
    [od] => 5000
    [do] => 10000
)
Array
(
    [zgoda] => tak
)

/*
echo "<pre>";
print_r($podstawowe);
print_r($umiejetnosci);
print_r($informacje);
print_r($ZGODA);
echo "</pre>";


?>