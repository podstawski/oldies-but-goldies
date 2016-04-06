<?
if (!$AUTO_PAGE_GENERATOR) $AUTO_PAGE_GENERATOR=1;


if (!is_array($PAGE_RIGHTS) ) $zakres=$PAGE_RIGHTS;
if (!strlen($zakres)) $zakres="$AUTO_PAGE_GENERATOR-2000000000";



$zakresy=explode(";",$zakres);
for ($z=0;$z<count($zakresy);$z++)
{
	$oddo=explode("-",$zakresy[$z]);
	$od=$oddo[0];
	if ($od[strlen($od)-1]=="+") continue;
	$od+=0;
	$do=$oddo[1]+0;
	if (!$do) $do=$od;

	/*
	$query="SELECT DISTINCT id FROM webpage_used
				WHERE ver=$ver AND server=$SERVER_ID
				AND lang='$lang' 
				AND id>=$od AND id<=$do
				ORDER BY id";

	$query="SELECT DISTINCT f_webpage_used($SERVER_ID,int2($ver),'$lang',$od,$do) AS id ORDER BY id";
	*/

	// Build query array
	$Aquery = array();
	$Aquery['postgres'] ="select f_webpage_used ($SERVER_ID,int2($ver),'$lang',$od,$do) as id order by id";
	$Aquery['mssql'] ="select id from f_webpage_used ($SERVER_ID,$ver,'$lang',$od,$do) order by id";

	// Chose right query for correct database
	$query = getProperQuery($Aquery);
	$used_res=$adodb->Execute($query);
	$ile=$used_res->RecordCount();
	
	if ($ile>=($do-$od+1)) continue;
	unset($zakres_used);
	for ($u=0;$u<$ile;$u++)
	{
		parse_str(ado_ExplodeName($used_res,$u));
		$zakres_used[$id]=1;

		//echo ($od+$u)." < $id <br>";
		if ($od+$u < $id) break;		
		
	}
	for ($u=$od;$u<=$do;$u++) 
		if (!$zakres_used[$u])
		{
			//echo "Wybór: $u <br>";
			$page_id=$u;
			break;
		}
	break;
	
}

if ($page_id<=0) $error=label("No more feee pages in your access list");

$referer+=0;

?>