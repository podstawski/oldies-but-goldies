<?
if ($APIFUN_==1) return;
else $APIFUN_=1;

$GLOBAL_HIDDEN="<input type=hidden name=api_post value=1>";

if (isset($HTTP_GET_VARS)) 
{
	reset($HTTP_GET_VARS);
	while ( list( $key, $val ) = each( $HTTP_GET_VARS ) ) 
	{ 
		if ($key=="QS") continue;
		if (ereg("api_",$key)) continue;
		if (ereg("page",$key)) continue;
//		$url.="&$key=".urlencode($val); 
		$GLOBAL_HIDDEN.="<input type=hidden name='$key' value='$val'>";
	}
}





function checkService($key,$service)
{
 GLOBAL $adodb;
 $SQL =" SELECT nexpired FROM services WHERE servername='$key' AND service='$service' ";
 $result=$adodb->Execute($SQL);
 $rows=$result->RecordCount();
 if ($rows==1)
  return 0;
 else
  return 1;
}

?>
