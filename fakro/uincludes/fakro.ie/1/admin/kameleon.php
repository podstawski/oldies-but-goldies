<?
return;
function kameleon_get_tree($root) {
	global $SERVER_ID, $lang, $ver, $adodb;
	$sql = "SELECT * FROM webpage 
			WHERE prev=".$root." AND server=".$SERVER_ID." AND lang='".$lang."' AND ver=".$ver." 
			ORDER BY title";
	$res = $adodb->execute($sql);		
	$tree_id="";
	for ($i=0;$i<$res->RecordCount();$i++) {
		parse_str(ado_explodename($res, $i));		
		$tree_id.=$id.",";	
		$tree_id.=kameleon_get_tree($id);
	}
	return $tree_id;
}

function kameleon_get_tree_li($root) {
	global $SERVER_ID, $lang, $ver, $adodb;
	$sql = "SELECT * FROM webpage 
			WHERE prev=".$root." AND server=".$SERVER_ID." AND lang='".$lang."' AND ver=".$ver." 
			ORDER BY title";
	$res = $adodb->execute($sql);		
	$tree_id = "<ul>";
	for ($i=0;$i<$res->RecordCount();$i++) {
		parse_str(ado_explodename($res, $i));		
		if ($hidden) continue;
		$tree_id.="<li>".$title.kameleon_get_tree_li($id);
	}
	$tree_id.= "</ul>";
	return $tree_id;
}

function titlepage($_lang,$old_title,$new_title,$sqladd="")
{
	global $ver,$SERVER_ID,$adodb;
	$sql = "UPDATE webpage SET title='".$new_title."' WHERE title='".$old_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver." ".$sqladd.";";
	$ret = $sql."<br>";
//	$res = $adodb->execute($sql);			
	return $ret;
}

function titletd($_lang,$old_title,$new_title,$sqladd="")
{
	global $ver,$SERVER_ID,$adodb;
	$sql = "UPDATE webtd SET title='".$new_title."' WHERE title='".$old_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver." ".$sqladd.";";
//	$res = $sql."<br>";
	$res = $adodb->execute($sql);	
	return $res;
}

function plaintd($_lang,$titletd,$src_arr,$dest_arr,$sqladd="")
{
	global $ver,$SERVER_ID,$adodb;
	$sqls = "SELECT * FROM webtd WHERE title='".$titletd."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver." ".$sqladd.";";
	$ress = $adodb->execute($sqls);			
	$ret_count=0;
	for ($i=0;$i<$ress->RecordCount();$i++)
	{	
		parse_str(ado_explodename($ress, $i));	
		if (!strlen($plain)) continue;
		$plain_new = $plain;
		
		$plain_new = str_replace($src_arr,$dest_arr,$plain_new);
		if ($plain_new==$plain) continue;
		
		$plain_new=addslashes(stripslashes($plain_new));
		
//		echo $plain_new;
		
		$ret_count++;
		
		
		if (strlen($plain_new) && $sid)
		{
			$sql = "UPDATE webtd SET plain='".$plain_new."' WHERE sid=".$sid." AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver;
			//echo $sql."<br>";
			//$res = $adodb->execute($sql);
		}	
	}
	$ret.= $ret_count;
	return $ret;
}


//echo kameleon_get_tree_li(1);


//$ret = titletd("e","Charakterystyka","Features:");

