<?
if (!$AUTO_MENU_GENERATOR) $AUTO_MENU_GENERATOR=1;

if (!is_array($MENU_RIGHTS) ) $zakres=$MENU_RIGHTS;
if (!strlen($zakres)) $zakres="$AUTO_MENU_GENERATOR-2000000000";

$zakresy=explode(";",$zakres);
for ($z=0;$z<count($zakresy);$z++)
{
	$oddo=explode("-",$zakresy[$z]);
	$od=$oddo[0]+0;
	$do=$oddo[1]+0;
	if (!$do) $do=$od;

	if (!$od && !$do) continue;
	
	$query="SELECT DISTINCT menu_id AS id FROM weblink_used
				WHERE ver=$ver AND server=$SERVER_ID
				AND lang='$lang' 
				AND menu_id>=$od AND menu_id<=$do
				ORDER BY menu_id";

	$queryArray['postgres']="SELECT DISTINCT f_weblink_used($SERVER_ID,int2($ver),'$lang',$od,$do) AS id ORDER BY id";
	$queryArray['mssql'] = "SELECT DISTINCT(menu_id) as id from f_weblink_used($SERVER_ID, $ver ,'$lang',$od,$do) order by menu_id";

	$query = getProperQuery($queryArray);

	//$adodb->debug=1;
	$used_res=$adodb->Execute($query);
	//$adodb->debug=0;
	$ile=$used_res->RecordCount();
	
	if ($ile>=($do-$od+1)) continue;
	unset($zakres_used);
	for ($u=0;$u<$ile;$u++)
	{
		parse_str(ado_explodeName($used_res,$u));
		$zakres_used[$id]=1;

		//echo ($od+$u)." < $id <br>";
		if ($od+$u < $id) break;		
		
	}
	for ($u=$od;$u<=$do;$u++) 
		if (!$zakres_used[$u])
		{
			//echo "Wybór: $u <br>";
			$menu_id=$u;
			break;
		}
		
	break;		
}


if ($menu_id<=0) $error=label("No more feee menus in your access list");

return;
