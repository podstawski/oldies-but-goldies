<?


	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');
	define ('ADODB_DIR',"../adodb/");
	include_once ("../include/adodb.h");

	include ("../include/const.h");
	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");

	chdir(dirname($argv[0]));

	for($i=1;$i<count($argv);$i++)
	{
		parse_str($argv[$i]);
	}


	if (strlen($server))
	{

		$sql="SELECT id AS server, ver AS v, lang AS l FROM servers WHERE nazwa='$server'";
		parse_str(ado_query2url($sql));
		if (!isset($ver)) $ver=$v;
		if (!isset($lang)) $lang=$l;
		
	}
	
	if ($server+0==0) return;
	


	$query="INSERT INTO ftp (username,server,lang,ver) 
		VALUES ('auto',$server,'$lang',$ver)";
	$adodb->Execute($query);



	$query="SELECT max(id) AS id FROM ftp WHERE server=$server";
	parse_str(ado_query2url($query));
	

	for ($i=$argc;$i>0;$i--)
	{
		$argv[$i]=$argv[$i-1];
	}
	$argv[1]=$id;
	$argc=count($argv);


	$dont_check_rights = true;

	include('./ftp.php');
