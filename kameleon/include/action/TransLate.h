<?
	$GENERATE_ONLY_WEBPAGE_OBJECT=1;
	
	if (!is_array($wt_t_plain)) return;

	list($wt_sid,$wt_t_plain)=each ($wt_t_plain);
	$wt_t_plain=addslashes(stripslashes($wt_t_plain));
	if (!strlen(trim($wt_t_plain))) return;


	include_once('include/utf8.h');
	$wt_t_plain=utf82lang($wt_t_plain);


	while (is_array($settransmode) && list($cn,$cv)=each($settransmode)) if (strlen($cv)) 
	{
		$adodb->setCookie($cn,$cv);
		eval("\$$cn=\$cv;");
	}

	$sql="SELECT wt_similar FROM webtrans WHERE wt_sid=$wt_sid";
	parse_str(ado_query2url($sql));

	if ($ch_saveall && strlen($wt_similar)) $wt_sid.=",$wt_similar";

	$query="UPDATE webtrans SET 
			wt_t_plain='$wt_t_plain',
			wt_translation=".$adodb->now.",
			wt_translator='".$kameleon->user[username]."'
			WHERE wt_sid IN ($wt_sid) AND wt_server=$SERVER_ID AND wt_verification IS NULL";

	//$adodb->debug=1;	
	$adodb->execute($query);


?>