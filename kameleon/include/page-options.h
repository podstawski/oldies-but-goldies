<?

if (is_object($WEBPAGE) )
{
  $pagevisibility="<a class=\"km_icon km_page_visible ".($WEBPAGE->hidden ? "km_iconi_invisible" : "km_iconi_visible" )."\" onclick=\"km_page_visible('".$WEBPAGE->sid."')\" title=\"".($WEBPAGE->hidden ? label("Page invisible") : label("Page visible") )."\">".($WEBPAGE->hidden ? label("Page invisible") : label("Page visible") )."</a>";
  $pagesitemap="<a class=\"km_icon km_page_sitemap_visible ".($WEBPAGE->nositemap ? "km_iconi_nsm" : "km_iconi_sm" )."\" onclick=\"km_page_sitemap('".$WEBPAGE->sid."')\" title=\"".($WEBPAGE->nositemap ? label("Page invisible in sitemap") : label("Page visible in sitemap") )."\">".($WEBPAGE->nositemap ? label("Page invisible in sitemap") : label("Page visible in sitemap") )."</a>";


  $pageproof="";

	if (strlen($WEBPAGE->noproof))
	{
		include_once('include/proof-send.h');

		if ($MAY_PROOF)
		{

			if ($WEBPAGE->noproof>0 || !$FTP_RIGHTS)
			{
				$pageproof.="<li><a class=\"km_icon km_iconi_stop\" href=\"#\" title=\"".label('Page not published since last update').', '.label('prof_status_'.abs($WEBPAGE->noproof))."\">".label('Page not published since last update').', '.label('prof_status_'.abs($WEBPAGE->noproof))."</a></li>";
			}


			if ($FTP_RIGHTS) 
			{
				$pageproof.="<li><a class=\"km_icon km_iconi_prooftp\" href=\"ftp.php?start=1&ftplimit=$page&page=$page&action=proof&setreferpage=$page\" title=\"".label("FTP page")."\">".label("FTP page")."</a></li>";
			}
			elseif ($WEBPAGE->noproof >= 0 && $WEBPAGE->proof_date<$WEBPAGE->nd_update  )
			{
				$label=addslashes(label('Proof and send ftp request'));
				$pageproof.="<li><a class=\"km_icon km_iconi_doproof\" href=\"$SCRIPT_NAME\" onclick=\"return proofSend('ProofFtpRequest','".$label."')\" title=\"".$label."\">".$label."</a></li>";
			}

		}
		else
		{
			if (abs($WEBPAGE->noproof)==1)
			{
				$pageproof.="<li><a class=\"km_icon km_iconi_stop\" href=\"#\" title=\"".label('Page unproved')."\">".label('Page unproved')."</a></li>";
				$label=addslashes(label('Send proof request'));
				$pageproof.="<li><a class=\"km_icon km_iconi_proof\" href=\"$SCRIPT_NAME\" onclick=\"return proofSend('ProofRequest','".$label."')\" title=\"".label('Page unproved')."\">".label('Page unproved')."</a></li>";

			}
			else
			{
				$pageproof.="<li><a class=\"km_icon km_iconi_stop\" href=\"#\" title=\"".label('Page unwritable due to wait state')."\">".label('Page unwritable due to wait state')."</a>";
				$editmode=0;

				$pagevisibility='';
				$pagesitemap='';
				$delete='';
				$edit='';
			}

		}


	
	}

	

}

echo "
<div class=\"km_toolbar\">
<ul>
";
$new_page = "<a class=\"km_icon km_iconi_new\" href=\"$SCRIPT_NAME?page=-1&referer=$page&page_id=-1&action=DodajStrone\" title=\"".label("Add new page")."\">".label("Add new page")."</a>";

$new_module="<a class=\"km_icon km_iconi_newmodule\" href=$SCRIPT_NAME?page=$page&page_id=$page_id&action=DodajTD title=\"".label("Insert module")."\">".label("Insert module")."</a>";

