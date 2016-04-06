<?php
	if (!strlen($svn_files)) return;
	
	$pos=strpos($svn_files,':');
	$svn_html=substr($svn_files,0,$pos);

	$rest=substr($svn_files,$pos+1);

	eval("\$KAMELEON_UINCLUDES=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
	eval("\$KAMELEON_UINCLUDES_SVN=\"$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN\";");

	if (!file_exists("$KAMELEON_UINCLUDES/$svn_html")) 
	{
		if (!file_exists(dirname("$KAMELEON_UINCLUDES/$svn_html"))) mkdir_r(dirname("$KAMELEON_UINCLUDES/$svn_html"));
		touch ("$KAMELEON_UINCLUDES/$svn_html");
	}
	

	if (!file_exists($KAMELEON_UINCLUDES_SVN)) mkdir_r($KAMELEON_UINCLUDES_SVN,0777);
	if (!file_exists($KAMELEON_UINCLUDES_SVN)) return;


	if (!file_exists("$KAMELEON_UINCLUDES_SVN/$svn_html"))
	{
		$dir=dirname("$KAMELEON_UINCLUDES/$svn_html");
		$file=basename("$KAMELEON_UINCLUDES/$svn_html");

		$username=$kameleon->user[username];
		$password=$kameleon->user[svn];
		$cmd=$kameleon->current_server->svn;
		$command='update';
		eval("\$prg=\"$cmd\";");

		ob_start();
		system($prg);
		$wynik=ob_get_contents();
		ob_end_clean();
		
		cp_r("$KAMELEON_UINCLUDES/$svn_html","$KAMELEON_UINCLUDES_SVN/$svn_html",0777);
	}


	include("include/svnfun.h");

	if (strlen($rest))
	{
		$ftree=getFileTree($KAMELEON_UINCLUDES,$rest);

		foreach ($ftree AS $more_svn)
		{
			if (strpos($more_svn,'/'))
			{
				$dir="$KAMELEON_UINCLUDES_SVN/".dirname($more_svn);
				if (!file_exists($dir)) mkdir_r($dir,0777);
			}
			if (!file_exists("$KAMELEON_UINCLUDES_SVN/$more_svn") && is_writable("$KAMELEON_UINCLUDES/$more_svn") )
			{
				
				$dir=dirname("$KAMELEON_UINCLUDES/$svn_html");
				$file=basename("$KAMELEON_UINCLUDES/$svn_html");

				$username=$kameleon->user[username];
				$password=$kameleon->user[svn];
				$cmd=$kameleon->current_server->svn;
				$command='update';
				eval("\$prg=\"$cmd\";");

				ob_start();
				system($prg);
				$wynik=ob_get_contents();
				ob_end_clean();				
				
				
				cp_r("$KAMELEON_UINCLUDES/$more_svn","$KAMELEON_UINCLUDES_SVN/$more_svn",0777);
			}
		}

		
	}
	

?>