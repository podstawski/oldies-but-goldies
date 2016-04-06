<?
$PAGES_TO_ANAL="";
$PAGES_TO_ANAL[0]="DONE";

gentree(0);
$action="ZbudujSciezki";

function gentree($page)
{
	global $SERVER_ID,$ver,$lang;
	global $adodb;
	global $SHOW,$HIDE;
	global $C_MULTI_HF,$MULTI_HF_STEP;
	global $PAGES_TO_ANAL;
	global $depth;


	$pages_to_walk="";
	$ile_stron=0;

	$query="SELECT ver AS _ver,type AS pagetype 
		 FROM webpage 
		 WHERE id=$page AND server=$SERVER_ID AND ver<=$ver AND lang='$lang'
		 ORDER BY ver DESC LIMIT 1";
	parse_str(ado_query2url($query));


	$query="SELECT header, footer FROM servers WHERE id=$SERVER_ID";
	parse_str(ado_query2url($query));

	if ($C_MULTI_HF) 
	{
		$header-=$MULTI_HF_STEP*$pagetype;
		$footer-=$MULTI_HF_STEP*$pagetype;
	}

	$pages[]="$header";
	$pages[]="$page";
	$pages[]="$footer";


	for ($pagepart=0;$pagepart<count($pages);$pagepart++)
	{
		$query="SELECT menu_id FROM webtd
			 WHERE server=$SERVER_ID 
			 AND ver=$_ver AND page_id=$pages[$pagepart] 
			 AND menu_id>0 AND lang='$lang'
			 AND (hidden IS NULL OR hidden=0)
			 ORDER BY level,pri
			";
		$m_res=$adodb->Execute($query);
		//echo "$query =====> "; echo $m_res->RecordCount()."<br>";

		for ($menu=0;$menu<$m_res->RecordCount();$menu++)
		{
			parse_str(ado_ExplodeName($m_res,$menu));

			$query="SELECT ver AS m_ver
				 FROM weblink
				 WHERE menu_id=$menu_id AND server=$SERVER_ID AND ver<=$ver
				 AND lang='$lang'
				 ORDER BY ver DESC LIMIT 1";
			parse_str(ado_query2url($query));
			
			$query="SELECT page_target FROM weblink
				 WHERE menu_id=$menu_id AND server=$SERVER_ID
				 AND ver=$m_ver AND lang='$lang'
				 AND page_target IS NOT NULL
				 ORDER BY pri";
			$p_res=$adodb->Execute($query);

			for ($p=0;$p<$p_res->RecordCount();$p++)
			{
				parse_str(ado_ExplodeName($p_res,$p));
				$pages_to_walk[]=$page_target;
			}		
		}
		$query="SELECT next,more FROM webtd
			 WHERE server=$SERVER_ID 
			 AND ver=$_ver AND page_id=$pages[$pagepart] 
			 AND lang='$lang'
			 ORDER BY level,pri
			";
		$td_res=$adodb->Execute($query);

		for ($td=0;$td<$td_res->RecordCount();$td++)
		{
			$next=""; $more="";
			parse_str(ado_ExplodeName($td_res,$td));
			if (strlen($more)) $pages_to_walk[]=$more;
			if (strlen($next)) $pages_to_walk[]=$next;
		}
		

	}


	for ($i=0;$i<count($pages_to_walk);$i++)
	{
		$pt=$pages_to_walk[$i];
		if (strlen($PAGES_TO_ANAL[$pt])) continue;
		$PAGES_TO_ANAL[$pt]="$page";	

	}

	
	for ($i=0;$i<count($pages_to_walk) && is_array($pages_to_walk);$i++)
	{
		$pt=$pages_to_walk[$i];
		if ($PAGES_TO_ANAL[$pt]!="$page") continue;
		
		$PAGES_TO_ANAL[$pt]="DONE";

		$v=0;
		$query="SELECT title,id,ver AS v FROM webpage
			 WHERE id=$pt AND server=$SERVER_ID AND ver<=$ver
			 AND lang='$lang'
			 ORDER BY ver DESC LIMIT 1";
		parse_str(ado_query2url($query));
		if (!$v) continue;
		$query="UPDATE webpage SET prev=$page 
				WHERE id=$pt AND server=$SERVER_ID AND ver=$v
				AND lang='$lang'";
		if ($adodb->Execute($query)) logquery($query);
		//echo "$query <br>";
		
		$ile_stron++;
		$ile_stron+=gentree($pt);
	}	
	return($ile_stron);	
}

