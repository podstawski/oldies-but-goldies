<?

	if (!function_exists(exploreKameleonTree))
	{
		function exploreKameleonTree($SERVER_ID,$page,$lang,$ver)
		{
			global $adodb;

			$query="SELECT prev,sid FROM webpage WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND id=$page";
			parse_str(ado_query2url($query));

			$wynik[]="$page:$prev:$sid";

			$query="SELECT id FROM webpage WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND prev=$page";
			$res=$adodb->Execute($query);
			for ($i=0;$i<$res->RecordCount();$i++)
			{
				parse_str(ado_ExplodeName($res,$i));
				$wynik=array_merge($wynik,exploreKameleonTree($SERVER_ID,$id,$lang,$ver));
			}
			return $wynik;
		}
	}


	if (!$_REQUEST['paste']) 
	{
		$error=label("Nothing found in kameleon cliboard");
		return;
	}

	$sql="SELECT server AS ct0,lang AS ct1,ver AS ct2 ,id AS ct3  FROM webpage WHERE sid=".$_REQUEST['paste'];
	parse_str(ado_query2url($sql));


	if (!$ct0)
	{
		$error=label("Nothing found in kameleon cliboard");
		return;
	}

	$clibpage="$ct0:$ct1:$ct2:$ct3";



	$cp=explode(":",$clibpage);
	if (4!=count($cp)) $error=label("Nothing found in kameleon cliboard");
	if (strlen($error)) return;
	$src=$cp;



	$wklej_tree_zakres=exploreKameleonTree($src[0],$src[3],$src[1],$src[2]);
	if (!is_array($wklej_tree_zakres)) return;

	//echo '<pre>';
	//print_r($wklej_tree_zakres);
	//echo '</pre>';
	
	if (!$kameleon->checkRight('write','page',$page))
	{
		$error=$norights;
		return;
	}	
	

	push($clibpage);
	$clibpage_base=$src[0].':'.$src[1].':'.$src[2].':XXX';

	foreach ($wklej_tree_zakres AS $wklej_tree_i )
	{
		$para=explode(':',$wklej_tree_i);
		if (strlen($PAGE_ID_TRANSLATION[$para[1]])) $referer=$PAGE_ID_TRANSLATION[$para[1]];
		$clibpage=str_replace('XXX',$para[0],$clibpage_base);


		$c=0;
		$query="SELECT count(*) AS c FROM webpage WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND id=".$para[0];
		parse_str(query2url($query));

		
		$page=$c?-1:$para[0];
		
		//echo "wklej $page<br>";
		$_REQUEST['paste']=$para[2];
		include('include/action/Wklej_page.h');
	}

	$clibpage=pop();
