<?
	global $WEBPAGE,$WEBTD;
	if (!is_object($WEBPAGE)) return;
	if ($WEBTD->page_id==$page) return;

	$adodb=$kameleon_adodb;

	$style="display:none";

	function metalowy_pradziadek($adodb,$page,$tree)
	{
		global $ver,$lang,$SERVER_ID;
		$query="SELECT id
			FROM webpage WHERE prev=$page
			AND ver=$ver AND lang='$lang' AND server=$SERVER_ID
			AND (hidden IS NULL OR hidden=0)";
		$result=$adodb->Execute($query);
		for ($i=0;$i<$result->RecordCount();$i++)
		{
			parse_str(ado_explodeName($result,$i));
			
			if (strstr($tree,":$id:")) return $id;
		}	
	}


	function metalowe_dzieci($adodb,$page,$tree,$size=1,$counter=1)
	{
		global $ver,$lang,$SERVER_ID;

		$query="SELECT id,title
			FROM webpage WHERE prev=$page 
			AND ver=$ver AND lang='$lang' AND server=$SERVER_ID
			AND (hidden IS NULL OR hidden=0)
			AND (nositemap IS NULL OR nositemap=0)
			ORDER BY title_short,title";
		$result=$adodb->Execute($query);
		if (!$result) return "";
		if (!$result->RecordCount()) return;
		
		$table_width = 175 - (($counter -1) * 17);
		
		$wynik="<table width=\"".$table_width."\" cellpadding=0 cellspacing=0 border=0 class=\"kattable\">";
		for ($i=0;$i<$result->RecordCount();$i++)
		{
			parse_str(ado_explodeName($result,$i));
			$href=kameleon_href("","",$id);
			$class=strstr($tree,":$id:")?"inTree":"";

			if ($size > $counter || strstr($tree,":$id:"))
				$sub=metalowe_dzieci($adodb,$id,$tree,$size,$counter+1);
			else
				$sub="";
				
			$style_nb="";	
			if ($counter>1) $style_nb=" style=\"border: none;\"";
			
			$wynik.="<tr><td class=\"mark\" $style_nb>::</td><td $style_nb><a href=\"$href\" class=\"$class\">$title</a>$sub</td></tr>";
		}
		$wynik.="</table>";
		return $wynik;
	}

	$pradziadek=metalowy_pradziadek($kameleon_adodb,$WEBTD->page_id,$WEBPAGE->tree."$page:");
	$tree=metalowe_dzieci($kameleon_adodb,$pradziadek,$WEBPAGE->tree."$page:",$size);
	
	$_show=sysmsg("show_menu","system");
	$_hide=sysmsg("hide_menu","system");
	if (!strlen($tree)) return;


	$_div_top = "25";

?>

<div style="position:relative;">

<table class="kat_but" cellspacing=0 cellpadding=0  align="left" width="100%">
<tr>
	<th title="<?echo $path?>" onClick="hideOrShow()" align="left">
	<img src="<?echo $SKLEP_IMAGES;?>/i_mapa.gif" width=16 height=18 border=0 align="absmiddle">
	<span id="katLabel" _show="<?echo $_show; ?>" _hide="<?echo $_hide?>"><?echo $_show?></span>
	</th>
</tr>
</table>

<div class="kategorie" style="position: absolute; display: none; top: -<?if ($KAMELEON_MODE) echo "120"; else echo "<? if (\$AUTH[id]>0) {echo \"105\";} else {echo \"25\";} ?>";?>px; left: 5px;" id="katTree" >
<div style="text-align: right;">
	<a href="javascript:hideOrShow()">
	<img src="<?echo $UIMAGES?>/autoryzacja/i_nie.gif" border=0 style="z-index: 100;" alt="<?echo $_hide?>"></a></div>
	<?
		echo $tree;
	?>
</div>

</div>

	

