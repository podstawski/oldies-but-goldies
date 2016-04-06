<?
if ($_KAMELEON_FUNCTION_INCLUDED==1) return;
$_KAMELEON_FUNCTION_INCLUDED=1;

if (!function_exists("cache_var_file") && file_exists("include/cache.h"))
{
	include_once("include/cache.h");
}


if (!$KAMELEON_MODE )
{
	$from_cache=cache_var_file("global_page_filename",CACHE_READ);
	if (is_array($from_cache))
	{
		$global_page_filename=$from_cache;
	}
	else
	{
		$global_page_filename=array();
		$exclude_minor_vers=is_array($CONST_EXCLUDE_MINOR_VERS)?'AND ver NOT IN ('.implode(',',$CONST_EXCLUDE_MINOR_VERS).')':'';

		$query="SELECT id,file_name AS fn,lang AS target_lang 
				FROM webpage WHERE server=$SERVER_ID AND ver<=$ver $exclude_minor_vers
				AND file_name IS NOT NULL AND file_name<>''
				ORDER BY ver,id";

		$_res=$adodb->Execute($query);
		if ($log_also_select) logquery($query);
		for($i=0;is_object($_res) && $i<$_res->RecordCount();$i++)
		{
			parse_str(ado_explodeName($_res,$i));
			$global_page_filename["${target_lang}_$id"]=$fn;
		}
		cache_var_file("global_page_filename",CACHE_WRITE,$global_page_filename);
		
		@cache_var_file("const_exclude_minor_vers",CACHE_WRITE,$CONST_EXCLUDE_MINOR_VERS);
		@cache_var_file("const_exclude_minor_vers_sql",CACHE_WRITE,$query);
	}
	
}

function kameleon_find_next_page($page,$ver,$lang,$SERVER_ID,$cycle=null)
{
	static $kameleon_next_page;
	global $log_also_select,$CONST_EXCLUDE_MINOR_VERS;

	if (is_array($cycle)) if (in_array($page,$cycle)) return $page;
	if ($cycle===null) $cycle=array($page);

	if ($kameleon_next_page[$page]) return $kameleon_next_page[$page];


	$exclude_minor_vers=is_array($CONST_EXCLUDE_MINOR_VERS)?'AND ver NOT IN ('.implode(',',$CONST_EXCLUDE_MINOR_VERS).')':'';

	$query="SELECT next FROM webpage WHERE id=$page AND lang='$lang' AND server=$SERVER_ID
			AND ver<=$ver $exclude_minor_vers
			ORDER BY ver DESC
			LIMIT 1";
	parse_str(ado_query2url($query));
	if ($log_also_select) logquery($query);

	if ($next) $next=kameleon_find_next_page($next,$ver,$lang,$SERVER_ID,$cycle);
	else $next=$page;

	$kameleon_next_page[$page]=$next;
	return $next;
}




