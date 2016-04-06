<?
		if (!$WEBTD->menu_id) 
		{
			echo "BRAK Menu";
			return;
		}
		
		$query="SELECT page_target FROM weblink WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND page_target>0 AND menu_id=".$WEBTD->menu_id." LIMIT 1";
		parse_str(ado_query2url($query));

		if (!$page_target)
		{
			echo "Brak linków w menu";
			return;
		}
		$sql = "SELECT type AS page_type FROM webpage WHERE id = $page_target
				AND server = $SERVER_ID  AND lang = '$lang' AND ver = $ver";
		parse_str(ado_query2url($sql));


		include_once('include/webver.h');

		$this_page = $page;
		
//		$adodb->BeginTrans();
//		$adodb->debug=1;
		//===== TWORZY STRONE NL =====//
		$referer = $this_page;
		$page = -1;
		$page_id = -1;
		$html = "";
		$title = $obj[nazwa];
		INCLUDE("include/action/DodajStrone.h");
		$strona_nl = $page_id;

		//===== MODYFIKUJE =====//
		$sql = "SELECT * FROM webpage WHERE id = $strona_nl
				AND server = $SERVER_ID  AND lang = '$lang' AND ver = $ver";
		parse_str(ado_query2url($sql));


		$type=$page_type;
		$prev = $referer;
		$page = $strona_nl;
		$page_id = $strona_nl;
		$title = "Newsletter ".date("d-m-Y");
		$html = "";
		$description = "";
		$keywords = "";
		$id = $strona_nl;
		$newid = $strona_nl;
		INCLUDE("include/action/ZapiszStrone.h");

		//dodajemy do menu

		$menu = $WEBTD->menu_id;
		INCLUDE("include/action/DodajLink.h");

		$sql = "SELECT * FROM weblink WHERE menu_id = $menu
				AND server = $SERVER_ID  AND lang = '$lang' AND ver = $ver 
				ORDER BY pri DESC LIMIT 1";
		parse_str(ado_query2url($sql));

		$alt = $title;
		INCLUDE("include/action/ZapiszLink.h");

		$sql = "UPDATE weblink SET page_target = $strona_nl WHERE menu_id = $menu
				AND ver=$ver AND lang='$lang' AND server=$SERVER_ID AND pri=$pri";

		parse_str(ado_query2url($sql));

//		$adodb->debug=0;
//		$adodb->RollbackTrans();
?>
<script>
	location.href = '<? echo kameleon_href("","",$strona_nl); ?>';
</script>