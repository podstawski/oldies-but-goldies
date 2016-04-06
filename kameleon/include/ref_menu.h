<?
if (strlen($ref_menu))
{	
	$rm=explode(":",$ref_menu);
	$ref_menu=$rm[0];
	$ref_pri=$rm[1];
	$query="SELECT page_target FROM weblink 
		 WHERE ver=$ver AND server=$SERVER_ID
		 AND lang='$lang'AND menu_id=$ref_menu
		 AND page_target IS NOT NULL
		 ORDER BY pri DESC LIMIT 1";
	parse_str(ado_query2url($query));


	$query="SELECT prev AS referer FROM webpage
		 WHERE ver=$ver AND server=$SERVER_ID
		 AND lang='$lang'AND id=$page_target";
	if (strlen($page_target)) parse_str(ado_query2url($query));

	if ($ref_pri) 
	{
			$ref_menu_query.="UPDATE weblink SET page_target=$page_id
					 WHERE ver=$ver AND server=$SERVER_ID
					 AND lang='$lang' AND menu_id=$ref_menu
					 AND pri=$ref_pri;";

			$query="SELECT alt FROM weblink
					 WHERE ver=$ver AND server=$SERVER_ID
					 AND lang='$lang' AND menu_id=$ref_menu
					 AND pri=$ref_pri;";
			parse_str(ado_query2url($query));

			$alt=addslashes(stripslashes($alt));
			if (strlen($alt))
			   $ref_menu_query.="\nUPDATE webpage SET title='$alt'
					 WHERE ver=$ver AND server=$SERVER_ID
					 AND lang='$lang' AND id=$page_id ;";
				

	}


}

?>