$titletd="";
$LABEL_SRC[]="klasa szczelnoci";
$LABEL_DSC[]="watertightness";
$LABEL_SRC[]="klasa szczelno¶ci";
$LABEL_DSC[]="watertightness";
$LABEL_SRC[]="najwy¿sza";
$LABEL_DSC[]="the highest";
$LABEL_SRC[]="Grupa";
$LABEL_DSC[]="Group";
$LABEL_SRC[]="zobacz film";
$LABEL_DSC[]="SEE THE MOVIE";
$LABEL_SRC[]="ZOBACZ FILM";
$LABEL_DSC[]="SEE THE MOVIE";
$LABEL_SRC[]="ZOBACZ";
$LABEL_DSC[]="SEE";
$LABEL_SRC[]="PARAMETRY TECHNICZNE";
$LABEL_DSC[]="THE TECHNICAL SPECIFICATIONS";
$LABEL_SRC[]="Parametry techniczne";
$LABEL_DSC[]="Technical Specifications";
$LABEL_SRC[]="wspó³czynnik U<sub>OKNA</sub>";
$LABEL_DSC[]="window U-value";
$LABEL_SRC[]="wspó³czynnik U<sub>SZYBY</sub>";
$LABEL_DSC[]="glazing U-value";
$LABEL_SRC[]="wspó³czynnik Rw";
$LABEL_DSC[]="Rw coefficient";
$LABEL_SRC[]="zestaw szybowy wyp. gazem";
$LABEL_DSC[]="inert gass filled panes";
$LABEL_SRC[]="zestaw szybowy";
$LABEL_DSC[]="glazing";
$LABEL_SRC[]="warstwa niskoemisyjna";
$LABEL_DSC[]="low emission coating";
$LABEL_SRC[]="szyba hartowana";
$LABEL_DSC[]="toughened glass";
$LABEL_SRC[]="typ nawiewnika";
$LABEL_DSC[]="air inlet type";
$LABEL_SRC[]="wydajnoæ nawiewnika";
$LABEL_DSC[]="air inlet air flow";
$LABEL_SRC[]="wydajno¶æ nawiewnika";
$LABEL_DSC[]="air inlet air flow";
$LABEL_SRC[]="lakierowanie drewna";
$LABEL_DSC[]="varnishing";
$LABEL_SRC[]="uszczelki";
$LABEL_DSC[]="seals";
$LABEL_SRC[]="mikrouchylanie okna";
$LABEL_DSC[]="multi-point opening";
$LABEL_SRC[]="klamka";
$LABEL_DSC[]="handle";
$LABEL_SRC[]="czterokrotne";
$LABEL_DSC[]="four times";
$LABEL_SRC[]="czterokrotnie";
$LABEL_DSC[]="four times";
$LABEL_SRC[]="trzykrotne";
$LABEL_DSC[]="three times";
$LABEL_SRC[]="dwukrotne";
$LABEL_DSC[]="twice";
$LABEL_SRC[]="trzy";
$LABEL_DSC[]="three";

$LABEL_SRC[]="dopuszczalne obci±¿enie";
$LABEL_DSC[]="maximum safe loading";
$LABEL_SRC[]="wspó³czynnik U<sub>SCHODÓW</sub>";
$LABEL_DSC[]="insulation coefficient";
$LABEL_SRC[]="wysokoæ skrzyni";
$LABEL_DSC[]="box hight";
$LABEL_SRC[]="szerokoæ stopni";
$LABEL_DSC[]="tread width";
$LABEL_SRC[]="d³ugoæ stopni";
$LABEL_DSC[]="tread length";
$LABEL_SRC[]="gruboæ stopnia";
$LABEL_DSC[]="tread thickness";
$LABEL_SRC[]="gruboæ izolacji termicznej";
$LABEL_DSC[]="hatch insulation thickness";
/**/

$ret = plaintd("e",$titletd,$LABEL_SRC,$LABEL_DSC,"AND type=3 AND level=2")."<br>";
echo $ret;
return;

$sqls = "SELECT * FROM webtd WHERE title='Charakterystyka:' AND server=3 AND lang='e' AND ver=1";
$ress = $adodb->execute($sqls);
echo $ress->RecordCount()."<br>";
return;


global $db;


