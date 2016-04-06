<?

	$LANGS=array();

	$query="SELECT msg_lang FROM messages WHERE msg_lang<>'ms' GROUP BY msg_lang ORDER BY msg_lang";
	$res=$projdb->execute($query);

	for ($i=0;$i<$res->recordCount();$i++)
	{	
		parse_str(ado_ExplodeName($res,$i));
		$LANGS[]=$msg_lang;
	}

	$CVS="Lp;etykieta [label];grupa";
	foreach ($LANGS AS $l) $CVS.=";".sysmsg($l,"system")." [$l]";
	$CVS.="\r\n";



	$query="SELECT msg_label,msg_group FROM messages WHERE msg_lang='ms' ORDER BY msg_group,msg_label";
	$res=$projdb->execute($query);

	for ($i=0;$i<$res->recordCount();$i++)
	{	
		parse_str(ado_ExplodeName($res,$i));

		if ($msg_group!=$last_group) $CVS.="\r\n";
		$last_group=$msg_group;

		$CVS.=$i+1;
		$CVS.=";";
		$CVS.="$msg_label;$msg_group";

		foreach ($LANGS AS $l)
		{
			$msg_msg="";
			$query="SELECT msg_msg FROM messages WHERE msg_lang='$l' AND msg_label='$msg_label'";
			parse_str(ado_query2url($query));
			$msg_msg=ereg_replace("[\r\n]+"," ",$msg_msg);

			$CVS.=";\"$msg_msg\"";
		}

		$CVS.="\r\n";
	}

	$CVS=iso2win($CVS);
	if ($KAMELEON_MODE) {echo nl2br($CVS); return;}

	$name="langs.csv";
	Header("Content-Type: application/x-csv ; name=\"$name\"");
	Header("Content-Length: ".strlen($CVS));
	Header("Content-Disposition: attachment; filename=\"$name\"");
	echo $CVS;
	exit();


?>