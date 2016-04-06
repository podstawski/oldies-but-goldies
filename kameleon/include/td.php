<?
global 	$CONST_MODE, $CONST_PARSER_INTEGRATED , $editmode, $hf_editmode,$helpmode,$main_buttons,$main_copy;
global $insert_td;
global $C_SWF_STYLE, $CONST_SWF_JS, $C_EDITOR_FORM;
global $kameleon_editor_form_counetr;
global $AUTH_BY_ACL_PLUGIN;

// Na potrzeby JS menu na belkach
global $APIS, $adodb;
global $kameleon;
global $SERVER,$SERVER_NAME;
global $DEFAULT_PATH_KAMELEON_UINCLUDES;
global $DEFAULT_PATH_KAMELEON_UINCLUDES_SVN;


push ($this_editmode);

if ($editmode && $page_id>=0 && !$hf_editmode ) $this_editmode=1;
if ($editmode && $page_id<0 && !$hf_editmode ) $this_editmode=0;
if ($editmode && $page_id>=0 && $hf_editmode ) $this_editmode=0;
if ($editmode && $page_id<0 && $hf_editmode ) $this_editmode=1;

if ($CONST_MODE=="express" && $editmode) $this_editmode=1;
if (!$C_SWF_STYLE) $WEBTD->swfstyle=0;
if ($C_SWF_STYLE) include_once('include/swf.h');


// wykrycie czy modu� opublikowany czy nie i ustalenie koloru
$bg_color='';
if (!$WEBPAGE->hidden) if (strstr($WEBPAGE->unproof_sids,':'.$WEBTD->sid.':') || $WEBTD->hidden==100) $bg_color="<li><a class=\"km_icon km_icontd_new\" title=\"".label("To publish")."\">".label("To publish")."</a></li>";


