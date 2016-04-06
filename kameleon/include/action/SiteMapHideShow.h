<?
	if (!function_exists('sitemapize'))
	{
		function sitemapize($page,$new_sitemap_flag,$alltree)
		{
			global $SERVER_ID,$lang,$ver,$adodb;
	
			$query="UPDATE webpage SET nositemap=$new_sitemap_flag
					WHERE id=$page AND server=$SERVER_ID 
					AND ver=$ver AND lang='$lang'";
			
			if ($adodb->Execute($query)) logquery($query);
			else return;
	
	
			if (!$alltree) return;
	
			$query="SELECT id AS page FROM webpage
					WHERE prev=$page AND server=$SERVER_ID 
					AND ver=$ver AND lang='$lang'";
	
			$res=$adodb->Execute($query);
	
			for ($i=0;$i<$res->RecordCount();$i++ )
			{
				parse_str(ado_ExplodeName($res,$i));
				sitemapize($page,$new_sitemap_flag,$alltree);
			}
	
	
		}

	}


	$query="SELECT nositemap FROM webpage WHERE id=$page AND server=$SERVER_ID AND ver=$ver AND lang='$lang' ";
	parse_str(ado_query2url($query));

	$new_sitemap_flag=$nositemap?0:1;

	sitemapize($page,$new_sitemap_flag,$alltree);

