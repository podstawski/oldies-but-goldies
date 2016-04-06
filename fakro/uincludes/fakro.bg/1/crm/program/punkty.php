<?
global $id, $_GET;

$_path = $INCLUDE_PATH.'/crm/program/';

require_once($_path.'includes/conf.php');
require_once($_path.'includes/idb_mysql.php');

$idb =  new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);

if($KAMELEON_MODE) $link = '&';
	else $link = '?';

$_crm_id_osoba = $_SESSION["CAUTH"]["su_id_crm"];

$result = $idb->query("
						SELECT
							pl_programy.id_pl_programy,
							pl_programy.data_rozpoczecia,
							pl_programy.data_zakonczenia,
							pl_programy.nazwa,
							pl_programy.opis,
							pl_programy__firmy__osoby.id_osoby
						FROM
							pl_programy,
							pl_programy__firmy__osoby
						WHERE
							pl_programy.id_pl_programy = pl_programy__firmy__osoby.id_pl_programy AND
							pl_programy._is_adding = '0000-00-00 00:00:00' AND
							pl_programy__firmy__osoby.id_osoby = '".$_crm_id_osoba."' AND
							pl_programy.id_pl_programy = '".$_GET['id']."'
						GROUP BY
							pl_programy.id_pl_programy
						ORDER BY
							nazwa
						LIMIT 15");

for($i = 0; $i < $idb->rowcount(); $i++) {
	$idb->getvalues();
	
	$row['id_pl_programy'] = $idb->row['id_pl_programy'];
	$row['data_rozpoczecia'] = $idb->row['data_rozpoczecia'];
	$row['data_zakonczenia'] = $idb->row['data_zakonczenia'];
	$row['nazwa'] = $idb->row['nazwa'];
	$row['opis'] = $idb->row['opis'];
	
	$wyniki[0]['program'][$i] = $row;
	}

$result_liczba_punktow = $idb->query("
						SELECT
							SUM(p.ilosc*pp.punkty) AS suma
						FROM
							pl_programy__zgloszenia AS z
							INNER JOIN pl_programy__zgloszenia__produkty AS p ON (p.id_pl_programy__zgloszenia = z.id_pl_programy__zgloszenia)
							INNER JOIN pl_programy__opcje_produkty AS pp ON (z.id_pl_programy = pp.id_pl_programy and p.id_pl_opcje_produkty = pp.id_pl_opcje_produkty)
						WHERE
							(((z.id_wlasciciel<1) OR (z.id_wlasciciel is null)) AND
							z.id_osoby_z_firmy = '".$_crm_id_osoba."') AND
							z.id_pl_programy = '".$_GET['id']."'");
$result_liczba_punktow = $idb->getvalues($result_liczba_punktow);
$wyniki[0]['liczba_punktow'] = $result_liczba_punktow['suma'];

$result_wydane_punkty = $idb->query("
						(SELECT IF(SUM(pl_programy__opcje_nagrody.punkty)>0,SUM(pl_programy__opcje_nagrody.punkty),0) AS suma
						FROM
							pl_wydawanie_nagrod AS z
							LEFT JOIN pl_programy__opcje_nagrody ON (pl_programy__opcje_nagrody.id_pl_programy = z.id_pl_programy AND
							(pl_programy__opcje_nagrody.id_pl_opcje_nagrody = z.id_pl_opcje_nagrody))
						WHERE
							pl_programy__opcje_nagrody.id_pl_programy = '".$_GET['id']."' AND
							(z.id_osoby = '".$_crm_id_osoba."'))");
$result_wydane_punkty = $idb->getvalues($result_wydane_punkty);
$wyniki[0]['wydane_punkty'] = $result_wydane_punkty['suma'];

$result_liczba_wydan = $idb->query("
						(SELECT COUNT(pl_programy__opcje_nagrody.punkty) AS suma
						FROM
							pl_wydawanie_nagrod AS z
							LEFT JOIN pl_programy__opcje_nagrody ON (pl_programy__opcje_nagrody.id_pl_programy = z.id_pl_programy AND
							(pl_programy__opcje_nagrody.id_pl_opcje_nagrody = z.id_pl_opcje_nagrody))
						WHERE
							pl_programy__opcje_nagrody.id_pl_programy = '".$_GET['id']."' AND
							(z.id_osoby = '".$_crm_id_osoba."'))");
$result_liczba_wydan = $idb->getvalues($result_liczba_wydan);
$wyniki[0]['liczba_wydan'] = $result_liczba_wydan['suma'];

$wyniki[0]['roznica_punktow'] = ($result_liczba_punktow['suma'] - $result_wydane_punkty['suma']);


/*
liczba_punktow
select sum(p.ilosc*pp.punkty) as suma from pl_programy__zgloszenia as z 
					inner join pl_programy__zgloszenia__produkty as p on (p.id_pl_programy__zgloszenia = z.id_pl_programy__zgloszenia ) 
					inner join pl_programy__opcje_produkty as pp on (z.id_pl_programy = pp.id_pl_programy and p.id_pl_opcje_produkty = pp.id_pl_opcje_produkty ) where  (((z.id_wlasciciel<1) or (z.id_wlasciciel is null) )  and z.id_osoby_z_firmy ="16083") and z.id_firmy = "60267" and  z.id_pl_programy = "601"

wydane_punkty
(select  IF(sum( pl_programy__opcje_nagrody.punkty)>0,sum( pl_programy__opcje_nagrody.punkty),0) as suma  from pl_wydawanie_nagrod as z 
					left join pl_programy__opcje_nagrody on (pl_programy__opcje_nagrody.id_pl_programy =  z.id_pl_programy and (pl_programy__opcje_nagrody.id_pl_opcje_nagrody =  z.id_pl_opcje_nagrody )  )
					 where z.id_firmy = "60267"  and  pl_programy__opcje_nagrody.id_pl_programy = "601" and ( z.id_osoby ="16083"     ))

liczba_wydan
(select  count( pl_programy__opcje_nagrody.punkty ) as suma  from pl_wydawanie_nagrod as z 
					left join pl_programy__opcje_nagrody on (pl_programy__opcje_nagrody.id_pl_programy =  z.id_pl_programy and (pl_programy__opcje_nagrody.id_pl_opcje_nagrody =  z.id_pl_opcje_nagrody )  )
					 where z.id_firmy = "60267"  and  pl_programy__opcje_nagrody.id_pl_programy = "601" and ( z.id_osoby ="16083"     ))

roznica_punktow = liczba_punktow - wydane_punkty
*/
?>