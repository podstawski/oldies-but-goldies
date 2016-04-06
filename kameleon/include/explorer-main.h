<?


if (file_exists('const.php')) include_once('const.php'); else include_once('const.h');



function ojcowie($id,&$str,$lang)
{
	global $adodb,$ver,$SERVER_ID;
	global $LOOP_DETECTION;
		
	if ($id==-1) $LOOP_DETECTION="";

	if ($id!=-1)
	{

		$SQL="SELECT prev FROM webpage WHERE id=$id AND ver=$ver AND lang='$lang' AND server=$SERVER_ID LIMIT 1";
		parse_str(ado_query2url($SQL));
		if (!strlen($prev)) return;
		$str="$id:".$str;
		if ( !$LOOP_DETECTION[$prev] )
		{
			$LOOP_DETECTION[$prev]=1;
			ojcowie($prev,$str,$lang);
		}
	}	
}
$parents="";



function drzewo($parent,$node,$lng)
{
	global $adodb,$ver,$SERVER_ID,$lang, $page;
	global $parents;
	global $LOOP_DETECTION;
	global $IMG,$TreeFollowLink,$TreeDontShowPageNumber;

	if (!strlen($IMG)) $IMG="img";
	
	$IMG=$_REQUEST["path"];
	
	if ($parent==-1)
	{
		//echo "var TREE_IMG='$IMG';\n";
		$LOOP_DETECTION="";
		$SQL="SELECT id FROM webpage WHERE prev=-1 AND ver=$ver AND lang='$lng' AND server=$SERVER_ID";
		$result=$adodb->Execute($SQL);
		$rows=$result->RecordCount();
		if (!$rows)
		{
			//echo "document.write('".label("Missing home page!")."');\n";
			return;
		}
	}

	//wyswietelenie galezi;
	if ($node!=-1 && strlen($node))
	{
		ojcowie($node,$parents,$lng);
	}

	$SQL =" SELECT id,title,title_short,hidden, nositemap,sid";
	$SQL.=" FROM webpage ";
	$SQL.=" WHERE prev=$parent AND ver=$ver AND lang='$lng' AND server=$SERVER_ID";
//	$SQL.=" ORDER BY id ";
	$SQL.=" ORDER BY title_short,title,id ";

	$result=$adodb->Execute($SQL);
	if (!$result)
	{
		//echo "B³ad zapytania.\n";
		return;
	};
	
	$rows=$result->RecordCount();
	$row=0;
	$firstowe=$_REQUEST["firstis"];
	$_REQUEST["firstis"]=0;
	
	for ($i;$i<$rows;$i++)
	{  
		parse_str(ado_ExplodeName($result,$i));

    

		if (!empty($title_short) && $title_short != 'Null') $title=$title_short;
		$title=stripslashes($title);
		$title=ereg_replace("'","\'",$title);
		$title=str_replace('"','&quot;',$title);
		$orgtitle=$title;
		$hidden+=0;
		$nositemap+=0;

		$SQL = "SELECT count(*) AS lisc FROM webpage 
				WHERE prev=$id  AND ver=$ver 
				AND lang='$lng' AND server=$SERVER_ID";		

		if ($TreeDontShowPageNumber ) $SQL.=" AND (hidden=0 OR hidden IS NULL) AND (nositemap=0 OR nositemap IS NULL) ";

		parse_str(ado_query2url($SQL));

		$save_id=($lang==$lng)?$id:"$lng:$id";
		$saveLink="javascript:wstaw_link('".$save_id."','".$orgtitle."')"; // ckeditor
		if ($TreeFollowLink) $saveLink=kameleon_href("","",$id);

		



		if (!$TreeDontShowPageNumber)
		{
			$title.=" ($id)";
		}
		else
			if ($hidden || $nositemap) continue;
		
		$nomap="";
		if ($nositemap) $nomap=" nomap";
		
		if ($hidden)
		{
		  $title="<span class='notvisible".$nomap."'>".$title."</span>";
		}
		else
		{
		  $title="<span class='visible".$nomap."'>".$title."</span>";
			$_color="black";
			$_std_and="server=$SERVER_ID AND lang='$lng' AND ver=$ver";
		}

		//rekurencja po ojcach aby wyswietlic aktywna galaz drzewa
		$parents_arr=explode(":",$parents);
		$rekurencja=0;
		for ($p=0;$p<count($parents_arr);$p++)
		{
			$pre=$parents_arr[$p];
			if (strlen($pre) && $pre==$id)
				$rekurencja=1;
				
		}
		//usuniêcie ostatniego
		$parents="";
		for ($p=0;$p<count($parents_arr)-1;$p++)
		{
			$pre=$parents_arr[$p];
			$parents.="$pre:";
		}


		$classes="";
	

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
				$background=" background=\"$IMG/tree_linia.gif\"";
			}
			$href=kameleon_href('','', $id);
			$p="";
			if ($parent!=-1 && $firstowe==0) $p=" parent_id=\"".$lng."_".$parent."\" ";
      
			echo '{ "data" : { "title" : "'.stripslashes($title).'", "href" : "'.$saveLink.'", "icon" : "'.$IMG.'/tree_strona.gif" }, "attributes" : { "id" : "'.$lng."_".$id.'", "link" : "'.$saveLink.'", '.$classes.' "title" : "'.stripslashes($orgtitle).'", "page_id" : "'.$id.'", "sid" : "'.$sid.'"} }';
			if ($i!=$rows-1) echo ",";
		}
		else
		{

			if ($parent==-1) $ikonka=", \"icon\" : \"".$IMG."/tree_serwis.gif\" "; else $ikonka="";


			$p="";
			if ($parent!=-1 && $firstowe==0) $p=" parent_id=\"".$lng."_".$parent."\" ";

			if ($rekurencja) $state = "open"; else $state="closed";


			echo '{ "data" : { "title" : "'.stripslashes($title).'", "href" : "'.$saveLink.'" '.$ikonka.' }, "state" : "'.$state.'",  "attributes" : { "id" : "'.$lng."_".$id.'", "link" : "'.$saveLink.'", '.$classes.' "title" : "'.stripslashes($orgtitle).'", "page_id" : "'.$id.'", "sid" : "'.$sid.'"} ';

			if ($rekurencja)
			{
				echo ', "children" : [';
				drzewo($id,-1,$lng);
				echo "]";
			}
			
			echo ' }';
			if ($i!=$rows-1) echo ",";

		}
	}
}

header('Content-type: text/x-json');
echo "[";
$node=$_GET["node"];
$parent=$_GET["parent"];
$p=explode("_",$parent);
$parent=$p[1];
if (!strlen($node))	$node=-1;
if (!strlen($parent))	$parent=-1;
$_REQUEST["firstis"]=1;
drzewo($parent,$node,$lng);
echo "]";
