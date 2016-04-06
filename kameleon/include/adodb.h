<?php

error_reporting(E_ERROR);

include_once(ADODB_DIR."../include/class/kdb.php");
include_once(ADODB_DIR."../include/class/kameleon.php");
require_once(ADODB_DIR."../include/class/dateTime.php");

if (strlen($C_DB_CONNECT) && !strlen($C_DB_CONNECT_DBTYPE))
{
	parse_str(ereg_replace("[ ]+","&",$C_DB_CONNECT));
	$C_DB_CONNECT_DBTYPE="postgres";
	$C_DB_CONNECT_HOST="$host:$port";
	$C_DB_CONNECT_USER=$user;
	$C_DB_CONNECT_PASSWORD=$password;
	$C_DB_CONNECT_DBNAME=$dbname;
}



$adodb=new KDB($C_DB_CONNECT_DBTYPE,$persistant_connection, 
				$C_DB_CONNECT_HOST, $C_DB_CONNECT_USER, $C_DB_CONNECT_PASSWORD, $C_DB_CONNECT_DBNAME,$DEBUG_IP);

$db=&$adodb->_connectionID;
$kameleon=new KAMELEON($adodb);
$DT = new DT();

if (!function_exists('ado_query2limit'))
{
 function ado_query2limit($query)
 {
	 if (is_array($query))
	 {
		$query =  getProperQuery($query);
	 }
	 
	//$query=str_replace('"','\"',$query);

	$orig_query=$query;
	$query=eregi_replace("offset[\ \t]+([0-9]+)",",\\1",$query);
	$query=eregi_replace("limit[\ \t]+([0-9]+)","\",\\1",$query);


	if ($orig_query==$query) $query="$query\"";
	$query="\"$query";

	return $query;
 }
}


if (!function_exists('ado_ExplodeName'))
{
 function ado_ExplodeName (&$result,$row)
 {
 	$text="";

	$result->Move($row);
	$data=$result->FetchRow();

	while ( list( $key, $val ) = each( $data ) )
	{
		if (strlen($text)) $text.="&";
		$text.=$key."=".urlencode(trim($val));
	}
 	return $text;
 }
}

if (!function_exists('ado_query2url'))
{
 function ado_query2url(&$query)
 {
	global $adodb;
	$new_query=ado_query2limit($query);
	
	

	ob_start();
	eval("\$result=\$adodb->SelectLimit($new_query);");
	$rs=ob_get_contents();
	ob_end_clean();

	if (strlen($rs))
	{
		echo "$rs $new_query";
		//exit();
	}
	if (!$result) return;
	if ( $result->RecordCount()!=1 ) return "";

	$wynik=ado_ExplodeName($result,0);
	$result->Close();
	return ($wynik);
 }
}

if (!function_exists('ado_ObjectArray'))
{
 function ado_ObjectArray( &$adodb, &$query)
 {
	$wynik="";
	$new_query=ado_query2limit($query);

	eval("\$result=\$adodb->SelectLimit($new_query);");
	if (!$result) return $wynik;

	$ile = $result->RecordCount();

	for ($i=0;$result && $i<$ile; $i++)
	{
		unset($obj);
		$result->Move($i);
		$data=$result->FetchRow();
		$obj=new _NIC;
		while ( list( $key, $val ) = each( $data ) )
		{
			$obj->$key=trim($val);
		}
		$wynik[]=$obj;
	}
	if ($result) $result->Close();
	return($wynik);
 }
}

if (!function_exists('getProperQuery'))
{
 function getProperQuery($queryArray)
 {
	global $adodb;
	
	if ( !is_array($queryArray) ) return $queryArray;
	
	$db = $adodb->dbType;

	if (isset($queryArray[$db]))
	{
		//$adodb->puke('to jest pytanie do bazy:' . $queryArray[$db] . ' ::KONIEC');
		return $queryArray[$db];
	}
	else
	{
		$adodb->puke('Nie ma w tablicy pytania dla bazy danych '.$db.'.');
	}
 }
}
