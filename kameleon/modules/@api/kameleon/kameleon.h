<?
if ($_KAMELEON_H_INCLUDED) return;
$_KAMELEON_H_INCLUDED=1;



if (dirname($SCRIPT_NAME)=="/api")
	@include_once("../incuser/convert.h");
else
	@include_once("incuser/convert.h");


function logquery($sql,$dir='.')
{
	global $REMOTE_USER,$REMOTE_ADDR;
	global $C_LOGFILE;
	global $PHP_AUTH_USER,$SERVER_ID;

		
    $t=getdate(time());
    $teraz=sprintf("%02d-%02d-%04d %02d:%02d",
	$t["mday"],$t["mon"],$t["year"],
	$t["hours"],$t["minutes"]);


	$sciezka="$dir/$C_LOGFILE.$SERVER_ID";
	$plik=@fopen($sciezka,"a");
	if (!$plik) return "!$sciezka";


	fwrite($plik,"-- $teraz $PHP_AUTH_USER ($REMOTE_ADDR)\n");
	if (!is_array($sql)) fwrite($plik," $sql ;\n\n");
	else foreach ($sql AS $i=>$s)
	{
		fwrite($plik,"--a:$i\n");
		fwrite($plik," $s ;\n\n");
		
	}
	fclose($plik);
	
	return $sciezka;

}

function label($l,$lang='',$set='')
{
	global $kameleon;
	return $kameleon->label($l);
}

function label8($l,$lng='')
{
	include_once('include/utf8.h');

	global $lang;
	if (!strlen($lng)) $lng=$lang;

	return lang2utf8(label($l),$lng);
}

function label2($l)
{
  global $adodb;
  global $lang;
  global $LABEL_CACHE;
  global $CHARSET;

	if (strlen($LABEL_CACHE["${lang}_$l"])) return $LABEL_CACHE["${lang}_$l"];

	$defaultlang="e";

	$lab=$l;
	$l=addslashes($l);

	$label="";
  	$query="SELECT label FROM label 
		WHERE label='$l' AND lang='$defaultlang'
		LIMIT 1";
  	parse_str(ado_query2url($query));
  	if (!strlen($label) && strlen($l)) 
	{
		$query="INSERT INTO label (label,lang,value) VALUES ('$l','$defaultlang','$l')";
		$adodb->Execute($query);

	}	


  	$query="SELECT value FROM label 
		WHERE label='$l' AND lang='$lang'
		LIMIT 1";
  	parse_str(ado_query2url($query));
	
  	if (!strlen($value)) $value=$l;

	if (function_exists("b_convert_charset") && function_exists("s_convert_charset")) 
		if (b_convert_charset($lang,$CHARSET,"label"))
		{
			$value=s_convert_charset($value,$lang,$CHARSET,"label");
		}
	
	$LABEL_CACHE["${lang}_$l"]=stripslashes($value);

  	return stripslashes($value);
}



function include_js($js_file,$encoded=true,$js_ext="js",$jsenc_ext="js", $extraPath='')
{
	$language=$encoded?"JScript.Encode":"JScript";
	$dir=$encoded?"jsencode":"jsdecode";
	$ext=$encoded?$jsenc_ext:$js_ext;


	echo "<SCRIPT LANGUAGE=\"$language\" src=\"$extraPath$dir/$js_file.$ext\"></SCRIPT>";
	
/*
	if (strtolower($CHARSET)=="utf-8" && 0)
	{
		echo "<SCRIPT LANGUAGE=\"$language\">\n";
		readfile("$dir/$js_file.$ext");
		echo "\n</SCRIPT>";
	}
	else
	{
		
	}

*/
}

function checkRights($nr,$zakres)
{
	global $SERVER_ID,$lang,$ver,$page;

	if (!strlen($nr)) return 1;
	if (!strlen($zakres)) return 1;
	if ($nr==-1) return 1;
	if ($zakres=='-') return 0;
	

	if ($nr<0) $nr=0;
	$nr+=0;

	$zakresy=explode(";",$zakres);
	for ($i=0;$i<count($zakresy);$i++)
	{
		$oddo=explode("-",$zakresy[$i]);
		
		if ( strpos($oddo[0],"+"))
		{

			$root=$oddo[0]+0;
			if ($nr==$root) return 1;
			$page+=0;
			//echo "$root + ($tree) $query";
			$tree=kameleon_tree($page);
			if (strstr($tree,":$root:")) return 1;
			else continue;
		}
	

		$od=$oddo[0]+0;
		$do=$oddo[1]+0;
		if (!$do) $do=$od;
		if ($nr>=$od && $nr<=$do) return 1;
	}
	return 0;
}

