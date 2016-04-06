<?php

if (!function_exists("getFullPath"))
{
	function getFullPath($id)
	{		
		$sql = "SELECT ka_nazwa, ka_parent FROM kategorie WHERE ka_id = $id";
		parse_str(ado_query2url($sql));
		if ($ka_parent)
			return "$ka_nazwa;".getFullPath($ka_parent);
		else
			return "$ka_nazwa";
	}
}


if (!function_exists("unPolish"))
{
	function unPolish($str)
	{
		$str=ereg_replace("Ё","A",$str);
		$str=ereg_replace("Б","a",$str);
		$str=ereg_replace("Ѕ","A",$str);
		$str=ereg_replace("Й","a",$str);
		$str=ereg_replace("І","S",$str);
		$str=ereg_replace("Ж","s",$str);
		$str=ereg_replace("","S",$str);
		$str=ereg_replace("","s",$str);
		$str=ereg_replace("Ќ","Z",$str);
		$str=ereg_replace("М","z",$str);
		$str=ereg_replace("","Z",$str);
		$str=ereg_replace("","z",$str);
		$str=ereg_replace("ъ","e",$str);
		$str=ereg_replace("Ъ","E",$str);
		$str=ereg_replace("П","z",$str);
		$str=ereg_replace("Џ","Z",$str);
		$str=ereg_replace("ё","n",$str);
		$str=ereg_replace("б","N",$str);
		$str=ereg_replace("ѓ","o",$str);
		$str=ereg_replace("г","O",$str);
		$str=ereg_replace("ц","c",$str);
		$str=ereg_replace("Ц","C",$str);
		$str=ereg_replace("Г","l",$str);
		$str=ereg_replace("Ѓ","L",$str);
		return ($str);
	}
}

if (!function_exists("unhtml"))
{
	function unhtml($html)
	{
		return ereg_replace("\<[^>]*>","",$html);
	}
}


if (!function_exists("sysmsg"))
{
	function sysmsg($msg,$grupa="")
	{
		global $WM;
		return $WM->_sysmsg($msg,$grupa);
	}
}

if (!function_exists("win2iso"))
{
	function win2iso($str)
	{
		$str=ereg_replace("Ѕ","Ё",$str);
		$str=ereg_replace("","І",$str);
		$str=ereg_replace("","Ќ",$str);
		$str=ereg_replace("Й","Б",$str);
		$str=ereg_replace("","Ж",$str);
		$str=ereg_replace("","М",$str);
		return ($str);
	}

	function iso2win($f_text)
	{
		$f_text = strtr($f_text, 'Ж', '');
		$f_text = strtr($f_text, 'Б', 'Й');
		$f_text = strtr($f_text, 'М', '');
		$f_text = strtr($f_text, 'І', '');
		$f_text = strtr($f_text, 'Ё', 'Ѕ');
		$f_text = strtr($f_text, 'Ќ', '');

		return $f_text;
	}

} 



 function system_UnAuthorize($realm,$text="")
 {
        Header("WWW-Authenticate: Basic realm=\"$realm\"");
        Header("HTTP/1.0 401 Unauthorized");
        echo $text;
        exit();
 }



if (!function_exists("ado_ExplodeName")) {
 function ado_ExplodeName ($result,$row)
 {
	global $WM;
	return $WM->ado_ExplodeName ($result,$row);
 }
}

if (!function_exists("fcontent")) {
	function fcontent($f)
	{
		$plik=@fopen($f,"r");
		if (!$plik) return;
		while(1)
		{
			$c=fread($plik,1024);
			if (!strlen($c)) break;
			$wynik.=$c;
		}
		fclose($plik);
		return $wynik;
	}
}


function quoteUrlEnc($txt)
{
	return str_replace("%22","%26quot;",$txt);
}