$sqls = "SELECT * FROM webtd WHERE plain~*'>Dopasowanie kolorystyczne:</td>' AND server=".$SERVER_ID." AND lang='f' AND ver=".$ver." ".$sqladd.";";
$ress = $adodb->execute($sqls);
echo $ress->RecordCount()."<br>";
for ($i=0;$i<$ress->RecordCount();$i++)
{	
		parse_str(ado_explodename($ress, $i));	
		$sql = "UPDATE webtd SET hidden=1 WHERE sid=".$sid;
//		$res = $adodb->execute($sql);
}	
return ;
/*
$titletd="";
$LABEL_ARR["Dane materia³owe"]="Datos del material";
$LABEL_ARR["Materia³"]="Material";
$LABEL_ARR["Gêsto¶æ"]="Densidad";
$LABEL_ARR["Grubo¶æ"]="Espesor";
$LABEL_ARR["Waga"]="Peso";
$LABEL_ARR["Pozosta³e dane"]="Otros datos";
$LABEL_ARR["polistyren extrudowany"]="poliestireno extrusionado";
$LABEL_ARR["Izolacja nad ogrzewanie pod³ogowe"]="Aislamiento sobre suelos radiantes";
$LABEL_ARR["polistyren ekspandowany"]="poliestireno expandido";
$LABEL_ARR["folia"]="lámina";
$LABEL_ARR["Paroizolacja"]="Barrera antivapor";
$LABEL_ARR["polietylen"]="polietileno";
$LABEL_ARR["Izolacja nad ogrzewanie pod³ogowe"]="Aislamiento sobre suelos radiantes";
$LABEL_ARR["paroizolacja"]="Barrera antivapor";
$LABEL_ARR["folia polietylenowa"]="lámina de polietileno";


$titletd="Parametry techniczne";
$LABEL_ARR["Trwa³o¶c pod³ogi"]="Durabilidad del suelo";
$LABEL_ARR["Odporno¶æ na obci±¿enia"]="Resistencia a la carga";
$LABEL_ARR["Niweluje nierówno¶ci w pod³o¿u"]="Cubre efectivamente las fi suras e irregularidades de la superfi cie";
$LABEL_ARR["Niska nasi±kliwo¶æ wod±"]="Baja absorción de agua";
$LABEL_ARR["Komfort u¿ytkowania"]="Comodidad de uso";
$LABEL_ARR["Izolacja akustyczna"]="Aislamiento acústico";
$LABEL_ARR["Izolacja termiczna"]="Aislamiento térmico";
*/
//$ret= plaintd("s",$titletd,$LABEL_ARR,"AND type IS NULL AND level=2")."<br>";
$ret = titletd("s","Izolacje","Aislamientos");
print_r($ret);

return;
/*ZMIANA TYTU£ÓW stron*/
$_title = "Monta¿ na ko³ek";
$_title_new = "Montaje con pasador";

$_lang = "s";
$sql = "SELECT * FROM webpage WHERE title='".$_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
$res = $adodb->execute($sql);		

$sql_u	= "UPDATE webpage SET title='".$_title_new."' WHERE title='".$_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
$adodb->execute($sql_u);
echo $sql_u."<br>".$res->RecordCount();
return;
/*END ZMIANA TYTU£ÓW STRON*/

/*ZMIANA LINKÓW*/
/*
$_title = "zakoñczenie";
$_title_new = "punta";

$_title = "³±cznik";
$_title_new = "pieza de unión";

$_title = "klamra mocuj±ca";
$_title_new = "grapa de fijación";

$_title = "Puszka MultiBox";
$_title_new = "caja de empalme MultiBox";

$_title = "naro¿nik<br>zewnêtrzny";
$_title_new = "cantonera<br>exterior";

$_title = "naro¿nik<br>wewnêtrzny";
$_title_new = "cantonera<br>interior";

$_lang = "s";

$sql = "SELECT * FROM weblink WHERE alt='".$_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
$res = $adodb->execute($sql);		

$sql_u	= "UPDATE weblink SET alt='".$_title_new."' WHERE alt='".$_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
$adodb->execute($sql_u);
echo $sql_u."<br>".$res->RecordCount();
return;
*/
/*END ZMIANA LINKÓW*/

/*ZMIANA TYTU£ÓW TD*/
/*
$_title = "Opakowanie";
$_title_new = "Embalaje";

$_title = "Monta¿";
$_title_new = "Montaje";

$_lang = "s";
$sql = "SELECT * FROM webtd WHERE title='".$_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
$res = $adodb->execute($sql);		

$sql_u	= "UPDATE webtd SET title='".$_title_new."' WHERE title='".$_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
$adodb->execute($sql_u);
echo $sql_u."<br>".$res->RecordCount();
return;
*/
/*END ZMIANA TYTU£ÓW TD*/







/*ZMIANA TYTU£ÓW STRON*/
/*
$sql = "SELECT * FROM webpage WHERE title='".$_title."' AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
$res = $adodb->execute($sql);		

	$tree_id = "<ul>";
	for ($i=0;$i<$res->RecordCount();$i++) {
		parse_str(ado_explodename($res, $i));		
		if ($hidden) continue;
		$tree_id.="<li>".$id.$title."<br>";
		
		$sql_u	= "UPDATE webpage SET title='".$_title_new."' WHERE id=".$id." AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;;
		
		$tree_id.="<br>".$plain_new;

		$adodb->execute($sql_u);
	}
	$tree_id.= "</ul>";
	echo  $tree_id;
return;
*/
/*END ZMIANA TYTU£ÓW STRON*/





//return;

?>