if (!function_exists('read_file'))
{
 function read_file($strfile)
 {
		global $READ_FILE_CACHE;
		$idx=ereg_replace("[-\./]","_",$strfile);

		if (strlen($READ_FILE_CACHE[$idx])) return $READ_FILE_CACHE[$idx];
        if($strfile == "" || !file_exists($strfile)) return;
        $fd = fopen ($strfile, "r");
        $contents = fread ($fd, filesize ($strfile));
        fclose ($fd);

		$wynik=" ".$contents;
		$READ_FILE_CACHE[$idx]=$wynik;
        return $wynik;
 }
}
if (!function_exists('read_file2'))
{
 function read_file2($strfile) 
 {
        if($strfile == "" || !file_exists($strfile)) return;
        $thisfile = file($strfile);
        while(list($line,$value) = each($thisfile)) 
	{
                $value = ereg_replace("(\r|\n)","",$value);
                $result .= "$value\r\n";
        }
        return $result;
 }
}
if (!function_exists('array_key_exists'))
{
 function array_key_exists($key, $search)
 {
	if (in_array($key, array_keys($search))) 
		return true;
	else 
		return false;
 }

}

function kameleon_global($token)
{
	global $adodb;

	if (substr($token,0,7)!="GLOBAL(" ) return ($token);

	$glob=substr($token,7);
	$glob=substr($glob,0,strlen($glob)-1);
	$code="\$wynik = $glob;";

	$globalize_string=eregi_replace("(\\\$[a-z0-9_]+)","{global \\1 ;}",$glob);
	$globalize_string=eregi_replace("^[^{]+","",$globalize_string);
	$globalize_string=eregi_replace("}[^{]+","}",$globalize_string);

	$s2e="$globalize_string ; $code";
	if ($adodb->debug) echo "<font color='navy'>(globalizer): $s2e</font><br>";

	eval($s2e);

	//if (!strlen(trim($wynik))) return($token);
	return ($wynik);
}

function kameleon_user($username,$field="")
{
	global $adodb;

	$query="SELECT * FROM passwd WHERE username='$username'";
	$res=$adodb->Execute($query);

	if (!$res) return "";
	if (!$res->RecordCount()) return "";

	$res->Move(0);
	$data=$res->FetchRow();

	if (strlen($field)) return $data[$field];
	return $data;

	
}


function kameleon_acl_update_add($o_id,$o_name,$user,$rights)
{

	global $SERVER_ID;
	$o_id+=0;
	$query="SELECT count(*) AS c FROM kameleon_acl 
		WHERE ka_server=$SERVER_ID AND ka_oid=$o_id AND ka_resource_name='$o_name'
		AND ka_username='$user'";

	parse_str(ado_query2url($query));
	
	if (!$c)
		$query="INSERT INTO kameleon_acl (ka_server, ka_oid, ka_resource_name, ka_username,ka_rights)
			VALUES ($SERVER_ID,$o_id,'$o_name','$user','$rights-');";
	else
		$query="UPDATE kameleon_acl SET ka_rights='$rights-'
			WHERE ka_server=$SERVER_ID AND ka_oid=$o_id AND ka_resource_name='$o_name'
			AND ka_username='$user'";

	return $query;
}

function kameleon_include_plain($plain,$level=0)
{
	static $identifiers;
	global $editmode,$SERVER_ID;

	if (!$level) $identifiers=array();

	

	$plain=eregi_replace('<img[^>]* name="([^">]+)"[^>]*src="[\.\/]+img/include.gif"[^>]*>','{[\1]}',$plain);
	$plain=eregi_replace('<maska[^>]* name="([^">]+)"[^>]*></maska>','{[\1]}',$plain);
	
	$p=$plain;
	while (1)
	{
		$pos1=strpos($p,'{[');
		if (!strlen($pos1)) break;
		$pos2=strpos($p,']}');
		if (!strlen($pos2)) break;
		if ($pos1>$pos2) break;

		$mid=substr($p,$pos1+2,$pos2-$pos1-2);
		$mids[]=$mid;
		$p=substr($p,$pos2+2);
	}


	foreach ($mids AS $mid)
	{
		

		if (in_array($mid,$identifiers))
		{
			$plain=str_replace("{[$mid]}","",$plain);
			continue;
		}

		$p='';
		$sql="SELECT plain AS p,page_id,title FROM webtd WHERE uniqueid='$mid' AND server=$SERVER_ID";
		parse_str(ado_query2url($sql));
		$p=stripslashes(stripslashes($p));
		$start_span='';
		$stop_span='';
		if ($editmode)
		{
			$title=stripslashes(str_replace('"','&quot;',$title));
			$start_span='<span title="'.label('Text referenced from page').' '.$page_id.' ('.$title.')">';
			$stop_span='</span>';
		}
		$plain=str_replace("{[$mid]}","$start_span$p$stop_span",$plain);

		$identifiers[]=$mid;
	}

	if (eregi('<img[^>]* src="[\.\/]+img/include.gif"[^>]*>',$plain))
	{
		$plain=kameleon_include_plain($plain,1);
	}

  if (eregi('<maska',$plain))
	{
		$plain=kameleon_include_plain($plain,1);
	}

	return $plain;
}

