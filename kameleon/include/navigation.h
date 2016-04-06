<?
if ($navigationhidden) return;

$rp=$referpage?$referpage:$page+0;
if ($mybasename=="index") $rp=$page+0;
if (isset($CONST_LANGS)) $langs=$CONST_LANGS;
else $langs=array("pl","en","de","fr"); 

if (!$SERVER->hide_identity)	include("include/identity.h");

echo "<div class=\"km_mainmenu\"><ul>";
    
if (basename($SCRIPT_NAME)!="index.$KAMELEON_EXT")
{
	$page_href_id = $helpmode ? "id=\"help_navi_page\"" : "";
	echo "<li class=\"km_item_n\" " . $page_href_id . "><a class=\"km_item_href\" href=\"index." . $KAMELEON_EXT . "?page=" . $rp . $GLOBAL_LINK ."\">" . label('Page') . " " . label("No") . " ". $rp ."</a></li>";
}


$activebookmark	= 'n';

if (basename($SCRIPT_NAME)!="consistency.$KAMELEON_EXT") 
  echo "<li class=\"km_item_". $activebookmark ."\" ><a class=\"km_item_href\" href=\"consistency." . $KAMELEON_EXT ."?page= ". $page . "&setreferpage=" . $rp . "\">" . label("Page Explorer") . "</a></li>";    


if (basename($SCRIPT_NAME)=="menus.$KAMELEON_EXT") 
    $activebookmark = 'a';
else    
    $activebookmark = 'n';


if (basename($SCRIPT_NAME)!="menus.$KAMELEON_EXT")
	echo "<li class=\"km_item_". $activebookmark ."\" ><a class=\"km_item_href\" href=\"menus." . $KAMELEON_EXT . "?setreferpage=" . $rp . "&menu=" . $menu ."\">".label("Menu")."</a></li>";

if (!$BASIC_RIGHTS && ($ver<$C_MAX_VER || !$C_MAX_VER) && basename($SCRIPT_NAME)!="tdedit.$KAMELEON_EXT")
{
   echo "<li class=\"km_item_n\"><div class=\"km_item_block\" id=\"km_ver_span\"><a href=\"javascript:\" onclick=\"zmiana_wersji()\">".label("Version")."</a>: <b>$ver</b></div><div class=\"km_item_block\" id=\"km_ver_vdiv\" style=\"display:none\"><input id=\"km_ver_input\" style=\"width:50px;\" value=\"".$ver."\" onblur=\"zmiana_wersji(this.value)\" /></div></li>";
}

if ($CLASS_RIGHTS && basename($SCRIPT_NAME)!="tdedit.$KAMELEON_EXT") 
{
    if (basename($SCRIPT_NAME)=="style.$KAMELEON_EXT") 
        $activebookmark = 'a';
    else    
        $activebookmark = 'n';
    echo "<li class=\"km_item_".$activebookmark."\"><a class=\"km_item_href\" href=\"style." . $KAMELEON_EXT . "?setreferpage=" . $rp ."\">".label("Class")."</a></li>";
}


	
if ($FTP_RIGHTS && basename($SCRIPT_NAME)!="tdedit.$KAMELEON_EXT") 
{    
	$ftp_href_id=$helpmode?"id=\"help_navi_ftp\"":"";

    if (basename($SCRIPT_NAME)=="ftp.$KAMELEON_EXT") 
        $activebookmark = 'a';
    else    
        $activebookmark = 'n';
    echo "<li class=\"km_item_".$activebookmark."\" ". $ftp_href_id . "><a class=\"km_item_href\" href=\"ftp." . $KAMELEON_EXT . "?setreferpage=" . $rp ."\">".label("FTP")."</a></li>";
}



//if ($SERVER_PROOF && 
if ((basename($SCRIPT_NAME)=="index.$KAMELEON_EXT" || basename($SCRIPT_NAME)=="ftp.$KAMELEON_EXT" || basename($SCRIPT_NAME)=="proof.$KAMELEON_EXT") )
{
	$active_zakl='n';
	if (basename($SCRIPT_NAME)=="proof.$KAMELEON_EXT")
	{
		$active_zakl='a';
		if (!$page) $page=$referpage;
	}
  echo "<li class=\"km_item_${active_zakl}\"><a class=\"km_item_href\" href=\"proof.php?setreferpage=" . $page ."\">" . label("Proof pages") . "</a></li>"; 
}

