<style>
.ex_tbl {
		padding: 0px 0px 0px 0px;
		margin: 0px;
		border-width: 0px;
}
.ex_td {
		padding: 0px 0px 0px 0px;
		font-family: arial, sans-serif; 
		font-size: 11px;
		vertical-align: top;
}
.ex_tr {
		visibility:hidden;
		display:none;
}
</style>

<?
function ojcowie($id,$str,$lang)
{
	global $adodb,$ver,$SERVER_ID;
	global $LOOP_DETECTION;
		
	if ($id==-1) $LOOP_DETECTION="";

	if ($id!=-1)
	{

		$SQL="SELECT prev FROM webpage WHERE id=$id AND ver=$ver AND lang='$lang' AND server=$SERVER_ID LIMIT 1";
		parse_str(ado_query2url($SQL));
		if (!strlen($prev)) return;
		$str.="$id:";
		if ( !$LOOP_DETECTION[$prev] )
		{
			$LOOP_DETECTION[$prev]=1;
			ojcowie($prev,&$str,$lang);
		}
	}	
}


$parents="";

function drzewo($parent,$node,$lng)
{
	global $adodb,$ver,$SERVER_ID,$lang;
	global $parents;
	global $LOOP_DETECTION;
	global $IMG,$TreeFollowLink,$TreeDontShowPageNumber;

	if (!strlen($IMG)) $IMG="img";
	
	if ($parent==-1)
	{
		echo "<script>var TREE_IMG='$IMG';</script>";
		$LOOP_DETECTION="";
		$SQL="SELECT id FROM webpage WHERE prev=-1 AND ver=$ver AND lang='$lng' AND server=$SERVER_ID";
		$result=$adodb->Execute($SQL);
		$rows=$result->RecordCount();
		if (!$rows)
		{
			echo label ("Missing home page!");
			return;
		}
	}

	//wyswietelenia galezi;
	if ($node!=-1 && strlen($node))
	{
		ojcowie($node,&$parents,$lng);
	}

	$SQL =" SELECT id,title,hidden, nositemap";
	$SQL.=" FROM webpage ";
	$SQL.=" WHERE prev=$parent AND ver=$ver AND lang='$lng' AND server=$SERVER_ID";
	$SQL.=" ORDER BY title ";

	$result=$adodb->Execute($SQL);
	if (!$result)
	{
		echo "Blad zapytania.\n";
		return;
	};
	
	$rows=$result->RecordCount();
	$row=0;
	
	for ($i;$i<$rows;$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$title=stripslashes($title);
		$SQL = "SELECT count(*) AS lisc FROM webpage 
				WHERE prev=$id  AND ver=$ver 
				AND lang='$lng' AND server=$SERVER_ID";		

		if ($TreeDontShowPageNumber ) $SQL.=" AND (hidden=0 OR hidden IS NULL) AND (nositemap=0 OR nositemap IS NULL) ";

		parse_str(ado_query2url($SQL));

		$save_id=($lang==$lng)?$id:"$lng:$id";
		$saveLink="javascript:treeSaveId('$save_id')";
		if ($TreeFollowLink) $saveLink=kameleon_href("","",$id);

		$przekreslenie_b="";$przekreslenie_e="";
		if ($hidden)
		{
			$przekreslenie_b="<font style=\"text-decoration:line-through\">";
			$przekreslenie_e="</font>";
		}

		if (!$TreeDontShowPageNumber)
		{
			$title.=" ($id)";
		}
		else
			if ($hidden || $nositemap) continue;

	

		if ($lisc==0)
		{
			if ($i==$rows-1)
			{
				$menu="$IMG/tree_menu_e.gif";
				$background="";
			}
			else
			{
				$menu="$IMG/tree_menu.gif";
				$background=" background='$IMG/tree_linia.gif'";
			}
			$href=kameleon_href('','', $id);
			echo "
			<table class=\"ex_tbl\" cellspacing=\"0\">
				<tr>
					<td class=\"ex_td\" rowspan=2 $background><img src=$menu width=19 height=16 border=0 alt=''></td>
					<td class=\"ex_td\"><img src='$IMG/tree_strona.gif' width=19 height=16 border=0 alt='' align=absmiddle></td>
					<td class=\"ex_td\" id=m_${id}_$lng><a href=\"$saveLink\">${przekreslenie_b}${title}$przekreslenie_e</a></td>
				</tr>
			</table>
			";
		}
		else
		{
			if ($i==$rows-1)
			{
				$plus="$IMG/tree_plus_e.gif";
				$background="";
			}
			else
			{
				$plus="$IMG/tree_plus.gif";
				$background="background=$IMG/tree_linia.gif";
			}

			if ($parent==-1)
				$ikonka="$IMG/tree_serwis.gif";
			else
				$ikonka="$IMG/tree_folder.gif";

			echo "
			<table class=\"ex_tbl\" cellspacing=\"0\">
			<tr>
				<td class=\"ex_td\" onclick=\"show_hide('node_${id}_$lng','td_${id}_$lng','img_${id}_$lng','td2_${id}_$lng')\" id=td_${id}_$lng width=19 valign=top $background><img id=img_${id}_$lng src='$plus' width=19 height=16 border=0 alt=''></td>
				<td class=\"ex_td\" $background2 id='td2_${id}_$lng' width=1%><img src=$ikonka width=19 height=16 border=0 align=absmiddle></td>
				<td class=\"ex_td\" id=m_${id}_$lng><a href=\"$saveLink\">${przekreslenie_b}${title}$przekreslenie_e</a></td>
			</tr>
			<tr id='node_${id}_$lng' class=\"ex_tr\">
				<td class=\"ex_td\" colspan=2>
			";
			if ( !$LOOP_DETECTION[$id] )
			{
				$LOOP_DETECTION[$id]=1;
				drzewo($id,-1,$lng);
			}
			echo "
				</td>
			</tr>
			</table>";
		}
	}

	//wyswietelenie aktywnej galezi;
	if ($parent==-1 && $node!=-1)
	{
		$parents_arr=explode(":",$parents);
		$java="";
		for ($p=0;$p<count($parents_arr);$p++)
		{
			$id=$parents_arr[$p];
			if (strlen($id))
				if ($p==0)
					$java="markTree('m_${id}_$lng');".$java;
				else
					$java="show_hide('node_${id}_$lng','td_${id}_$lng','img_${id}_$lng','td2_${id}_$lng');".$java;
		}
		echo "<script LANGUAGE=\"JScript\">$java</script>";
	}
}
?>
