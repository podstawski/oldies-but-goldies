<?
if ($_KAMELEON_H_INCLUDED) return;
$_KAMELEON_H_INCLUDED=1;





if (dirname($SCRIPT_NAME)=="/api")
	@include_once("../incuser/convert.h");
else
	@include_once("incuser/convert.h");


if (!function_exists('logquery'))
{

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
}

if (!file_exists('label'))
{

	function label($l,$lang='',$set='')
	{
		global $kameleon;
		return $kameleon->label($l,$lang,$set);
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

}

if (!function_exists('json_encode')) {
	require_once 'class/json.php';
	
	function json_encode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->encode($arg);
	}
}

function include_js($js_file,$encoded=true,$js_ext="js",$jsenc_ext="js", $extraPath='')
{
	global $adodb;
	//$rev=$adodb->GetCookie('KAMELEON_VERSION_REV');
	$encoded=false; // zmienione przez Cartmana - brak obsługi Encode w Firefox
	//$language=$encoded?"JScript.Encode":"javascript";
	$dir=$encoded?"jsencode":"jsdecode";
	$dir="jsencode"; // zmienione bo decode się nie publikuje
	$ext='js';

	echo "<script type=\"text/javascript\" src=\"$extraPath$dir/$js_file.$ext?t=".$rev."\"></script>";
}

function include_css($css_file, $media='all', $extraPath='')
{
	global $kameleon, $adodb;
	$rev=$adodb->GetCookie('KAMELEON_VERSION_REV');
	return "<link type=\"text/css\" rel=\"stylesheet\" href=\"".$kameleon->user[skinpath]."/".$css_file."?t=".$rev."\">";
}


