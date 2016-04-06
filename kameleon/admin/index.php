<?php

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");


	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');

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

			$CONST_LICENSE_VALID = $unix = substr($_rest,0,strpos($_rest,":"))+0;
			//$CONST_LICENSE_VALID=date("20y-m-d",$unix);
			$d=date("Y-m-d",$CONST_LICENSE_VALID);
			$CONST_LICENSE_SERVERS=$unix-mktime(1,0,0,substr($d,5,2),substr($d,8,2),substr($d,0,4));
			if (time()>$unix) $CONST_LICENSE_INVALID=1;
		}
	
		unset($_md5); unset($_rest); unset($_rmd5);
	}
	unset($CONST_LICENSE_SECRET);
	unset($CONST_LICENSE_KEY);

	$KAMELEON_MODE=1;
    define ('ADODB_DIR','../adodb/');
	include("../include/request.h");
    include_once ("../include/adodb.h");

	include_once ("../include/fun.h");
	include_once("../include/kameleon.h");
	include("../include/request.h");	




	$kameleon->init(strlen($KAMELEON_LANG)?$KAMELEON_LANG:$lang,1,0,"",$referpage);


	include("include/auth.h");


	$kameleon->setpagelang($lang);
	
	if (!strlen($CONST_LICENSE_NAME))
	{
		$TRIAL_LENGTH=30;
		$CONST_LICENSE_SERVERS=1;
		$CONST_LICENSE_NAME=label("trial");
		$CONST_LICENSE_HOST="*";

		$valid="";
		//Z³e pytanie
		$query="SELECT min(nd_update)+".$DT->getDays($TRIAL_LENGTH)." AS valid 
				FROM webtd WHERE autor_update IN 
				(SELECT trim(username) FROM passwd WHERE trim(username)=webtd.autor_update)";
		parse_str(ado_query2url($query));
		$CONST_LICENSE_VALID=strlen($valid)?$valid:time()+$DT->getDays($TRIAL_LENGTH);

	}

	
	if (strlen($REDIRECT_URL) && strstr($REDIRECT_URL,'.php')) $SCRIPT_NAME=$REDIRECT_URL;

	$_mn=explode(" ",$SCRIPT_NAME);
	$mybasename=basename($_mn[0]);
	$mybasename=ereg_replace("\.php","",$mybasename);

	//to jest potrzebne

	if (!strlen($lang)) $lang=$kameleon->pagelang;
	if (!strlen($lang)) $lang=$kameleon->lang;

	include("include/ustawzmienne.h");
	include ("../include/const.h");
	$C_LOGFILE="../log/webadmin.log";

	if (strstr(strtolower($CHARSET),'utf') ) $adodb->adodb->SetCharSet('UTF-8');

	$norights=label("Insufficient rights");

	include("include/zakladkifun3.h");
	include("include/update.h");

	if (isset($setreferpage)) 
	{
		$referpage=$setreferpage;
		$adodb->SetCookie("referpage",$referpage);
	}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET;?>">
<title>WebKameleon - admin</title>
<link href="../<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]; ?>/kameleon.css" rel="stylesheet" type="text/css">
<link href="../<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]; ?>/admin.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" media="all" href="../skins/<? echo $kameleon->user[skin]; ?>/calendar.css" title="win2k-cold-1" />

<?
switch($lang)
	{
		case "p":
		case "i":
			$clang = "pl";
			break;
		case "d":
		case "de":
			$clang = "de";
			break;
		case "f":
		case "fr":
			$clang = "fr";
			break;
		case "r":
		case "ru":
			$clang = "ru";
			break;
		case "t":
		case "cs":
			$clang = "cs";
			break;
		case "s":
		case "es":
			$clang = "es";
			break;
		case "h":
			$clang = "hu";
			break;
		case "g":
			$clang = "gr";
			break;
		default: 
			$clang="en";
	}
	include_js("calendar",1,"js","js", '../');
	include_js("calendar-$clang",1,"js","js", '../');
	include_js("calendar-setup",1,"js","js", '../');
?>
</head>

<body>

<script language="javascript" src="../jsencode/prompt.js"></script> 
<script language="javascript">
function licence()
{
        a=open("../licence.<?echo $KAMELEON_EXT?>","licence",
        "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width=560,height=350");
}
</script>


	
	<? 
	$rev=$adodb->GetCookie('KAMELEON_VERSION_REV');
	if (strlen($rev)) $rev=".$rev";
	
	$who=$login;
  if (strlen($who))
  {
  	$who=label('Logged in').':<br /><span style="font-size: 12px; color: #979797; font-weight: bold;">'.$who.'</span>';
  }
	
	echo "
    <div class=\"km_head\">
      <div class=\"km_left\">
        <a href=\"../index.php\" class=\"km_head_logo km_icon km_iconkm_logo\">webkameleon</a> 
        <div class=\"km_verinfo\">$KAMELEON_VERSION$rev, <a href=\"../index.php\">".label("Copyright")." &copy; 2001 - ".date('Y')." Gammanet Sp. z o.o. ".label("All right reserved")."</a></div>  
      </div>";
      if (strlen($identity_sysinfo)>0) 
      {
      	if (strlen($identity_sysinfo_link)) $identity_sysinfo="<a href=\"$identity_sysinfo_link\">$identity_sysinfo</a>";
      	echo "<div class=\"km_sysinfo\"><span class=\"km_icon km_iconi_sysinfo\"></span>".$identity_sysinfo."</div>";
	}
      
  echo "
      <div class=\"km_clean\"></div>
    </div>  
  ";

		
	include ("include/header.h"); ?>


	<? 
		include ("include/$mybasename".".h"); 

	?>


</body>
</html>

<?
	$adodb->Close($sysinfo);
	
	if (is_object($auth_acl)) 
	{
		$auth_acl->puke_debug_as_comment();
		$auth_acl->close();
	}