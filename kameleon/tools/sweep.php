<?
	error_reporting(7);

	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');
	define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	include ("../include/const.h");
	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");

	for ($i=1;$i<$argc;$i++)
	{
		parse_str($argv[$i]);
	}


	$query="SELECT id FROM servers";
	$res=$adodb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$query="SELECT max(id) AS max_id FROM ftp
				WHERE server=$id AND t_end IS NOT NULL";
		parse_str(ado_query2url($query));

		$query="INSERT INTO ftp_arch 
				SELECT * FROM ftp 
				WHERE server=$id AND t_end IS NOT NULL AND id<>$max_id;
				DELETE FROM ftp 
				WHERE server=$id AND t_end IS NOT NULL AND id<>$max_id;
				";

		$adodb->Execute($query);
		
	}

	$query="DELETE FROM ftp 
			WHERE server NOT IN (SELECT id FROM servers WHERE id=ftp.server);
			";
	$adodb->Execute($query);

	$query="INSERT INTO ftplog_arch
			SELECT * FROM ftplog 
			WHERE ftp_id NOT IN (SELECT id FROM ftp WHERE id=ftplog.ftp_id);
			DELETE FROM ftplog 
			WHERE ftp_id NOT IN (SELECT id FROM ftp WHERE id=ftplog.ftp_id);
			";

	$adodb->Execute($query);

?>