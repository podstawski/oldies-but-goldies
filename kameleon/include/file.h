<?
function webfile($wf_file,$wf_gal,$pole='')
{
	global $ver,$SERVER_ID;
	global $kameleon;
	global $adodb;




	$wf_file=str_replace('./','',$wf_file);
	$query="SELECT * FROM webfile WHERE wf_server=$SERVER_ID AND wf_gal=$wf_gal AND wf_ver=$ver 
			AND wf_file='$wf_file' LIMIT 1";
	parse_str(ado_query2url($query));



	if ($wf_id)
	{
		$wf_accesslevel+=0;

		if ($wf_accesslevel > $kameleon->current_server->accesslevel) return false;
		else 
		{
			if (strlen($pole)) eval("\$wf_id=\$$pole;");
			return $wf_id;
		}
	}

	if (strlen($wf_file)>1)
	{
		$wf_dir=dirname($wf_file);		
		while(strlen($wf_dir) && $wf_dir!='.')
		{
			$query="SELECT wf_accesslevel,wf_id AS _wf_id 
					FROM webfile WHERE wf_server=$SERVER_ID AND wf_gal=$wf_gal AND wf_ver=$ver 
					AND wf_file='$wf_dir' LIMIT 1";
			parse_str(ado_query2url($query));

			if ($_wf_id) break;
			$wf_dir=dirname($wf_dir);	
		}
		$wf_accesslevel+=0;
		if ($wf_accesslevel > $kameleon->current_server->accesslevel) return false;
	}

	$wf_accesslevel+=0;

	$galeria=$wf_gal;
	include_once('include/ufiles_const.h');	



	$wf_type=@is_dir("$rootdir/$wf_file")?'D':'F';

	$now=time();
	$kto=$kameleon->user[username];
	$query="INSERT INTO webfile (wf_server,wf_ver,wf_gal,wf_file,wf_type,wf_status,wf_accesslevel,wf_autor,wf_d_create)
			VALUES ($SERVER_ID,$ver,$wf_gal,'$wf_file','$wf_type','N',$wf_accesslevel,'$kto',$now)";

	$adodb->execute($query);

	$query="SELECT * FROM webfile WHERE wf_server=$SERVER_ID AND wf_gal=$wf_gal AND wf_ver=$ver 
			AND wf_file='$wf_file'";
	parse_str(ado_query2url($query));
		
	if (strlen($pole)) eval("\$wf_id=\$$pole;");
	return $wf_id;
}

?>