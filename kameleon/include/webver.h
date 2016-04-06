<?php

function webver_page($page,$action,$sid=0,$dontrecurse=false,$wv_uwagi='')
{


	global $ver,$SERVER_ID,$lang;
	global $kameleon;

	global $adodb;

	//$adodb->debug=1;

	if (!$kameleon->current_server->versions) return;
	$autor=$kameleon->user[username];

	$query="SELECT * FROM webpage WHERE id=$page AND ver=$ver AND server=$SERVER_ID and lang='$lang'";
	if ($sid) $query="SELECT * FROM webpage WHERE sid=$sid";
	$url=ado_query2url($query);

	parse_str($url);
	if (!$sid) return;

	$sql="DELETE FROM webpage WHERE sid=$sid;
			DELETE FROM webtd WHERE page_id=$id AND lang='$lang' AND ver=$ver AND server=$server;";

	

	foreach (explode('&',$url) AS $pair)
	{
		$pair=explode('=',$pair);
		$pair[1]=urldecode($pair[1]);

		if (!strlen($pair[1])) $pair[1]='NULL';
		else $pair[1]="'".str_replace("'","''",stripslashes($pair[1]))."'";

		$inserts[]=$pair[0];
		$values[]=$pair[1];
	}


	$sql.="INSERT INTO webpage (".implode(',',$inserts).") VALUES (".implode(',',$values).");";


	if (!$dontrecurse)
	{
		$query="SELECT sid AS tdsid FROM webtd WHERE page_id=$id AND ver=$ver AND server=$server AND lang='$lang'";

		$res=$adodb->Execute($query);

		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_ExplodeName($res,$i));

			$wv_id=0;

			$query="SELECT max(wv_id) AS wv_id FROM webver WHERE wv_sid=$tdsid AND wv_table='webtd'";
			parse_str(ado_query2url($query));
			if ($wv_id) $wv_webver[]=$wv_id;
			else
			{
				webver_td(0,0,$action,$tdsid,true);
				parse_str(ado_query2url($query));
				if ($wv_id) $wv_webver[]=$wv_id;
			}
		}
	}

	if (is_array($wv_webver)) $wv_webver=implode(':',$wv_webver);


	$ssql=addslashes($sql);
	$wv_noproof=strlen($noproof)?$noproof:'NULL';
	$query="INSERT INTO webver (wv_date,wv_action,wv_table,wv_sid,
								wv_query,
								wv_webver,wv_autor,wv_uwagi,wv_noproof) 
			VALUES (".time().",'$action','webpage',$sid,
								'$ssql',
								'$wv_webver','$autor','".addslashes(stripslashes($wv_uwagi))."',$wv_noproof)";
	
	$adodb->execute($query);
}


function webver_td($page,$pri,$action,$sid=0,$dontrecurse=false)
{
	global $ver,$SERVER_ID,$lang;
	global $kameleon;

	global $adodb;

	//$adodb->debug=1;


	if (!$kameleon->current_server->versions) return;
	$_autor=$kameleon->user[username];


	$query="SELECT * FROM webtd WHERE page_id=$page AND ver=$ver AND server=$SERVER_ID AND lang='$lang' AND pri=$pri";
	if ($sid) $query="SELECT * FROM webtd WHERE sid=$sid";

	$url=ado_query2url($query);

	parse_str($url);
	if (!$sid) return;

	$sql="DELETE FROM webtd WHERE sid=$sid;";

	foreach (explode('&',$url) AS $pair)
	{
		$pair=explode('=',$pair);
		$pair[1]=urldecode($pair[1]);

		if (!strlen($pair[1])) $pair[1]='NULL';
		else $pair[1]="'".str_replace("'","''",stripslashes($pair[1]))."'";

		$inserts[]=$pair[0];
		$values[]=$pair[1];
	}


	$sql.="INSERT INTO webtd (".implode(',',$inserts).") VALUES (".implode(',',$values).");";
	
	$ssql=addslashes($sql);
	$query="INSERT INTO webver (wv_date,wv_action,wv_table,wv_sid,wv_query,wv_autor,wv_uwagi) 
			VALUES (".time().",'$action','webtd',$sid,'$ssql','$_autor','".addslashes(stripslashes($title))."')";
	
	ado_query2url($query);


	if ($page_id>=0 && !$dontrecurse) webver_page($page_id,$action,0,false,$title);
}


