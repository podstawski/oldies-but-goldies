<?
// 27-10-2010 CARTMAN: zmiana nazw z ado_ na ado_ żeby się z ado kameleona nie gryzło
 define('ADODB_FETCH_CASE', 2);
 @include_once(ADODB_DIR."adodb.inc.php");
 $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
 function ado_query2limit($query)
 {
	$orig_query=$query;
	$query=eregi_replace("offset[\ \t]+([0-9]+)",",\\1",$query);
	$query=eregi_replace("limit[\ \t]+([0-9]+)","\",\\1",$query);


	if ($orig_query==$query) $query="$query\"";
	$query="\"$query";

	return $query;
 }




 function ado_ExplodeName ($result,$row)
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

 function ado_query2url($query)
 {
	global $dodb;
	echo "odwolanie";
	$new_query=ado_query2limit($query);
	
	echo $new_query;
	return "";
  eval("\$result=\$dodb->SelectLimit($new_query);");

	if (!$result) return;
	if ( $result->RecordCount()!=1 ) return "";

	$wynik=ado_ExplodeName($result,0);
	$result->Close();
	return ($wynik);
 }

 function ado_ObjectArray($adodb,$query)
 {
	$wynik="";
	$new_query=ado_query2limit($query);
	eval("\$result=\$adodb->SelectLimit($new_query);");

	/*
	if (!$result)
	{	
		$adodb->debug=true;
		eval("\$result=\$adodb->SelectLimit($new_query);");
	}
	*/
	
	for ($i=0;$result && $i<$result->RecordCount();$i++)
	{
		unset($obj);
		$result->Move($i);
		$data=$result->FetchRow();
		while ( list( $key, $val ) = each( $data ) )
		{
			$obj->$key=trim($val);
		}
		$wynik[]=$obj;
	}
	if ($result) $result->Close();
	return($wynik);
 }
