<?
	$action="";
	
	$query="SELECT count(*) AS c FROM servers WHERE groupid=$grupa";
	parse_str(ado_query2url($query));
	if ($c)
	{
		$error=label("There are servers in this group !");
		return;
	}

	$query="SELECT count(*) AS c FROM passwd WHERE groupid=$grupa";
	parse_str(ado_query2url($query));
	if ($c)
	{
		$error=label("There are users in this group !");
		return;
	}
	
	$query="DELETE FROM groups WHERE id=$grupa";
	
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;

	$groupid="";
	$query="SELECT min(id) AS groupid FROM groups";
	parse_str(ado_query2url($query));
	$SetGroup=$groupid;
	
?>
