<?
	global $tid, $tquant, $tadd, $clearCart, $kwant, $cenat, $powiazane;



	header("Content-type: application/x-javascript");

	if (!$WEBTD->sid)
	{
		foreach ( array_keys($_REQUEST) AS $k ) eval("\$$k=\$_REQUEST[\"$k\"];");
		foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");
	}

	$INCLUDE_PATH = "..";
	include("$INCLUDE_PATH/pre.php");
	
	global $SKLEP_SESSION;	

	if (!function_exists("u_Cena"))
	{
		function u_Cena ($c)
		{
			return number_format($c,2,","," "). " zГ";
		}	
	}

	if ($clearCart)
	{
		unset($SKLEP_SESSION["KOSZYK_OFERT"]);
		echo "mainForm.submit();";
		return;
	}

	$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];

	if ($kwant != 0 && strlen($kwant))
	{
		$ilosc_kwantow = ceil($tquant / $kwant);
		$tquant = $ilosc_kwantow * $kwant;
	}

	$tquant+=0;

	if (!is_array($KOSZYK_OFERT) && !$tadd) return;
	
	if (is_array($KOSZYK_OFERT)) foreach (array_keys($KOSZYK_OFERT) AS $_tid)
	{
		$pow=$WM->towary_powiazane($_tid);
		$TOWARY_PROMOCYJNE_MAX[$pow]+=$KOSZYK_OFERT[$_tid];
	}

	$ts_aktywny=0;
	$query="SELECT ts_aktywny FROM towar_sklep WHERE ts_to_id=$tid AND ts_sk_id=".$SKLEP_SESSION[sklep];
	parse_str(ado_query2url($query));
	//$b=$adodb->projdb->database.'@'.$adodb->projdb->host;
	//echo "prompt('$b','$query');\n";
	//echo "alert('Tu jest roznica - ts_aktywny: $ts_aktywny');\n";
	

	$old_quant=$KOSZYK_OFERT[$tid];

	if ($tadd)
		$KOSZYK_OFERT[$tid] = $KOSZYK_OFERT[$tid]+$tquant;
	else
		$KOSZYK_OFERT[$tid] = $tquant;

	if (!$ts_aktywny && $TOWARY_PROMOCYJNE_MAX[$tid]<$tquant) $KOSZYK_OFERT[$tid]=$TOWARY_PROMOCYJNE_MAX[$tid];


	if ($powiazane)
	{
		if (isset($KOSZYK_OFERT[$powiazane])) 
		{

			if ($tquant==0) $KOSZYK_OFERT[$powiazane] = $KOSZYK_OFERT[$powiazane] - $old_quant;
			else $KOSZYK_OFERT[$powiazane] = $tquant;
			if ($KOSZYK_OFERT[$powiazane]<0) $KOSZYK_OFERT[$powiazane]=0;
		}
	}




	foreach (array_keys($KOSZYK_OFERT) AS $_tid) if (!$KOSZYK_OFERT[$_tid]) unset($KOSZYK_OFERT[$_tid]);

	$SKLEP_SESSION["KOSZYK_OFERT"] = $KOSZYK_OFERT;
	$WM->forget_debug = true;
	include("$INCLUDE_PATH/post.php");
?>

function getObject(objectId) {
	// cross-browser function to get an object's style object given its id
	if(document.getElementById && document.getElementById(objectId)) {
	// W3C DOM
	return document.getElementById(objectId);
	} else if (document.all && document.all(objectId)) {
	// MSIE 4 DOM
	return document.all(objectId);
	} else if (document.layers && document.layers[objectId]) {
	// NN 4 DOM.. note: this won't find nested layers
	return document.layers[objectId];
	} else {
	return false;
	}
} // getObject

<? if (strlen($cenat) && $tquant!=0) {?>
ilosc_input = getObject('ilosc_<? echo $tid ?>');
wartosc_td = getObject('wartosc_<? echo $tid ?>');
wartosc_td.innerHTML = '<? echo u_cena($cenat*$KOSZYK_OFERT[$tid])?>';
ilosc_input.value = '<? echo $KOSZYK_OFERT[$tid] ?>';
<?}?>

//if (<?echo $tquant+0?>==0) location.href=location.href;
location.href=location.href;
<?
	if (!$tadd) return;
	if ($SYSTEM[koszyk]) 
	{
		echo "goCart();";
		return;
	}
?>
alert(art_add);
obj=getObject('koszyk_ofert_szt');
if (obj!=null)
{
	ile=obj.innerHTML;
	ile=parseInt(ile);
	ile+=<?echo $tquant?>;
	obj.innerHTML=ile;
}
