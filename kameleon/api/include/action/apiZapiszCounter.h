<?
if ($SERVICE!="counter")
{
	$api_action="";
	return;
}
$api_action="";



$numdigits+=0;

$sql="SELECT count FROM counter WHERE servername='$KEY' AND page=$page";
$result=$adodb->Execute($sql);
if ($result->RecordCount())
{
	parse_str(ado_ExplodeName($result,0));
	if ($counter_reset==1)
		$war="count=0,";
	else
		$war="";
	$sql="
		UPDATE counter SET 
		$war
		params='imgdir=$imgdir&numdigits=$numdigits'
		WHERE servername='$KEY' AND page=$page";
}
else
{
	$sql="INSERT INTO counter (page,count,servername,params) VALUES ($page,1,'$KEY','imgdir=$imgdir&numdigits=$numdigits')";
}

//echo $sql;
$adodb->Execute($sql);
?>
