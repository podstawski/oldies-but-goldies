<?

genpath(0,":");


function genpath($page,$path)
{
	global $SERVER_ID,$ver,$lang;
	global $adodb;
	global $SHOW,$HIDE;
	global $C_MULTI_HF,$MULTI_HF_STEP;
	global $PAGES_TO_ANAL;
	global $depth;


	if (strstr($path,":$page:")) return;
	if ($PAGES_TO_ANAL[$page]) return;

	$subpath="$path$page:";
	$query="UPDATE webpage SET tree='$path'
			WHERE id=$page AND server=$SERVER_ID AND ver=$ver AND lang='$lang'";

	if ($adodb->Execute($query)) logquery($query);
	//echo "$query<br>";

	$PAGES_TO_ANAL[$page]=1;

	$query="SELECT id FROM webpage
			 WHERE prev=$page AND server=$SERVER_ID AND ver=$ver
			 AND lang='$lang'
			 ORDER BY id";
	$res=$adodb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		genpath($id,$subpath);
	}

}

