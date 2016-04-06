<?
$_kameleon_debug=0;


	$_kameleon_debug+=0;
	if (strlen($API_CLASS))
	{
		if ($_kameleon_debug) echo "API_CLASS=$API_CLASS<br>";

		$css=ereg_replace("\.[a-z]+","",basename($html)).".css";
		if (file_exists("$INCLUDE_PATH/$css"))
			echo "<SCRIPT>
					apiClassName='$API_CLASS';
					apiClassFile='$INCLUDE_PATH/$css';
					apiClassDebug=$_kameleon_debug;
					</SCRIPT>
					<SCRIPT SRC=\"$INCLUDE_PATH/api.js\"></SCRIPT>
				";

		$API_CLASS="";
	}


	
	$_API_VARS=array("SERVER_ID","ver","lang","page","html","costxt","cos","next","self","more",
			"KAMELEON_MODE","IMAGES","UIMAGES","UFILES","sid","size");

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



global $CMS_API_HOST;

if ($_kameleon_debug)
{
	$_href="$CMS_API_HOST/modules/@api/api.php?$_api_params";
	echo "<br><a href='$_href'>$_api_params</a><br>";
}
$_api_params=koduj_url($_api_params);

?>

<SCRIPT LANGUAGE="JavaScript">
document.writeln('<SCRIPT LANGUAGE="JavaScript" SRC="<?echo $CMS_API_HOST?>/modules/@api/api.php?<?echo $_api_params ?>"><\/SCRIPT>');
</SCRIPT>

<?
if ($_kameleon_debug) echo "<hr size=1>";
?>
