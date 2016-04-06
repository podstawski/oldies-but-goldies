#!/php -q
<?php
require_once __DIR__.'/../../../application/autoload.php';

$classname = isset($argv[3]) ? $argv[3] : 'GN_Game_Base';

$config = Config::parse();

set_time_limit(0);
try
{
	if ($argc<3){throw new Exception('Not enough arguments (port,supasswd)');}
	else
	{
		$port = (int)$argv[1];
		$magic_key = $argv[2];
		echo "Launch arguments:\n";
		echo "port: " . $port . "\n";
		echo "magic_key: " . $magic_key . "\n";
		echo "classname: " . $classname . "\n";

		/*$pidfile = sys_get_temp_dir().'/'.strtolower($classname).'.pid';

		if (file_exists($pidfile))
		{
			$pid=file_get_contents($pidfile);
			echo "try to kill process: " . $pid .' ... ';
			echo posix_kill($pid,3) ? 'ok':'ee';
			flush();
			sleep(1);
			echo "\n";
			
		}
		*/
		$game = new $classname("localhost",$port,$magic_key,$config['googleapps.scoreUrl'],$config['googleapps.hash']) ;
	}

}
catch (Exception $e) {
    echo 'Failure: ',  $e->getMessage(), "\n";
	exit(1);
}

