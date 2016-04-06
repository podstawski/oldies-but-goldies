<?
	$dir='';
	switch ($what)
	{
		case 'inc':
			eval("\$dir=\"$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN\";");	
			eval("\$dest=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
			break;
	}

	if (!strlen($dir)) return;
	if (!is_array($svn)) return;

	include_once("include/svnfun.h");

	$ok=true;
	foreach (array_keys($svn) AS $file)
	{
		$thesame=fcmp("$dir/$file","$dest/$file");
		if ($thesame)
		{
			unlink("$dir/$file");
			continue;
		}

		push($dir);
		push($file);

		$dir=dirname("$dest/$file");
		$file=basename("$dest/$file");

		$username=$kameleon->user[username];
		$password=$kameleon->user[svn];
		$cmd=$kameleon->current_server->svn;
		$command='commit';
		eval("\$prg=\"$cmd\";");


		$file=pop();
		$dir=pop();

		$prg_a=explode(' ',$prg);

		if (!file_exists($prg_a[0]))
		{
			$SVN_LOG.=label('File').' '.$prg_a[0].' '.label('does not exist');
			$ok=false;
			break;
		}

		
		cp_r("$dest/$file","$dest/$file.$username",0755);
		cp_r("$dir/$file","$dest/$file",0755);

		
		ob_start();
		system($prg);
		$wynik=ob_get_contents();
		ob_end_clean();
		

		if (strlen($wynik))
		{
			$ok=false;
			$SVN_LOG.="<span style=\"font-family: Tahoma; font-size:11px; color: red\">$file:</span>
						<pre style=\"font-family: Tahoma; font-size:12px; margin:0px\">$wynik</pre><hr size=1>";
			
			cp_r("$dest/$file.$username","$dest/$file",0755);

		}
		else
		{	
			unlink("$dir/$file");
		}

		unlink("$dest/$file.$username");
	}


	if ($ok)
	{
		$SVN_LOG="<script>top.opener.location.href='index.php?page=$page';window.close();</script>";
	}
?>