function kameleon_href($href,$variables,$page_target,$follow_link_if_const=true)
{
	global $IMAGES,$SCRIPT_NAME,$GLOBAL_LINK;
	global $KAMELEON_MODE,$editmode;
	global $page,$ver,$lang;
	global $SERVER,$SERVER_ID;
	global $KAMELEON_EXT;
	global $DEFAULT_PATH_PAGES,$PATH_PAGES,$DEFAULT_PATH_PAGES_PREFIX;
	global $WEBPAGE;
	global $log_also_select;
	global $global_page_filename;
	global $C_FORGET_DOCBASE,$C_DIRECTORY_INDEX;
	global $CONST_NEXT_PAGE_LINK_FOLLOW;


	$orig_href=$href;

	if (!is_array($C_DIRECTORY_INDEX)) if (strlen($C_DIRECTORY_INDEX)) $C_DIRECTORY_INDEX=array($C_DIRECTORY_INDEX);


	if ($editmode)
	{
		//AM #4 zgodno�� z w3c standard
		if (strlen($variables)) $variables.="&amp;";
		$variables.="referer=$page";
	}

	if (strpos($page_target,":"))
	{
		$target=explode(":",$page_target);
		eval("\$lang_target=\"$target[0]\";");
		eval("\$page_target=\"$target[1]\";");
		$page_target+=0;
	}
	if (strpos($page_target,"#"))
	{
		$target=explode("#",$page_target);
		eval("\$hash=\"#$target[1]\";");
		eval("\$page_target=\"$target[0]\";");
		$page_target+=0;

	}

	if ($CONST_NEXT_PAGE_LINK_FOLLOW && strlen($page_target) && $page_target>0 && !$editmode && $follow_link_if_const)
	{
		$page_target=kameleon_find_next_page($page_target,$ver,strlen($lang_target)?$lang_target:$lang,$SERVER_ID);
	}

	$orig_href=$href;

	if (!strlen($page_target) && !strlen($href)) $variables="";
	$ext=$SERVER->file_ext;	


	if (strlen($page_target)) $href="index.$KAMELEON_EXT?page=$page_target";
	else eval("\$href=\"$href\";");


	if ($href[0]=='#')
	{
		return $href;
	}
	else
	{

		if (!$KAMELEON_MODE)
		{
			$curr_lang=$lang;
			if (strlen($lang_target))
			{
				$lang=$lang_target;
			}

			if (strlen($page_target))
			{
				$file_name=$global_page_filename["${lang}_$page_target"];
				if (strlen($file_name))
					eval("\$href=\"$file_name\";");
				else
					eval("\$href=\"$DEFAULT_PATH_PAGES/$page_target.$ext\";");

				
				eval("\$PATH_PAGES_PREFIX=\"$DEFAULT_PATH_PAGES_PREFIX\";");
				$href="$PATH_PAGES_PREFIX$href";

			}
			
			if ($C_FORGET_DOCBASE )
			{
				$dwukropek=strpos($href,":");
				$pytajnik=strpos($href,"?");
				if (!$dwukropek || ($dwukropek>$pytajnik && $pytajnik>0) ) 
					$href=kameleon_relative_dir($WEBPAGE->file_name,$href);
			}

			$lang=$curr_lang;

		}
		else
		{
			if (strlen($lang_target)) $href.="&setlang=$lang_target";
		}
	}

	if (strlen($variables))
	{
		$pytajnik=strpos($href,"?");
		//AM #4 zgodno�� z w3c standard
		if ($pytajnik) $href.="&amp;$variables";
		else $href.="?$variables";
	}

	if (!$KAMELEON_MODE && !strlen($orig_href) && is_array($C_DIRECTORY_INDEX) )
		foreach($C_DIRECTORY_INDEX AS $directory_index)
		{
			$href=ereg_replace('/'.$directory_index.'$','/',$href);
			if ("$href"=="$directory_index") $href='.';
		}


	return "$href$hash";
}

function kameleon_td_sid($sid)
{
	global $adodb;
	global $log_also_select;
	
	$query="SELECT * FROM webtd WHERE sid=$sid";
	$webtd=ado_ObjectArray($adodb,$query);
	if ($log_also_select) logquery($query);	
	
	if (count($webtd)) return $webtd[0];
}

