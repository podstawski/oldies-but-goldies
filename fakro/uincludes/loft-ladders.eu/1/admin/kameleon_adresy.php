<?
function kameleon_get_tree($root) {
	global $SERVER_ID, $lang, $ver, $adodb;
	$sql = "SELECT * FROM webpage 
			WHERE prev=".$root." AND server=".$SERVER_ID." AND lang='".$lang."' AND ver=".$ver." 
			ORDER BY title";
	$res = $adodb->execute($sql);		
	$tree_id="";
	for ($i=0;$i<$res->RecordCount();$i++) {
		parse_str(ado_explodename($res, $i));		
		$tree_id.=$id;	
		$tree_id.=",";	
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
	//$ret = $sql."<br>";
	$res = $adodb->execute($sql);	
	return $res;
}

function plaintd($_lang,$titletd,$plain_arr,$sqladd="")
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
		
		for ($p=0;$p<count($plain_arr);$p++)
		{
			$key_plain = key($plain_arr);
			next($plain_arr);
			$plain_new = ereg_replace($key_plain,$plain_arr[$key_plain],$plain_new);
		}	
		reset($plain_arr);
		if ($plain_new==$plain) continue;
		
		$ret_count++;
		
		$plain_new=addslashes(stripslashes($plain_new));
		if (strlen($plain_new) && $sid)
		{
			$sql = "UPDATE webtd SET plain='".$plain_new."' WHERE sid=".$sid." AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver;
			$res = $adodb->execute($sql);
		}	
		
	}
	$ret.= $ret_count;
	return $ret;
}

//print_r($WEBTD);
$rep_arr1[]='<table width="100%" border="0" class="tabelka">';
$rep_arr1[]='</table>';
$rep_arr1[]='<colgroup><col class="sklc1"></col><col class="sklc2"></col><col class="sklc3"></col></colgroup>';
$rep_arr1[]='<tbody>';
$rep_arr1[]='</tbody>';
$rep_arr1[]='<font face="Arial" color="#646464">';
$rep_arr1[]='</font>';
$rep_arr1[]='Leroy Merlin';
$rep_arr1[]='Praktiker';
$rep_arr1[]='Nomi';
$rep_arr1[]='Bricomarche';
$rep_arr1[]='OBI';
$rep_arr1[]='Castorama';
$rep_arr1[]='<tr>';
$rep_arr1[]='</tr>';
$rep_arr1[]='</div></td>';
$rep_arr1[]='</td>';
$rep_arr1[]='<td>';
$rep_arr1[]='&nbsp;';

/*
$rep_arr1[]='<tr>';
$rep_arr1[]='</tr>';
$rep_arr1[]='<td>';
$rep_arr1[]='</td>';
*/

$rep_arr2[]='<ul class="sklepy">';
$rep_arr2[]='</ul>';
$rep_arr2[]='';
$rep_arr2[]='';
$rep_arr2[]='';
$rep_arr2[]='';
$rep_arr2[]='';
$rep_arr2[]='<div class=nazwa>Leroy Merlin</div>';
$rep_arr2[]='<div class=nazwa>Praktiker</div>';
$rep_arr2[]='<div class=nazwa>Nomi</div>';
$rep_arr2[]='<div class=nazwa>Bricomarche</div>';
$rep_arr2[]='<div class=nazwa>OBI</div>';
$rep_arr2[]='<div class=nazwa>Castorama</div>';
$rep_arr2[]='<li>';
$rep_arr2[]='</li>';
$rep_arr2[]='</div>';
$rep_arr2[]='<br>';
$rep_arr2[]='';
$rep_arr2[]='';
/*
$rep_arr2[]='<li>';
$rep_arr2[]='</li>';
$rep_arr2[]='';
$rep_arr2[]='&nbsp;';
*/

/*
echo kameleon_get_tree_li(448);
return;
*/
$page_tree = kameleon_get_tree(445);
$page_tree = substr($page_tree,0,(strlen($page_tree)-1));
$sql = "SELECT * FROM webtd WHERE page_id IN (".$page_tree.") AND level=2 AND server=".$SERVER_ID." AND lang='i' AND ver=".$ver;
$res = $adodb->execute($sql);
for ($i=0;$i<$res->RecordCount();$i++)
{	
	parse_str(ado_explodename($res, $i));	
	
	$plain_new = str_replace($rep_arr1,$rep_arr2,$plain);
	echo $page_id.": ".$title."<br>".$plain_new."<br><br>";
	$plain_new = addslashes(stripslashes($plain_new));
	
	
	$sqlu = "UPDATE webtd SET title='',plain='".$plain_new."',level=3 WHERE sid=".$sid;
//	echo $sqlu."<br>";
//	$resu = $adodb->execute($sqlu);
}	

return;

$ret = titletd("s","Kolorystyka","Colores");
print_r($ret);

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