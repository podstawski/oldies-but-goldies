<?
/*
	Koszyk w Java Script
	
	Utworzono: 	01-07-2002
	Autor:		Piotr Podstawski

	Modfikacja:	09-04-2003
	Kto:		Piotr
	Opis:		doda³em funkcje ZwiekszWKoszyku

	
	
	Dostepne funkcje:
	 DodajDoKoszyka(cena,id), UsunZKoszyka(id), OproznijKoszyk(), ZwiekszWKoszyku(id,ilosc)

	 (id - identyfikator tekstowy)
	 
	koszyk jest dostepny w zmiennej $API_KOSZYK jako tablica
	dostep do niej najlepiej realizowac w petli:
		while ( list( $id, $wartosc ) = each( $API_KOSZYK ) )
		{
			...
		}
		$wartosc postaci: $cena:$ilosc
	
*/

global $API_KOSZYK_INCLUDED;
global $apikoszyk;


if (!isset($API_KOSZYK_DODANO)) $API_KOSZYK_DODANO="Dodano pozycjê do koszyka";
if (!isset($API_KOSZYK_OPROZNIONO)) $API_KOSZYK_OPROZNIONO="Usuniêto wszystkie pozycje z koszyka";
if (!isset($API_KOSZYK_USUNIETO)) $API_KOSZYK_USUNIETO="Usuniêto pozycjê z koszyka";

$API_KOSZYK=array();
if (strlen($apikoszyk))
{
	$KOSZYK_TAB=explode("&",$apikoszyk);

	for ($i=0;$i<count($KOSZYK_TAB);$i++)
	{
		$pos=explode(":",$KOSZYK_TAB[$i]);
		if (!strlen($pos[0]) || !strlen($pos[1]) ) continue;
		$id=$pos[0];
		$cena=ereg_replace(",",".",$pos[1]);
		$ilosc=$pos[2];		

		if ($id==".") 
		{
			$API_KOSZYK=array();
			continue;
		}
		
		
		$ilosc_old=$API_KOSZYK[$id];
		$ilosc_old=explode(":",$ilosc_old);
		$ilosc_old=0+$ilosc_old[1];
		
		if ($ilosc[0]=="#")
		{
			$ilosc=0+substr($ilosc,1);
			$ilosc+=$ilosc_old;
		}

		$API_KOSZYK[$id]="$cena:$ilosc";
			
	}
	$apikoszyk="";
	while ( list( $id, $wartosc ) = each( $API_KOSZYK ) )
	{
		$w=explode(":",$wartosc);
		$cena=$w[0];
		$ilosc=$w[1];
		if (!$ilosc) continue;
		if (strlen($apikoszyk)) $apikoszyk.="&";
		$apikoszyk.="$id:$cena:$ilosc";
	}
	
	$API_KOSZYK=array();
	$KOSZYK_TAB=explode("&",$apikoszyk);
	for ($i=0;$i<count($KOSZYK_TAB) && strlen($apikoszyk);$i++)
	{
		$pos=explode(":",$KOSZYK_TAB[$i]);
		$id=$pos[0];
		$cena=$pos[1];
		$ilosc=$pos[2];		

		$API_KOSZYK[$id]="$cena:$ilosc";
	}	
}

if ($API_KOSZYK_INCLUDED==1) return;
$API_KOSZYK_INCLUDED=1;



?>
<script>
 apikoszyk="<?echo addslashes($apikoszyk)?>";
 <? if (strlen($apikoszyk)) {?>
 document.cookie= "apikoszyk="+apikoszyk;
 <? } ?>


 
 function DodajDoKoszyka(cena,id)
 {
	apikoszyk = apikoszyk + "&" + id + ":" + cena + ":" + "#1";
	document.cookie =  "apikoszyk="+apikoszyk;
	<? if (strlen($API_KOSZYK_DODANO)) {?> 
	alert ("<?echo $API_KOSZYK_DODANO?>");
	<?}?>


 }
 function OproznijKoszyk()
 {
	apikoszyk = apikoszyk + "&.:0:0";
	document.cookie =  "apikoszyk="+apikoszyk;
	<? if (strlen($API_KOSZYK_OPROZNIONO)) {?> 
	alert ("<?echo $API_KOSZYK_OPROZNIONO?>");
	<?}?>


 }
 function UsunZKoszyka(id)
 {
	apikoszyk = apikoszyk + "&" + id + ":0:0";
	document.cookie =  "apikoszyk="+apikoszyk;
	<? if (strlen($API_KOSZYK_USUNIETO)) {?> 
	alert ("<?echo $API_KOSZYK_USUNIETO?>");
	<?}?>
	
 } 
 
 function ZwiekszWKoszyku(id,ilosc)
 {
	apikoszyk = apikoszyk + "&" + id + ":" + "0" + ":" + "#" + ilosc;
	document.cookie =  "apikoszyk="+apikoszyk;
 }

 
</script>

