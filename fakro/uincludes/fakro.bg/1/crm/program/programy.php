<?
global $goto, $next;
global $link_punkty;

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
							pl_programy__firmy__osoby.id_osoby = '".$_crm_id_osoba."'
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
	
	$row['next'] = $link.'id='.$idb->row['id_pl_programy'];
	
	$wyniki[0]['program'][$i] = $row;
	}

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