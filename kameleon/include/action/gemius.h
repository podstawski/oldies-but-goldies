<?
if (!$_CONST_GEMIUS_FUN_DECLARED)
{
   $_CONST_GEMIUS_FUN_DECLARED=1;

   function gemius($page_id,$file_name,$title)
   {
	global $SERVER_ID;

	
	$gemius_label="";
	$_title=substr(stripslashes($title),0,30);
	for ($i=0;$i<strlen($_title);$i++) // unpolish
	{
		$c=$_title[$i];
		switch ($c)
		{
			case "¥":
			case "¡":
				$c="A";
				break;
			case "¹":
			case "±":
				$c="a";
				break;
			case "Œ":
			case "¦":
				$c="S";
				break;
			case "œ":
			case "¶":
				$c="s";
				break;
			case "":
			case "¯":
			case "¬":
				$c="Z";
				break;
			case "¿":
			case "Ÿ":
			case "¼":
				$c="z";
				break;
				
			case "Ê":
				$c="E";
				break;
			case "Ó":
				$c="O";
				break;
			case "Æ":
				$c="C";
				break;
			case "Ñ":
				$c="N";
				break;
			case "£":
				$c="L";
				break;

			case "ê":
				$c="e";
				break;
			case "ó":
				$c="o";
				break;
			case "æ":
				$c="c";
				break;
			case "ñ":
				$c="n";
				break;
			case "³":
				$c="l";
				break;
		}

		$gemius_label.=$c;
		
	}
	
	$parent_tree=gemius_get_parent(dirname($file_name));
	$pt=explode(":",$parent_tree);
	$parent=0+$pt[count($pt)-1];
	$label=urlencode($gemius_label);
	$url="AddNode.php?script=1&label=$label&parent=$parent";
	$response=gemius_geturl($url);	
	parse_str($response);	
	

	$wynik=array("$parent_tree:$gemius_id",$gemius_key);
	
	return($wynik);
   }		


   function gemius_get_parent($dn)
   {
	global $ver,$lang,$SERVER_ID;
	global $C_GEMIUS_ROOT_ID;
	
	$root=$C_GEMIUS_ROOT_ID+0;
	if (!strlen($dn) || $dn==".") return "$root";

	$parent=$root;
	
	$query="SELECT tree FROM webpage
			WHERE lang='$lang' AND server=$SERVER_ID
			AND ver=$ver AND tree<>'' AND tree IS NOT NULL
			AND file_name LIKE '${dn}/%' LIMIT 1";
	parse_str(ado_query2url($query));		
	
	if (strlen($tree))
	{
		$slashes=substr_count($tree,":")-substr_count($dn,"/")-2;
		for ($pos=strlen($tree)-1;$pos;$pos--)
		{
			if ($tree[$pos]==":") 
			{
				if ($slashes==0) return (substr($tree,0,$pos));
				else $slashes--;
			}
		}
		return "";
	}

	$parent_tree=gemius_get_parent(dirname($dn));
	$pt=explode(":",$parent_tree);
	$parent=0+$pt[count($pt)-1];
	
	
	$url_dn=urlencode($dn);
	$url="AddNode.php?script=0&label=$url_dn&parent=$parent";
	$response=gemius_geturl($url);
	parse_str($response);
	return("$parent_tree:$gemius_id");
   }

   function gemius_geturl($url)
   {
	global $C_GEMIUS_HREF,$C_GEMIUS_SITE,$C_GEMIUS_PASS;
	
	$url="$C_GEMIUS_HREF/$url&site=$C_GEMIUS_SITE&pass=$C_GEMIUS_PASS";
	for ($try=0;$try<5;$try++)
	{
		$f=file($url);
		//echo "URL: $url <br>\n";
		for ($i=0;$i<count($f) && is_array($f);$i++)
		{
			$f[$i]=ereg_replace("\n","",$f[$i]);
			$f[$i]=ereg_replace("\r","",$f[$i]);
		}
		
		if (trim($f[0])=="OK") break;
	}
	$wynik="gemius_id=$f[1]";
	$wynik.="&gemius_key=$f[2]";
	//echo "Wynik:$wynik <br>\n";
	return ($wynik);
   }
}


return;

/* KONIEC FUNKCJI */

/*
$query="SELECT tree FROM webpage WHERE server=$SERVER_ID
	AND id=$page AND ver=$ver AND lang='$lang'";
parse_str(ado_query2url($query));
*/

if (strlen($pagekey) || !strlen($file_name)) return;

if (!strlen($C_GEMIUS_HREF) || !strlen($C_GEMIUS_SITE)
		|| !strlen($C_GEMIUS_PASS) ) return;



$fn=$file_name;

$a=gemius($page,$fn,$title);
if (is_array($a) && strlen($a[1]) )
{
	$tree=$a[0];
	$pagekey=$a[1];
}
