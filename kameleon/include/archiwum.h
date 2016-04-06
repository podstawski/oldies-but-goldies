<html>
<head>
    <title>KAMELEON: <?echo label("Version archive");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/tdedit.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">


</head>
<body>

<?
	//print_r($kameleon);

	switch ($wv_table)
	{
		case 'page':
			$query="SELECT id FROM webpage WHERE sid=$wv_sid";
			parse_str(ado_query2url($query));
			//$MAY=checkRight($id,$PROOF_RIGHTS) && checkRights($id,$PAGE_RIGHTS) ;
			$MAY=$kameleon->checkRight('proof','page',$id) && $kameleon->checkRight('write','page',$id) ;
			break;

		case 'td':
			$query="SELECT page_id,sid FROM webid WHERE sid=$wv_sid";
			parse_str(ado_query2url($query));
			$MAY=$kameleon->checkRight('proof','page',$page_id) && $kameleon->checkRight('write','box',$sid) ;
			//$MAY=checkRights($page_id,$PROOF_RIGHTS) && checkRights($page_id,$PAGE_RIGHTS) ;
			break;

		case 'link':

			//$MAY=checkRights($menu,$MENU_RIGHTS) ;
			$MAY=$kameleon->checkRight('write','box',$menu);
			break;
	
	
	}
	$WFTABLE=$wf_table;
?>

	

<?
		include("include/navigation.h");
		
?>

<?if ($MAY) { ?>
<table cellspacing="1" cellpadding="0" class="tabelka">
<tr>
	<th><? echo label("Date")?></th>
	<th><? echo label("Author")?></th>
	<th><? echo label("Action responsible")?></th>
	<th><? echo label("FTP")?></th>
	<th><? echo label("Restore")?></th>
</tr>

<?
	$no_admin_where='';
	$no_admin_limit='';
	if ($wv_table=='page' && !$ADMIN_RIGHTS )
	{
		$no_admin_limit='LIMIT 5';
		$no_admin_where=' AND wv_date_ftp>0';

	}
	if ($ADMIN_RIGHTS )
	{
		$no_admin_limit='LIMIT 100';
	}

	$query="SELECT wv_id,wv_action,wv_date,wv_webver,wv_autor,pwd1.fullname,wv_table ,wv_date_ftp,wv_autor_ftp, pwd2.fullname AS fullname_ftp,wv_uwagi
			FROM webver LEFT JOIN passwd pwd1 ON pwd1.username=wv_autor LEFT JOIN passwd pwd2 ON pwd2.username=wv_autor_ftp
			WHERE wv_sid=$wv_sid AND wv_table='web$wv_table' $no_admin_where
			ORDER BY wv_date DESC $no_admin_limit";

	$res=$adodb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		
		$ser_bgcolor="bgcolor=\"#D0D0D0\"";
		if (($i&1)==0) $ser_bgcolor="bgcolor=\"#E0E0E0\"";

		echo " <tr class=\"line_".($i % 2)."\">\n";
		echo "  <td>".date('d-m-Y, H:i',$wv_date);
		echo "  <td>";

		echo $kameleon->userFullName($wv_autor);
		
		$action_label=label("action:$wv_action");
		if ($action_label=="action:$wv_action") $action_label=$wv_action;
		echo "  <td>$action_label";

		if (strlen($wv_uwagi)) echo " (".$wv_uwagi.")";
		
		
		echo "	<td>";
		if (!strlen($wv_autor_ftp))
			echo '&nbsp;';
		else
		{
			echo $kameleon->userFullName($wv_autor_ftp);
			echo date(', d-m-Y, H:i',$wv_date_ftp);
		}
		
		
		$label_restore=label('Restore');


		$href=($wv_table=='weblink')?"menus.php?menu=$menu":"index.php?page=$referpage";

		if ($i)
			echo "<td>
								<a href='$href&wv_id=$wv_id&action=Odtworz'><img 
								align=\"absmiddle\" class=k_imgbutton border=0 src=\"img/i_export_n.gif\" 
								onmouseover=\"this.src='img/i_export_a.gif'\" 
								onmouseout=\"this.src='img/i_export_n.gif'\" 
								alt=\"$label_restore\"></a>	
				";
		else
			echo "<td>&nbsp;</td>";


	}
		

?>
</table></td></tr>
<? } ?>

</body>
</html>