$preview = "<a class=\"km_icon km_iconi_previewmode\" href=\"".$SCRIPT_NAME."?page=".$page."&seteditmode=0\" ".( $helpmode ? "id=\"help_page_preview_icon\"" : "" )." title=\"".label("Show preview mode")."\" >".label("Show preview mode")."</a>";

$edit="<a class=\"km_icon km_iconi_property\" href=\"strona.$KAMELEON_EXT?page=$page\" ".( $helpmode ? "id=\"help_page_property_icon\"" : "" )." title=\"".label("Edit page")."\">".label("Edit page")."</a>";

$delete="<a class=\"km_icon km_iconi_delete\" href=\"javascript:zmiana('".$page."','UsunStrone')\" title=\"".label("Delete page")."\">".label("Delete page")."</a>";

$editmodeswitch="<a class=\"km_icon km_iconi_hfswitch\" href=\"$SCRIPT_NAME?page=$page&switcheditmode=1\" ".( $helpmode ? "id=\"help_page_hfswitch_icon\"" : "" )." title=\"".label("Switch: edit body or header/footer")."\">".label("Switch: edit body or header/footer")."</a>";

 
if (is_array($kameleon->current_server->trans)) if ($kameleon->current_server->trans[$lang][ver]=="$ver")	if (in_array($kameleon->user[username],$kameleon->current_server->trans[$lang][users]))
  $editmodeswitch.="<a class=\"km_icon km_iconi_trans\" href=\"index.php?setreferpage=".$page."\">".label("Translation mode")."</a>";


$imgGallery = "<a class=\"km_icon km_iconi_image\" href=\"#\" onclick=\"window.open('ufiles.php?page=0&galeria=4','window','height=380,width=852,status=no,toolbar=no,menubar=no,location=no');\" title=\"".label("Click to open graphic files library")."\">".label("Click to open graphic files library")."</a>";
$rootGallery = 	"<a class=\"km_icon km_iconi_root\" href=\"#\" onclick=\"window.open('ufiles.php?page=0&galeria=6','window','height=380,width=440,status=no,toolbar=no,menubar=no,location=no');\" title=\"".label("Click to open root files library")."\">".label("Click to open root files library")."</a>";

if ($TEMPL_RIGHTS)
{
	$templGallery="<a class=\"km_icon km_iconi_szablon\" href=\"#\" onclick=\"window.open('ufiles.php?page=0&galeria=5','window','height=380,width=440,status=no,toolbar=no,menubar=no,location=no')\" title=\"".label("Click to open template files library")."\">".label("Click to open template files library")."</a>";
}				

if ($CONST_MODE=="express") $editmodeswitch="";



$help="";
//if (!$helpmode) $help="<li><a class=\"km_icon km_iconi_help\" href=\"".$HELP_LINK."\" target=\"_blank\">".label("Help")."</a></li>"; CARTMAN - OJ NIE DZIAŁA TEN HELP ;)        

$chpass="<a class=\"km_icon km_iconi_rights\" href=\"#\" onclick=\"km_chpass(event);\" title=\"".label("Change password")."\">".label("Change password")."</a>";
			
if (is_object($WEBPAGE) && $WEBPAGE->prev>=0 && strlen($WEBPAGE->prev)) 
  $prev_ar="<a class=\"km_icon km_iconi_prev\" href=\"" . $SCRIPT_NAME . "?page=" . $WEBPAGE->prev ."\" title=\"".label("Goto previous page")."\">".label("Goto previous page")."</a>";
else 
  $prev_ar="";
  
if (is_object($WEBPAGE) && $WEBPAGE->next) 
  $next_ar="<a class=\"km_icon km_iconi_next\" href=\"". $SCRIPT_NAME . "?page=" . $WEBPAGE->next . "&referer=" . $page . "\" title=\"".label("Goto next page")."\">".label("Goto next page")."</a>";
else 
  $next_ar="";

