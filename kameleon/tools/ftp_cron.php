<?
	return;

	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');
	define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	include ("../include/const.h");
	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");

	chdir(dirname($argv[0]));

	$query="SELECT page_id,server,lang,ver FROM webtd
			WHERE nd_valid_from=".time()." OR nd_valid_to=".time()."-1";

	$res=$adodb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$query="INSERT INTO ftp (username,server,lang,ver) 
			VALUES ('auto',$server,'$lang',$ver)";
		$adodb->Execute($query);

		$query="SELECT max(id) AS id FROM ftp WHERE server=$server";
		parse_str(ado_query2url($query));
		
		


		$cmd="$PHP_EXE ftp.php $id limitpage=$page_id $PHP_SUFFIX";
		exec("$cmd");
	}
?>