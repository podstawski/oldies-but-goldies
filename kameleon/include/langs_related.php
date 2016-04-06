<?php

	$langs_related=unserialize($WEBPAGE->langs_related);
	$tdks=kameleon_td($page,$ver,$lang);
	$now=time();

	foreach ($CONST_LANGS_RELATED[$lang] AS $lang_related)
	{
		$sql="SELECT count(*) AS page_related FROM webpage WHERE ver=$ver AND server=$SERVER_ID AND lang='$lang_related' AND id=$page";
		parse_str(ado_query2url($sql));


		if (!$page_related)
		{
			$sql=kameleon_copy_query('webpage', 
				array('hidden'=>1,'file_name'=>'','langs_related'=>'','lang'=>"'$lang_related'"),
				array('lang'=>"'$lang'",'server'=>$SERVER_ID,'id'=>$page,'ver'=>$ver)
			);
			$adodb->execute($sql);
			logquery($sql);
		}

		if (!is_array($langs_related[$lang_related]))
		{
			$tdks_related=kameleon_td($page,$ver,$lang_related);
			$langs_related[$lang_related]=array();
			
			foreach ($tdks AS $i=>$td)
			{
				$tdsid=$td->sid;
				if ($tdks_related[$i]->sid) $langs_related[$lang_related][$td->sid]=array($tdks_related[$i]->sid);
				
			}

		}
		else
		{
			foreach ($tdks AS $td)
			{
				$related_td=new stdClass;
				$related_td->nd_update=0;
				$related_sid=@end($langs_related[$lang_related][$td->sid]);
				if ($related_sid)
				{
					$related_td=kameleon_td_sid($related_sid);
				}
				if ($td->nd_update > $related_td->nd_update)
				{
					if ($related_td->hidden)
					{
						$sql="UPDATE webtd SET plain='".addslashes($td->plain)."',
							title='".addslashes($td->title)."',
							nd_update=$now
							WHERE sid=$related_sid";
						if ($adodb->execute($sql)) logquery($sql);
					}
					else
					{
						$maxpri=0;
						$maxsid=0;
						$query="SELECT max(pri) AS maxpri FROM webtd WHERE server=$SERVER_ID AND ver=$ver AND page_id=$page AND lang='$lang_related'";
						parse_str(query2url($query));
						$maxpri++;
						$sql=kameleon_copy_query('webtd', 
							array('pri'=>$maxpri,'hidden'=>1,'lang'=>"'$lang_related'",'menu_id'=>''),
							array('sid'=>$td->sid)
						);
						if ($adodb->execute($sql)) logquery($sql);
						
						$query="SELECT max(sid) AS maxsid FROM webtd WHERE server=$SERVER_ID AND ver=$ver AND page_id=$page AND lang='$lang_related'";
						parse_str(query2url($query));
						if ($maxsid)
						{
							$langs_related[$lang_related][$td->sid][]=$maxsid;
						}

					}

					
					
				}
			}
		}


	}
	$langs_related_new=serialize($langs_related);
	$sql="UPDATE webpage SET langs_related='$langs_related_new' WHERE sid=".$WEBPAGE->sid;
	if ($WEBPAGE->langs_related!=$langs_related_new)
	{
		$adodb->execute($sql);
	}