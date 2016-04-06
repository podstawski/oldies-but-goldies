<?
	global $_REQUEST,$SKLEP_SESSION,$_SESSION,$_SERVER;
	global $WM;
	global $SKLEP_ID;
	
	if (!strlen($SKLEP_INCLUDE_PATH)) $SKLEP_INCLUDE_PATH=$INCLUDE_PATH;

	$SKLEP_IMAGES=$IMAGES;
	
	$SKLEP_INSTALL_DIR=str_replace($INCLUDE_PATH,'',$SKLEP_INCLUDE_PATH);
	if (file_exists("$IMAGES$SKLEP_INSTALL_DIR")) $SKLEP_IMAGES="$IMAGES$SKLEP_INSTALL_DIR";

	include_once("$SKLEP_INCLUDE_PATH/xml.php");

	include_once("$SKLEP_INCLUDE_PATH/constant/const.inc");
	include_once("$SKLEP_INCLUDE_PATH/fun.php");
	include_once("$SKLEP_INCLUDE_PATH/autoryzacja/afun.h");
	include_once("$SKLEP_INCLUDE_PATH/autoryzacja/sysfun.h");


	if (!is_object($WM))
	{		
		include_once("$SKLEP_INCLUDE_PATH/class/wm.php");
		if (file_exists("$SKLEP_INCLUDE_PATH/constant/wmclass.php")) 
		{
			include_once("$SKLEP_INCLUDE_PATH/constant/wmclass.php");
			$WM = new FWM($SERVER_ID,$lang);
		}
		else
		{
			$WM = new WM($SERVER_ID,$lang);
		}

		if (!$WEBTD->sid) $WM->projdb->SetCharSet("LATIN2");

		if (!$WM->_connectionID)
		{
			echo "Nie mo¿na siê pod³¹czyæ do bazy danych";
			exit();
		}

	}

	
	$kameleon_adodb=$adodb;
	$db=$WM->_connectionID;	
	$projdb=&$WM;
	if ($WEBTD->sid)
		$adodb=$WM;
	else
		$adodb=&$WM;
	
	$NOW = $WM->now;
	$SKLEP_ID = $WM->sklep;

	$LIST = $_REQUEST["list"];
	$CIACHO=$_REQUEST["ciacho"];
	$action=$_REQUEST["action"];
	$HTTP_REFERER=$_SERVER["HTTP_REFERER"];
	$FORM=txt_addslash($_REQUEST["form"]);
	$oddzial_id = $FROM["parent_id"];
	$upraw = $_REQUEST["upraw"];
	$SC=$_REQUEST["sc"];
	$SKLEPY = $_REQUEST["SKLEPY"];
	while (is_array($SC) && list($ciastko,$v)=each($SC)) 
	{
		if (headers_Sent()) echo "<script>document.cookie='ciacho[$ciastko]=$v';</script>";
		else SetCookie("ciacho[$ciastko]",$v);
		$CIACHO[$ciastko]=$v;
	}

	$AUTH=$_REQUEST["AUTH"];
	$CAUTH=$_REQUEST["CAUTH"];
	$REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];

	$SKLEP_KW=$_REQUEST["SKLEP_KW"];
	$SKLEP_CZ=$_REQUEST["SKLEP_CZ"];
	$SKLEP_CE=$_REQUEST["SKLEP_CE"];
	$SKLEP_MG=$_REQUEST["SKLEP_MG"];
	$SKLEPY=$_REQUEST["SKLEPY"];

	if (!$KAMELEON_MODE)
		$next_char="?";
	else
		$next_char="&";
	
	
	$sql="SELECT so_wart AS autologout FROM system_opcje WHERE so_nazwa2='autologout'";
	parse_str(ado_query2url($sql));
	
	$WM->session_start();

	ini_set("magic_quotes_gpc","Off");

	if (!is_array($SKLEP_SESSION["SYSTEM"]))
	{
		$sql="SELECT * FROM system_opcje ORDER BY so_nazwa2";
		$result = $projdb->Execute($sql);
		$query="";
		if (is_object($result))
 		 for ($i=0;$i<$result->RecordCount();$i++)
		 {
			parse_str(ado_ExplodeName($result,$i));
			$SYSTEM["$so_nazwa2"]=$so_wart;
		 }
		$SKLEP_SESSION["SYSTEM"]=$SYSTEM;
	}
	else
	{
		$SYSTEM=$SKLEP_SESSION["SYSTEM"];
	}

	$WM->system=$SYSTEM;
	$WM->include_path=$SKLEP_INCLUDE_PATH;
	$WM->ufiles=$UFILES;
	$WM->session=$SKLEP_SESSION["WMS"];
	$SOAP_PATH="$UFILES/soap";


	if (is_array($_COOKIE[auto_to_id]))
	{
		foreach (array_keys($_COOKIE[auto_to_id]) AS $_tid)
		{
			if (!$_COOKIE[auto_to_id][$_tid]) unset ($_COOKIE[auto_to_id][$_tid]);
		}
	}

	if ($WEBTD->sid && !strlen($AUTH[id]))
	{
		global $KAMELEON;
		$sql = "SELECT su_id, su_parent, su_imiona, su_nazwisko FROM system_user WHERE su_login = '".$KAMELEON[username]."'
				AND su_parent IS NOT NULL";
		parse_str(ado_query2url($sql));
		if (strlen($su_id)) $AUTH[id] = $su_id;
		$AUTH[parent] = $su_parent;
		$AUTH[p_price] = 1;
		$AUTH[imiona]=$su_imiona;
		$AUTH[nazwisko]=$su_nazwisko;
		$AUTH[p_admin]=1;
	}

?>