<html>
<head>
    <title>KAMELEON: <?echo label("Menu");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/tdedit.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
	<script type="text/javascript">
		var km_infos = new Array();
		km_infos["ajax_link"]='<?php echo str_replace("menus.php","ajax.php",$SCRIPT_NAME); ?>';
		km_infos["menu_id"]='<?php echo $menu; ?>';
	</script>
	<?php
		include_js("lang/pl");
		include_js("jquery-1.4");
		include_js("jquery-ui.min");
		include ("include/tree_js.h");
		include_js("kameleon");
		
		include("ajax_variables.php");
	?>
	
</head>
<? if (!$editmode) echo "<script>location.href='/';</script>"; ?>
<body bgcolor="#c0c0c0" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>

	

<script type="text/javascript">
var active_sid = -1;

function new_menu(menu)
{
    if (!(menu)) menu='0';
	menu_id=prompt("<?echo label("Issue menu number you want to create");?>: ",menu);
    if (menu_id) 
    {   
    	document.location.href="menus.<?echo $KAMELEON_EXT?>?menu="+menu_id;
    }
}

function open_menu(menu_id)
{
    document.location.href="menus.<?echo $KAMELEON_EXT?>?menu="+menu_id;
}

function zmiana(menu,action)
{
	document.getElementById('km_menu_id').value=menu;	
	document.getElementById('km_action').value=action;	
	document.getElementById('km_zmiany').submit();
	if ( action=="UsunLink"  && !confirm("<?echo label("Are you sure you want to delete");?> ?")) return;
}

function saveLink(targety,action)
{
	km_link_target(active_sid,targety);
	active_sid=-1;
}

function saveId(key,val)
{
    document.all[pole].value=val;
	zmiana(document.getElementById('km_menu_id').value+val,'ModyfikujTarget')		
}

</script>    



<?
  if (strlen($menu)==0) { $ex = explode(":",$menu_id); $menu=$ex[0]; }

	if ($CONST_MODE!="express") 
	{
		include("include/navigation.h");

    if ($menu==-1)
		{
			$menu_id=-1;
			include ("include/menu_max.h");
			$menu=$menu_id;
		}
		elseif ($menu)
		{
			$menu_name="";
			$query="SELECT name AS menu_name FROM weblink 
					WHERE ver=$ver AND server=$SERVER_ID
					AND lang='$lang' AND menu_id=$menu
					LIMIT 1";
			parse_str(ado_query2url($query));

			if (!strlen($menu_name)) $menu_name=label("menu name");
			$menu_name=stripslashes($menu_name);
		}
	}
  
  echo "
  <div class=\"km_toolbar\">
      <ul>
        <li><a href=\"#\" onclick=\"new_menu(".$menu.")\" title=\"".label("New menu")."\" class=\"km_icon km_iconi_new\">".label("New menu")."</a></li>
        <li class=\"km_sep\"></li>
        <li class=\"km_label\">
          <label for=\"selmenu\">".label("Menus")."</label>
          <select id=\"selmenu\" name=\"openmenu\" onchange=\"open_menu(this[this.selectedIndex].value)\">
            <option value=\"0\">".label("Select menu")."</opiton>
            <option value=\"-1\">".label("New menu")."</opiton>";
            
            include ("include/menu_options.h");
            
            echo "
          </select>
        </li>
        <li class=\"km_sep\"></li>
        <li id=\"km_lang_link\"><span class=\"km_icon km_iconi_lang_".$lang."\" title=\"".label($lang)."\">".label($lang)."</span></li>
      </ul>";
    
  if (strlen($menu_name))
  {
    echo "
    <div class=\"km_toolbar_right2\">
      ".label('Title')."
      <input name=\"menu_name\" style=\"width: 300px;\" type=\"text\" value=\"".$menu_name."\" onchange=\"zmiana('".$menu.":'+value,'ZapiszMenu')\" />
    </div>
    ";
  }
    
  echo "</div>";
include ("include/lang-change.h");

// blokada na pokazywanie reszty strony
if (!$menu) return;


$query="SELECT * FROM weblink WHERE server=$SERVER_ID
	AND ver=$ver AND menu_id=$menu AND lang='$lang'
	ORDER BY pri";