if ($FTP_RIGHTS && basename($SCRIPT_NAME)=="index.$KAMELEON_EXT" && $MAY_PROOF && $kameleon->current_server->versions)
{
	$sid=$WEBPAGE->sid;
  echo "<li class=\"km_item_n\"><a class=\"km_item_href\" href=\"archiwum.php?setreferpage=" . $page . "&wv_sid=" . $sid . "&wv_table=page\">" . label("Version archive") . "</a></li>";
}

if ($ADMIN_RIGHTS && basename($SCRIPT_NAME)=="index.$KAMELEON_EXT" )
{
    echo "<li class=\"km_item_n\"><a class=\"km_item_href\" href=\"admin/index.php?setreferpage=" . $page . "\">" . label("Administration") . "</a></li>"; 
}

if ($ADMIN_RIGHTS && basename($SCRIPT_NAME)=="tdedit.$KAMELEON_EXT" )
{
	if ($kameleon->current_server->versions)
	{
	 $query="SELECT sid AS tdsid FROM webtd WHERE page_id=$page_id AND pri=$pri AND server=$SERVER_ID AND ver=$ver AND lang='$lang'";
	 parse_str(ado_query2url($query));
   echo "<li class=\"km_item_n\"><a class=\"km_item_href\" href=\"archiwum.php?setreferpage=" . $page . "&wv_sid=" . $tdsid . "&wv_table=td\">" . label("Version archive") . "</a></li>";
	}
}

if (basename($SCRIPT_NAME)=="tdedit.$KAMELEON_EXT") 
{
	 $link=$REQUEST_URI;
	 if (!strstr($link,'toggleeditormode')) $link.="&toggleeditormode=1";
   echo "<li class=\"km_item_n\"><a class=\"km_item_href\" href=\"" . $link . "\">" . label("Toggle editor mode") . "</a></li>";
}

if ($ADMIN_RIGHTS && basename($SCRIPT_NAME)=="menus.$KAMELEON_EXT" )
{
	if ($kameleon->current_server->versions)
	{
	 $menu+=0;
	 $query="SELECT min(menu_sid) AS menu_sid FROM weblink WHERE menu_id=$menu AND server=$SERVER_ID AND ver=$ver AND lang='$lang'";
	 parse_str(ado_query2url($query));
	 if ($menu_sid) echo "<li class=\"km_item_n\"><a class=\"km_item_href\" href=\"archiwum.php?wv_sid=" . $menu_sid . "&wv_table=link&menu=" . $menu ."\">" . label("Version archive") . "</a></li>";
	}
}


if (strlen($expiration_warrning))
{
    echo "<li class=\"km_item_n\"><div class=\"km_item_block\">" . $expiration_warrning ."</div></li>";
}

if (sizeof($kameleon->plugins))
{
  echo "<li class=\"km_item_n\"><a class=\"km_item_href\" id=\"km_plugin_link\" href=\"#\">".label('Plugins')."</a></li>";
}

?>
</ul>
</div>
<form name="wersje" style="margin: 0; padding: 0" method="post" action="<?echo $SCRIPT_NAME?>">
 <input type="hidden" name="action" value="">
 <input type="hidden" name="page" value="<?=$page?>">
 <input type="hidden" name="pole" value="" id="kameleon_wersje_pole_id">
</form>

<script language="javascript">
function zmiana_wersji(ver)
{
	if (ver==null)
	{
		document.getElementById('km_ver_span').style.display='none';
		document.getElementById('km_ver_vdiv').style.display='block';
		document.getElementById('km_ver_input').focus(); 
		document.getElementById('km_ver_input').select()
	}
	else
	{
		if (ver+0>0 && ver!=<?=$ver?>) 
		{
			document.wersje.kameleon_wersje_pole_id.name="version";
			document.wersje.kameleon_wersje_pole_id.value=ver;
			document.wersje.submit();
		}
		else
		{
			document.getElementById('km_ver_span').style.display='block';
			document.getElementById('km_ver_vdiv').style.display='none';
		}
	}

}

function licence()
{
  a=open("licence.<?echo $KAMELEON_EXT?>","licence","toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width=560,height=350");
}

</script>