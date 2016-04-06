<?
	require_once(dirname(__FILE__).'/acl_page.h');

	$resource_name="webpage";

	$query="SELECT id AS page,ver AS v,hidden,sid
			FROM webpage 
			WHERE server=$SERVER_ID AND lang='$lang' 
			AND ver<=$ver
			ORDER BY id,ver DESC";

	$res=$adodb->Execute($query);

	$acl_todo="";
	$acl_pages="";
	$more_todo=1;
	$phase=0;
	while ($more_todo)
	{
		$acl_pageanalysed="";
		$more_todo=0;
		$phase++;

		for ($i=0;$i<$res->RecordCount();$i++)
		{

			if ($_kameleon_debug) 
			   echo "\r<br>                 \rPhase A$phase, rec. ".($i+1)."/".$res->RecordCount();

			parse_str(ado_ExplodeName($res,$i));
			if ($hidden) continue;
			if ($acl_pageanalysed[$page]) continue;
			$acl_pageanalysed[$page]=$v;
			if (strlen($acl_todo[$page])) continue;

			$acl_sids[$page]=$sid;
			$tree=kameleon_tree($page);

			foreach(explode(":",$tree) AS $parent)
				if (strlen($parent)) 
					if ( strlen($acl_todo[$parent]))
					{
						$acl_todo[$page]=$tree;
						$more_todo=1;
					}
			if (strlen($acl_todo[$page])) continue;

			$rights_count=$acl_pages[$page]+0;


			if (!$rights_count) 
			{
				$query="SELECT count(*) AS rights_count FROM kameleon_acl
						WHERE ka_server=$SERVER_ID 
						AND ka_oid=$sid AND ka_resource_name='$resource_name'";
				parse_str(ado_query2url($query));
			}

			if ($rights_count) 
			{
				$acl_pages[$page]=$rights_count;
				$acl_todo[$page]=$tree;
				$more_todo=1;
			}
		}
		if ($_kameleon_debug) echo "; ".count($acl_todo)." pages.";
		if ($_kameleon_debug) echo "\n<br>\r     \r";
	}



	$acl_pages_protected=$acl_todo;
	$acl="";

	$phase=0;

	$last_todo_count=0;
	
	while (is_array($acl_todo) && count($acl_todo))
	{
		$phase++;
		if ($_kameleon_debug) echo "Phase B$phase - todo (".count($acl_todo)."): ";

		if ($last_todo_count==count($acl_todo)) sleep(1);
		$last_todo_count=count($acl_todo);

		foreach($acl_todo AS $page=>$tree)
		{
			if ($_kameleon_debug) echo "$page;";
			$acl_parent=array();
			$acl_parent_required=0;
			foreach(explode(":",$tree) AS $parent)
				if (strlen($parent)) 
					if ( strlen($acl_pages_protected[$parent]))
					{
						if (is_array($acl[$parent])) $acl_parent=$acl[$parent];
						else $acl_parent_required=1;
					}

			if ($acl_parent_required) continue;

			$_acl=$acl_parent;
			
			$acl_this=acl_users_rights($acl_sids[$page],$resource_name);

			foreach ($acl_this AS $user=>$passwd) $_acl[$user]=$passwd; 
			$acl[$page]=$_acl;

			unset($acl_todo[$page]);
		}
		if ($_kameleon_debug) echo "\n";
	}

	if ($_kameleon_debug && is_array($acl) ) echo "Count=".count($acl)."\n";

	
