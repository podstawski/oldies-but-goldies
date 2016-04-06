<?
	$action="";

	$ch_query="SELECT count(*) AS duplikaty FROM webtd WHERE (sid,uniqueid) IN (SELECT max(sid),uniqueid FROM webtd GROUP BY uniqueid HAVING count(uniqueid)>1)";
	parse_str(ado_query2url($ch_query));

	while ($duplikaty)
	{
		$sql="UPDATE webtd SET uniqueid=NULL WHERE (sid,uniqueid) IN (SELECT max(sid),uniqueid FROM webtd GROUP BY uniqueid HAVING count(uniqueid)>1)";
		$adodb->execute($sql);	
		
		parse_str(ado_query2url($ch_query));
	}
	

?>