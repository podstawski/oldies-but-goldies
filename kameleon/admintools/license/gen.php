#!/usr/local/bin/php -q
<?

$CONST_LICENSE_KEY="";
$CONST_LICENSE_SECRET="klj7G#h^*de@#^*jhjf";


if ($argc==2 && file_exists($argv[1]) )
{
        $f=file($argv[1]);
        for ($i=0;$i<count($f);$i++)
        {
		if (strlen(trim($f[$i]))) $linia=$f[$i];
        }
	eval($linia);	




	
	if (strlen($CONST_LICENSE_KEY))
	{
		$_md5=$md5=substr($CONST_LICENSE_KEY,0,strpos($CONST_LICENSE_KEY,"g"));
		$_rest=substr($CONST_LICENSE_KEY,strpos($CONST_LICENSE_KEY,"g")+1);
		$_rmd5=md5($_rest.$CONST_LICENSE_SECRET);
		if ($_md5==$_rmd5)
		{
			$_rest=base64_decode($_rest);
			$CONST_LICENSE_NAME=substr($_rest,strpos($_rest,":")+1);
			$pos=strpos($CONST_LICENSE_NAME,":");
			if ($pos)
			{
				$CONST_LICENSE_HOST=substr($CONST_LICENSE_NAME,$pos+1);
				$CONST_LICENSE_NAME=substr($CONST_LICENSE_NAME,0,$pos);
			}

			$unix=substr($_rest,0,strpos($_rest,":"))+0;
			$CONST_LICENSE_VALID=date("20y-m-d",$unix);
			$d=$CONST_LICENSE_VALID;
			$CONST_LICENSE_SERVERS=$unix-mktime(1,0,0,substr($d,5,2),substr($d,8,2),substr($d,0,4));
			if (time()>$unix) $CONST_LICENSE_INVALID=1;
		}
	
		unset($_md5); unset($_rest); unset($_rmd5);
	}

	//Usage: ./gen.php licence_name valid_to(sql format) max_servers host_name
	
	echo "php $argv[0] $CONST_LICENSE_NAME $CONST_LICENSE_VALID $CONST_LICENSE_SERVERS $CONST_LICENSE_HOST \n";
	
	return;
}



if ($argc!=5)
{
	echo "Usage: $argv[0] licence_name valid_to(sql format) max_servers host_name \n";
	exit();
}

//echo $CONST_LICENSE_SECRET;

$d=$argv[2];
$key=mktime(1,0,0,substr($d,5,2),substr($d,8,2),substr($d,0,4));
$key+=$argv[3];
$string=base64_encode("$key:$argv[1]:$argv[4]");
$md5=md5($string.$CONST_LICENSE_SECRET);

$CONST_LICENSE_KEY=$md5."g".$string;
echo "\$CONST_LICENSE_KEY=\"$CONST_LICENSE_KEY\"; \n";

$plic=fopen("license.txt","a+");
//fseek($plic,0,SEEK_END);
fwrite($plic,"$argv[1] $argv[2] $argv[3] $argv[4] \n"); 
fwrite($plic,"\$CONST_LICENSE_KEY=\"$CONST_LICENSE_KEY\"; \n");
fclose($plic);

//include ("/www/kameleon2.gammanet.pl/include/const.h");
//echo "$CONST_LICENSE_NAME , $CONST_LICENSE_VALID , $CONST_LICENSE_SERVERS \n";
//echo "$CONST_LICENSE_KEY \n";

?>
