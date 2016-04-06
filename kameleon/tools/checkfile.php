<?php
	if (!strlen($argv[1]))
	{
		echo "Usage:  ".$argv[0]." file_name\nResult: numer of appearance on stdout, null if file not found or file uncheckable \n";
	}
	chdir(dirname($argv[0]));
	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');

	define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	$file=$argv[1];
	chdir('..');

	$checkable=array('ufiles','uimages');
	$suma='';

	foreach ($checkable AS $ch)
	{
		$pos=strpos($file,"$ch/");
		if (!strlen($pos)) continue;
		$file=substr($file,$pos+strlen($ch)+1);

		if (!strlen($file)) break;

		$pos=strpos($file,'/');
		$dir=substr($file,0,$pos);
		$file=substr($file,$pos+1);
		
		if (!file_exists("$ch/$dir/$file")) break;	
		if (is_dir("$ch/$dir/$file")) break;

		if ($ch=='ufiles')
		{
			$_dir=explode('-',$dir);
			$SERVER_ID=$_dir[0];

			$sql="SELECT count(*) AS c FROM weblink WHERE server=$SERVER_ID AND ufile_target='$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;

			$sql="SELECT count(*) AS c FROM webtd WHERE server=$SERVER_ID AND plain ~ '$ch/$dir/$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;			
		}
		if ($ch=='uimages')
		{
			$SERVER_ID=$dir;


			$sql="SELECT count(*) AS c FROM weblink WHERE server=$SERVER_ID AND img='$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;

			$sql="SELECT count(*) AS c FROM weblink WHERE server=$SERVER_ID AND imga='$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;

			$sql="SELECT count(*) AS c FROM webpage WHERE server=$SERVER_ID AND background='$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;

			$sql="SELECT count(*) AS c FROM webtd WHERE server=$SERVER_ID AND plain ~ '$ch/$dir/$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;

			$sql="SELECT count(*) AS c FROM webtd WHERE server=$SERVER_ID AND img='$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;

			$sql="SELECT count(*) AS c FROM webtd WHERE server=$SERVER_ID AND bgimg='$file'";
			parse_str(ado_query2url($sql));
			$suma+=$c;
		}
	}

	echo $suma;


	$adodb->Close();
?>