if (is_object($WEBPAGE) && $WEBPAGE->menu_id) 
	$menu_id="<a class=\"km_icon km_iconi_menu\" href=\"menus." . $KAMELEON_EXT ."?menu=". $WEBPAGE->menu_id ."\" title=\"Menu: ".$WEBPAGE->menu_id."\">Menu: ".$WEBPAGE->menu_id."</a>";
else 
  $menu_id="";
    
if (is_object($WEBPAGE) && $WEBPAGE->submenu_id) 
	$submenu_id="<a class=\"km_icon km_iconi_submenu\" href=\"menus." . $KAMELEON_EXT . "?menu=" . $WEBPAGE->submenu_id ."\" title=\"Submenu: ".$WEBPAGE->submenu_id."\">Submenu: ".$WEBPAGE->submenu_id."</a>";
else 
  $submenu_id="";

$copy="<a class=\"km_icon km_iconi_copy\" style=\"cursor: pointer\" onclick=\"skopiuj('".$WEBPAGE->sid."','page')\" title=\"".label("Copy page")."\">".label("Copy page")."</a>";

if (count($clib['page']))
{
	$paste="<a class=\"km_icon km_iconi_paste\" onclick=\"wklej('$page','page')\" title=\"".label("Paste page")."\">".label("Paste page")."</a>";
}

$current_page="<li class=\"km_sep\"></li><li class=\"km_label\"><label>".label("Current page").":</label><b>$page</b></li>";

if ($C_HIDE_PAGE_SB_HIDDEN) $pagevisibility="";
if ($C_HIDE_PAGE_SB_SITEMAP) $pagesitemap="";
if ($C_HIDE_PAGE_SB_CURRENT) $current_page="";



if (is_object($WEBPAGE) && $WEBPAGE->ver==$ver)
{
	echo "
    <li>$preview</li>
    <li>$edit</li>
    <li>$copy</li>		
    <li>$delete</li>
    <li>$pagevisibility</li>
    <li>$pagesitemap</li>
    $pageproof
    <li class=\"sep\"></li>
    <li>$editmodeswitch</li>
    <li>$prev_ar</li>
    <li>$next_ar</li>
    <li class=\"sep\"></li>
    <li>$menu_id</li>
    <li>$submenu_id</li>
    <li>$imgGallery</li>
    <li>$rootGallery</li>
    <li>$templGallery</li>
    <li class=\"sep\"></li>
  ";
}
else
{

  echo "
  <li class=\"km_label\"><b>".label("No page")."!</b></li>
  <li class=\"km_sep\"></li>
  <li><a class=\"km_icon km_iconi_new\" href=\"".$SCRIPT_NAME . "?page=$page&page_id=$page&action=DodajStrone&referer=$referer&ref_menu=$ref_menu\">".label("Create")."</a></li>
  <li>$paste</li>
  <li class=\"km_sep\"></li>
  <li class=\"km_label\"><label for=\"km_copyfrom\">".label("Copy from").":</label><input id=\"km_copyfrom\" type=\"text\" class=\"km_text\" size=\"5\"  onchange='kopia_strony(this.value)' name=\"pagesrc\" value=\"\"><input class=\"km_iconi_copy_m\" type=\"submit\" value=\"".label("Copy")."\" /></li>
  ";

	$query="SELECT ver AS verminor FROM webpage WHERE server=$SERVER_ID AND ver < $ver AND id=$page AND lang='$lang' ORDER BY ver DESC";

	//$adodb->puke($query);
	$result=$adodb->Execute($query);
	$ile=$result->RecordCount();
}

echo "
  <li class=\"km_sep\"></li>
  <li class=\"km_label\">
    <form name=\"change_page\"><label for=\"km_gotopage\">".label("Go to page").":</label><input type=\"text\" class=\"km_text\" size=\"5\" name=\"page\" value=\"".( $page>=0 ? $page : $referpage )."\"><input class=\"km_iconi_enter\" type=\"submit\" value=\"".label("Go to page")."\" title=\"".label("Go to page")."\" /></form>
  </li>
