<?

$C_DB_CONNECT="host=sql2.gammanet.pl port=5432 user=root password=45S18g2P dbname=lsystem";
$db=pg_Connect($C_DB_CONNECT);




if (!function_exists("query2url")) {		
	function query2url($query)
	{
		global $db;
		$result=pg_Exec($db,$query);
		if ( pg_numRows($result)!=1 ) return "";
	
		$data=pg_fetch_row($result,0);
		$wynik="";
		for ($i=0;$i<count($data);$i++)
		{	
			if ($i) $wynik.="&";
			$wynik.=pg_fieldname($result,$i)."=".urlencode(trim($data[$i]));
		}
		return $wynik;
	}
}


if (!function_exists("pg_ExplodeName")) {		
	function pg_ExplodeName ($result,$row)
	{
	 $text="";
	 $cols=pg_NumFields($result);
	 $data=pg_fetch_row($result,$row);
	 for ($i=0;$i<$cols;$i++)
	 {
	  $name=pg_FieldName($result,$i);
	  $value=urlencode(trim($data[$i]));
	  $text.="$name=$value";
	  if ($i!=$cols-1)
	   $text.="&";
	 }
	 return $text;
	}
}

if (!function_exists("f_data")) {		
	function f_data ($data,$format='d-m-Y')
	{
		return date($format,strtotime($data));
	}
}