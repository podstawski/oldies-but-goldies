<?
	$_kameleon_debug+=0;
	if (strlen($API_CLASS))
	{
		if ($_kameleon_debug) echo "API_CLASS=$API_CLASS<br>";

		$css=ereg_replace("\.[a-z]+","",basename($html)).".css";
		if (file_exists("$INCLUDE_PATH/$css"))
		{
			global $WEBTD;
			$path=$INCLUDE_PATH;
			if ($WEBTD->staticinclude && !$KAMELEON_MODE ) $path="$REMOTE_INCLUDE_PATH/@api";

			echo "<SCRIPT>
					apiClassName='$API_CLASS';
					apiClassFile='$path/$css';
					apiClassDebug=$_kameleon_debug;
				</SCRIPT>
				<SCRIPT SRC=\"$path/api.js\"></SCRIPT>
				";
		}
		$API_CLASS="";
	}


	
	$_API_VARS=array("SERVER_ID","ver","lang","page","html","next","self","more",
			"KAMELEON_MODE","IMAGES","UIMAGES","UFILES","sid");

	if ($_kameleon_debug) 
	{
		echo "API_VARS=";
		print_r($API_VARS);
		echo "<br>";
	}

	if (is_array($API_VARS))
	{
		$__t=time();
		$API_VARS[]="__t";
	}
	else $API_VARS=array();

	$_api_params="";
	foreach(array_merge($_API_VARS,$API_VARS) AS $k)
	{

		if (strlen($k))
		{
			eval("\$v=\$$k;");
			

			if (!is_Array($v))
			{
				if (strlen($v))
				{
					if (strlen($_api_params)) $_api_params.="&";
					$_api_params.="$k=".urlencode(stripslashes($v));
				}
				
			}
			else
			{
				while (list($_k,$_v)=each($v))
				{
					if (strlen($_api_params)) $_api_params.="&";
					$_api_params.="${k}[$_k]=".urlencode(stripslashes($_v));
				}
			}
		}
	}
	$API_VARS=null;



global $CMS_API_HOST,$_COOKIE;

if ($PreferServerToFetch) $_api_params.="&PreferServerToFetch=1";

if ($_kameleon_debug)
{
	$_href="$CMS_API_HOST/modules/@api/api.php?$_api_params";
	echo "<br><a href='$_href'>$_api_params</a><br>";
}


$_api_params=api2_koduj_url($_api_params,false);
if ($PreferServerToFetch) $_api_params.="&WKSESSID=".$_COOKIE["WKSESSID"];

$_href="$CMS_API_HOST/modules/@api/api.php?$_api_params";

if ($_kameleon_debug) 
{
	echo "Request length: ".strlen($_href)."<br>";
}

if ($nocache || $KAMELEON_MODE) $_href.="&nocache=1";


$wait="";
if (!strlen($KAMELEON_UIMAGES)) $KAMELEON_UIMAGES=$UIMAGES;
foreach (array("$KAMELEON_UIMAGES/api","$KAMELEON_UIMAGES","$INCLUDE_PATH") AS $path)
{
	if (file_exists("$path/wait.gif")) 
	{
		global $WEBTD;		
		if ($WEBTD->staticinclude && !$KAMELEON_MODE )
		{
			if ($path==$INCLUDE_PATH) $path="$REMOTE_INCLUDE_PATH/@api";
			else $path=ereg_replace($KAMELEON_UIMAGES,$UIMAGES,$path);
		}
		$wait="<img src=\"$path/wait.gif\">";
		break;
	}
}



if (!$sid || $DontShowHourglass) $wait="";


if ($PreferServerToFetch)
{
	@readfile($_href);
}
else
{
	global $_api_span_counter;
	global $HTTP_POST_VARS , $HTTP_GET_VARS;

	$_api_span_counter+=1;
	$_action_m = ( strlen($HTTP_GET_VARS[action]) || strlen($HTTP_POST_VARS[action]) ) ? round(1000/$_api_span_counter) : 10 ;
	$_timeout = $sid ? $_action_m*$_api_span_counter : 1;
?>

<span id="api_span_<?echo $sid?>"><?echo $wait?></span>

<SCRIPT LANGUAGE="JavaScript" id="api_js_<?echo $sid?>" >
	
	function api_fun_<?echo $sid?>()
	{
//		document.all['api_js_<?echo $sid?>'].src="<? echo $_href?>";
		loadContent_<?echo $sid?>('<? echo $_href?>','api_js_<?echo $sid?>')
	}

	function loadContent_<?echo $sid?>(file,id)
	{
	  var head = document.getElementsByTagName('head').item(0)
	  var scriptTag = document.getElementById(id);
	  script = document.createElement('script');
	  script.src = file;
		script.type = 'text/javascript';
		script.id = 'loadScript';
		head.appendChild(script)
	}
	if (location.protocol!='https:') setTimeout(api_fun_<?echo $sid?>,<? echo $_timeout?>);
</SCRIPT>

<?
}
if ($_kameleon_debug) echo "<hr size=1>";
?>