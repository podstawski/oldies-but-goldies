<?
	$query="SELECT wf_sid FROM webfav 
			WHERE wf_user='$USERNAME' AND wf_server=$SERVER_ID AND wf_lang='$lang' AND wf_page_id=$page";
	parse_str(ado_query2url($query));

	$query=$wf_sid?"DELETE FROM webfav WHERE wf_sid=$wf_sid":"INSERT INTO webfav (wf_user,wf_server,wf_page_id,wf_lang) VALUES ('$USERNAME',$SERVER_ID,$page,'$lang')";
	
	if ($adodb->execute($query))
	{
		logquery($query);
	}
?>