if (!function_exists("ado_query2url")) {
 function ado_query2url($query,$may_cache=false)
 {
	global $WM;
	return $WM->ado_query2url($query,$may_cache);
 }
}

	function urlEncodedStr2arr($str)
	{

		$a=explode("&",$str);
		foreach ($a AS $pair)
		{
			$p=explode("=",$pair);
			$wynik[$p[0]]=urldecode($p[1]);

		}
		return $wynik;
	}


 function navi($href,$list,$size)
 {
	$ile=$list[ile]+0;

	if ($ile<=$size || !$size) return "";
	$next=$list[start]+$size;
	if ($next>=$ile) $next="";


	if ($list[start])
	{
		$prev=$list[start]-$size;


		if ($prev<0) $prev=0;

	}	



	$href.=strstr($href,"?")?"&":"?";
	$href.="list[ile]=".$list[ile];
	$href.="&list[sort_f]=".$list[sort_f];

	$href.="&list[sort_d]=".($list[sort_d]+0);

	if (strlen($list[szukaj])) $href.="&list[szukaj]=".urlencode($list[szukaj]);

	if (strlen($list[id])) $href.="&list[id]=".$list[id];

	$current=round($list[start]/$size);
	$first=$current-floor(C_NAVI_PAGES/2)+1;

	$last=$first+C_NAVI_PAGES;

	while ($first<0)
	{
		$first++;
		$last++;
	}


	while ($last*$size>=$ile)
	{
		if ($first>0) $first--;
		$last--;
	}

	for ($i=$first;$i<=$last;$i++)
	{
		$page=$i+1;
		$start=$i*$size;
		if ($i!=$current) $wynik.=" <a href=\"$href&list[start]=$start\">[";
		else $wynik.="<span class=\"current\">";


		$wynik.=$page;

		if ($i!=$current) $wynik.="]</a> ";
		else $wynik.="</span>";
	}


	if (strlen($prev)) $prev="<a href=\"$href&list[start]=$prev\">&laquo;&laquo;</a> ";
	if (strlen($next)) $next=" <a href=\"$href&list[start]=$next\">&raquo;&raquo;</a>";

	$razem=sysmsg("Total items","system");
	$strony=sysmsg("pages","system");
	$wynik="<span class=\"list_navi\">$razem: $ile, $strony: $prev$wynik$next</span>";


	return $wynik;
 }

 function txt_addslash($a)
 {

	if (is_array($a))
	{
		while (list($key,$val)=each($a))
		{
			$a[$key]=txt_addslash($val);
		}
		return ($a);
	}
	else
		return (addslashes(stripslashes(trim($a))));
 }


 function sort_navi_options($LIST)
 {

	if (!is_array($LIST)) return "";
	while (list($k,$v)=each($LIST))
	$wynik.="<input type=\"hidden\" name=\"list[$k]\" value=\"$v\">";

	return ($wynik);
 }

 function sort_navi_qs($LIST)
 {

	if (!is_array($LIST)) return "";

	while (list($k,$v)=each($LIST))
	{
		if ($k=="id") continue;
		if (strlen($wynik)) $wynik.="&";
		$wynik.="list[$k]=".urlencode($v);
	}
	return ($wynik);
 }

 
 function humandate($unix)
 {
	return date("d-m-Y",$unix);
 }
 function humanshort($unix)
 {
	return substr(humandate($unix),0,5);
 }

 
 function unixdate($human,$plus="")
 {

	$d=explode("-",ereg_replace("[^0-9\-]","",$human));
	if (3!=count($d)) return 0;


	if (!strlen($plus))
	{
		$h=date("H");
		$m=date("i");
		$s=date("s");
		return mktime($h,$m,$s,$d[1],$d[0],$d[2]);
	}
	return ($plus*3600*24) + mktime(0,0,0,$d[1],$d[0],$d[2]);
}



 function bm($month=0)
 {

	$month=round($month);
	$_d=date("d");
	$_m=date("m");
	$_y=date("Y");
	
	$d=date("d",mktime(0,0,0,$_m+$month,$_d,$_y));
	$m=date("m",mktime(0,0,0,$_m+$month,$_d,$_y));
	$y=date("Y",mktime(0,0,0,$_m+$month,$_d,$_y));

	$wynik->first_human=date("01-m-Y",mktime(0,0,0,$m,$d,$y));
	$wynik->last_human=date("d-m-Y",mktime(0,0,0,$m+1,1,$y)-3600);
	$wynik->first_unix=unixdate($wynik->first_human,0);
	$wynik->last_unix=unixdate($wynik->last_human,1)-1;

	return ($wynik);
}