";

if (is_object($WEBPAGE) && $WEBPAGE->ver==$ver && $page!=-1)
	echo "<li id=\"help_new_page\">$new_page</li>";
		

echo "<li id=\"km_bookmark_link\"><span class=\"km_icon km_iconi_bookmark\" title=\"".label("Bookmarks")."\">".label("Bookmarks")."</span></li>";


echo "<li class=\"km_sep\"></li>";


if (is_object($auth_acl))
{
	$query="SELECT nazwa,id FROM servers WHERE groupid<>$CONST_TRASH ORDER BY nazwa"; 
}
else
{
	$query="SELECT nazwa,id FROM servers WHERE groupid<>$CONST_TRASH 
				AND id IN (SELECT server FROM rights WHERE server=servers.id AND username='$USERNAME' AND (nexpire>=".time()." OR nexpire IS NULL))
			ORDER BY nazwa";
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


if (count($serwery)>1)
{
	echo "<li class=\"km_label\" id=\"km_server_link\"><label for=\"km_selectserver\">".label("Maintained servers").":</label><div class=\"km_server\"><span title=\"".label('Change server')."\">".$SERVER->nazwa."</span></div></li>\n";
}


$langicon = in_array($lang,array("no","nl","tr","t","gr","g","bg","cz","cz2","hu","h","it","lt","l","sp","s","fr","f","ru","r","en","e","de","d","pl","p","i","pr")) ? $lang : "other";
echo "<li id=\"km_lang_link\"><span class=\"km_icon km_iconi_lang_".$langicon."\" title=\"".label($lang)."\">".label($lang)."</span></li>";

echo "<li id=\"km_editsave\"><a class=\"km_icon km_iconi_save\" title=\"".label("Zapisz zmiany")."\">".label("Zapisz zmiany")."</a></li>";
//echo "<li class=\"km_sep\"></li><li>".$chpass."</li>".$help;
echo "</ul>
</div>";	

include ("include/lang-change.h");

?>

<div id="km_chpass_div" style="position:absolute; display:none; left:0px; top: 0px; width:450px; background-color:white;z-index:100003">
	<div class="km_schowek_header"><? echo label("Change password") ?><img src="<?php echo $kameleon->user[skinpath]; ?>/img/multischowek/close.gif" alt="<? echo label("Close") ?>" onclick="km_chpass_close()" /></div>
	<form name="kameleon_chpass_form" action="<?echo $SCRIPT_NAME?>" method="post" class="k_td" style="margin:0px">
  	<input type="hidden" name="page" value="<? echo $page?>">
  	<input type="hidden" name="action" value="KameleonChpass">
  	<div class="km_schowek_items">
      <ul>
    	 <li><label for="km_chpass_op"><?echo label('Type previous password')?></label><input id="km_chpass_op" class="k_input" name="op" size="20" type="password"></li>
    	 <li><label for="km_chpass_np"><?echo label('Type new password')?></label><input id="km_chpass_np" name="np" size="20" type="password"></li>
    	 <li><label for="km_chpass_rp"><?echo label('Retype new password')?></label><input id="km_chpass_rp" name="rp" size="20" type="password"></li>
    	</ul>
    </div>
    <div class="km_schowek_buttons">
      <input type="button" value="<?echo label('Cancel')?>" onclick="km_chpass_close()">
  		<input type="submit" value="<?echo label('Change')?>">
    </div>
  </form>
</div>

<div id="km_bookmarks_div" style="display: none; z-index: 100002">
  <div class="km_schowek_header"><? echo label('Bookmarks') ?><img src="<?php echo $kameleon->user[skinpath]; ?>/img/multischowek/close.gif" alt="<? echo label("Close") ?>" onclick="km_bookmarks_close()" /></div>
  <div class="km_schowek_items"><ul>
    <?=$bookmarks?><li><a class="<?=$lab_class?>" href="index.php?page=<?=$page?>&action=Bookmark"><?=$lab_add?></a></li>
  </ul></div>
</div>

<div id="km_chserver_div" style="display: none; z-index: 100002; position: absolute; ">
  <div class="km_schowek_header"><? echo label('Change server') ?><img src="<?php echo $kameleon->user[skinpath]; ?>/img/multischowek/close.gif" alt="<? echo label("Close") ?>" onclick="km_chserver_close()" /></div>
  <div class="km_schowek_items"><ul>
    <?
		foreach ($serwery AS $i=>$s)
    	{
			$ico='ufiles/'.$serwery[$i]->id.'-att/.root/favicon.ico';
			if (!file_exists($ico)) $ico='root/favicon.ico';
			$style=" style=\"background-image:url('".$ico."');\"";
    		echo "<li><a href=\"".$SCRIPT_NAME."?_ts=".$t."&page=0&SetServer=".$serwery[$i]->nazwa."\"".$style.">".$serwery[$i]->nazwa."</a></li>";
    	}
    ?>
  </ul></div>
</div>

<div id="km_plugins_div" style="display: none; z-index: 100003; position: absolute; ">
  <div class="km_schowek_header"><? echo label('Plugins') ?><img src="<?php echo $kameleon->user[skinpath]; ?>/img/multischowek/close.gif" alt="<? echo label("Close") ?>" onclick="km_plugins_close()" /></div>
  <div class="km_schowek_items"><ul>
    <?
      foreach ($kameleon->plugins as $plugin)
    	{
    	  $return_path=base64_encode($_SERVER["REQUEST_URI"]);
			  $style="";
        if (strlen($plugin["logo"])) $style="style=\"background-image:url('".$plugin["logo"]."');\"";
    		echo "<li><a href=\"".$plugin["link"].(strstr($plugin["link"],"?") ? "&" : "?")."return_path=".$return_path."\" ".$style." >".$plugin["name"]."</a></li>";
    	}
    ?>
  </ul></div>
</div>
<script type="text/javascript">
	
	jQueryKam("#km_chpass_open").bind('click',function(e)
  { 
	 jQueryKam('#km_chpass_div').css('left',(e.pageX-300)+'px');
		jQueryKam('#km_chpass_div').css('top',e.pageY+'px');
    jQueryKam('#km_chpass_div').show();
    jQueryKam(function() { 
    	jQueryKam("#km_chpass_div").draggable({ handle: '.km_schowek_header' });
    });
	});
	
	jQueryKam("#km_bookmarks_open").bind('click',function(e)
  { 
		jQueryKam('#km_bookmarks_div').css('left',e.pageX+'px');
		jQueryKam('#km_bookmarks_div').css('top',e.pageY+'px');
    jQueryKam('#km_bookmarks_div').show();
    jQueryKam(function() { 
    	jQueryKam("#km_bookmarks_div").draggable({ handle: '.km_schowek_header' });
    });
	});
	
	
	
	function km_plugins_close ()
	{
    jQueryKam('#km_plugins_div').hide();
  }
	
  function km_chpass_close ()
	{
    jQueryKam('#km_chpass_div').hide();
  }
  /*
  jQueryKam("#km_servers_open").bind('click',function(e)
  { 
		jQueryKam('#km_chserver_div').css('left',e.pageX+'px');
    jQueryKam('#km_chserver_div').css('top',e.pageY+'px');
    jQueryKam('#km_chserver_div').show();
    jQueryKam(function() { 
    	jQueryKam("#km_chserver_div").draggable({ handle: '.km_schowek_header' });
    });
	});
	*/
  function km_chserver_close ()
	{
    jQueryKam('#km_chserver_div').hide();
  }
	function km_bookmarks_close ()
	{
    jQueryKam('#km_bookmarks_div').hide();
  }

</script>

