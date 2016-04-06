<?
	chdir("../..");


	include_once("include/cache.h");

	if (!$_REQUEST[nocache] && !$_GET[bid] && !strlen($_REQUEST[action]) )
	{
		$API_CACHE_TOKEN='api.'.md5($HTTP_GET_VARS[QS]).'.js';
		if (kameleonCache($API_CACHE_TOKEN))
		{
			echo kameleonCacheContent($API_CACHE_TOKEN);
			exit();
		}
	}

	if (file_exists('const.php')) include_once('const.php'); 
	if (file_exists('const.h')) include_once('const.h');

	define ('ADODB_DIR','adodb/');
	if (strlen($WKSESSID)) $_COOKIE["WKSESSID"]=$WKSESSID;
	$persistant_connection=1;
	include ("include/adodb.h");
	include_once ("include/fun.h");
	include_once ("include/kameleon.h");
	include ("include/const.h");
	include_once ("include/xml_fun.h");

	include_once ("modules/@api/fun.inc");

	if (isset($HTTP_GET_VARS[QS])) 
	{
		$p=api2_rozkoduj_url($HTTP_GET_VARS[QS],false);
		if (strlen($p)>0) parse_str($p);
	}  


	include_once ("include/kameleon_href.h");

	push($KAMELEON_MODE);
	$KAMELEON_MODE=0;
	include("include/auth.h");
	$KAMELEON_MODE=pop();
	include ("include/const.h");

	include_once("include/request.h");

	if (strstr(strtolower($CHARSET),'utf') ) $adodb->adodb->SetCharSet('UTF-8');


	if ($sid)
	{
		$server=0;
		$query="SELECT cos, costxt, size, xml,server FROM webtd WHERE sid=$sid";
		parse_str(ado_query2url($query));

		$xml=stripslashes($xml);
		$costxt=stripslashes($costxt);
		if (!$server || $server!=$SERVER_ID) return;
	}

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

	if (file_exists("$SZABLON_PATH/const.h") && !strlen($error) ) include("$SZABLON_PATH/const.h");
	if (file_exists("$SZABLON_PATH/const.php") && !strlen($error) ) include("$SZABLON_PATH/const.php");

	if (strlen($SZABLON_PATH))
	{
		$webpage_ar=kameleon_page($page+0);

		if (is_array($webpage_ar))
		{
        		$WEBPAGE=$webpage_ar[0];
				if (!strlen($WEBPAGE->file_name)) 
				{
					eval("\$PATH_PAGES=\"$DEFAULT_PATH_PAGES\";");
					eval("\$PATH_PAGES_PREFIX=\"$DEFAULT_PATH_PAGES_PREFIX\";");
					$WEBPAGE->file_name="$PATH_PAGES/$page.$SERVER->file_ext";
				}

		}

	}
	

	eval("\$KAMELEON_UIMAGES = \"$DEFAULT_PATH_KAMELEON_UIMAGES\";");
	eval("\$KAMELEON_UFILES=\"$DEFAULT_PATH_KAMELEON_UFILES\";");

	$_API_MODULE_MODE=1;
	$INCLUDE_PATH="modules/@api";
	$editmode=0;
	if (!strlen(trim($html)) ) return;
	$html=basename($html);
	if (!file_exists("$INCLUDE_PATH/$html")) return;


	Header("Content-Type: application/x-javascript; charset=$CHARSET\n");

	$kameleon->init($lang,$ver,$SERVER_ID,$CHARSET,$page);


	//echo "document.all['api_span_$sid'].innerHTML=''\n";

	$JScript="";
	ob_start();
	include("$INCLUDE_PATH/$html");
	$plain=ob_get_contents();
	ob_end_clean();	

	ob_start();


	if (!$PreferServerToFetch)
	{
		echo "content_$sid='';\n";

		$plain_line = explode("\n",$plain);	
		for ($i=0; $i < count($plain_line); $i++)
		{
			$content = addslashes(stripslashes(trim($plain_line[$i])));

			//echo "document.writeln('$content ');\n";
	
			echo "content_$sid += '$content';\n";
		}
		//echo "document.all['api_span_$sid'].innerHTML = content_$sid; \n";
		echo "document.getElementById('api_span_$sid').innerHTML = content_$sid; \n";
		//echo "document.all['api_span_$sid'].innerHTML = 'ala'; \n";

		echo $JScript;
	}
	else
	{
		echo $plain;
	}

	$out=ob_get_contents();
	ob_end_clean();	

	echo $out;

	if (strlen($API_CACHE_TOKEN)) kameleonCache($API_CACHE_TOKEN,$out);

	$adodb->dontSaveSession=true;
	$adodb->Close($sysinfo,1);
?>