function kameleon_td($page_id,$ver,$lang,$level=0,$clearpricounter=0)
{
	global $SERVER_ID;
	global $adodb;
	global $log_also_select;
	global $editmode,$KAMELEON_MODE,$CONST_EXCLUDE_MINOR_VERS;

	static $pricounter;

	if ($clearpricounter) $pricounter=array();

	if (0+$level) $and="AND level=$level";

	$exclude_minor_vers=is_array($CONST_EXCLUDE_MINOR_VERS)?'AND ver NOT IN ('.implode(',',$CONST_EXCLUDE_MINOR_VERS).')':'';
	$query="SELECT MAX(ver) AS _ver FROM webtd
			WHERE page_id=$page_id AND server=$SERVER_ID 
			AND lang='$lang' AND ver<=$ver $exclude_minor_vers";


	parse_str(ado_query2url($query));
	if (!$_ver) $_ver=$ver;

	if ($log_also_select) logquery($query);

	
	if (!$editmode && $KAMELEON_MODE) 
					$and.=" AND (nd_valid_from IS NULL OR nd_valid_from<=".time()." )
					AND (nd_valid_to IS NULL OR nd_valid_to>=".time()." )";
	


	$query = array();
	$query['postgres']="
		SELECT * , ((nd_valid_from IS NULL OR nd_valid_from<=".time()." )
					AND (nd_valid_to IS NULL OR nd_valid_to>=".time()." )) as valid
		FROM webtd 
		WHERE page_id=$page_id AND server=$SERVER_ID 
		AND ver=$_ver AND lang='$lang' $and
		ORDER BY level,pri";
	
	$query['mssql']="
		SELECT * , valid =
         CASE
            WHEN ((nd_valid_from IS NULL OR nd_valid_from<=".time()." )
					AND (nd_valid_to IS NULL OR nd_valid_to>=".time()." )) THEN 1
            ELSE 0
		 END
		FROM webtd
		WHERE page_id=$page_id AND server=$SERVER_ID 
		AND ver=$_ver AND lang='$lang' $and
		ORDER BY level,pri";
	
	//$adodb->puke($query);
	$webtd=ado_ObjectArray($adodb,$query);
	if ($log_also_select) logquery($query);

	if (!count($webtd)) return $webtd;

	
	for($i=0;$i<count($webtd);$i++)
	{
		
		if ($pricounter[$webtd[$i]->page_id][$webtd[$i]->pri])
		{
			$sql="SELECT max(pri)+1 AS maxpri FROM webtd WHERE page_id=$page_id AND ver=$_ver AND server=$SERVER_ID AND lang='$lang'";
			parse_str(ado_query2url($sql));
			if ($log_also_select) logquery($sql);
			$webtd[$i]->pri=$maxpri;
			$sql="UPDATE webtd SET pri=$maxpri WHERE sid=".$webtd[$i]->sid;
			$adodb->execute($sql);
			logquery($sql);
		}
		$pricounter[$webtd[$i]->page_id][$webtd[$i]->pri]=1;

		if (!strlen($webtd[$i]->uniqueid))
		{
			$j=0;
			$t=time();
			while (true)
			{
				$uid=sprintf("%08X",$t-$j+$webtd[$i]->sid);
				$sql="SELECT count(*) AS c FROM webtd WHERE uniqueid='$uid'";
				parse_str(ado_query2url($sql));
				if ($c)
				{
					$j++;
					continue;
				}
				$sql="UPDATE webtd SET uniqueid='$uid' WHERE sid=".$webtd[$i]->sid;
				$adodb->execute($sql);
				$webtd[$i]->uniqueid=$uid;
				//echo $sql.'<br>';
				break;
			}

		}

		$a=unserialize(base64_decode($webtd[$i]->d_xml));
		if (is_array($a)) foreach ($a AS $k=>$v) $webtd[$i]->$k=stripslashes($v);
	}
	
	reset($webtd);
	return ($webtd);

}

function kameleon_td_count($page_id,$ver,$lang,$level)
{
	global $SERVER_ID;
	global $adodb;
	global $log_also_select;
	global $CONST_EXCLUDE_MINOR_VERS;

	$exclude_minor_vers=is_array($CONST_EXCLUDE_MINOR_VERS)?'AND ver NOT IN ('.implode(',',$CONST_EXCLUDE_MINOR_VERS).')':'';

	$query="SELECT MAX(ver) AS _ver FROM webtd
			WHERE page_id=$page_id AND server=$SERVER_ID
			AND lang='$lang'  AND ver<=$ver $exclude_minor_vers";

	parse_str(ado_query2url($query));
	if (!$_ver) $_ver=$ver;
	if ($log_also_select) logquery($query);

	if (0+$level) $and="AND level=$level";

	$query="SELECT count(*) AS cr FROM webtd 
		WHERE page_id=$page_id AND server=$SERVER_ID
		AND ver=$_ver AND lang='$lang' $and";

	parse_str(ado_query2url($query));
	if ($log_also_select) logquery($query);
	
	return ($cr);
}

