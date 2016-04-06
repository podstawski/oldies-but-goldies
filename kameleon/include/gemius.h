<?
$pagekey="";
$ver+=0; $page+=0;
$operator=$C_GEMIUS_INHERIT_VER?"<=":"=";

$_tree=$C_GEMIUS_INHERIT_TREE ? $WEBPAGE->tree.$page : $page;
$_tree = explode(':',$_tree);


for ($i=count($_tree)-1;$i>=0 ;$i-- )
{
	$_page=$_tree[$i];
	if (!strlen($_page)) continue;
	
	$query="SELECT pagekey, ver AS _v 
		FROM gemius WHERE server=$SERVER_ID AND page_id=$_page
		AND ver $operator $ver AND lang='$lang'
		ORDER BY ver DESC LIMIT 1";
	parse_str(ado_query2url($query));


	if (strlen($pagekey)) break;
}	

if (strlen($pagekey))
{
	$tokens['KAMELEON_CSS']="
	<script type=\"text/javascript\">
		var gemius_identifier = new String('$pagekey');
	</script>
	<script type=\"text/javascript\" src=\"$C_GEMIUS_MAIN_SCRIPT\"></script>\n";
}

	function titlelize($title)
	{
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

		return $gemius_label;
	}




   function gemius_geturl($url)
   {
	global $C_GEMIUS_HREF,$C_GEMIUS_SITE,$C_GEMIUS_PASS;
	
	$url="$C_GEMIUS_HREF/$url&site=$C_GEMIUS_SITE&pass=$C_GEMIUS_PASS";
	for ($try=0;$try<5;$try++)
	{
		$f=@file($url);
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
	$wynik.="&gemius_res=".urlencode($f[0]);
	//echo "Wynik:$wynik <br>\n";
	return ($wynik);
   }


?>
