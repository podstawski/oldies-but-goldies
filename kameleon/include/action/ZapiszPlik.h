<?
	include_once('include/file.h');
	$wf_id=0+webfile($_REQUEST[plik],$galeria);
	$wf_accesslevel=webfile($_REQUEST[plik],$galeria,'wf_accesslevel');

	include('include/ufiles_const.h');
	
	if ($wf_accesslevel > $kameleon->current_server->accesslevel)
	{
		$error=label("Insufficient rights");
		return;
	}

	$txt=$_REQUEST[file_contents];

	$ini=ini_get_all();
	if ($ini['magic_quotes_gpc']['local_value']) $txt=stripslashes($txt);

	if (file_exists("$rootdir/../".$kameleon->user[username].'/'.$_REQUEST[plik])) $rootdir="$rootdir/../".$kameleon->user[username];


	$path=$rootdir.'/'.$_REQUEST[plik];


	if (!file_exists($path)) return;
	
	$plik=@fopen($path,'w');
	@fwrite($plik,$txt);
	@fclose($plik);

