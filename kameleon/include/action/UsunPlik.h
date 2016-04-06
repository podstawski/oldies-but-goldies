<?
 $action="";
 include_once('include/file.h');
 include('include/ufiles_const.h');
 $katalog=$rootdir;
 if ($BASIC_RIGHTS) $katalog.="/$USERNAME";
 $lista=str_replace($rootdir."/","",$lista);

 if (strlen($lista))
 {
	$plik_do_wykilowania="$katalog/$lista";

	if (strstr($plik_do_wykilowania,".."))
	{
		$sysinfo=label("You can not delete the root directory !");
		return;
	}

	$wf_id=0+webfile($lista,$galeria);
	$wf_accesslevel=webfile($lista,$galeria,'wf_accesslevel');

	if ($wf_accesslevel > $kameleon->current_server->accesslevel)
	{
		$sysinfo=label("Insufficient rights");
		return;
	}

	if ( is_dir($plik_do_wykilowania) )
	{
		if (!@rmdir($plik_do_wykilowania))
		{
    		$sysinfo=label("Directory is not empty. First delete all files from this directory");
    }
		else $zdalny_plik_do_wykilowania=$plik_do_wykilowania;
	}
	else 
	{
		@unlink($plik_do_wykilowania);
		$zdalny_plik_do_wykilowania=$plik_do_wykilowania;
	}

	if (strlen($zdalny_plik_do_wykilowania))
	{
		$now=time();
		$query="UPDATE webfile SET wf_status='D',wf_d_create=$now WHERE wf_id=$wf_id";
		$adodb->execute($query);
	}
 }
 
?>