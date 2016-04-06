<?
$api_action="";
if ($SERVICE!="news")
{
	return;
}

$query="SELECT max(pri) as maxpri FROM webaktual
		WHERE servername='$KEY' ";
$res=$adodb->Execute($query);
parse_str(ado_ExplodeName($res,0));
$maxpri+=1;

$query="SELECT max(rok) as maxrok FROM webaktual
		WHERE servername='$KEY' ";
$res=$adodb->Execute($query);
	parse_str(ado_ExplodeName($res,0));
	if ($maxrok==0) $maxrok=2001;

	$query="SELECT max(mies) as maxmies FROM webaktual
		WHERE servername='$KEY' AND rok=$maxrok";
   $res=$adodb->Execute($query);
	parse_str(ado_ExplodeName($res,0));
	if ($maxmies==0) $maxmies=1;	

	
	$query="INSERT INTO webaktual
		 (rok,mies,servername,pri,nd_akt)
		  VALUES
		 ($maxrok,$maxmies,'$KEY',$maxpri,".time().")";

   $adodb->Execute($query);
	
?>