$result=$adodb->Execute($query);
$ile=$result->RecordCount();
if (!$ile)
{
	$query="SELECT DISTINCT ver AS verminor FROM weblink 
			WHERE server=$SERVER_ID AND menu_id=$menu 
			AND lang='$lang' AND ver<$ver
			ORDER BY ver DESC"; 
	
	$result=$adodb->Execute($query);

	if ($result->RecordCount() ) 
		echo "<br>&nbsp; ".label("Missing menu items")."  $menu, ".label("but do exist in previous versions, copy from").":";

	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		echo "&nbsp; <a href=menus.$KAMELEON_EXT?src=$verminor&menu=$menu&action=SkopiujMenu>$verminor</a>";
	}
	
	
	$query="SELECT DISTINCT lang AS otherlang FROM weblink
			WHERE server=$SERVER_ID 
			AND menu_id=$menu AND lang<>'$lang' 
			AND ver=$ver"; 
			
	$result=$adodb->Execute($query);
	
	
	if ($result->RecordCount()) 
		echo "<br>&nbsp; ".label("Missing menu items")."  $menu, ".label("but do exist in other languages, copy from").":";

	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$lng=label($otherlang);
		echo "&nbsp; <a class=k_a href='menus.$KAMELEON_EXT?src=$otherlang&menu=$menu&action=SkopiujMenuLang'>$lng</a>";
	}
    
	
	echo "
        <form name=copymenu action=menus.$KAMELEON_EXT method=post>
        <input type='hidden' name=menu value=$menu>
        <input type='hidden' name=action value=SkopiujMenuWTejWersji>
        ";
  	echo "&nbsp; ".label("Make copy from menu nr").": ";
    	echo "<select class=k_select name=menusrc>";
	
	include ("include/menu_options.h"); 
   	echo "</select>
          <img class=k_imgbutton src=img/i_copy_n.gif onclick='document.copymenu.submit()' style='cursor:hand;' onmouseover=\"this.src='img/i_copy_a.gif'\" onmouseout=\"this.src='img/i_copy_n.gif'\" border=0 alt='".label("Copy")."' width=23 height=22 align=absmiddle>
	    </form>";

	echo "<hr>";		  
}
?>
<script type="text/javascript">
	km_infos["menu_id"]='<?php echo $menu; ?>';
</script>
<table class="tabelka" cellpadding="1" cellspacing="0">

<tr>
	<th colspan="2"><b><?echo label('Menu no');echo ": ".$menu?></th>
	<th>
    <form method="post" action="<?echo $SCRIPT_NAME?>" id="dodajlink">
	    <input type="hidden" name="action" value="DodajLink">
	    <input type="hidden" name="menu" value="<?echo $menu?>">
	    <a class="km_icon km_iconi_new_m" type="submit" href="javascript:document.getElementById('dodajlink').submit()" title="<?echo  label('New menu item');?>"> 
	    <a class="km_icon km_iconi_property_m" href="link.<?echo $KAMELEON_EXT?>?menu_id=<?echo $menu;?>&pri=0" title="<?=label('Change all items');?>"><?=label('Change all items');?></a>
	    <a class="km_icon km_iconi_delete_m" href="javascript:zmiana('<?echo $menu?>:0','UsunLink')" title="<?=label('Delete all items')?>"><?=label('Delete all items')?></a>
	  </form>
	</th>
</tr>
<tbody id="km_menus">
<?