function webver_link($menu,$action,$menu_sid=0,$wv_uwagi='')
{
	global $ver,$SERVER_ID,$lang;
	global $kameleon;
	global $adodb;

	if (!$kameleon->current_server->versions) return;
	$_autor=$kameleon->user[username];

	$query="SELECT min(menu_sid) AS menu_sid FROM weblink WHERE menu_id=$menu AND ver=$ver AND server=$SERVER_ID AND lang='$lang'";
	if (!$menu_sid)
	{
		$url=ado_query2url($query);
		parse_str($url);
	}
	if (!$menu_sid) return;

	$sql="DELETE FROM weblink WHERE menu_sid=$menu_sid;\n";

	$query="SELECT * FROM weblink WHERE menu_id=$menu AND ver=$ver AND server=$SERVER_ID AND lang='$lang'";
	if (!$menu) $query="SELECT * FROM weblink WHERE menu_sid=$menu_sid";
	$res=$adodb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		$url=ado_ExplodeName($res,$i);

		$inserts=array();
		$values=array();
		foreach (explode('&',$url) AS $pair)
		{
			$pair=explode('=',$pair);
			$pair[1]=urldecode($pair[1]);

			if (!strlen($pair[1])) $pair[1]='NULL';
			else $pair[1]="'".str_replace("'","''",stripslashes($pair[1]))."'";

			$inserts[]=$pair[0];
			$values[]=$pair[1];
		}
		
		$sql.="INSERT INTO weblink (".implode(',',$inserts).") VALUES (".implode(',',$values).");\n";
	}
	
	$ssql=addslashes($sql);
	$query="INSERT INTO webver (wv_date,wv_action,wv_table,wv_sid,wv_query,wv_autor,wv_uwagi) 
			VALUES (".time().",'$action','weblink',$menu_sid,'$ssql','$_autor','".addslashes(stripslashes($wv_uwagi))."')";
	
	ado_query2url($query);

}


 function webver_resore($id,$action)
 {
	global $adodb;

	//$adodb->debug=1;

	$query="SELECT * FROM webver WHERE wv_id=$id";
	parse_str(ado_query2url($query));




	$wv_query=stripslashes($wv_query);
	$adodb->execute($wv_query);

	//echo "<h2>$id</h2>";
	//echo '<pre>';
	//echo htmlspecialchars($wv_query);
	//echo '</pre>';

	foreach (explode(':',$wv_webver) AS $subver) 
	{
		if ($subver) webver_resore($subver,$action);
	}

	if ($wv_table=='webpage' && $wv_date_ftp>0)
	{

		$sql="UPDATE webpage SET noproof=NULL,unproof_sids=':',unproof_counter=0,unproof_comment='' WHERE sid=$wv_sid";
		$adodb->execute($sql);
	}


	if ($wv_table=='webpage') webver_page(0,$action,$wv_sid);
	if ($wv_table=='webtd') webver_td(0,0,$action,$wv_sid,true);
	if ($wv_table=='weblink') webver_link(0,$action,$wv_sid);

 }

 function webver_ftpPageResore($sid,$action)
 {
	$query="SELECT wv_id FROM webver WHERE wv_sid=$sid AND wv_table='webpage' AND wv_date_ftp>0 ORDER BY wv_id DESC LIMIT 1";
	parse_str(ado_query2url($query));

	if ($wv_id) webver_resore($wv_id,$action);
	else return label('No published page found in archive');

 }


?>