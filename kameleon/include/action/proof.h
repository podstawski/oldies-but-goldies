<?
	$action="";

	if (!$MAY_PROOF) $error=$norights;
	if (!$MAY_PROOF) return;

	$warunki=" lang='$lang' AND ver=$ver AND server=$SERVER_ID";

	$query="SELECT noproof AS np,unproof_autor FROM webpage WHERE id=$page AND $warunki LIMIT 1";
	parse_str(ado_query2url($query));

	if (!$np) 
	{
		$query="UPDATE webpage SET proof_date=".time().",proof_autor='$KAMELEON[username]' WHERE id=$page AND $warunki ";

		if ($adodb->Execute($query)) 
		{
			logquery($query) ;
			webver_page($page,'proof');
		}
		return;
	}

	$moreset=",proof_date=".time().",proof_autor='$KAMELEON[username]'";

	$query="UPDATE webpage SET noproof=0 $moreset WHERE id=$page AND $warunki ";
	if($np==0)  $query.=";
			DELETE FROM webtd WHERE $warunki AND page_id=$page AND hidden=100";

	
	//echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_page($page,'proof');
	}

