<?php
$datas = array();
$acl = true; 
switch ($_GET["action"])
{
	
// STRONA
	// WIDOCZNOŚĆ STRONY
	case 'page_visible':
		$datas['status']=0;
		if (strlen($_GET["pagesid"]))
		{
			// wyciągnięcie aktualnej widoczności linka z bazy
			$query="SELECT hidden,id FROM webpage WHERE server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND sid=".(int)$_GET["pagesid"];
			parse_str(ado_query2url($query));
			
			if (!$kameleon->checkRight('write','page',$id)) { $acl=false; break; } // ACL

			$hidden = $hidden == 1 ? 0 : 1;
			
			$adodb->Execute("UPDATE webpage SET hidden=".$hidden." WHERE server=".$SERVER_ID." AND ver=".$ver." AND lang='".$lang."' AND sid=".(int)$_GET["pagesid"]);
			$datas['status']=1;
			$datas['hidden']=$hidden;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
	// WIDOCZNOŚĆ MAPY STRONY
	case 'page_sitemap_visible':
		$datas['status']=0;
		if (strlen($_GET["pagesid"]))
		{
			// wyciągnięcie aktualnej widoczności linka z bazy
			$query="SELECT nositemap,id FROM webpage WHERE server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND sid=".(int)$_GET["pagesid"];
			parse_str(ado_query2url($query));

			if (!$kameleon->checkRight('write','page',$id)) { $acl=false; break; } // ACL
			
			$nositemap = $nositemap == 1 ? 0 : 1;
			
			$adodb->Execute("UPDATE webpage SET nositemap=".$nositemap." WHERE server=".$SERVER_ID." AND ver=".$ver." AND lang='".$lang."' AND sid=".(int)$_GET["pagesid"]);
			$datas['status']=1;
			$datas['nositemap']=$nositemap;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
// BOOKMARK
	case 'bookmark_addremove':
		$datas['status']=0;
		if (strlen($_GET["page"]))
		{
			$query="SELECT wf_sid FROM webfav WHERE wf_user='".$USERNAME."' AND wf_server=$SERVER_ID AND wf_lang='$lang' AND wf_page_id=".$_GET["page"];
			parse_str(ado_query2url($query));

			$query= $wf_sid ? "DELETE FROM webfav WHERE wf_sid=$wf_sid" : "INSERT INTO webfav (wf_user,wf_server,wf_page_id,wf_lang) VALUES ('".$USERNAME."',$SERVER_ID,".$_GET["page"].",'$lang')";
	
			if ($adodb->execute($query))
			{
				logquery($query);
			}
			$datas['status']=1;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
		
	
// MODUŁ
	// ZMIANA POZYCJI MODUŁU
	case 'module_drag':
		if (!$kameleon->checkRight('write','box',$_GET['tdsid'])) { $acl=false; break; } // ACL
		if (strlen($_GET['kolejka']) && strlen($_GET['level']) && strlen($_GET['tdsid'])) 
		{
    		$kolejka=$_GET["kolejka"];
    		$levelek=$_GET["level"];
    		$tdsid=explode(",",$_GET["tdsid"]);
    		$sidek=$tdsid[0];
    
  			$STD_WHERE="lang='$lang' AND server=$SERVER_ID";
  			if (isset($ver)) $STD_WHERE.=" AND ver=$ver";
  			if (strlen($pole)) $STD_WHERE.=" AND page_id='".$_GET["page_id"]."' ";
  
			$kolej = explode(";",substr($kolejka,0,-1));
			$sidy=array();
			$primy = array();
			
			for ($i=0;$i<sizeof($kolej);$i++)
			{
				$ktmp = explode(",",$kolej[$i]);
				$sidy[$i] = $ktmp[0];
				$primy[$i] = $ktmp[1];
			}
			sort($primy);
			
			for ($i=0;$i<sizeof($kolej);$i++)
			{
				if ($sidy[$i]==$sidek) $supd=", level=".$levelek." "; else $supd="";
				$adodb->Execute("UPDATE webtd SET pri=".$primy[$i].$supd." WHERE sid=".$sidy[$i]." AND $STD_WHERE");
			}
		}
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
	// USUNIĘCIE MODUŁU
	case 'module_delete':
		$page_id = $_GET["page_id"].":".$_GET["tdsid"];
		include "action/UsunTD.h";
		
		if (!strlen($error)) $datas['status']=1;
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
	// ZMIANA WIDOCZNOŚCI MODUŁU
	case 'module_visible':
		if (!$kameleon->checkRight('write','box',$_GET['tdsid'])) { $acl=false; break; } // ACL
		$datas['status']=0;
		if (strlen($_GET["tdsid"]))
		{
			// wyciągnięcie aktualnej widoczności linka z bazy
			$query="SELECT hidden FROM webtd WHERE server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND sid=".(int)$_GET["tdsid"];
			parse_str(ado_query2url($query));

			$hidden = $hidden == 1 ? 0 : 1;
			
			$adodb->Execute("UPDATE webtd SET hidden=".$hidden." WHERE server=".$SERVER_ID." AND ver=".$ver." AND lang='".$lang."' AND sid=".(int)$_GET["tdsid"]);
			$datas['status']=1;
			$datas['hidden']=$hidden;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break; 	
		
	
// CONTENT
	// TYTUŁ
	case 'contenttitle_save':
		if (!$kameleon->checkRight('write','box',$_GET['tdsid'])) { $acl=false; break; } // ACL
		
		$query="SELECT plain,pri,page_id FROM WEBTD WHERE server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND sid=".(int)$_GET["tdsid"]." AND page_id=".(int)$_GET["page_id"]." LIMIT 1";
		parse_str(ado_query2url($query));
		
		$title =  $_GET["txt"];
		include_once('include/search.h'); // potrzebne do poniższej funkcji
		$nohtml=polishtolower(wordsFromHtml(stripslashes($title." ".$plain)));
		$query="UPDATE webtd SET title='".$title."', nohtml='".$nohtml."', nd_update=".time().",autor_update='".$PHP_AUTH_USER."' WHERE ver=".$ver." AND lang='".$lang."' AND sid=".$tdsid." AND server=".$SERVER_ID." AND page_id=".$page_id;
		$adodb->Execute($query);
		webver_td($page_id,$pri,"ZapiszTD");
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
	// LINK
	case 'contentalt_save':
		$where=" server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND sid=".(int)$_GET["linksid"];
		$query="SELECT alt, alt_title, menu_id FROM weblink WHERE ".$where." LIMIT 1";
		parse_str(ado_query2url($query));
		
		if (!$kameleon->checkRight('write','menu',$menu_id)) { $acl=false; break; } // ACL
		
		$title =  $_GET["txt"];
		$query="UPDATE weblink SET alt='".$title."', nd_update=".time()." WHERE ".$where;
		$adodb->Execute($query);
		webver_link($menu_id,"ZapiszLink",0,$alt." ".$alt_title);
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
	
// LINKI

	// ZMIANA POZYCJI LINKU w MENU
	case 'link_pos':
		if (!$kameleon->checkRight('write','menu',$_GET['menu_id'])) { $acl=false; break; } // ACL
		
		$datas['status']=0;
		if (strlen($_GET['kolejka']) && strlen($_GET['linksid'])) 
		{
    		$kolejka=$_GET["kolejka"];
    		$sidek=$_GET["linksid"];
    
  			$STD_WHERE="lang='$lang' AND server=$SERVER_ID";
  			if (isset($ver)) $STD_WHERE.=" AND ver=$ver";
  			$STD_WHERE.=" AND menu_id=".(int)$_GET["menu_id"]." ";
  
			$kolej = explode(";",substr($kolejka,0,-1));
			
			$datas['upd']=array();
			$i=0;
			if (sizeof($kolej)>0)
			{
				foreach ($kolej as $key)
				{
					$adodb->Execute("UPDATE weblink SET pri=".$i." WHERE sid=".$key." AND $STD_WHERE");
					$datas['upd'][]="UPDATE weblink SET pri=".$i." WHERE sid=".$key." AND $STD_WHERE";
					$i+=1;
				}
			}
			$datas['status']=1;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
	
	// ZMIANA TARGETU DLA LINKU
	case 'link_target':
		$datas['status']=0;
		if (strlen($_GET["linksid"]))
		{
			// wyciągnięcie opisu linka z bazy
			$query="SELECT alt FROM weblink	WHERE server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND menu_id=".(int)$_GET["menu_id"]." AND sid=".(int)$_GET["linksid"];
			parse_str(ado_query2url($query));

			if (!strlen($alt))
			{
				// jeżeli brak opisu to ma zaciągnąć opis z tytułu strony
				$query="SELECT title AS alt FROM webpage WHERE id=".$_GET["target"]." AND server=".$SERVER_ID."	AND ver=".$ver." AND lang='".$lang."'";
				parse_str(ado_query2url($query));
			}
			$alt=addslashes(stripslashes($alt));
			
			$target = $_GET["target"];
			$lang_target = "NULL";
			if (strstr($target,":"))
			{
				$tmp = explode(":",$target);
				$target = $tmp[1];
				$lang_target = "'".$tmp[0]."'";
			}
			if (strlen($target)==0) $target="NULL";
			elseif (strlen($target)>0 && strlen($lang_target)==0) $lang_target = $lang;
			if (!$kameleon->checkRight('write','menu',$_GET['menu_id'])) { $acl=false; break; } // ACL			
			$adodb->Execute("UPDATE weblink	SET page_target=".$target.", lang_target=".$lang_target.", alt='".$alt."' WHERE server=".$SERVER_ID." AND ver=".$ver." AND lang='".$lang."' AND menu_id=".(int)$_GET["menu_id"]."	AND sid=".(int)$_GET["linksid"]);
			$datas['status']=1;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
	// ZMIANA WIDOCZNOŚCI LINKU
	case 'link_visible':
		
		$datas['status']=0;
		if (strlen($_GET["linksid"]))
		{
			// wyciągnięcie aktualnej widoczności linka z bazy
			$query="SELECT hidden FROM weblink WHERE server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND menu_id=".(int)$_GET["menu_id"]." AND sid=".(int)$_GET["linksid"];
			parse_str(ado_query2url($query));

			$hidden = $hidden == 1 ? 0 : 1;
			
			if (!$kameleon->checkRight('write','menu',$_GET['menu_id'])) { $acl=false; break; } // ACL
			
			$adodb->Execute("UPDATE weblink	SET hidden=".$hidden." WHERE server=".$SERVER_ID." AND ver=".$ver." AND lang='".$lang."' AND menu_id=".(int)$_GET["menu_id"]."	AND sid=".(int)$_GET["linksid"]);
			$datas['status']=1;
			$datas['hidden']=$hidden;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break; 	
	
	// USUNIĘCIE LINKU
	case 'link_delete':
		if (!$kameleon->checkRight('delete','menu',$_GET['menu_id'])) { $acl=false; break; } // ACL
		$datas['status']=0;
		if (strlen($_GET["linksid"]))
		{
			$adodb->Execute("DELETE FROM weblink WHERE server=".$SERVER_ID." AND ver=".$ver." AND lang='".$lang."' AND menu_id=".(int)$_GET["menu_id"]."	AND sid=".(int)$_GET["linksid"]);
			$datas['status']=1;
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;	
		
	// DODANIE NOWEJ POZYCJI W MENU
	case 'link_add':
		if (!$kameleon->checkRight('insert','menu')) { $acl=false; break; } // ACL
		$query="SELECT menu_id FROM webtd WHERE server=".$SERVER_ID." AND ver=$ver AND lang='".$lang."' AND sid=".(int)$_GET["tdsid"]." LIMIT 1";
		parse_str(ado_query2url($query));
		$menu = (int)$menu_id;
		$datas['menu']=$menu;
		$link_name="...";
		if ($menu>0)
		{
			include "action/DodajLink.h";
		}
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
// DROPMENU
	
	//ŁADOWANIE PLUGINOW
	case 'dropmenu_load_plugin':
		$datas["items"] = array();
		if (sizeof($kameleon->plugins))
		{
			foreach ($kameleon->plugins as $plugin)
	    	{
				$item = array();
				$item['title']=$plugin["name"];
				$item['img']=strlen($plugin["logo"]) ? $plugin['logo'] : "";
				$item['href']=$plugin["link"].(strstr($plugin["link"],"?") ? "&" : "?")."return_path=".$_GET["return_link"];
				$item['onclick']="";//"wybierz_to('".$plugin["name"]."')";
				$item['css']="";
				$datas["items"][]=$item;
	    	}	
		}
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
	//ŁADOWANIE JEZYKOW
	case 'dropmenu_load_lang':
		$datas["items"] = array();
		if (isset($CONST_LANGS)) $langs=$CONST_LANGS;
		else $langs=array("pl","en","de","fr"); 
		if ($BASIC_RIGHTS) $langs=array($lang);

        if (basename($SCRIPT_NAME)!="tdedit.$KAMELEON_EXT" && count($langs)>1)
        {
        	for ($i=0;$i<count($langs);$i++)
        	{
        		$langicon = in_array($langs[$i],array("no","nl","tr","t","gr","g","bg","cz","cz2","hu","h","it","lt","l","sp","s","fr","f","ru","r","en","e","de","d","pl","p","i","pr")) ? $langs[$i] : "other";
        		$item = array();
				$item['title']=label($langs[$i]);
				$item['img']=$kameleon->user[skinpath]."/img/lang/".$langicon.".png";
				$item['href']=$_GET["page_link"].(strstr("?",$_GET["page_link"]) ? "&" : "?")."setlang=".$langs[$i]."&page=".$_REQUEST["page"];
				$item['onclick']="";
				$item['css']="";
				$datas["items"][]=$item;
        	}       
        }
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
		
	// ŁADOWANIE ZAKŁADEK
	case 'dropmenu_load_bookmark':
		$datas["items"] = array();
		$query="SELECT webfav.*,webpage.title FROM webfav LEFT JOIN webpage ON id=wf_page_id AND server=$SERVER_ID AND ver=$ver AND lang='$lang' WHERE wf_user='".$USERNAME."' AND wf_server=$SERVER_ID AND wf_lang='$lang' ORDER BY wf_sid";
		$book=ado_ObjectArray($adodb,$query);
		$datas["dodany"]=0;
		if (is_array($book))
		{ 
			for ($i=0;$i<count($book);$i++)
			{
				if ($_GET["page"]==$book[$i]->wf_page_id)
				{
					$datas["dodany"]=1;
				}
				$item = array();
				$item['title']=$book[$i]->title." [".$book[$i]->wf_page_id."]";
				$item['img']="";
				$item['href']="index.php?page=".$book[$i]->wf_page_id;
				$item['onclick']="";
				$item['css']="";
				$datas["items"][]=$item;
			}
		}	
		if ($datas["dodany"]==1)
		{
			$item = array();
			$item['title']=label('Delete from bookmarks');
			$item['img']=$kameleon->user[skinpath]."/img/icon_delete.png";
			$item['href']="";
			$item['onclick']="km_bookmark(".$_GET["page"].")";
			$item['css']="";
			$datas["items"][]=$item;
		}
		else
		{
			$item = array();
			$item['title']=label('Add to bookmarks');
			$item['img']=$kameleon->user[skinpath]."/img/icon_add.png";
			$item['href']="";
			$item['onclick']="km_bookmark(".$_GET["page"].")";
			$item['css']="";
			$datas["items"][]=$item;
		}
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
		
		
		
	// ŁADOWANIE SERWISÓW
	case 'dropmenu_load_server':
		$datas["items"] = array();
		if (is_object($auth_acl))
		{
			$query="SELECT nazwa,id FROM servers WHERE groupid<>$CONST_TRASH ORDER BY nazwa"; 
		}
		else
		{
			$query="SELECT nazwa,id FROM servers WHERE groupid<>$CONST_TRASH AND id IN (SELECT server FROM rights WHERE server=servers.id AND username='$USERNAME' AND (nexpire>=".time()." OR nexpire IS NULL)) ORDER BY nazwa";
		}
		$serwery=ado_ObjectArray($adodb,$query);
		$t=time();
		
		if (is_object($auth_acl))
		{
			foreach ($serwery AS $i=>$s)
			{
				$auth_acl->init($serwery[$i]->nazwa,$serwery[$i]->id);
				if (!$auth_acl->hasRight('read','kameleon')) unset($serwery[$i]); 
			}		
			$auth_acl->init('',$SERVER_ID);
		}	
		if (sizeof($serwery))
		{
			foreach ($serwery AS $i=>$s)
	    	{
	    		$item = array();
				$item['title']=$serwery[$i]->nazwa;
				$ico='ufiles/'.$serwery[$i]->id.'-att/.root/favicon.ico';
				
				$item['img']=file_exists($ico) ? $ico : 'root/favicon.ico';
				$item['href']=$_GET["page_link"].(strstr($_GET["page_link"],"?") ? "&" : "?")."_ts=".$t."&SetServer=".$serwery[$i]->nazwa;
				$item['onclick']="";//"wybierz_to('".$plugin["name"]."')";
				$item['css']="";
				$datas["items"][]=$item;
	    	}
		}
		$datas['status']=1;
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($datas);
		break;
}
if ($acl==false)
{
	$datas=array();
	$datas['status']=0;
	$datas['acl']="no access";
	header('Content-type: application/json; charset=utf-8');
	echo json_encode($datas);
}