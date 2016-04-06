<?php

if (!function_exists("unPolish"))
{
	function unPolish($str)
	{
		$str=ereg_replace("¡","A",$str);
		$str=ereg_replace("±","a",$str);
		$str=ereg_replace("¥","A",$str);
		$str=ereg_replace("¹","a",$str);
		$str=ereg_replace("¦","S",$str);
		$str=ereg_replace("¶","s",$str);
		$str=ereg_replace("","S",$str);
		$str=ereg_replace("","s",$str);
		$str=ereg_replace("¬","Z",$str);
		$str=ereg_replace("¼","z",$str);
		$str=ereg_replace("","Z",$str);
		$str=ereg_replace("","z",$str);
		$str=ereg_replace("ê","e",$str);
		$str=ereg_replace("Ê","E",$str);
		$str=ereg_replace("¿","z",$str);
		$str=ereg_replace("¯","Z",$str);
		$str=ereg_replace("ñ","n",$str);
		$str=ereg_replace("Ñ","N",$str);
		$str=ereg_replace("ó","o",$str);
		$str=ereg_replace("Ó","O",$str);
		$str=ereg_replace("æ","c",$str);
		$str=ereg_replace("Æ","C",$str);
		$str=ereg_replace("³","l",$str);
		$str=ereg_replace("£","L",$str);
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

if (!function_exists("win2iso"))
{
	function win2iso($str)
	{
		$str=ereg_replace("¥","¡",$str);
		$str=ereg_replace("","¦",$str);
		$str=ereg_replace("","¬",$str);
		$str=ereg_replace("¹","±",$str);
		$str=ereg_replace("","¶",$str);
		$str=ereg_replace("","¼",$str);
		return ($str);
	}
} 

if (!function_exists("ado_ExplodeName")) 
{
	function ado_ExplodeName ($result,$row)
	{
		global $WM;
		return $WM->ado_ExplodeName ($result,$row);
 	}
}

if (!function_exists("humandate"))  
{
	function humandate($unix) 
	{
		return date("d-m-Y",$unix);
	}
}

if (!function_exists("humanshort")) 	
{
	function humanshort($unix)
	{
		return substr(humandate($unix),0,5);
	}
}
 
if (!function_exists("FormatujDate")) 	
{
	function FormatujDate ($d)
	{
		return substr($d,8,2)."-".substr($d,5,2)."-".substr($d,0,4);
	}
}
 
if (!function_exists("FormatujDateSQL")) 	
{
	function FormatujDateSQL ($d)
	{
		return substr($d,6,4)."-".substr($d,3,2)."-".substr($d,0,2);
	}
}
 
if (!function_exists("u_Cena")) 	
{
	function u_Cena ($c)
	{
		return number_format($c,2,","," "). " z³";
	}
}
 
if (!function_exists("query2url")) 	
{
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
}
 
if (!function_exists("pg_ExplodeName")) 	
{
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
 
if (!function_exists("toFloat")) 	
{
	function toFloat($f)
	{
		if (!strlen(trim($f))) return "NULL";
		$f=ereg_replace("[^0-9,\.]*","",$f);
		$f=ereg_replace(",",".",$f);
		return $f+0;
	}
}

if (!function_exists("iso2utf")) {
	function iso2utf($f_text)
	{
		global $INCLUDE_PATH,$lang;
		if (!class_exists("ConvertCharset"))
			include("ConvertCharset.class.php");
		$newEncoding = new ConvertCharset;
		
		$_iso = "iso-8859-";
		SWITCH ($lang)
		{
			CASE 'i':	$_iso.=2; BREAK;
			CASE 'r':	$_iso.=5; BREAK;
			DEFAULT: $_iso.=1;
		}
		if ($lang=="t" || $lang=="p") $_iso = "windows-1250";
		
		return $newEncoding->Convert($f_text, $_iso, 'utf-8', false);
	}
}

if (!function_exists(utf82iso)) {		
	function utf82iso($f_text)
	{

		$f_text = str_replace("Å","³",$f_text);
		$f_text = str_replace("³","³",$f_text);
		$f_text = str_replace("Å","£",$f_text);
		$f_text = str_replace("Å","ñ",$f_text);
		$f_text = str_replace("Å","Ñ",$f_text);
		$f_text = str_replace("Ä","ê",$f_text);
		$f_text = str_replace("Ä","Ê",$f_text);
		$f_text = str_replace("Ã³","ó",$f_text);
		$f_text = str_replace("ó","ó",$f_text);
		$f_text = str_replace("Ã","Ó",$f_text);
		$f_text = str_replace("Å¼","¿",$f_text);
		$f_text = str_replace("³»","¿",$f_text);
		$f_text = str_replace("Å»","¯",$f_text);
		$f_text = str_replace("Å","",$f_text);
		$f_text = str_replace("¾","±",$f_text);//¹		
		$f_text = str_replace("Ä","±",$f_text);//¹
		$f_text = str_replace("Ä","¡",$f_text);//¥
		$f_text = str_replace("Ä","¼",$f_text);//¥
		$f_text = str_replace("³","¶",$f_text);//
		$f_text = str_replace("Å","¦",$f_text);//		
		$f_text = str_replace("ê","æ",$f_text);
		$f_text = str_replace("Ä","Æ",$f_text);

		return $f_text;
	}
}

if (!function_exists(sysmsg)) {		
	function sysmsg($msg,$grupa="")
	{
	  global $db;
	  global $lang;
	
		$defaultlang="ms";
	
		$msg=trim($msg);
	
		$m=addslashes($msg);
	
	  	$query="SELECT msg_msg FROM messages WHERE msg_label='$m' AND msg_lang='$defaultlang'";
	  	parse_str(query2url($query));
	  	if (!strlen($msg_msg)) 
		{
			$query="INSERT INTO messages (msg_label,msg_lang,msg_msg,msg_group) 
					VALUES ('$m','$defaultlang','$m','$grupa')";
			pg_Exec($db,$query);
		}	
	
	
	  	$query="SELECT msg_msg FROM messages 
				WHERE msg_label='$m' AND msg_lang='$lang'";
	  	parse_str(query2url($query));
		
		if (!strlen($msg_msg)) return $msg;
	  	return stripslashes($msg_msg);
	}
}
?>
