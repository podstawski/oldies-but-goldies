<?

	global $PHP_AUTH_USER,$DEFAULT_TD_LEVEL,$pri,$page_id;
	global $HTTP_GET_VARS;


	push($page);
	push($page_id);
	
	$page_id=$page;
	include("include/action/DodajTD.h");
	$query="SELECT * FROM webtd WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND page_id=$page_id AND pri=$pri";
	$res=$adodb->Execute($query);
	if ($res && $res->RecordCount())
	{
		$d=$res->FetchRow();
		$WEBTD=$d;
		reset($HTTP_GET_VARS);
		$set="";
		while ( is_array($HTTP_GET_VARS) && list($k,$v) = each($HTTP_GET_VARS))
		{
			if (array_key_exists($k,$d))
			{
				if (strlen($set)) $set.=",";
				$set.="$k='$v'";
			}

		}
		if (strlen($set)) 
		{
			$q="UPDATE webtd SET $set WHERE sid=$d[sid];";
			if ($adodb->Execute($q)) logquery($q);
		}
	}


	$page_id=pop();
	$page=pop();
?>