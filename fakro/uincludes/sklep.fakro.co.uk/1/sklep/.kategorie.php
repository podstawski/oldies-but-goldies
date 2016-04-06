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

		$wynik="<table cellpadding=0 cellspacing=0 border=0 class=\"kattable\">";
		for ($i=0;$i<$result->RecordCount();$i++)
		{
			parse_str(ado_explodeName($result,$i));
			$href=kameleon_href("","",$id);
			$class=strstr($tree,":$id:")?"inTree":"";

			if ($size > $counter || strstr($tree,":$id:"))
				$sub=metalowe_dzieci($adodb,$id,$tree,$size,$counter+1);
			else
				$sub="";

			$wynik.="<tr><td class=\"mark\">::</td><td nowrap><a href=\"$href\" class=\"$class\">$title</a>$sub</td></tr>";
		}
		$wynik.="</table>";
		return $wynik;
	}

	$pradziadek=metalowy_pradziadek($kameleon_adodb,$WEBTD->page_id,$WEBPAGE->tree."$page:");
	$tree=metalowe_dzieci($kameleon_adodb,$pradziadek,$WEBPAGE->tree."$page:",$size);
	
	$_show=sysmsg("show_menu","system");
	$_hide=sysmsg("hide_menu","system");
	if (!strlen($tree)) return;


?>


<div style="position:relative;">

<table class="kategorie" cellspacing=0 cellpadding=0  align="left" style="border: 0px;" width="100%">
<tr>
	<th title="<?echo $path?>" onClick="hideOrShow()" align="left">
	<img src="<?echo $SKLEP_IMAGES;?>/i_mapa.gif" width=16 height=18 border=0 align="absmiddle">
	<span id="katLabel" _show="<?echo $_show; ?>" _hide="<?echo $_hide?>"><?echo $_show?></span>
	</th>
</tr>
</table>

<div class="kategorie" style="width: 225px; height: 250px; overflow-y: auto; position:absolute; top: -100px; left: 5px; display: none; background-color: silver;" id="katTree" >
	<?
		echo $tree;
	?>
</div>


</div>

	

