<?php

	function kameleon_ftp_reload($explore)
	{
		global $debug_mode;

		if ($debug_mode) return;
		usleep(2000);



		$reload = '
				<script language="javascript">
					function kameleon_ftp_reload()
					{
						location.href="'.$_SERVER['SCRIPT_NAME'].'?explore='.$explore.'";
					}
					setTimeout(kameleon_ftp_reload,300);
				</script>';


		ini_set('max_execution_time',0);


		$cotrzebawyswietlic='';
		if (ini_get('output_buffering') == 1)
		{
			ob_implicit_flush();
			while (ob_get_level())
			{
				$cotrzebawyswietlic.=ob_get_contents();
				
				ob_end_clean();
				echo '('.strlen($cotrzebawyswietlic).')';
			}
		}


		echo $reload;



		if (ini_get('output_buffering')*1 > 1)
		{
			for ($i=0; $i<3*ini_get('output_buffering')*1; $i++) 
			{
				echo "\n";
				ob_end_flush();
			}
			$cotrzebawyswietlic='cos';
		}

		



		if (!strlen($cotrzebawyswietlic))
		{
			for ($i=0; $i<10; $i++) 
			{
				echo "\n";
				flush();
				ob_flush();
			}
		}
		
		ob_end_clean();
	
		for ($i=0; $i<4096; $i++) 
		{
			echo "\n";
			flush();
			ob_flush();
		}	

		if (session_id()) session_commit();

		
	}


	function kameleon_include_ftp($argv)
	{
		global $adodb;

		$argc=count($argv);
		include('ftp.php');
	}
	
	
	function kameleon_do_ftp($array_of_ftpids,$string_of_options)
	{
		global $adodb;
		
		chdir(dirname(__FILE__)."/../tools");
		$cwd=getcwd();
		
		if (!is_array($array_of_ftpids)) $array_of_ftpids=array($array_of_ftpids);

		

		foreach ($array_of_ftpids AS $array_of_ftpid)
		{
			$cmdline="ftp.php $array_of_ftpid $string_of_options";
			$cmdline=ereg_replace(' +',' ',$cmdline);
			$argv=explode(' ',$cmdline);
			$argc=count($argv);

			chdir($cwd);
			kameleon_include_ftp($argv);

		}
		
		
	}
	

	function kameleon_ftp($array_of_ftpids,$string_of_options)
	{
		global $adodb;
		global $debug_mode;


		$die=$debug_mode?false:true;

		if ($die) kameleon_ftp_reload($array_of_ftpids[0]);

		/*
		if (!$die) kameleon_do_ftp($array_of_ftpids,$string_of_options);
		else register_shutdown_function(kameleon_do_ftp,$array_of_ftpids,$string_of_options);
		*/
		kameleon_do_ftp($array_of_ftpids,$string_of_options);
			
		if ($die) exit();
	}


	function kameleon_ftp_page($string_of_page_options,$ftp_pipe_prg,$ftp_tmp_filename)
	{
		$get=ereg_replace(' +','&',$string_of_page_options);
		$get='_WKSESSID='.$_COOKIE['WKSESSID'].'&'.$get;
		$dir=dirname($_SERVER['REQUEST_URI']);
		if ($dir=='.' || $dir=='/') $dir='';
		$link='http://'.$_SERVER['HTTP_HOST'].$dir.'/index.php?'.$get;

		$file_contents=implode('',file($link));

		$plik=fopen($ftp_tmp_filename,'w');
		fwrite($plik,$file_contents);
		fclose($plik);

	}