if ($CONST_PARSER_INTEGRATED || file_exists("$SZABLON_PATH/td.h")) 
{
	if ($this_editmode )
	{
		

    // ustawienie strza�ek w zale�no�ci od tego czy w sekcji czy header/stopka
		$alt_up=label("Move up");
		$img_up="km_iconi_arr_up";
		$alt_down=label("Move down");
		$img_down="km_iconi_arr_down";		
		if ($page_id<0 && is_Array($TD_POZIOMY_HF))
		{
			$alt_up=label("Move left");
			$img_up="km_iconi_arr_left";
			$alt_down=label("Move right");
			$img_down="km_iconi_arr_right";
		}
		
		// identyfikatory u�ywane
    $hash="kameleon_td${page_parts_index}_$WEBTD->pri";
		$hashencoded=urlencode($hash);
		$tab_name="tab".ereg_replace("-","_",$WEBTD->page_id)."_$WEBTD->pri";
		$domenuId = 'km_td_' . $WEBTD->sid;
		
		
		// informacje o module do info
		$info_text = "<table class=\"km_table\">";
		if (strlen($WEBTD->type))			$info_text .= "<tr><td>".label("Type").":</td><td>".$WEBTD->type."</td></tr>";
		if (strlen($WEBTD->level))			$info_text .= "<tr><td>".label("Level").":</td><td>".$WEBTD->level."</td></tr>";
		if (strlen($WEBTD->menu_id))		$info_text .= "<tr><td>".label("Menu").":</td><td>".$WEBTD->menu_id."</td></tr>";
		if (strlen($WEBTD->html))			$info_text .= "<tr><td>".label("Include file").":</td><td>".$WEBTD->html."</td></tr>";
		if (strlen($WEBTD->api))			$info_text .= "<tr><td>".label("Include api").":</td><td>".$WEBTD->api."</td></tr>";
		if (strlen($WEBTD->staticinclude) && ($WEBTD->staticinclude == 1))	$info_text .= "<tr><td colspan=\"2\">".label("Files included during publication")."</td></tr>";
		if (strlen($WEBTD->bgimg) && !$WEBTD->swfstyle)			$info_text .= "<tr><td>".label("Background image").":</td><td>".$WEBTD->bgimg."</td></tr>";
		if (strlen($WEBTD->bgimg) && $WEBTD->swfstyle)			$info_text .= "<tr><td>".label("Macromedia SWF file").":</td><td>".$WEBTD->bgimg."</td></tr>";
		if (strlen($WEBTD->bgcolor))		$info_text .= "<tr><td>".label("Background color").":</td><td>".$WEBTD->bgcolor."</td></tr>";
		if (strlen($WEBTD->align))			$info_text .= "<tr><td>".label("Horizontal align").":</td><td>".$WEBTD->align."</td></tr>";
		if (strlen($WEBTD->valign && !$WEBTD->swfstyle))			$info_text .= "<tr><td>".label("Vertical align").":</td><td>".$WEBTD->valign."</td></tr>";
		if (strlen($WEBTD->width ))			$info_text .= "<tr><td>".label("Width").":</td><td>".$WEBTD->width."</td></tr>";
		if (strlen($WEBTD->size && $WEBTD->swfstyle))			$info_text .= "<tr><td>".label("Height").":</td><td>".$WEBTD->size."</td></tr>";
		if (strlen($WEBTD->class))			$info_text .= "<tr><td>".label("Class name").":</td><td>".$WEBTD->class."</td></tr>";
		if (strlen($WEBTD->img) && !$WEBTD->swfstyle)			$info_text .= "<tr><td>".label("Title image").":</td><td>".$WEBTD->img."</td></tr>";
		if (strlen($WEBTD->img) && $WEBTD->swfstyle)			$info_text .= "<tr><td>".label("Image parameter").":</td><td>".$WEBTD->img."</td></tr>";
		if (strlen($WEBTD->more))			$info_text .= "<tr><td>".label("More").":</td><td>".$WEBTD->more."</td></tr>";
		if (strlen($WEBTD->next))			$info_text .= "<tr><td>".label("Next page").":</td><td>".$WEBTD->next."</td></tr>";
		if (strlen($WEBTD->size && !$WEBTD->swfstyle))			$info_text .= "<tr><td>".label("Size").":</td><td>".$WEBTD->size."</td></tr>";
		if (strlen($WEBTD->cos))			$info_text .= "<tr><td>".label("Number parameter").":</td><td>".$WEBTD->cos."</td></tr>";
		if (strlen($WEBTD->costxt))			$info_text .= "<tr><td>".label("Text parameter").":</td><td>".$WEBTD->costxt."</td></tr>";
		if ($WEBTD->valid=='f')	
		{
			$info_text .= "<tr><td colspan=\"2\">".label("Module date activity").":</td></tr>";
			if (strlen($WEBTD->nd_valid_from))	$info_text .= "<tr><td>".label("valid from").":</td><td>". FormatujDate($WEBTD->nd_valid_from, 'd-m-Y H:i')."</td></tr>";
			if (strlen($WEBTD->nd_valid_to))		$info_text .= "<tr><td>".label("valid to").":</td><td>". FormatujDate($WEBTD->nd_valid_to, 'd-m-Y H:i')."</td></tr>";
		}
		$info_text.="</table>";
		
    $info="";//<li class=\"km_r\"><a class=\"km_icon km_icontd_info\" target=\"swf-debug\" href=\"".."\">".$info_text."</a></li>";	
		$delete="<li class=\"km_r\"><a class=\"km_icon km_icontd_delete\" href=\"javascript:km_module_delete(".$WEBTD->sid.",".$WEBTD->page_id.")\" title=\"".label("Delete")."\">".label("Delete")." ".$WEBTD->title."</a></li>";
		$edit="<li><a class=\"km_icon km_icontd_edit\" href=\"javascript:tdedit($WEBTD->page_id,$WEBTD->sid,'$hashencoded','$tab_name')\" title=\"".label("Edit")."\">".label("Edit")."</a></li>";
		$copy="<li><a class=\"km_icon km_icontd_copy\" href=\"#\" onclick=\"skopiuj('$WEBTD->sid','td','".addslashes(strip_tags($WEBTD->title))." [$page]')\" title=\"".label("Copy")."\">".label("Copy")." ".strip_tags($WEBTD->title)."</a></li>";
		$new="<li><a href=$SCRIPT_NAME?page=$page&page_id=$page_id&_level=$WEBTD->level&_type=$WEBTD->type&action=DodajTD#$hash><img class=\"k_imgbutton\" src=\"img/i_newmodule_n.gif\" onmouseover=\"this.src='img/i_newmodule_a.gif'\" onmouseout=\"this.src='img/i_newmodule_n.gif'\" width=17 height=17  border=0 alt='$insert_td'></a></li>";
    
    if ($drag_disable==true)
    {
      $up="<li><a class=\"km_icon ".$img_up."\" href=\"$SCRIPT_NAME?table=td&page=$page&pole=page_id&wart=$WEBTD->page_id&pri=$WEBTD->pri&dir=up&action=Move\" title=\"".$alt_up."\" >".$alt_up."</a></li>";
		  $down="<li><a class=\"km_icon ".$img_down."\" href=\"$SCRIPT_NAME?table=td&page=$page&pole=page_id&wart=$WEBTD->page_id&pri=$WEBTD->pri&dir=down&action=Move\" title=\"".$alt_down."\">".$alt_down."</a></li>";
		  $dragicon="";
    }
    else
    {
      $up=$down="";
      $dragicon="<li><a class=\"km_icon km_icontd_dragdrop km_dragicon\" title=\"".label("Drag & Drop")."\">".label("Drag & Drop")."</a></li>";  
    }
	
	$showmenu = "<li class=\"km_r\"><a class=\"km_icon km_icontd_showmenu\" title=\"".label("Show context menu")."\">".label("Show context menu")."</a></li>"; 

		if ($WEBTD->hidden)
		{
		  $vis_lab=label("Module invisible");
			if ($WEBTD->hidden==100) $vis_lab=label("Module ready to delete");
			$vis_class="km_contenxtmenu_menu_visibility_off";
			$vis_class2="km_icontd_visible_off";
		}
		else	
		{
			if ($WEBTD->valid=='f')
			{
        $vis_lab = label("Module visible, but expired");
        $vis_class="km_contenxtmenu_menu_visibility_d";
        $vis_class2="km_icontd_visible_d";
      }
			else
			{
			  $vis_lab=label("Module visible");
				$vis_class="km_contenxtmenu_menu_visibility_on";
        $vis_class2="km_icontd_visible_on";      
      }
		}

		$svn_cmd="";
		eval("\$KAMELEON_UINCLUDES=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
		if ( substr($WEBTD->html,0,1)!='@' && strlen($WEBTD->html) && strlen($SERVER->svn) && (@is_writable($KAMELEON_UINCLUDES.'/'.$WEBTD->html) || @is_writable($KAMELEON_UINCLUDES) && !file_exists($KAMELEON_UINCLUDES.'/'.$WEBTD->html)) )
		{

		
			eval("\$KAMELEON_UINCLUDES_SVN=\"$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN\";");

			$html=$WEBTD->html;
			if ( !file_exists("$KAMELEON_UINCLUDES_SVN/$html") )
			{
				$svn_cmd="<li><a class=\"km_icon km_iconi_svn_start\" href=\"#\" onclick=\"svn_start('$html')\" title=\"".label("Start editing include file").": ".$html."\">".label("Start editing include file").": $html</a></li>";

			}
			else
			{
				$svn_cmd="<li><a class=\"km_icon km_iconi_svn_end\" href=\"#\" onclick=\"svn_end('inc')\" title=\"".label("File being edited, click to confirm changes")."\" >".label("File being edited, click to confirm changes")."</a></li>";

			}

			$svn_cmd="$svn_cmd";

		}

		if ($WEBTD->menu_id)
		{
			$menu_id="<li><a class=\"km_icon km_iconi_menu_m\" href=\"menus.".$KAMELEON_EXT."?menu=".$WEBTD->menu_id."&setreferpage=".$page."\" title=\"".label("Menu").": ".$WEBTD->menu_id."\" />".label("Menu").": ".$WEBTD->menu_id."</a></li>";
			if ($C_CONTENT_EDITABLE) $menu_id.= "<li><a class=\"km_icon km_icontd_menuadd\" onclick=\"km_link_add(".$WEBTD->sid.")\" title=\"".label('Add item to menu')."\">".label('Add item to menu')."</a></li>";
		} 
		else
			$menu_id="";
            
	$visibility="<li class=\"km_r\"><a class=\"km_icon km_modul_visible ".$vis_class2."\" onclick=\"km_module_visible('".$WEBTD->sid."')\" title=\"".$vis_lab."\">".$vis_lab."</a></li>";			
			
    if ($WEBTD->hidden==true || ($WEBTD->hidden==false && $WEBTD->valid=='f')) 
    {
      
    }
    else
    {
      //$visibility="";
      if ($WEBTD->next) 
  			$visibility.="<li><a class=\"km_icon km_iconi_next_m\" href=\"$SCRIPT_NAME?page=$WEBTD->next&referer=$page\" title=\"".label('Next page')."\">".label('Next page')."</a></li>";		
          
      if ($WEBTD->more) 
  			$visibility.="<li><a class=\"km_icon km_iconi_more_m\" href=\"$SCRIPT_NAME?page=$WEBTD->more&referer=$page\" title=\"".label('More')."\">".label('More')."</a></li>";		 
    }
			
		global $drag_disable;
		if ($drag_disable==true) $classdrag="";



		//$onContextMenu=" oncontextmenu=\"return showMenu('TD', '".$WEBTD->page_id."', '".$WEBTD->sid."', '".$WEBTD->server."', '".$WEBTD->type."', '".$WEBTD->level."', '".$WEBTD->api."', '".$WEBTD->pri."', '".$WEBPAGE->id."', '".$hashencoded."', '".$tab_name."', '".$WEBTD->hidden."', '".$WEBTD->valid."', '".$SCRIPT_NAME."', '".$WEBTD->menu_id."', '".addslashes($WEBTD->title)."', event )\"";
    $configurations = "<li class=\"km_r\"><a class=\"km_icon km_icontd_info\" title=\"".label('Module configuration')."\">".label('Module configuration')."</a></li>"; 
		
		$buttons="$dragicon$showmenu$up$down$edit$copy".$next.$menu_id.$more;
		$classdrag=" class=\"km_dragitem km_dragbox\" id=\"km_dragbox_".$WEBTD->sid."\" ";		

		if ( !$AUTH_BY_ACL_PLUGIN && $WEBTD->accesslevel > $kameleon->current_server->accesslevel)
		{
			$link=label("No rights to edit this module");
			$buttons="$link";
			$visibility="";
			$delete="";
			$info="";
			$onContextMenu="";
			$configurations="";
		}


		if ($WEBTD->page_id != $page_id)
		{
			$link=label("Link from page");
			$buttons="<li class='km_nodragbox_link'><a href=\"$SCRIPT_NAME?page=$WEBTD->page_id\" title='pri:$WEBTD->pri,level:$WEBTD->level,ver:$WEBTD->ver'>$link: $WEBTD->page_id</a></li>";
			$visibility="";
			$delete="";
			$info="";
			$onContextMenu="";
			$classdrag=" class=\"km_nodragbox\"";
			$configurations="";
		}
	
		if ($WEBTD->ver != $ver)
		{
			$buttons="<li class='km_nodragbox_link' title='pri:$WEBTD->pri,level:$WEBTD->level,page:$WEBTD->page_id'>".label("No module in this version")." ($WEBTD->ver)</li>";
			$visibility="";
			$delete="";
			$onContextMenu="";
			$classdrag=" class=\"km_nodragbox\"";
			$configurations="";

		}
		
		//if ($helpmode && $WEBTD->page_id==$page_id && $WEBTD->ver==$ver) 
		//	echo "<tdhelp name=\"h_$tab_name\">";

/*
		if ($CONST_MODE=="express")
		{
			$info="";
			$down="";
			$up="";
			$onContextMenu="";
			echo "
			<div title=\"".$WEBTD->sid.",".$WEBTD->pri."\" \"".$classdrag."\">
      <a name=$hash></a>
					<table id='$tab_name'
						onmouseover=\"wk_show('id_td_$tab_name');  this.style.border='2px dotted $SERVER->editbordercolor';event.cancelBubble = true;\" 
						onMouseOut=\"wk_hide('id_td_$tab_name'); this.style.border='0px dotted $SERVER->editbordercolor';event.cancelBubble = true;\"
						width=100% border=0 cellspacing=0 cellpadding=0>";
			echo "<tr>
				<td nowrap>
				<div  id='id_td_$tab_name' style='display:none;visibility:hidden;position:absolute;'>
					<table border='1' cellspacing='0' cellpadding='0' width=100%>
						<tr>
						 <td nowrap bgcolor=\"$bg_color\" class=k_td> $new $buttons $main_buttons </td>
						 $info $visibility $delete
						</tr>
					</table>
				</div>
				</td>
			  </tr>";

		}
		else 
		{
*/

		echo "
    <div title=\"".$WEBTD->sid.",".$WEBTD->pri."\" ".$classdrag.">
    <a name=\"$hash\"></a>
    	<script type=\"text/javascript\">
			identyfiersArray['".$WEBTD->sid."']='".$WEBTD->uniqueid."';
			jQueryKam(document).ready(function(jQueryKam){
				 jQueryKam('#".$domenuId."').addcontextmenu('km_jsdomenu','".(substr($WEBTD->page_id,0,1)=="-" ? "hf" : "td")."', '".$WEBTD->page_id."', '".$WEBPAGE->id."', '".$WEBTD->sid."', '".$WEBTD->server."', '".(int)$WEBTD->type."', '".$WEBTD->level."', '".$WEBTD->api."', '".$WEBTD->pri."', '".$WEBPAGE->id."', '".$hashencoded."', '".$tab_name."', '".$WEBTD->hidden."', '".$WEBTD->valid."', '".$SCRIPT_NAME."', '".$WEBTD->menu_id."', '".addslashes($WEBTD->title)."','".($WEBTD->swfstyle ? urldecode(kameleon_mode_swf_link($WEBTD)) : "" )."', '".$WEBTD->more."', '".$WEBTD->next."', '".$SCRIPT_NAME."', '".$KAMELEON_EXT."', '".label('Show/hide module')."','".$vis_class."');
	      	});
		</script>
      <div class=\"km_smallbar\"  id=\"".$domenuId."\"><ul>". $buttons . $bg_color . $svn_cmd . $delete  . $configurations . $visibility . $info . $savebtn . "</ul></div>
      <div id=\"km_infotd_".$WEBTD->sid."\" class=\"km_infotd\">".$info_text."</div>
      <div class=\"km_tdin\" id=\"".$tab_name."\">";

		//$align="";$valign="";$class="";$width="";
		//if (strlen($WEBTD->align)) $align="align=\"$WEBTD->align\"";
		//if (strlen($WEBTD->valign)) $valign="valign=\"$WEBTD->valign\"";
		//if (strlen($WEBTD->width)) $width="width=\"$WEBTD->width\"";
		//echo "<tr><td $align $valign $width id=\"cid_td_$tab_name\"><div id=\"div_td_$tab_name\">\n";
	}

	//***** Teraz zamiana UIMAGES w czasie publikacji

	if (ereg("<[^>]+=\\\\\"[^>]*>",$WEBTD->plain)) $WEBTD->plain = stripslashes( $WEBTD->plain );

	if (eregi('<img[^>]* src="[\.\/]+img/include.gif"[^>]*>',$WEBTD->plain))
	{
		$WEBTD->plain=kameleon_include_plain($WEBTD->plain);
	}

	if (eregi('<maska',$WEBTD->plain))
	{
		$WEBTD->plain=kameleon_include_plain($WEBTD->plain);
	}

	if (!$KAMELEON_MODE)
	{
		$WEBTD->plain=ereg_replace($EREG_REPLACE_KAMELEON_UIMAGES,$UIMAGES,$WEBTD->plain);
		$WEBTD->plain=ereg_replace($KAMELEON_UFILES,$UFILES,$WEBTD->plain);
	}


	if ($C_EDITOR_FORM)
	{
		$n=$WEBTD->next;
		if (!$n) $n=$page;

		if (strstr(strtolower($WEBTD->plain),'<form')) $kameleon_editor_form_counetr++;
		$n=kameleon_href('','',$n);
		if (!ereg("<form[^>]+action=[^>]+>",$WEBTD->plain)) $WEBTD->plain=eregi_replace("<(form[^>]+)>","<\\1 action=\"$n\">",$WEBTD->plain);
		if (!ereg("<form[^>]+id=[^>]+>",$WEBTD->plain)) $WEBTD->plain=eregi_replace("<(form[^>]+)>","<\\1 id=\"kameleon_form_sid_".$WEBTD->sid."\">",$WEBTD->plain);
		if (!ereg("<form[^>]+name=[^>]+>",$WEBTD->plain)) $WEBTD->plain=eregi_replace("<(form[^>]+)>","<\\1 name=\"kameleon_form_".$kameleon_editor_form_counetr."\">",$WEBTD->plain);
	}




	//******* Znajdowanie link�w wewn�trznych
	
	$kameleon_inside_link="kameleon:inside_link(";
	
	$plain=strtolower($WEBTD->plain);

	while( strstr($plain,$kameleon_inside_link) )
	{

		$pos=strpos($plain,$kameleon_inside_link);

		$end=strpos(substr($plain,$pos),")");
		$page_target=substr($WEBTD->plain,$pos+strlen($kameleon_inside_link),$end-strlen($kameleon_inside_link));

		$pt=explode(',',$page_target);

		$href=kameleon_href("",$pt[1],$pt[0]);

		$WEBTD->plain=substr($WEBTD->plain,0,$pos)."$href".substr($WEBTD->plain,$pos+$end+1);		
		
		$plain=strtolower($WEBTD->plain);
	}


	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_from)
		echo "\n<?php if (".$WEBTD->nd_valid_from."<time()) { ?>\n";

	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_to)
		echo "\n<?php if (time()<".$WEBTD->nd_valid_to.") { ?>\n";


	if (!$WEBTD->hidden && !$this_editmode && ($WEBTD->ob & 1) )
	{
		if ($KAMELEON_MODE || $WEBTD->staticinclude) include('remote/ob_start.h');
		else echo read_file('remote/ob_start.h');
	}

	if (!$WEBTD->hidden || $this_editmode) 
	{
		if ($WEBTD->swfstyle)
		{
			global $ORYGINAL_WEBTD;
			$ORYGINAL_WEBTD=$WEBTD;

			if (strlen($WEBTD->html)) $WEBTD->staticinclude=1;
	
			$plain=kameleon_td2swf_obj($WEBTD);

			$WEBTD->plain=$plain;
			$WEBTD->menu_id='';
			$WEBTD->html='';
			$WEBTD->api='';	
			$WEBTD->title='';
			$WEBTD->more='';	
			$WEBTD->next='';	
			$WEBTD->img='';	
			$WEBTD->bgimg='';
		}
		$WEBTD->title_edit = ($this_editmode && $WEBTD->page_id==$WEBPAGE->id) ? "<acronym title=\"".label("Right click to edit")."\" class=\"km_title_edit\" id=\"km_title_".$WEBTD->sid."\">".$WEBTD->title."</acronym>" : $WEBTD->title;
	
		if ($CONST_PARSER_INTEGRATED) 
		{
			include("include/parser_td.h");
		}
		else
		{
			include("$SZABLON_PATH/td.h");
		}
		
	}
	$ORYGINAL_WEBTD=null;

	if (!$WEBTD->hidden && !$this_editmode && ($WEBTD->ob & 2) )
	{
		if (($WEBTD->ob & 1) && ($WEBTD->ob & 2) && strlen($WEBTD->api) && !strstr($WEBTD->api,'kameleon') ) 
		{
			if ($KAMELEON_MODE || $WEBTD->staticinclude) ob_end_clean();
			else echo '<? ob_end_clean(); ?>';
			
		}
		else
		{
			if ($KAMELEON_MODE || $WEBTD->staticinclude) include('remote/ob_end.h');
			else echo ereg_replace("[\r\n\t]+"," ",read_file('remote/ob_end.h'));
		}
	}

	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_from)
		echo "\n<?php } ?>\n";
	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_to)
		echo "\n<?php } ?>\n";
	
	if ($this_editmode )
	{
		echo "\n</div></div>\n";	
	}
}
$this_editmode=pop();

?>
