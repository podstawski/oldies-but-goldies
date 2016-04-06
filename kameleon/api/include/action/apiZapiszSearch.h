<?
if ($SERVICE!="search")
{
	$api_action="";
	return;
 }
$api_action="";



$u_button_img=urlencode("$u_button_img");
$u_index_type+=0;
$params=addslashes("u_status=$u_status&u_lang=$u_lang&u_ver=$u_ver&page_result=$page&u_button_img=$u_button_img&u_index_type=$u_index_type&u_tree=$u_tree");

if (is_array($tsearch2)) foreach ($tsearch2 AS $k=>$v) $tsearch2[$k]=stripslashes($v);
$u_tsearch2=base64_encode(serialize($tsearch2));

$u_status+=0;
$u_ver+=0;
$query =" SELECT u_params FROM search_ustawienia WHERE servername='$KEY' AND u_sid=".$API_REQUEST['sid'];
$result=$adodb->Execute($query);
$len=$result->RecordCount();
if ($len==1)
{
	$query =" UPDATE search_ustawienia SET";
	$query.=" u_params='$params',u_tsearch2='$u_tsearch2' "; 
	$query.=" WHERE servername='$KEY' AND u_sid=".$API_REQUEST['sid'];
}
else
	$query ="
		INSERT INTO search_ustawienia (u_params, servername,u_tsearch2,u_sid)
		VALUES ('$params', '$KEY', '$u_tsearch2',".$API_REQUEST['sid'].")";

//echo "$query<br>";return;
$result=$adodb->Execute($query);
if (!$result)
	echo label("Error");
else
	echo label("OK");