function kameleon_menus($menu_id)
{
	global $page,$ver,$lang,$version;
	global $SERVER_ID;
	global $adodb;
	global $log_also_select;
	global $editmode;

	global $CONST_EXCLUDE_MINOR_VERS;

	$exclude_minor_vers=is_array($CONST_EXCLUDE_MINOR_VERS)?'AND ver NOT IN ('.implode(',',$CONST_EXCLUDE_MINOR_VERS).')':'';

	$menu_id+=0;
	$query="SELECT MAX(ver) AS _ver FROM weblink
			WHERE menu_id=$menu_id AND server=$SERVER_ID
			AND lang='$lang' AND ver<=$ver $exclude_minor_vers";
	parse_str(ado_query2url($query));
	if (!$_ver) $_ver=$version;

	if ($log_also_select) logquery($query);

	if (!$editmode) $hidden_warunek="AND (hidden=0 OR hidden IS NULL)";

	$query="SELECT * FROM weblink 
			WHERE menu_id=$menu_id AND server=$SERVER_ID
			AND ver=$_ver AND lang='$lang' $hidden_warunek
			ORDER BY pri";
	
	$menu=ado_ObjectArray($adodb,$query);

	for ($i=0;is_Array($menu) && $i<count($menu);$i++)
	{
		if (strlen($menu[$i]->lang_target) && strlen($menu[$i]->page_target))
			$menu[$i]->page_target=$menu[$i]->lang_target.":".$menu[$i]->page_target;
		
		if (!strlen($menu[$i]->href) && $editmode)
		{
			if (!strlen($menu[$i]->page_target)) $menu[$i]->page_target=-1;
			if (!strlen($menu[$i]->variables))
				$menu[$i]->variables="ref_menu=$menu_id:".$menu[$i]->pri;
		}

		$baseclass=$menu[$i]->class;

		if ($i==0 && strlen($baseclass)) $menu[$i]->class.=' '.$baseclass.'_f';
		if ($i==count($menu)-1 && strlen($baseclass)) $menu[$i]->class.=' '.$baseclass.'_l';
		if ($page==$menu[$i]->page_target && strlen($baseclass)) $menu[$i]->class.=' '.$baseclass.'_c';
		
		if ($menu[$i]->hidden && $editmode) $menu[$i]->class='k_invisible '.$menu[$i]->class;

		$menu[$i]->alt_edit = $editmode ? "<acronym id=\"km_alt_".$menu[$i]->sid."\" class=\"km_alt_edit\" title=\"".label('Right click to edit')."\">".$menu[$i]->alt."</acronym>" : $menu[$i]->alt;

		$a=unserialize(base64_decode($menu[$i]->d_xml));
		if (is_array($a)) foreach ($a AS $k=>$v) $menu[$i]->$k=stripslashes($v);

	}

	


	if ($log_also_select) logquery($query);
	return ($menu);
	
}

function kameleon_tree($page,$circle=null)
{
	global $ver,$lang,$version;
	global $SERVER_ID;
	global $log_also_select;
	global $CONST_EXCLUDE_MINOR_VERS;
	
	static $cache;

	if (strlen($cache[$page])) return $cache[$page];

	$exclude_minor_vers=is_array($CONST_EXCLUDE_MINOR_VERS)?'AND ver NOT IN ('.implode(',',$CONST_EXCLUDE_MINOR_VERS).')':'';

	if (is_array($circle) && $circle[$page]) return (':');
	$circle[$page]=1;

	$query="SELECT prev,sid FROM webpage
		WHERE id=$page AND server=$SERVER_ID
		AND  ver<=$ver $exclude_minor_vers AND lang='$lang'
		ORDER BY ver DESC LIMIT 1";
	parse_str(ado_query2url($query));
	if ($log_also_select) logquery($query);
	

	if (!$sid) $wynik='-:';
	elseif ($prev==-1 || !strlen($prev) ) $wynik=':';
	else $wynik=kameleon_tree($prev,$circle)."$prev:";
		
	$cache[$page]=$wynik;
	return $wynik;
}