//depriciated
function checkRights($nr,$zakres)
{
	if ($nr==-1 && !is_array($zakres)) return 1;
	
	if ($nr<0) $nr=0;
	$nr+=0;

	if (is_array($zakres))
	{
		$str2eval='$wynik=';
		if (is_object($zakres['obj'])) $str2eval.='$zakres[\'obj\']->';
		$str2eval.=$zakres['fun'].';';
		
		//echo $str2eval;
		eval($str2eval);

		//echo "$str2eval [$wynik]<br/>";

		return $wynik;
	}


	global $SERVER_ID,$lang,$ver,$page;

	if (!strlen($nr)) return 1;
	if (!strlen($zakres)) return 1;
	if ($nr==-1) return 1;
	if ($zakres=='-') return 0;
	



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


function display_opt($display,$wt=1)
{
	$wynik='';
	foreach ($display AS $name=>$v)
	{
		$id=strlen($v['arg']['id'])?$v['arg']['id']:'in_'.str_replace(']','',str_replace('[','_',$name));
		
		if (isset($v['hidden']))
		{
			$wynik.='<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$v['hidden'].'" />';
			if (!isset($v['input']) && !isset($v['after'])) continue;
		}

		if (isset($v['title']))
		{
			$wynik.='<h2>'.$v['title'].':</h2>'."\n";
			continue;
		}
		
		

		$wynik.='<div class="litem_'.$wt.'"><label for="'.$id.'">'.$v['label'].':</label>';
		$wynik.='<div class="inputer">';
		if (strlen($v['input'])) $wynik.=$v['input'];

		if (is_array($v['arg']) && !isset($v['input']))
		{
			if (!strlen($v['arg']['class'])) $v['arg']['class']='k_input';
			if (!strlen($v['arg']['type'])) $v['arg']['type']='text';
			$wynik.='<input id="'.$id.'" name="'.$name.'"';
			foreach ($v['arg'] AS $tag=>$tv) if ($tag!='id') $wynik.=' '.$tag.'="'.$tv.'"';
			$wynik.=' />';
		}

		

		if (strlen($v['icon']))
		{
			$i=explode('|',$v['icon']);
			$wynik.=' <img align="absmiddle" border="0" width="23" height="22" style="cursor:hand" src="'.$i[0].'"';
			if (strlen($i[1])) $wynik.=' onmouseover="this.src=\''.$i[1].'\'" onmouseout="this.src=\''.$i[0].'\'"';
			if (strlen($i[2])) $wynik.=' onclick="'.$i[2].'"';
			if (strlen($i[3])) $wynik.=' alt="'.$i[3].'" title="'.$i[3].'"';
			$wynik.='/>';
		}

		if (strlen($v['more'])) $wynik.=' '.$v['more'];

		$wynik.='</div></div>'."\n";
		
		if (strlen($v['after'])) $wynik.=' '.$v['after'];
		
		$wt = ($wt==1 ? 2 : 1);
	}

	return $wynik;
}

function str2input($values,$name,$value,$style)
{

	if (strtolower($values)=='true|false' || strtolower($values)=='false|true')
	{
		
		$checked=$value?' checked':'';

		$select='<input type="hidden" name="'.$name.'" value="0"/><input'.$checked.' style="'.$style.'" class="k_checkbox" type="checkbox" name="'.$name.'" value="1"/>';
	}
	else
	{
		$v=explode('|',$values);

		$select = '<select class="k_select" style="'.$style.'" name="'.$name.'"><option value="">'.label('Choose').'</option>';
		foreach ($v AS $val)
		{
			$sel=($value==$val) ? ' selected':'';
			$select.='<option'.$sel.' value="'.$val.'">'.$val.'</option>';
		}
		$select.='</select>';
	}


	return $select;
	

}



function copy_menu_from($menu_id,$where_array)
{
	global $SERVER_ID,$lang,$ver;
	global $adodb;

	$sql="SELECT sid,submenu_id FROM weblink WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver AND menu_id=$menu_id AND submenu_id>0";


	$res=$adodb->execute($sql);

	if ($res) for($i=0; $i<$res->RecordCount(); $i++)
	{
		parse_str(ado_explodeName($res,$i));

		$maxmenu='';
		$sql="SELECT max(menu_id) AS maxmenu FROM weblink WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver";
		parse_str(ado_query2url($sql));
		$maxmenu++;

		$where_array['menu_id']=$submenu_id;
		
		$query=kameleon_copy_query('weblink',
					array('server'=>$SERVER_ID,'lang'=>"'$lang'",'ver'=>$ver,'menu_id'=>$maxmenu),
					$where_array);

		$query.="; UPDATE weblink SET submenu_id=$maxmenu WHERE sid=$sid";

		if ($adodb->execute($query))
		{
			copy_menu_from($maxmenu,$where_array);
		}

	}


}


function ponazywaj_strony($root_page_id,$SERVER_ID,$ver,$lang)
{
	global $adodb;

	global $C_DIRECTORY_INDEX,$CHARSET;



	$sql="SELECT id,file_name FROM webpage WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND prev=$root_page_id";

	$ponazywaj_strony_res=$adodb->execute($sql);

	if ($ponazywaj_strony_res) for($ii=0; $ii<$ponazywaj_strony_res->RecordCount(); $ii++)
	{
		parse_str(ado_explodeName($ponazywaj_strony_res,$ii));


		$page=$id;
		

		if (!strlen($file_name))
		{

			$action='alamakota-zeby-nie-wykonal-akcji';
			$_title='';
			$sql_change='';
			include('include/action/ZapiszStroneNazwa.h');
			
			$zabezpieczenie=5;
			$adodb->execute($sql_change);
			while ($zabezpieczenie-- && !$adodb->adodb->_affectedrows())
			{
				$_title.='-'.$page;
				include('include/action/ZapiszStroneNazwa.h');
				$adodb->execute($sql_change);
			}
			
		}

		ponazywaj_strony($page,$SERVER_ID,$ver,$lang);
	}
}


function kameleon_template($SZABLON_PATH,$TYPY,$objtype)
{
	$template="";
	for ($i=0;$i<count($TYPY);$i++)
	{
		$_type=$TYPY[$i];
		if ($objtype==$_type[0])
		$template=$_type[2];	
		if (strlen($template)) break;
	}


	$parser_template="$SZABLON_PATH/$template";
	if (!file_exists($parser_template)) $parser_template="$SZABLON_PATH/themes/$template";

	if (!file_exists($parser_template) && substr($template,0,strlen('kameleon:'))=='kameleon:' )
	{
		$parser_template='themes/'.substr($template,strlen('kameleon:')).'.html';
	}


	return $parser_template;
}

function kameleon_bread_crumbs($page,$level=0)
{
	global $adodb;
	global $lang,$ver,$SERVER_ID,$WEBPAGE;
	$debug=false;

	static $cache=array();
	

	
	if ($debug) echo "Entering page $page<br>";
	
	$p=$page;
	while($p>0)
	{
		$this_page=kameleon_page($p);
		$prev_page=kameleon_page($this_page[0]->prev);
		$p=$this_page[0]->prev;
		if ( $prev_page[0]->hidden+$prev_page[0]->nositemap==0) break;

	}
	$prev=$page?$prev_page[0]->id:-1;
	
	$tree=kameleon_tree_of_titles($prev);
	
	foreach ($tree AS $t)
	{
		if (isset($t['tree'])) unset($t['tree']);
		$t['selected']=($t['page']==$page)?1:0;
		$t['level']=$level;
		$cache[]=$t;
	}
	
	if ($prev>=0)
	{
		kameleon_bread_crumbs($prev,$level+1);
	}
	
	
	if ($debug) echo "Exiting page $page (prev=$prev)<br>";
	
	
	
	if ($level==0)
	{
		$wynik=array();
		$max=0;

		foreach($cache AS $c)
		{
			if ($c['level']>$max) $max=$c['level'];	
		}
		

		for ($L=$max;$L>=0;$L--)
		{
			$lp=0;
			foreach($cache AS $c)
			{
				if ($c['level']!=$L) continue;
				
				$wynik[$max-$L]['crumbs'][$c['title'].'-'.($lp++)]=$c;
				if ($c['selected']) $wynik[$max-$L]['selected']=$c;
				
			}
			
			if (is_array($wynik[$max-$L]['crumbs'])) ksort($wynik[$max-$L]['crumbs']);
		}
		

		if (!is_array($wynik[$max]['selected']))
		{
			$wynik[$max]['selected']['page']=$page;
			$wynik[$max]['selected']['href']=kameleon_href('','',$page);
			$wynik[$max]['selected']['title']=$WEBPAGE->title_short?$WEBPAGE->title_short:$WEBPAGE->title;
			
		}

	}
	
	return $wynik;
}



function kameleon_tree_of_titles($page=-1)
{
	global $adodb;
	global $lang,$ver,$SERVER_ID;
	static $cache;
	
	if (isset($cache[$page])) return $cache[$page];
	
	$wynik=array();
	
	$sql="SELECT * FROM webpage WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND prev=$page ORDER BY title_short,title";
	$res=$adodb->execute($sql);

	if ($res) for($i=0; $i<$res->RecordCount(); $i++)
	{
		parse_str(ado_explodeName($res,$i));
	
		$w=array();
		$w['page']=$id;
		$w['title']=$title_short?$title_short:stripslashes($title);
		$w['href']=kameleon_href('','',$id);
		
		$hid=$hidden+$nositemap;
		
		$sub=kameleon_tree_of_titles($id);
		if (count($sub))
		{
			if (!$hid) $w['tree']=$sub;
			else $wynik=array_merge($wynik,$sub);
		}
		
		if (!$hid) $wynik[]=$w;
	}
	
	$cache[$page]=$wynik;
	return $wynik;
}
