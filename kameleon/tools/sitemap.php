<?php
	$sitemap='';
	
	if (!strlen($http_url)) return;
	$last=$http_url[strlen($http_url)-1];
	$slash=$last=='/'?'':'/';
	
	$query="SELECT id AS page,file_name AS fn,ver AS v,hidden,tree,nositemap
			FROM webpage 
			WHERE server=$SERVER_ID AND lang='$lang' $_exclude_vers
			AND ver<=$ver
			ORDER BY id,ver DESC";

	$res=$adodb->Execute($query);


	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		if ( $page_sitemaped[$page] ) continue;
		$page_sitemaped[$page]=1;
		
		if ($hidden || $nositemap) continue;
		
		$treearraycount=count(explode(':',$tree));
		
		if (strlen($fn)  )
		{
			$page_path="$PATH_PAGES_PREFIX$fn";	

		}
		else
		{
			$page_path="$PAGES/$page.$file_ext";
		}
				
		if ($treearraycount<=2) $pri=1;
		if ($page>0 && $treearraycount<2) $pri=0.2;
		if ($treearraycount>2) $pri=0.8;
		if ($treearraycount>3) $pri=0.7;
		if ($treearraycount>4) $pri=0.6;
		if ($treearraycount>5) $pri=0.5;
		if ($treearraycount>6) $pri=0.4;
		
		
		if (substr($page_path,-9)=='index.php') $page_path=substr($page_path,0,-9);
		$sitemap.="<url><loc>$http_url$slash$page_path</loc><priority>".sprintf("%0.4f",$pri)."</priority></url>";
	}
	
	if (strlen($sitemap)) $sitemap="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">$sitemap</urlset>";