function kameleon_page($page)
{
	global $ver,$lang,$version;
	global $SERVER_ID;
	global $adodb;
	global $log_also_select;


	$query="SELECT * FROM webpage 
			WHERE id = $page 
			AND server = $SERVER_ID 
			AND  ver <= $ver 
			AND lang='$lang' 
			ORDER BY ver DESC LIMIT 1";

	$webpage_ar=ado_ObjectArray($adodb, $query);

	if ($log_also_select) logquery($query);
	if (is_array($webpage_ar)) 
	{
		$tree=kameleon_tree($page);

		if ($tree[0]=='-')
		{
			$tree=':0:';
			$webpage_ar[0]->tree = $tree;
			$sid=0+$webpage_ar[0]->sid;
			$query="UPDATE webpage SET tree='$tree',prev=0 WHERE sid=$sid";
			if ($adodb->Execute($query)) logquery($query);

		}
		
		if ($webpage_ar[0]->tree != $tree)
		{
			$webpage_ar[0]->tree = $tree;
			$sid=0+$webpage_ar[0]->sid;
			$query="UPDATE webpage SET tree='$tree' WHERE sid=$sid";
			if ($adodb->Execute($query)) logquery($query);
		}

		$a=unserialize(base64_decode($webpage_ar[0]->d_xml));
		if (is_array($a)) foreach ($a AS $k=>$v) $webpage_ar[0]->$k=stripslashes($v);


	}
	return($webpage_ar);
}


function kameleon_relative_dir($myself,$target)
{
	global $WEBPAGE;
	global $PATH_PAGES_PREFIX;

	$myself=ereg_replace("^\./","",$myself);
	$target=ereg_replace("^\./","",$target);
	$myself=ereg_replace("/\./","/",$myself);
	$target=ereg_replace("/\./","/",$target);
	



	$me=explode("/","$PATH_PAGES_PREFIX$myself");
	$him=explode("/",$target);


	$the_same=1;
	for($i=0;$i<count($me)-1;$i++)
	{
		if ($me[$i]!=$him[$i]) $the_same=0;

		if (!$the_same)
		{
			$up.="../";
			if (strlen($wynik) && strlen($him[$i])) $wynik.="/";
			$wynik.="$him[$i]";
		}
	}
	for(;$i<count($him);$i++)
	{
		if (strlen($wynik)) $wynik.="/";
		$wynik.="$him[$i]";
	}
	$wynik="$up$wynik";

	return "$wynik";
}

function mkdir_p($dir)
{
	$d=explode("/",$dir);

	$u=umask(0000);
	for ($i=0;$i<count($d);$i++)
	{
		if (strlen($curdir)) $curdir.="/";
		$curdir.=$d[$i];
		if (!file_exists($curdir)) @mkdir($curdir,0777);
	}
	umask($u);
}