for ($i=0;$i<$ile;$i++)
{
	parse_str(ado_ExplodeName($result,$i));

	if ($i) unset($ids);
	else
	{
		$ids[edit]="id=help_menu_edit_item";
		$ids[pri]="id=help_menu_pri_item";
		$ids[target]="id=help_menu_target_item";
	}

	if (strlen($img)) 
	{
		$insidelink="<img border=0 src='$UIMAGES/$img' alt='$alt_title'>$alt";
	}
	else $insidelink="$alt";

	if (strlen($lang_target)) $page_target="$lang_target:$page_target";
	$page=$referpage;
	if (!strlen($href) && !strlen($page_target))
		
		$href=kameleon_href($href,"ref_menu=$menu_id:$pri",-1);	
	else	
		$href=kameleon_href($href,"ref_menu=$menu_id:$pri$variables",$page_target);


	$vis_label = $hidden ? label("Link invisible") : label("Link visible");
	$linkvisibility="<a onclick=\"km_link_visible('".$sid."')\" class='link_visible km_icon km_icontd_visible_".($hidden ? "off" : "on")."' title='".$vis_label."'>".$vis_label."</a>";
	
	echo "<tr id='link_".$sid."' title='".$sid."'>";
	echo "<td><a href='$href' class='k_a'>".stripslashes($insidelink)."</a></td>";

//	echo td("left",label("Hyperlink to")."<input size=5 name=page_target class=k_input value='$page_target' onChange=zmiana('$menu:$pri:'+value,'ModyfikujTarget')><img src=img/i_enter_n.gif style=\"cursor:hand;\" onmouseover=\"this.src='img/i_enter_a.gif'\" onmouseout=\"this.src='img/i_enter_n.gif'\" border=0 alt='".label('New menu')."' width=23 height=22>",0);

    echo "
            <td class=\"k_td\">
            ".label("Hyperlink to")."
            <input size=\"7\" id=\"page_target_".$sid."\" name=\"page_target_".$sid."\" class=\"k_input\" value=\"".$page_target."\" onchange=\"km_link_target(".$sid.",value)\">  ";
?>

	<img class="k_imgbutton" src="img/i_tree_n.gif"	onclick="active_sid=<? echo $sid; ?>; openTree('page_target_<?echo $sid?>',document.all['page_target_<?echo $sid?>'].value,'multi=1')" 
		style="cursor:hand;" 
		onmouseover="this.src='img/i_tree_a.gif'" 
		onmouseout="this.src='img/i_tree_n.gif'" 
		border=0 alt='<?echo label("Webpage explorer")?>' align="absmiddle" />
       </td>
<?  
	$delete	="<a class=\"km_icon km_icontd_delete\" onclick=\"km_link_delete('".$sid."')\" title=\"".label("Delete")." ".label("hyperlink")."\">".label("Delete")." ".label("hyperlink")."</a>";
	$edit	="<a class=\"km_icon km_icontd_edit\" href=\"link.".$KAMELEON_EXT."?menu_id=".$menu."&sid=".$sid."\" title=\"".label("Edit")." ".label("hyperlink")."\">".label("Edit")." ".label("hyperlink")."</a>";
	$up		="<a class=\"km_icon km_iconi_arr_up\" href=\"menus.$KAMELEON_EXT?table=link&menu=$menu&pole=menu_id&wart=$menu&sid=".$sid."&dir=up&action=Move\" title=\"".label("Move up")."\">".label("Move up")."</a>";
	$down	="<a class=\"km_icon km_iconi_arr_down\" href=\"menus.$KAMELEON_EXT?table=link&menu=$menu&pole=menu_id&wart=$menu&sid=".$sid."&dir=down&action=Move\" title=\"".label("Move down")."\">".label("Move down")."</a>";

	$sub="";

	if ($submenu_id)
	{
		$sub="<a class=\"km_icon km_iconi_menu_m\" href=\"menus.".$KAMELEON_EXT."?menu=".$submenu_id."&setreferpage=".$page_target."\" title=\"".label("Menu").": ".$submenu_id.")\">".label("Menu").": ".$submenu_id."</a>";
	}

	if ($CONST_MODE!="express")
		echo "<td>$up$down$edit$delete$linkvisibility$sub</td>";
	else
		echo "<td>$edit$delete</td>";

	echo "</tr>";
}



?>
</tbody>
</table>


<form id="km_zmiany" method="get">
 <input type="hidden" id="km_action" name="action" value="">
 <input type="hidden" id="km_menu_id" name="menu_id" value="<?=$menu?>">
</form>

<script type="text/javascript">
jQueryKam(document).ready( function() {
	jQueryKam('#km_menus').sortable({  
	    cursor: 'move',  
	    placeholder: 'km_placeholder',  
	    forceHelperSize : true,
	    forcePlaceholderSize: true,
	    tolerance: 'pointer', 
	    revert: true,
	    distance: 30,
	    opacity: 0.4,
	    start : function(event, ui) {
	      
	    },
	    stop: function(event, ui){
			var tm_drag='';
	        jQueryKam(ui.item).parent().find('tr').each(
	          function (i) {
	            tm_drag+=jQueryKam(this).attr('title')+';';
	          }
	        );
	  		var sid = jQueryKam(ui.item).attr("title");
	        km_link_drag(sid,tm_drag);
		}
  	});//.disableSelection();
});
</script>

</body>
</html>