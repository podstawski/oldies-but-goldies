<?
	$action="";

	if (!strlen($nazwa)) return;

	$query="SELECT count(*) AS c FROM groups WHERE groupname='$nazwa'";
	parse_str(ado_query2url($query));

	if ($c) $error=label("Groupname already exists!");
	if ($c) return;	

	$query="INSERT INTO groups (groupname) VALUES ('$nazwa')";
	
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;
	$query="SELECT max(id) AS groupid FROM groups";
	parse_str(ado_query2url($query));
	$SetGroup=$groupid;

?>