function kameleon_include($html,$param)
{
	global $ver,$page,$lang,$SERVER_ID,$SERVER_NAME;
	global $CONST_REMOTE_INCLUDES_ARE_HERE,$CONST_EXCLUDE_MINOR_VERS;
	global $DEFAULT_PATH_KAMELEON_UINCLUDES,$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN;
	global $SZABLON_PATH;
	global $WEBTD, $KAMELEON_MODE;
	global $adodb,$db;
	global $editmode;
	global $CONST_PRE_H,$CONST_POST_H,$CONST_ACTION_H;
	global $UIMAGES,$KAMELEON_UIMAGES;
	global $KAMELEON_UFILES,$UFILES;
	global $INCLUDE_PATH;
	global $kameleon;
	global $INSTALL_TGZ;

	if (!$CONST_REMOTE_INCLUDES_ARE_HERE) return;

	$kameleon_module=($html[0]=="@")?1:0;

	
	$ext=strtolower(substr($html,strlen($html)-3));
	$html_org=$html;
	if ( $editmode && !$kameleon_module) $html=".$html";
	if ( $editmode && !$kameleon_module && $ext=='tgz') $html=$html_org;
	$_ver=$ver;
	for ($ver=$_ver;$ver>=0 && !$kameleon_module;$ver--)
	{
		if (is_array($CONST_EXCLUDE_MINOR_VERS)) if (in_array($ver,$CONST_EXCLUDE_MINOR_VERS)) continue;
		if (!$ver) $ver="";
                eval("\$KAMELEON_UINCLUDES=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
		if ($ver==$_ver) $THE_BEST_KAMELEON_UINCLUDES=$KAMELEON_UINCLUDES;
		//if ($ver==$_ver) if (!file_exists("$KAMELEON_UINCLUDES")) mkdir_p($KAMELEON_UINCLUDES);

		if ( $editmode && !$kameleon_module) foreach (explode('/',$html_org) AS $part)
		{
			$_html=str_replace($part,".$part",$html_org);
			if (file_exists("$KAMELEON_UINCLUDES/$_html")) $html=$_html;
		}
		if (file_exists("$KAMELEON_UINCLUDES/$html")) break;
	}


	$action_h="action.h";
	$pre_h="pre.h";
	$post_h="post.h";


	if (strlen($CONST_PRE_H)>1) $pre_h=$CONST_PRE_H;
	if (strlen($CONST_POST_H)>1) $post_h=$CONST_POST_H;
	if (strlen($CONST_ACTION_H)>1) $action_h=$CONST_ACTION_H;

	if ($kameleon_module)
	{
		$KAMELEON_UINCLUDES="modules/".dirname($html);
		$m_name=substr(dirname($html),1);
		$THE_BEST_KAMELEON_UINCLUDES=$KAMELEON_UINCLUDES;

		global $MODULES;
		if (strlen($MODULES->$m_name->scripts->pre)) 
		{
			if (!$CONST_PRE_H) $CONST_PRE_H=1;
			$pre_h=$MODULES->$m_name->scripts->pre;
		}
		if (strlen($MODULES->$m_name->scripts->action)) 
		{
			if (!$CONST_ACTION_H) $CONST_ACTION_H=1;
			$action_h=$MODULES->$m_name->scripts->action;
		}
		if (strlen($MODULES->$m_name->scripts->post)) 
		{
			if (!$CONST_POST_H) $CONST_POST_H=1;
			$post_h=$MODULES->$m_name->scripts->post;
		}
		$html=basename($html);
	}


	$ver=$_ver;

	$_kameleon_vars = get_defined_vars();
	$str2eval.="";
	foreach ( $_kameleon_vars AS $key=>$val)
	{
		$valtab[$key]=$val;	
		$_str2eval_after_include.="\$$key=\$valtab['$key'];\n ";
	}

	$REMOTE_INCLUDE_PATH=$INCLUDE_PATH;



	if (!file_exists("$KAMELEON_UINCLUDES/$html") 
		&& !$editmode) echo label("Missing file").": $THE_BEST_KAMELEON_UINCLUDES/$html";
	if (!file_exists("$KAMELEON_UINCLUDES/$html")) return;
	

	
	parse_str($param);
	$INCLUDE_PATH=$KAMELEON_UINCLUDES;

	if ($CONST_CACHE_STATICINCLUDE) 
	{
		include_once("include/cache.h");
		$from_cache=cache_td($INCLUDE_PATH,$WEBTD,CACHE_READ);
	}
	
	if ($KAMELEON_MODE) eval("\$KAMELEON_UINCLUDES_SVN=\"$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN\";");	

	if (strlen($from_cache)) echo $from_cache;
	else
	{
		$kameleon_adodb=$adodb;

		$INCLUDE_PATH=$KAMELEON_UINCLUDES;
		if (file_exists("$KAMELEON_UINCLUDES_SVN/$pre_h") && $KAMELEON_MODE) 
			$INCLUDE_PATH=$KAMELEON_UINCLUDES_SVN;

		if (file_exists("$INCLUDE_PATH/$pre_h") 
			&& (file_exists("$SZABLON_PATH/pre.h") || $CONST_PRE_H)) 
		{
			include("$INCLUDE_PATH/$pre_h");
		}
	
		$INCLUDE_PATH=$KAMELEON_UINCLUDES;
		if (file_exists("$KAMELEON_UINCLUDES_SVN/$action_h") && $KAMELEON_MODE) 
			$INCLUDE_PATH=$KAMELEON_UINCLUDES_SVN;

		if (file_exists("$INCLUDE_PATH/$action_h")
			&& (file_exists("$SZABLON_PATH/action.h") || $CONST_ACTION_H)
			&& (!$WEBTD->staticinclude || $KAMELEON_MODE) 
			&& ($kameleon_module || !$editmode) ) 
			include("$INCLUDE_PATH/$action_h");
	


		$INCLUDE_PATH=$KAMELEON_UINCLUDES;
		if (file_exists("$KAMELEON_UINCLUDES_SVN/$html") && $KAMELEON_MODE) 
			$INCLUDE_PATH=$KAMELEON_UINCLUDES_SVN;



		if ($ext=='tgz' && $editmode)
		{
			$name=substr($html,0,strlen($html)-4);

			$INSTALL_NAME=$name;			
			if ($html==$INSTALL_TGZ)
			{
				@mkdir("$KAMELEON_UINCLUDES/$name",0755);
				exec("cd $KAMELEON_UINCLUDES/$name; tar -xzf ../$html");

				include("$INCLUDE_PATH/$name/INSTALL.php");

			}
			else
			{
				$submit=label("Install")." $name";
				if (file_exists("$INCLUDE_PATH/$name/INSTALL.INFO.php")) include("$INCLUDE_PATH/$name/INSTALL.INFO.php");

				
				echo "<form action=\"index.php?page=$page\" method=\"post\">
						<input type=\"hidden\" name=\"INSTALL_TGZ\" value=\"$html\">
				
						<input type=\"submit\" value=\"$submit\" class=\"k_button\"> 
						</form>
						";
			}
		}
		else
		{
			if ($ext!='tgz') include("$INCLUDE_PATH/$html");

		}

		$INCLUDE_PATH=$KAMELEON_UINCLUDES;
		if (file_exists("$KAMELEON_UINCLUDES_SVN/$post_h") && $KAMELEON_MODE) 
			$INCLUDE_PATH=$KAMELEON_UINCLUDES_SVN;
	
		if (file_exists("$INCLUDE_PATH/$post_h")
			&& (file_exists("$SZABLON_PATH/post.h")  || $CONST_POST_H) ) 
			include("$INCLUDE_PATH/$post_h");

		if ($CONST_CACHE_STATICINCLUDE) cache_td($INCLUDE_PATH,$WEBTD,CACHE_FLUSH);
		
		$kameleon_after_include_vars=get_defined_vars();
	}


	eval($_str2eval_after_include);
	$adodb->kameleon_after_include_vars=$kameleon_after_include_vars;
}

function isMenuTargetInPageTree($WEBLINK,$WEBPAGE)
{
        global $SERVER_ID, $ver, $lang;
        global $adodb;

        if ($WEBLINK->page_target == $WEBPAGE->id) return true;
        $tree=explode(":","$WEBPAGE->tree:$WEBPAGE->id:");
        $pages="";
        for ($i=0;$i<count($tree);$i++)
        {
                if (!strlen(trim($tree[$i]))) continue;
                if (strlen($pages)) $pages.=",";
                $pages.=$tree[$i];
                $pages_in_tree[]=$tree[$i];
        }
		if (!is_array($pages_in_tree)) return false;
        if (!in_array($WEBLINK->page_target,$pages_in_tree)) return false;
        $start=array_search($WEBLINK->page_target,$pages_in_tree);

        $query="SELECT  page_target FROM weblink
                        WHERE page_target IN ($pages) AND menu_id=$WEBLINK->menu_id
                        AND server=$SERVER_ID AND ver=$ver AND lang='$lang'";
        $res=$adodb->Execute($query);
        if (!$res) return false;
        $count=$res->RecordCount();

        if ($count==0) return false;
        if ($count==1) return true;

        for ($i=0;$i<$count;$i++)
        {
                parse_str(ado_ExplodeName($res,$i));
                $targets_in_tree[]=$page_target;
        }

        for ($i=$start+1;$i<count($pages_in_tree);$i++)
        {
                if (in_array($pages_in_tree[$i],$targets_in_tree)) return false;
        }

        return true;
}
