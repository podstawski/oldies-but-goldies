<?
	global $editmode;

	if (strstr($WEBTD->api,"kameleon:"))
	{
		$inside_api=1;
		$name=substr($WEBTD->api,9);
		include("include/kameleon_api_$name.h");
	}
	if ($inside_api) return;

	$key="KEY=".$SERVER->nazwa."&SERVICE=$api";
	$api_key=koduj_url($key);

	$param="cos=$WEBTD->cos&size=$WEBTD->size&class=$WEBTD->class&page=$page&lang=$lang&ver=$ver";

	if ($page_id>=0) $param.="&costxt=$WEBTD->costxt";
	
	
	$more=$WEBTD->more;
	$next=$WEBTD->next;
	if (!$next) $next=$page;
	if (!$more) $more=$page;

	$next=kameleon_href("","",$next);
	$more=kameleon_href("","",$more);
	$param.="&next=$next&more=$more&editmode=$editmode&sid=".$WEBTD->sid;

	if ($KAMELEON_MODE || $WEBTD->staticinclude)
	{
		parse_str($param);
		include("remote/api.h"); 
	}
	else
	{
		$param.="&API_SERVER=$API_SERVER";
		echo "<?\n";
		echo " parse_str(\"$param\");\n";
		echo " \$api_key=\"$api_key\";\n";
		echo " 	include(\"$INCLUDE_PATH/api.h\");\n";
		echo "?>";

	}

/*	if ($this_editmode && !$WEBTD->staticinclude && $cos!=1)
	{
		$cos=1;
		echo "<table width=100% border=1 cellspacing=0>";
		echo "<tr><td bgcolor=silver>\n";
		include("api.h"); 
		echo "\n</td></tr></table>\n";
		
	}
*/
?>
