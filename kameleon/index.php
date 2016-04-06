<?php 
	$KAMELEON_MODE = strlen($_SERVER['REMOTE_ADDR']) ? 1 : 0;

	if (!file_exists("const.php") && !file_exists("const.h")) 
	{
		Header("Location: setup/");
		return;
	}

	if (strlen($_SERVER['REDIRECT_URL']) &&  strstr($_SERVER['REDIRECT_URL'],'.php') ) $_SERVER['SCRIPT_NAME']=$_SERVER['REDIRECT_URL'];

	if (!strlen($_COOKIE['WKSESSID']) 
		&& strlen($_SERVER['REMOTE_ADDR']) 
		&& basename($_SERVER['SCRIPT_NAME'])!='index.php' && basename($_SERVER['SCRIPT_NAME'])!='ftp.php' ) return;

	include("include/request.h");

	if (file_exists('const.php')) include('const.php'); else include('const.h');


	define ('ADODB_DIR','adodb/');		
	include("include/adodb.h");
	
	
	
	include("include/request.h");

	if (!strlen($lang)) $lang = ( $_SERVER['HTTP_ACCEPT_LANGUAGE'] == 'pl' ) ? 'pl' : 'e';
	$kameleon->lang=$lang;

	if (!$adodb) 
	{
		Header("Location: setup/");
		return;
	}
  
	include_once ("include/fun.h");
	include_once ("include/kameleon.h");



	if (isset($setlang) && !$BASIC_RIGHTS) $lang=$setlang;
	if ($KAMELEON_MODE && isset($setlang)) $adodb->SetCookie("lang",$lang);
	if (isset($_GET["debug_mode"])) 
	{ 
		$debug_mode = (int)$_GET["debug_mode"];
		$adodb->SetCookie("debug_mode",$_GET["debug_mode"]); 
	}
	else
		$debug_mode = $adodb->getCookie("debug_mode");
    
	$kameleon->setpagelang($lang);

	include ("include/const.h");
	
	

	if (strstr(strtolower($CHARSET),'utf') ) $adodb->adodb->SetCharSet('UTF-8');

	if (strstr($adodb->adodb->GetCharSet(),'SQL'))
	{
		$CHARSET="iso-8859-2";
		$adodb->adodb->SetCharSet('LATIN2');		
	}	
	
	if (isset($_GET[QS])) 
	{
		$p=rozkoduj_url($_GET[QS]);
		if (strlen($p)>0) parse_str($p);
	}  

	$CONST_LICENSE_NAME="";
	$CONST_LICENSE_VALID="";
	$CONST_LICENSE_SERVERS=0;
	$CONST_LICENSE_SECRET="klj7G#h^*de@#^*jhjf";
	if (strlen($CONST_LICENSE_KEY))
	{
		$_md5=$md5=substr($CONST_LICENSE_KEY,0,strpos($CONST_LICENSE_KEY,"g"));
		$_rest=substr($CONST_LICENSE_KEY,strpos($CONST_LICENSE_KEY,"g")+1);
		$_rmd5=md5($_rest.$CONST_LICENSE_SECRET);
		if ($_md5==$_rmd5)
		{
			$_rest=base64_decode($_rest);
			$CONST_LICENSE_NAME=substr($_rest,strpos($_rest,":")+1);
			$pos=strpos($CONST_LICENSE_NAME,":");
			if ($pos)
			{
				$CONST_LICENSE_HOST=substr($CONST_LICENSE_NAME,$pos+1);
				$CONST_LICENSE_NAME=substr($CONST_LICENSE_NAME,0,$pos);
			}

			$unix=substr($_rest,0,strpos($_rest,":"))+0;
			$CONST_LICENSE_VALID=date("Y-m-d",$unix);
			$d=$CONST_LICENSE_VALID;
			$CONST_LICENSE_SERVERS=$unix-mktime(1,0,0,substr($d,5,2),substr($d,8,2),substr($d,0,4));
			if (time()>$unix) $CONST_LICENSE_INVALID=1;
		}

		unset($_md5); unset($_rest); unset($_rmd5);
	}
	unset($CONST_LICENSE_SECRET);
	unset($CONST_LICENSE_KEY);



	if (strlen($_REQUEST['_WKSESSID']))
	{
		$_COOKIE['WKSESSID']=$_REQUEST['_WKSESSID'];
		$KAMELEON_MODE=0;

	}

	$dbapi=$db;
 	$nodb=label("Service temporary unavailable");

	if (!strlen($CONST_LICENSE_NAME))
	{
		$TRIAL_LENGTH=30;
		$CONST_LICENSE_SERVERS=1;
		$CONST_LICENSE_NAME=label("trial");
		$CONST_LICENSE_HOST="*";

		$valid="";
		// z�e pytanie !!!
		$query="SELECT min(nd_update)+".$DT->getDays($TRIAL_LENGTH)." AS valid 
			 FROM webtd WHERE autor_update IN 
			 (SELECT trim(username) FROM passwd WHERE trim(username)=webtd.autor_update)";

		parse_str(ado_query2url($query));
		$CONST_LICENSE_VALID=strlen($valid)?$valid:time()+$DT->getDays($TRIAL_LENGTH);
		//$nodb=label("Missing or wrong license key !");
		//$db=0;
	}

	if ( strpos($CONST_LICENSE_VALID, '-') )
	{
		$CONST_LICENSE_VALID = strtotime($CONST_LICENSE_VALID);
	}

	
	if ($db && !$HTTP_COOKIE_VARS["SERVER_ID"] )
	{
		$query="SELECT count(*) AS c FROM webpage WHERE nd_update>$CONST_LICENSE_VALID";
		parse_str(ado_query2url($query));
		if ($c || $CONST_LICENSE_INVALID)
		{
			$nodb=label("License expired on")." " . FormatujDate($CONST_LICENSE_VALID,0);
			$db=0;
		}

		$now_plus = time() + $DT->getDays(30);
		$query="SELECT 1 AS prepayed_limit_near WHERE ".$now_plus." > $CONST_LICENSE_VALID";
		parse_str(ado_query2url($query));

		if ($prepayed_limit_near && !strlen($kameleon_warrning) ) 
			$kameleon_warrning=label("License expires on")."       ".FormatujDate($CONST_LICENSE_VALID);

	}
	if ($db && !$HTTP_COOKIE_VARS["SERVER_ID"])
	{
		$query="SELECT count(*) AS c FROM servers";
		parse_str(ado_query2url($query));
		if ($c>$CONST_LICENSE_SERVERS)
		{
			$nodb=label("Too many servers in system. License allows only:")." $CONST_LICENSE_SERVERS";
			$db=0;
		}
	}

	

	push($HTTP_HOST);
	$kat=dirname($SCRIPT_NAME);
	if ($kat[strlen($kat)-1] == "/" || $kat[strlen($kat)-1] == "\\") 
		$kat=substr($kat,0,strlen($kat)-1);

	$HTTP_HOST.=$kat;

	$_port_pos=strpos($HTTP_HOST,":");
	if ($_port_pos) $HTTP_HOST=substr($HTTP_HOST,0,$_port_pos);

	if ($db && strlen($HTTP_HOST) && $HTTP_HOST!=$CONST_LICENSE_HOST 
		&& !strlen(strpos($CONST_LICENSE_HOST,":$HTTP_HOST:")) 
		&& $CONST_LICENSE_HOST!="*"
		&& $KAMELEON_MODE)
	{
		$nodb=label("License granted for")." $CONST_LICENSE_HOST [$HTTP_HOST]";
		$db=0;
	}
	$HTTP_HOST=pop();


 	if (!$db) echo "<script> alert('$nodb')</script><center><img src='img/closed.jpg'></center>";
	if (!$db) exit();

	if (!isset($editmode) && $KAMELEON_MODE) $seteditmode=1;
	if (isset($seteditmode)) $editmode=$seteditmode;
	if ($KAMELEON_MODE && isset($seteditmode)) $adodb->SetCookie("editmode",$editmode);
	if ($appmode) $editmode=$appmode;


	if (isset($hidenavigation)) 
	{
		 $adodb->SetCookie("navigationhidden",$hidenavigation);
		 $navigationhidden=$hidenavigation;
	}

	$ADMIN_MODE=0;
	include ("include/auth.h");

	



	if (isset($_REQUEST[seteditmode])) $editmode=$_REQUEST[seteditmode];
	if ($KAMELEON_MODE && isset($_REQUEST[seteditmode])) $adodb->SetCookie("editmode",$editmode);
	
	//jesli sk�ra z bazy jest pusta, to ma byc 
	// ta o nazwie "kameleon"
	if ( empty($kameleon->user['skin']) )
	{
		$kameleon->user['skin'] = 'kameleon';
		$kameleon->user['skinpath'] = $CONST_SKINS_DIR."/".'kameleon';
	}	
	@setcookie ("SkinName", $kameleon->user['skin']);
	//$adodb->puke($kameleon->user);

	$GLOBAL_CONST_VARS_CLEAR=0;
	if (!isset($ver)) 
	{
		$ver=$SERVER->ver;
		$adodb->SetCookie("ver",$ver);
	}
	if (isset($version) && !$BASIC_RIGHTS) $ver=$version;
	if ($KAMELEON_MODE && isset($version)) $adodb->SetCookie("ver",$ver);
	if (isset($version)) $GLOBAL_CONST_VARS_CLEAR=1;
	$version=$ver;


	

	if ($BASIC_RIGHTS)
	{
		$lang=$SERVER->lang;
		$adodb->SetCookie("lang",$lang);

	}
	if (!isset($lang)) $lang=$SERVER->lang;
	if (isset($setlang) && !$BASIC_RIGHTS) $lang=$setlang;
	if ($KAMELEON_MODE && isset($setlang)) $adodb->SetCookie("lang",$lang);
	
	if (strstr(strtolower($CHARSET),'utf') ) $adodb->adodb->SetCharSet('UTF-8');


	if (isset($sethelpmode)) $helpmode=$sethelpmode;
	if ($KAMELEON_MODE && isset($sethelpmode)) $adodb->SetCookie("helpmode",$helpmode);

	if (isset($setreferpage)) $referpage=$setreferpage;
	if ($KAMELEON_MODE && isset($setreferpage)) $adodb->SetCookie("referpage",$referpage);
  
	$hf_editmode = $adodb->getCookie("hf_editmode");
  
	if ($switcheditmode==1 && $kameleon->checkRight('write','page','0')) 
	//if ($switcheditmode==1 && checkRights("0",$PAGE_RIGHTS)) 
	{
	  $hf_editmode=($hf_editmode)?0:100;	
		$adodb->SetCookie("hf_editmode",$hf_editmode);
	}

	

	if ($KAMELEON_MODE) 
	{
		@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
		@header("Cache-Control: no-store, no-cache, must-revalidate");
		@header("Cache-Control: post-check=0, pre-check=0", false);
		@header("Pragma: no-cache");
	}

	include ("include/const.h");
	include_once("include/cache.h");
	$GLOBAL_CONST_VARS=cache_var_file("kameleon_paths",CACHE_READ);

	if (!strlen($GLOBAL_CONST_VARS[SZABLON_PATH]) || $GLOBAL_CONST_VARS_CLEAR ) 
	{
		for ($i=$ver;$i>0;$i--)
		{
			$szablon="szablony/$SERVER->szablon/$i";
			if (file_exists($szablon))
			{
				$SZABLON_PATH=$szablon;
				break;
			}
		}
		if (!strlen($SZABLON_PATH))
		{
			$szablon="szablony/$SERVER->szablon";
			if (file_exists($szablon)) $SZABLON_PATH=$szablon;
		}

		$GLOBAL_CONST_VARS[SZABLON_PATH]=$SZABLON_PATH;
		$GLOBAL_CONST_VARS_CHANGED=1;
	}
	else
	{
		$SZABLON_PATH=$GLOBAL_CONST_VARS[SZABLON_PATH];
	}

	$adodb->setCookie('_SZABLON_PATH',$SZABLON_PATH);
	
	if (file_exists("$SZABLON_PATH/const.h") && !strlen($error)  ) include("$SZABLON_PATH/const.h");
	if (file_exists("$SZABLON_PATH/const.php") && !strlen($error)) include("$SZABLON_PATH/const.php");

	if (isset($CONST_SET_CLIENT_ENCOGING)) $adodb->adodb->SetCharSet($CONST_SET_CLIENT_ENCOGING);

	
	$adodb->SetCookie('KAMELEON_CHARSET_TAB',$CHARSET_TAB);
	$adodb->SetCookie('KAMELEON_CONST_LANGS',$CONST_LANGS);

	include_once ("include/kameleon_href.h");
	
	

	if (is_array($APIS))
	{
		$__api=$APIS;
		$_a=array();
		$APIS=array();
		foreach ($__api AS $a) 
		{
			if ($a[0]=='news') continue;
			if ($a[0]=='') $a[0]='-';
			if (!$_a[$a[0]])
			{
				$_a[$a[0]]=1;
				if ($a[0]=='-') $a[0]='';
				$APIS[]=$a;
				
			}
		}
	}
	
	$adodb->width=$C_PAGE_WIDTH;
	
	$kameleon->init(strlen($KAMELEON_LANG)?$KAMELEON_LANG:$lang,$ver,$SERVER_ID,$CHARSET,$page);

	include_once ("include/modules.h");

	if (strlen($SZABLON_PATH))
	{
		if ($KAMELEON_MODE) $IMAGES="$SZABLON_PATH/images";
		else eval("\$IMAGES=\"$DEFAULT_PATH_IMAGES\";");
	}



	if (!strlen($GLOBAL_CONST_VARS[UIMAGES]) ) 
	{
		
		push($ver);
		for (;$ver>0;$ver--)
		{
			if (is_array($CONST_EXCLUDE_MINOR_VERS)) if (in_array($ver,$CONST_EXCLUDE_MINOR_VERS)) continue;

			eval("\$KAMELEON_UIMAGES=\"$DEFAULT_PATH_KAMELEON_UIMAGES\";");

			if ($KAMELEON_MODE) $UIMAGES=$KAMELEON_UIMAGES;
			else eval("\$UIMAGES=\"$DEFAULT_PATH_UIMAGES\";");
			if (file_exists($KAMELEON_UIMAGES)) break;
		}
		$UIMAGES_VER=$ver;
		$ver=pop();

		$GLOBAL_CONST_VARS[KAMELEON_UIMAGES]=$KAMELEON_UIMAGES;
		$GLOBAL_CONST_VARS[UIMAGES_VER]=$UIMAGES_VER;
		$GLOBAL_CONST_VARS[UIMAGES]=$UIMAGES;

		$GLOBAL_CONST_VARS_CHANGED=1;
	}
	else
	{
		$UIMAGES=$GLOBAL_CONST_VARS[UIMAGES];
		$KAMELEON_UIMAGES=$GLOBAL_CONST_VARS[KAMELEON_UIMAGES];
		$UIMAGES_VER=$GLOBAL_CONST_VARS[UIMAGES_VER];
	}

	if ($GLOBAL_CONST_VARS_CHANGED) cache_var_file("kameleon_paths",CACHE_WRITE,$GLOBAL_CONST_VARS);

	
	
	eval("\$PATH_PAGES=\"$DEFAULT_PATH_PAGES\";");
	eval("\$PATH_PAGES_PREFIX=\"$DEFAULT_PATH_PAGES_PREFIX\";");

	eval("\$KAMELEON_UFILES=\"$DEFAULT_PATH_KAMELEON_UFILES\";");
	if ($KAMELEON_MODE) $UFILES=$KAMELEON_UFILES;
	else eval("\$UFILES=\"$DEFAULT_PATH_UFILES\";");

	if (!strlen($DOCBASE)) 
	{
		$kat=dirname($SCRIPT_NAME);
		if ($kat[strlen($kat)-1]!="/") $kat.="/";
		$DOCBASE="http://$HTTP_HOST$kat";
	}

	if (!strlen($mybasename))
	{
		if (strlen($REDIRECT_URL) && strstr($REDIRECT_URL,'.php')) $PHP_SELF=$REDIRECT_URL;
		$nazwa_pliku=basename($PHP_SELF);
		$kropka=strpos($nazwa_pliku,".");
		$mybasename=substr($nazwa_pliku,0,$kropka);

	}




	if ($KAMELEON_MODE) 
	{
		$strpslashes_needed = ini_get('magic_quotes_gpc') ;

		if (isset($_POST))
		  foreach($_POST AS $key => $val)
  		   {
			$str2eval= $strpslashes_needed ? "\$$key=addslashes(stripslashes(\$$key));" : "\$$key=addslashes(\$_POST['$key']);" ;

			if (!is_array($val)) eval($str2eval);
		   }


		//echo "$PROOF_RIGHTS";
		//$MAY_PROOF=checkRights($page,$PROOF_RIGHTS);
		$MAY_PROOF=$kameleon->checkRight('proof','page',$page);
		
    
		$_action_redirect=0;
		$action = (strlen($action)==0 ? $km_action : $action); // dodane, �eby w formularzach nie by�o problemu ze zmian� form action
		if (strlen($action) && $editmode) 
		{
			ob_start();
			include("include/action.h");
			$a=trim(ob_get_contents());
			ob_end_clean();
			if (strlen($a)) 
			{
				$_action_redirect=0;
				echo "<pre>$a</pre>";
			}
		}

		if ($_action_only)
		{
			$adodb->Close($sysinfo,$persistant_connection);
			die();
		}

		if (strlen($_POST[u])) $_action_redirect=1;

		if ($editmode && isset($_GET['SetServer'])) $_action_redirect=1;
		if ($editmode && isset($_GET['version'])) $_action_redirect=1;
		if (isset($_GET['seteditmode'])) $_action_redirect=1;

		if ($_REQUEST['dontredirectaction']) $_action_redirect=0;


		if ($_action_redirect)
		{
			$link=$_SERVER['REQUEST_URI'];
			$link=ereg_replace('page=[0-9]*','',$link);
			$link=ereg_replace('action=[^&]*','',$link);
			$link=eregi_replace('setserver=[^&]*','',$link);
			$link=eregi_replace('version=[^&]*','',$link);
			$link=eregi_replace('seteditmode=[^&]*','',$link);

			if ($page || $mybasename=='index')
			{
				$link.=strpos($link,'?')?'&':'?';
				$link.="page=".(0+$page);
			}

			foreach (array('exploreclass','menu') AS $_pole)
			{
				$link=ereg_replace('${_pole}=[^&]*','',$link);
				if (strlen($$_pole))
				{
					$link.=strpos($link,'?')?'&':'?';
					$link.="$_pole=".$$_pole;
				}
			}

			$link=ereg_replace("\?[&]+","?",$link);
			$link=ereg_replace('&+','&',$link);

			if ($mybasename=='tdedit') 
			{
				$link.=strpos($link,'?')?'&':'?';
				$link.="page_id=$page_id&pri=$pri&hash=$hash&td_width=$td_width&sid=".$sid."&page=".$page;
			}
			if ($mybasename=='fileedit') $link.="?plik=".urlencode($_REQUEST[plik]);

			if (strlen($galeria)) 
			{
				$link.=strstr($link,'?')?'&':'?';
				$link.="galeria=$galeria&obrazek_name=$obrazek_name";
			}


			if (strlen($hash) && $mybasename=='index' ) $link.="#$hash";


			if (!headers_sent()) Header("Location: $link"); 
			else echo "<script language=\"javaScript\">
							link='$link';
							location.href=link;
					</script>";

			$adodb->Close($sysinfo,$persistant_connection);
			die();

		}

		if (!strlen($CMS_API_HOST))
		{
			$_host="http";
			if (strlen($HTTPS)) $_host.="s";
			$_host.="://$HTTP_HOST";
			if (dirname($SCRIPT_NAME)!="." && dirname($SCRIPT_NAME)!="/") 
				$_host.="/".dirname($SCRIPT_NAME);
			$CMS_API_HOST=$_host;
		}
	}

	

	if ($dontdisplayanykameleonhtml) $KAMELEON_MODE=0;


	if (!$KAMELEON_MODE)
	{
		$editmode=0;
		$this_editmode=0;
		$hf_editmode=0;
	}



	if (strlen($SZABLON_PATH))
	{
		
		$webpage_ar=kameleon_page($page+0);

		if (is_array($webpage_ar))
		{
        		$WEBPAGE=$webpage_ar[0];
        		$id=$WEBPAGE->id;
        		$version=$WEBPAGE->ver;
		}
		
		
		if (!$LICENSE_AGREEMENT && $KAMELEON_MODE)
		{
			include ("include/license.h");
		}
		elseif (file_exists("include/".$mybasename.".php"))
		{
			include ("include/$mybasename".".php");
		}
		else
		{
			include ("include/$mybasename".".h");
		}
	}

	

	if ($KAMELEON_MODE) $adodb->Close($sysinfo,$persistant_connection);



	if ($KAMELEON_MODE) if (is_object($auth_acl)) if (!in_array($mybasename,array('explorer-main','ajax','kameleon_css')))
	{
		$auth_acl->puke_debug_as_comment();
		$auth_acl->close();
	}