function waluta()
{
	return sysmsg('currency','system');
}

if (!function_exists("FormatujDate")) {
 function FormatujDate ($d)
 {
   return substr($d,8,2)."-".substr($d,5,2)."-".substr($d,0,4);
   //return substr($d,3,2)."-".substr($d,0,2)."-".substr($d,6,4);

 }
 function FormatujDateSQL ($d)
 {
   return substr($d,6,4)."-".substr($d,3,2)."-".substr($d,0,2);
 }
 
 
 function u_Cena ($c,$w='')
 {
	if (!strlen($w)) $w=waluta();
	return number_format($c,2,","," "). " $w";
 }

 function query2url($query)
 {
        global $db;

        $result=pg_Exec($db,$query);
        if ( pg_numRows($result)!=1 ) return "";

        $data=pg_fetch_row($result,0);
        $wynik="";
        for ($i=0;$i<count($data);$i++)
        {
                if ($i) $wynik.="&";
                $wynik.=pg_fieldname($result,$i)."=".urlencode(trim($data[$i]));
        }
        return $wynik;
 }

 function pg_ExplodeName ($result,$row)
 {
 
	$text="";
	$cols=pg_NumFields($result);
 
	for ($i=0;$i<$cols;$i++)
	{
		$name=pg_FieldName($result,$i);
		$data=pg_fetch_row($result,$row);
		$value=urlencode(trim($data[$i]));
		$text.="$name=$value";
		if ($i!=$cols-1) $text.="&";
	}
	return $text;
 }

}

if (!function_exists("toDecimalOrNull")) {
 function toDecimalOrNull($f)
 {
	$f=ereg_replace(",",".",$f);
	$f=ereg_replace("[^0-9\.]","",$f);
	if (!strlen($f)) $f="NULL";
	return ($f);
 }
}

//Camel - dodalem ta funkcje bo sie nie moglem doczekac, az mi plik zrobisz.

if (!function_exists("naviIndex")) {
function naviIndex($href,$start,$offset,$ile,$size)
{
	global $navi;
	if ($start<0 || $start>$ile) $start=0;
	$start+=0;
	if (!strlen($navi)) $navi=1;
	$offset=0+$start;
	if ($start+$size<$ile)
		$next=$start+$size;

	$_dest=urlencode($dest);

	$naviend=5;
	$pom=$naviend * $size;
	if ($pom>$ile)
		$naviend=0+floor($ile / $size);
	else
		$naviend=5;

	if ($start==($navi+$naviend)*$size)
		$navi=$start/$size;
	else
	if ($start<$navi*$size)
		if ($navi-$naviend<=0)
			$navi=1;
		else
			$navi=$navi-$naviend;

	
	$all_link="$href&navi=$navi";

	$next_link=$all_link."&ile=$ile&start=$next";
	$back=$offset-$size;
	$prev_link=$all_link."&ile=$ile&start=$back";

	$linkp="&nbsp;";
	$linkn="&nbsp;";
	if ($start==0)
		$linkn="<a href=$next_link><b>nastъpne</b> &raquo;&raquo;</a> ";
	else
	{
		$linkp="<a href=$prev_link>&laquo;&laquo; <b>poprzednie</b></a>";
		if ($start+$size<$ile)
			$linkn="<a href=$next_link><b>nastъpne</b> &raquo;&raquo;</a>";
	}

	$pasek="";
	//echo "<br>navi=$navi, naviend=$naviend, ile=$ile, size=$size<br>";return;
	for ($i=$navi;$i<=$navi+$naviend;$i++)
	{
   		$n=$i*$size;
		$navistart=$n-$size;
		if ($n==$size)
		{
			if ($n==$start+$size)
				$pasek.="<font color=red><b>$i</b></font> ";
			else
				$pasek.="[<a style='nawigacja_link' href=$all_link&ile=$ile&start=$navistart>$i</a>] ";
		}
		else
		{
			if ($n==$start+$size)
				$pasek.=" <font color=red><b>$i</b></font> ";
			else
				$pasek.=" [<a style='nawigacja_link' href=$all_link&ile=$ile&start=$navistart>$i</a>] ";
		}
	}
	$stron= ceil ($ile / $size);
	$pasek.=" ... z <b>$stron</b>";
	if ($ile>$size)
	{
		$nawigacja="
		 <table border=0 cellpadding=0 cellspacing=3>
		 <tr>
		   <td align=left nowrap>$linkp &nbsp;&nbsp;</td>
		   <td align=left>Strony $pasek</td>
		   <td align=right>&nbsp;&nbsp;$linkn</td>
		 </tr>
 		 </table>";
	}
	return $nawigacja;
}
}