function mkdir_r($dir,$mode=0700)
{
	if (!is_dir(dirname($dir))) mkdir_r(dirname($dir),$mode);
	if (is_dir(dirname($dir))) 
	{
		mkdir($dir,$mode);
		chmod($dir,$mode);
	}
	
}

function cp_r($src,$dst,$mode=0700)
{
	if (is_dir($src))
	{
		if (file_exists($dst) && !is_dir($dst)) return;
		if (!is_dir($dst)) mkdir_r($dst,$mode);

		$handle=opendir($src);

		while (($file = readdir($handle)) !== false ) 
		{
			if ($file[0]==".") continue;
			if (is_dir("$src/$file")) 
			{
				$more[]=$file;
			}
			else 
			{
				cp_r("$src/$file",$dst,$mode);
			}
		}
		closedir($handle);

		if (is_array($more)) foreach($more AS $sub)  cp_r("$src/$sub","$dst/$sub",$mode);

	}
	else
	{
		if (is_dir($dst))
		{
			$name=basename($src);
			copy($src,"$dst/$name");
			chmod("$dst/$name",$mode);
		}
		else
		{
			$dir=dirname($dst);
			if (!is_dir($dir)) mkdir_r($dir);
			copy($src,$dst);
			chmod($dst,$mode);

		}
	}
}

function kameleon_path($page,$path_separator=' : ')
{
	static $__path;

	if (!strlen($page)) return;
	if (strlen($__path[$page])) return $__path[$page];

	$_WEBPAGE=kameleon_page($page+0);
	$WEBPAGE=$_WEBPAGE[0];

	$webpage_tree=explode(":",$WEBPAGE->tree);
	$webpage_tree[]=$WEBPAGE->id;

	for ($i_tree=0;$i_tree<(count($webpage_tree)) && is_array($webpage_tree);$i_tree++)
	{
		if (!strlen($webpage_tree[$i_tree])) continue;

		$parent_page=$webpage_tree[$i_tree];
		unset($PARENT_WEBPAGE);
		$PARENT_WEBPAGE=kameleon_page($parent_page);
		if ($PARENT_WEBPAGE[0]->nositemap) continue;
		
		if (strlen($PARENT_WEBPAGE[0]->title_short))
			$title=$PARENT_WEBPAGE[0]->title_short;
		else
			$title=$PARENT_WEBPAGE[0]->title;
			
		if ($PARENT_WEBPAGE[0]->hidden!=1)
			$title="<a href=\"".kameleon_href("","",$parent_page)."\">".$title."</a>";
			
		$title.=$path_separator;
		$path.=$title;
	} 

	$path = substr($path,0,(strlen($path)-strlen($path_separator)));
	$__path[$page]=$path;
	return $path;
}


function kameleon_copy_query($table,$change_array,$where_array)
{
	global $adodb;
	

    foreach ($adodb->adodb->MetaColumns($table) AS $attr)
	{
		$attr->dbtable=$table;
		$fields[$attr->name]=$attr;

		if ($attr->name=='sid') continue; 
		if (isset($change_array[$attr->name]))
		{
			if (!strlen($change_array[$attr->name])) continue;

			$inserts[]=$attr->name;
			$values[]=$change_array[$attr->name];
		}
		else 
		{
			$inserts[]=$attr->name;
			$values[]=$attr->name;		
		}
	}
	
	$where=array();
	foreach (array_keys($where_array) AS $w) $where[]=$w.'='.$where_array[$w]; 

	
	$query="INSERT INTO $table \n(".implode(',',$inserts).") \nSELECT ".implode(',',$values)." \nFROM $table \nWHERE ".implode(' AND ',$where);


	return $query;
}

?>