function toFloat($f)
{
		if (!strlen(trim($f))) return "NULL";
		$f=ereg_replace("[^0-9,\.]*","",$f);
		$f=ereg_replace(",",".",$f);
		return $f+0;
}

if (!function_exists("quoted_printable_encode")) {
	function quoted_printable_encode($str)
	{
		$_str = $str;

		$str_ok = explode(" ", $_str);
		
		$ret = "";
		for ($i=0; $i<sizeof($str_ok); $i++)
		{
			$orig = $str_ok[$i];

			$str_ok[$i] = ereg_replace("=", "=3D", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("\?", "=3F", $str_ok[$i]);



			$str_ok[$i] = ereg_replace("Ё", "=A1", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("І", "=A6", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("Ц", "=C6", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("Ќ", "=AC", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("Џ", "=AF", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("б", "=D1", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("г", "=D3", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("Ѓ", "=A3", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("Ъ", "=CA", $str_ok[$i]);

			$str_ok[$i] = ereg_replace("Б", "=B1", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("Ж", "=B6", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("ц", "=E6", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("М", "=BC", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("П", "=BF", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("ё", "=F1", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("ѓ", "=F3", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("Г", "=B3", $str_ok[$i]);
			$str_ok[$i] = ereg_replace("ъ", "=EA", $str_ok[$i]);

		

			$ret.= $str_ok[$i]." ";
		}

		$wynik='';
		for ($i=0;$i<strlen($ret);$i++)
		{
			if (ord($ret[$i])>127) $wynik.=sprintf('=%02X',ord($ret[$i]));
			else $wynik.=$ret[$i];
		}


		return $wynik;
	}
}

	function phpfun_html($HTML)
	{
		while($pos=strpos(strtolower($HTML),"<phpfun>"))
		{
			$wynik.=substr($HTML,0,$pos)."<phpfun>";
			$HTML=substr($HTML,$pos+8);
			$pos=strpos(strtolower($HTML),"</phpfun>");
			if ($pos)
			{
				$wynik.=stripslashes( substr($HTML,0,$pos) )."</phpfun>";
				$HTML=substr($HTML,$pos+9);
			}

		}
		$HTML=$wynik.$HTML;

		$HTML=eregi_replace("\<phpfun\>","\".",$HTML);
		$HTML=eregi_replace("\</phpfun\>",".\"",$HTML);

		return $HTML;
	}



	function enc2enc($txt,$e1,$e2)
	{
		if ($e1==$e2) return $txt;

		global $SKLEP_INCLUDE_PATH;
		static $cc;

		include_once("$SKLEP_INCLUDE_PATH/class/ConvertCharset.class.php");

		if (!is_object($cc)) $cc=new ConvertCharset();

		return $cc->Convert($txt, $e1, $e2);

	}



?>