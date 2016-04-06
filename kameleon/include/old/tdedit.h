<?
	if ($C_MIKOLAJ_EXPERIMENTAL)
	{
		include("include/tdedit_mikolaj.h");
		return;
	}


?>
<html>
<head>
    <title>KAMELEON: <?echo label("WebKameleon Editor");?></title>
    <link href="kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">

<?	//include ("include/tree_js.h"); ?>

</head>
<body bgcolor="#c0c0c0" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>
<?

  include("include/helpbegin.h");

  if (!$editmode) $WEBPAGE->title=label("WebKameleon Editor");

  if (!strlen($page)) $page=$page_id;

  include("include/userclass.h");
  include("include/usercolors.h");

  $td_width+=0;
  if (!$td_width) $td_width="100%";
  else
	$td_width+=34; //bo trzeba dodac padding

  if (strlen($u_color) && $u_color[0]!="#")  $u_color="#$u_color";

  $aligns[]=array("",label("Choose"));
  $aligns[]=array("left",label("Align left"));
  $aligns[]=array("right",label("Align right"));
  $aligns[]=array("center",label("Center"));
  $aligns[]=array("justify",label("Justify"));

  $valigns[]=array("",label("Choose"));
  $valigns[]=array("top",label("Top"));
  $valigns[]=array("middle",label("Middle"));
  $valigns[]=array("bottom",label("Bottom"));

  $filename = "$UIMAGES/$DEFAULT_TEXTFILE_CSS";
  if (!file_exists($filename)) $filename = "$IMAGES/textstyle.css";
  $fd = fopen( $filename, "r" );
  $stylecontent = fread( $fd, filesize( $filename ) );
  fclose( $fd ); 

  //if ($BASIC_RIGHTS) $UIMAGES.="/$USERNAME";
  
  $label_link=label("Issue page number");

  $my_host="http";
  if ($HTTPS) $my_host.="s";
  $my_host.="://$HTTP_HOST";
  if (dirname($SCRIPT_NAME)!="." && dirname($SCRIPT_NAME)!="/") $my_host.=dirname($SCRIPT_NAME);

?>
<SCRIPT>
	UIMAGES='<? echo "$my_host/$UIMAGES"?>'; 
	TDEDIT_WIDTH='<?echo $td_width?>'; 
	TEXTSTYLE='<?echo $filename;?>';
	UFILES='<? echo "$my_host/$UFILES"?>';
	label_prompt_cols='<? echo label("Issue number of columns");?>';
	label_prompt_rows='<? echo label("Issue number of rows");?>';
	label_prompt_width='<? echo label("Issue table width (%)");?>';
	label_prompt_border='<? echo label("Issue table border width");?>';
</SCRIPT>

<?	include ("include/tree_js.h"); ?>


<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="0">
<? if ($editmode) {?>
<tr>
 <td>
  <? if ($CONST_MODE!="express") include("include/navigation.h");?>
 </td>
</tr>
<?}?>
<tr>
 <td>    
 <table bgcolor="silver" valign=top width="100%" border="3" cellspacing="0" cellpadding="0">
<?
	if (strtolower($pri)=="max")
	{
		$query="SELECT max(pri) AS _pri FROM webtd
			WHERE  server=$SERVER_ID AND ver=$ver 
			AND lang='$lang' AND page_id=$page_id";
		parse_str(ado_query2url($query));		
		$pri=$_pri;
	}
?>



 <form method=post action=index.<?echo $KAMELEON_EXT?>#<?echo $hash?> name="edytujtd" id="edytujtd_id" onSubmit="ZapiszZmiany()">
  <input type=hidden name=page_id value="<?echo $page_id?>">
  <input type=hidden name=page value="<?echo $page?>">
  <input type=hidden name=pri value="<?echo $pri?>">
  <input type=hidden name=action value="">
  
<?

	if ($editmode)
	{
		
		$query="SELECT * FROM webtd
			WHERE  server=$SERVER_ID AND ver=$ver 
			AND lang='$lang' AND page_id=$page_id AND pri=$pri";

		parse_str(ado_query2url($query));
	}
	$title=stripslashes($title);


	$plain=stripslashes($plain);
	$costxt=stripslashes($costxt);

	if ($html[0]=="@")
	{
		$module=$html;
		$html="";

		$obj=html_txt2html_obj($module);

		$bn=basename($module);
		$dn=dirname($module);
		$m_name=substr($dn,1);

		if (is_Object($obj))
		{
			$MODULE_NAME=kameleon_global($obj->label);
			$MODULE_PATH="modules/$dn";
			if (file_exists("modules/$dn/.$bn")) $MODULE_ADV="modules/$dn/.$bn";
			if (is_array( $obj->var ))
			{
				reset ($obj->var);
				while ( list( $v_key, $v_val ) = each($obj->var) )
				{
					$cmd="\$$v_key = \"$v_val\" ; ";
					eval($cmd);
				}
			}
		}
	}

?>
<tr>
	<td align="left" width="100%" background="zakl01n_back.gif"></td>
</tr>
<? if ($C_SHOW_TD_TITLE && $editmode) 
	{ 
		$input_title_id=$helpmode?"id=\"help_title_input\"":"";
		if ($CONST_MODE!="express")
		{
?>
<tr>
	  <td class=k_td><?echo label("Title")?>:<br><input style='width:<?echo $td_width?>;' class='k_input' type=text name=title value="<?echo ereg_replace("\"","&quot;",$title)?>" <?echo $input_title_id?>></td>
</tr>
<?	
		}	
	}
?>
 <tr>
  <td>

<?
	include_js("tdedit");
?>

<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="0">
 <tr>
  <td align="left" width="100%" background="zakl01n_back.gif">
    <!-- ZAKLADKI -->
     <table border="0" cellspacing="0" cellpadding="0">
         <tr>
			<? if ($CONST_MODE!="express") {?>

				<td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img ></td>
				<td background=img/zakl01n_middle.gif nowrap>
					<? 
						if ($editmode) $zapisz_zmiany="ZapiszZmiany()";
						else $zapisz_zmiany="ZapiszZmianyZamknij()";

						$save_href_id=$helpmode?"id=\"help_td_save\"":"";
					?>
					<a href="javascript:<?echo $zapisz_zmiany?>" <?echo $save_href_id?> class=k_zakl01>&nbsp;<?echo label("Save and exit")?>&nbsp;</a>
				</td>
				<td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img ></td>

			
				<td><img src=img/zakl01a_left.gif width=2 height=20 border=0 name=zakLeftWysiwyg></td>
				<td id=zakMiddleWysiwyg background=img/zakl01a_middle.gif nowrap><a href="javascript:showWysiwyg()" class=k_zakl01>&nbsp;<?echo label("WYSIWYG")?>&nbsp;</a></td>
				<td><img src=img/zakl01a_right.gif width=2 height=20 border=0 name=zakRightWysiwyg></td>

				<td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img  name=zakLeftHtml></td>
				<td id=zakMiddleHtml background=img/zakl01n_middle.gif nowrap><a href="javascript:showHtml()" class=k_zakl01>&nbsp;<?echo label("HTML")?>&nbsp;</a></td>
				<td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img  name=zakRightHtml></td>
			<?}?>
			<? if ($editmode && $CONST_MODE!="express") {?>
				<td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img   name=zakLeftAdv></td>
				<td id=zakMiddleAdv background=img/zakl01n_middle.gif nowrap>
					<a href="javascript:showAdvanced()" class=k_zakl01>&nbsp;<?echo label("Options")?>&nbsp;</a>
				</td>
				<td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img   name=zakRightAdv></td>
			<?}?>

			<? if (strlen($MODULE_ADV) && $CONST_MODE!="express") {?>
	            <td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img  name=zakLeftModAdv></td>
		        <td id=zakMiddleModAdv background=img/zakl01n_middle.gif nowrap><a href="javascript:showModAdv(0)" class=k_zakl01>&nbsp;<?echo strlen($MODULE_NAME)?$MODULE_NAME:label("Module options")?>&nbsp;</a></td>
			    <td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img  name=zakRightModAdv></td>
			<?} else {?>
		        <td style="visibility:hidden;display:none" id=zakMiddleModAdv><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img  name=zakLeftModAdv><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img  name=zakRightModAdv></td>
			<?} ?>

			<? if ($CONST_MODE!="express") {?>

            <td width="100%" align="right" class="k_td">
            <?
                if (strlen($menu_id)>0) echo "<b>".label('Menu').": <a href=menus.$KAMELEON_EXT?menu=$menu_id>$menu_id</a></b>&nbsp;&nbsp;"; 
                if (strlen($html)>0) echo label('Plik').": $html&nbsp;&nbsp;"; 
            ?>
            </td>
			<?	}?>
		
		</tr>
        </table>
       <!-- ZAKLADKI END-->
    </td>
 </tr>
</table>
<table id=toolbar border=0 cellpadding=0 cellspacing=0>
<tr>
 <td>
  <!-- <img src=img/i_save_n.gif onClick="ZapiszZmiany()" style="cursor:hand;" border=0 alt='<?echo label("Save changes")?>' width=23 height=22>
  <img src=img/i_separator.gif border=0 width=4 height=22> -->
  <img class=k_imgbutton src=img/i_cut_n.gif onClick="styl('cut');" style="cursor:hand;" border=0 alt='<?echo label("Cut")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_copy_n.gif onClick="styl('copy')" style="cursor:hand;" border=0 alt='<?echo label("Copy")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_paste_n.gif onClick="styl('paste')" style="cursor:hand;" border=0 alt='<?echo label("Paste")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_bold_n.gif onClick="styl('bold');" style="cursor:hand;" border=0 alt='<?echo label("Bold")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_italic_n.gif onClick="styl('italic')" style="cursor:hand;" border=0 alt='<?echo label("Italic")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_underline_n.gif onClick="styl('underline')" style="cursor:hand;" border=0 alt='<?echo label("Underline")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_left_n.gif onClick="styl('justifyleft')" style="cursor:hand;" border=0 alt='<?echo label("Align left")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_center_n.gif onClick="styl('justifycenter')" style="cursor:hand;" border=0 alt='<?echo label("Center")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_right_n.gif onClick="styl('justifyright')" style="cursor:hand;" border=0 alt='<?echo label("Align right")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_dentin_n.gif onClick="styl('indent')" style="cursor:hand;" border=0 alt='<?echo label("Increase indent")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_dentout_n.gif onClick="styl('outdent')" style="cursor:hand;" border=0 alt='<?echo label("Decrease indent")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_ul_n.gif onClick="styl('insertunorderedlist')" style="cursor:hand;" border=0 alt='<?echo label("Bulleted list")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_ol_n.gif onClick="styl('insertorderedlist')" style="cursor:hand;" border=0 alt='<?echo label("Numbered list")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_outeranchor_n.gif onClick="kameleon_outer_link('<?echo $label_link?>')" style="cursor:hand;" border=0 alt='<?echo label("Create or edit outside hyperlink")?>' width=23 height=22 id="help_olink_icon">
  <?if ($editmode) {?>
  <img class=k_imgbutton src=img/i_inneranchor_n.gif onClick="kameleon_inner_link('<?echo $label_link?>')" style="cursor:hand;" border=0 alt='<?echo label("Create or edit inside hyperlink")?>' width=23 height=22 id="help_ilink_icon">
  <?}?>
  <?if ($editmode || strlen($SetServer) ) 
  {
	   $i_image_id=$helpmode?"id=\"help_image_icon\"":"";
		
	?>
  <img class=k_imgbutton <?echo $i_image_id?> src=img/i_image_n.gif onClick="obrazekGaleria('<?echo "ufiles.$KAMELEON_EXT?page=$page&galeria=2"?>')" style="cursor:hand;" border=0 alt='<?echo label("Insert or edit image")?>' width=23 height=22>
  <?} else {?>
  <img class=k_imgbutton src=img/i_image_n.gif onClick="obrazekBezGalerii()" style="cursor:hand;" border=0 alt='<?echo label("Insert or edit image")?>' width=23 height=22>
  <?}?>
  <img class=k_imgbutton src=img/i_file_n.gif onClick="plikGaleria('<?echo "ufiles.$KAMELEON_EXT?page=$page&galeria=1"?>')" style="cursor:hand;" border=0 alt='<?echo label("Attach file")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_table_n.gif onClick="insertTable()" style="cursor:hand;" border=0 alt='<?echo label("Insert simple table")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_bookmark_n.gif onClick="styl('createbookmark',prompt('Podaj nazwê ?','name')),true" style="cursor:hand;" border=0 alt='<?echo label("Bookmark")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_hr_n.gif onClick="styl('inserthorizontalrule')" style="cursor:hand;" border=0 alt='<?echo label("Horizontal line")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_resize_n.gif onClick="td_resize(this)" style="cursor:hand;" border=0 alt='<?echo label("Switch edytor width")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_nostyle_n.gif onClick="styl('RemoveFormat')" style="cursor:hand;" border=0 alt='<?echo label("Remove format")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <a href="<?echo $HELP_LINK?>"><img src=img/i_help_n.gif alt="<? echo label("Help")?>" border=0 width=23 height=22></a>


 </td>
</tr>
<tr>
 <td valign=top>
        <select class=k_select onChange="styl('fontSize', this[this.selectedIndex].value);this.selectedIndex=0"> 
        <option class="k_option" selected><?echo label("Size")?></option>
        <option class="k_option" value="1">1</option>
        <option class="k_option" value="2">2</option>
        <option class="k_option" value="3">3</option>
        <option class="k_option" value="4">4</option>
        <option class="k_option" value="5">5</option>
        <option class="k_option" value="6">6</option>
        <option class="k_option" value="7">7</option>
        </select>
        <select class=k_select onChange="styl('fontname', this[this.selectedIndex].value);this.selectedIndex=0"> 
        <option class="k_option" selected><?echo label("Font")?></option>
        <option class="k_option" value="Arial">Arial</option>
        <option class="k_option" value="Courier">Courier</option>
        <option class="k_option" value="Courier New">Courier New</option>
        <option class="k_option" value="Thaoma">Thaoma</option>
        <option class="k_option" value="Times New Roman">Times New Roman</option>
        <option class="k_option" value="Verdana">Verdana</option>
        </select>		
<?if ($CONST_MODE!="express") {?>
        <select class=k_select onChange="insertClass(this[this.selectedIndex].value);this.selectedIndex=0"> 
<?
        for ($c=0;$c<count($USERCLASS);$c++)
        {
         $row=$USERCLASS[$c];
         $kam_klasy=$row[0];
         if ($c==0)
         	echo "<option value=''>".label("Choose style")."</option>\n";
         else
			echo "<option value='$kam_klasy'>$kam_klasy</option>\n";
        }
?>
        </select>
<? } //if ($CONST_MODE!="express")?>

		<select class="k_select" onChange="styl('forecolor', this[this.selectedIndex].value);this.selectedIndex=0"> 
        <option class="k_option" selected style="background-color:white;" value=""><?echo label("Color")?></option>
<? 


 $len=count($USERCOLORS);
 $mod=1;
 for ($i=0;$i<$len;$i++)
 {
  $color=$USERCOLORS[$i];
  $option.="<option class=\"k_option\" style='background-color:$color' value='$color'>&nbsp;&nbsp;</option>\n";
 }
 echo $option;
?>
        </select>			  
        <img src=img/i_colors_n.gif onClick="openColors('#FFFFFF','<?echo $KAMELEON_EXT?>')" style="cursor:hand;" onmouseover="this.src='img/i_colors_a.gif'" onmouseout="this.src='img/i_colors_n.gif'" border=0 alt='<?echo label("Text color")?>'  width=23 height=22 align=absmiddle>
 </td>
</tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 ID="roolerbar">
<? if ($CONST_MODE!="express") {?>
<tr>
 <td background="img/podzialka.gif">
    <img src="img/spacer.gif" width=1 height=19 border=0>
 </td>
</tr>
<? }
	if ($CONST_MODE=="express") {?>
<tr>
<td class=k_td><?echo label("Title")?>:<br><input style='width:<?echo $td_width?>;border-style:none;' type=text name=title value="<?echo ereg_replace("\"","&quot;",$title)?>" <?echo $input_title_id?>></td>
</tr>
<?	}?>
</table>

<table bgcolor="silver" width="100%" border="1" cellspacing="0" cellpadding="0">
<tr>
 <td>
  <table ID="wysiwyghtml" style="display:inline;visibility:visible" border=0 cellspacing=0 cellpadding=0 width='100%' bordercolor=<?echo $SERVER->editbordercolor?>>
    <tr>
    	<td  height=400>
        <textarea style="display:none;visibility:hidden;width:100%;height:100%;border-style:none;" wrap=virtual name=plain><?echo $plain?></textarea>
        <iframe style="display:inline;visibility:visible;width:<?echo $td_width?>;height:100%;z-index:50" scrolling="yes" id="edytor" frameborder=0></iframe>
		<?if (!$editmode){?>
		<script>
			document.edytujtd.plain.value=window.clipboardData.getData("Text");
		</script>			
		<?}
			include_js("tdeditinit");
		?>
    	</td>
    </tr>
  </table>
<?
$C_SHOW_TD_INSIDE = $C_SHOW_TD_MENU | $C_SHOW_TD_MENU | $C_SHOW_TD_HTML | $C_SHOW_TD_API;
$C_SHOW_TD_DESIGN = $C_SHOW_TD_BGIMG | $C_SHOW_TD_BGCOLOR | $C_SHOW_TD_ALIGN | $C_SHOW_TD_VALIGN | $C_SHOW_TD_CLASS	| $C_SHOW_TD_WIDTH;
$C_SHOW_TD_POS_TYPE = $C_SHOW_TD_TYPE | C_SHOW_TD_LEVEL | $C_SHOW_TD_IMG;
$C_SHOW_TD_NAVIGATION = $C_SHOW_TD_MORE | $C_SHOW_TD_NEXT | $C_SHOW_TD_SIZE | $C_SHOW_TD_COS | $C_SHOW_TD_COSTXT;
?>
  <table ID="advanced" style="display:none;visibility:hidden;" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
<? if ($C_SHOW_TD_INSIDE ) { ?>   
   <tr>
    <td colspan=2 class=k_formtitle><?echo label("Inside")?>:</td>
   </tr>
<? } ?>
<? if ($C_SHOW_TD_MENU ) { ?>   
   <tr class=k_form>
    <td align=right><?echo label("Menu")?>: </td>
    <td><select class="k_select" style="width: 250px;" size=1 name=menu_id>
    	<option class="k_option" value="0"'><? echo label("Select menu")?></opiton>
		<option class="k_option" value="-1"><? echo label("New menu")?></opiton>
		<?
			if ($menu_id) 
				echo "<option class=\"k_option\" style='background-color:Silver' value='$menu_id' selected>menu $menu_id</opiton>";
			include ("include/menu_options.h");
		?>
		</select></td>
   </tr>
<? } ?>
<? if ($C_SHOW_TD_HTML ) { ?>
   <tr class=k_form>
    <td align=right><?echo label("Include file")?>: </td>
    <td><input class='k_input' type=text  style="width: 250px;" name=html 
		<? if (is_Array($C_MODULES)) {?>
			onChange="document.edytujtd.module.selectedIndex=0; old_selection=0" 
		<? }?>
		value="<?echo $html?>"></td>
   </tr>
<?}else {?>
	<input type=hidden name=html value="<?echo $html?>">
<? } ?>
<? if (!$BASIC_RIGHTS && $C_SHOW_TD_API ) { ?>
   <tr class=k_form>
    <td align=right><?echo label("Include api")?>: </td>
    <td><? echo CreateFormField(array("",1,"select",api,$api,$APIS)) ?></td>
   </tr>
<?}else {?>
	<input type=hidden name=api value="<?echo $api?>">   
<?}?>
<? if (!$BASIC_RIGHTS && $C_SHOW_TD_HTML && is_Array($C_MODULES) ) { ?>
   <tr class=k_form>
    <td align=right><?echo label("Include kameleon module")?>: </td>
    <td><select class="k_select" style="width: 250px;" name=module onChange="module_selected()">
	<option class=k_option value=""><? echo label("Select module")?></opiton>
	<?
		for ($_m=0;$_m<count($C_MODULES);$_m++)
		{	
			$m_name=$C_MODULES[$_m];
			if (!is_Object($MODULES->$m_name->files)) continue;
			$m_upname=strtoupper($C_MODULES[$_m]).":";
	
			echo "<option class=k_option value='-'>$m_upname</opiton>\n";
			reset ($MODULES->$m_name->files);
			while ( list( $m_key, $m_val ) = each( $MODULES->$m_name->files) )
			{
				$_s="";
				$_v="@$m_name/".$m_val->file;
				$_o=kameleon_global($m_val->label);
				if ($module==$_v)
				{
					$_s=" selected";
				}
				echo "<option$_s  class=k_option value='$_v'>&nbsp; $_o</opiton>\n";		
			}


			if (file_exists($f_name)) include($f_name);
		}
	?>
    </select></td>
   </tr>
<?}?>
<? if (!$BASIC_RIGHTS && $C_SHOW_TD_STATICINCLUDE ) { ?>
   <tr class=k_form>
    <td align=right><?echo label("Files included during publication")?>: </td>
    <td><input type=checkbox class=\"k_cbx\" name=staticinclude value=1 <?if ($staticinclude) echo "checked";?>></td>
   </tr>
<?}else {?>
	<input type=hidden name=staticinclude value="<?echo $staticinclude?>">   
<?}?>
<? if ($C_SHOW_TD_DESIGN ) { ?>
   <tr><td colspan=2 class=k_formtitle><?echo label("Design")?>:</td></tr>
<?}?>
<?	if($C_SHOW_TD_BGIMG) {?>
   <tr class=k_form>
    <td align=right><?echo label("Background image")?>:</td>
    <td><input class='k_input' type=text style="width: 220px;"
		name=bgimg value="<?echo $bgimg?>">
  	<img src=img/i_image_n.gif align=absmiddle onClick="openGalery('bgimg','<?echo "ufiles.$KAMELEON_EXT?page=$page&galeria=2"?>')" 
		style="cursor:hand;" onmouseover="this.src='img/i_image_a.gif'" 
		onmouseout="this.src='img/i_image_n.gif'" 
		border=0 alt='<?echo label("Insert or edit image")?>' width=23 height=22>
    </td>
   </tr>
<?}?>
<? if($C_SHOW_TD_BGCOLOR) {?>
   <tr class=k_form>
    <td align=right><?echo label("Background color")?>:#</td>
    <td>
     <input class='k_input' type=text maxlength="6" style="width: 220px;" name=bgcolor value="<?echo $bgcolor?>">
     <img class=k_imgbutton src=img/i_colors_n.gif onClick="openColors(document.edytujtd.bgcolor.value,'<?echo $KAMELEON_EXT?>')" style="cursor:hand;" onmouseover="this.src='img/i_colors_a.gif'" onmouseout="this.src='img/i_colors_n.gif'" border=0 alt='<?echo label("Text color")?>' width=23 height=22 align=absmiddle>
    </td>
   </tr>            
<?}?>
<? if($C_SHOW_TD_ALIGN) {?>
   <tr class=k_form>
    <td align=right><?echo label("Horizontal align")?>: </td>
    <td><? echo CreateFormField(array("",1,"select","align",$align,$aligns)) ?></td>
   </tr>
<?}?>
<? if($C_SHOW_TD_VALIGN) {?>
   <tr class=k_form>
    <td align=right><?echo label("Vertical align")?>: </td>
    <td><? echo CreateFormField(array("",1,"select","valign",$valign,$valigns)) ?></td>
   </tr>
<?}?>
<? if($C_SHOW_TD_WIDTH) {?>
   <tr class=k_form>
    <td align=right><?echo label("Width")?> (<?echo label("Pixel")?> <?echo label("or")?> %):</td>
    <td><input class='k_input' type=text size=10 name=width value="<?echo $width?>"></td>
   </tr>
<?}?>
<? if($C_SHOW_TD_CLASS) {?>
   <tr class=k_form>
    <td align=right><?echo label("Class name")?></td>
    <td><? echo CreateFormField(array("",1,"select","class",$class,$USERCLASS)) ?></td>
   </tr>
<?}?>
<? if($C_SHOW_TD_POS_TYPE) {?>
   <tr><td colspan=2 class=k_formtitle><?echo label("Type and position")?>:</td></tr>
<?}?>
<? if($C_SHOW_TD_TYPE) {?>
   <tr class=k_form>
    <td align=right><?echo label("Type")?>: </td>
    <td><? echo CreateFormField(array("",1,"select",type,$type,$TD_TYPY)) ?></td>
   </tr>
<?}?>
<? if($C_SHOW_TD_LEVEL) {?>
   <tr class=k_form>
    <td align=right><?echo label("Level")?>: </td>
    <?
	$poziomy=$TD_POZIOMY;
	if ($page_id<0 && is_Array($TD_POZIOMY_HF) ) $poziomy=$TD_POZIOMY_HF;
    ?>
    <td><? echo CreateFormField(array("",1,"select","level",$level,$poziomy)) ?></td>
   </tr>
<?}?>
<? if ($C_SHOW_TD_IMG) {?>
   <tr class=k_form>
    <td align=right><?echo label("Title image")?>:</td>
    <td><input class='k_input' type=text style="width: 220px;" name=img value="<?echo $img?>">
  	<img src=img/i_image_n.gif align=absmiddle 
		onClick="openGalery('img','<?echo "ufiles.$KAMELEON_EXT?page=$page&galeria=2"?>')" style="cursor:hand;" 
		onmouseover="this.src='img/i_image_a.gif'" 
		onmouseout="this.src='img/i_image_n.gif'" 
		border=0 alt='<?echo label("Insert or edit image")?>' width=23 height=22>
    </td>
   </tr>
<? } ?>
<? if ($C_SHOW_TD_NAVIGATION) {?>
   <tr><td colspan=2 class=k_formtitle><?echo label("Navigation")?>:</td></tr>
<? } ?>
<? if ($C_SHOW_TD_MORE) {?>
   <tr class=k_form>
    <td align=right><?echo label("More")?>: </td>
    <td><input class='k_input' type=text style="width: 195px;" name=more value="<?echo $more?>">
	 <img class=k_imgbutton src=img/i_tree_n.gif onClick="openTree('more',document.edytujtd.more.value,'')" style="cursor:hand;" onmouseover="this.src='img/i_tree_a.gif'" onmouseout="this.src='img/i_tree_n.gif'" border=0 alt='<?echo label("Webpage explorer")?>' width=23 height=22 align=absmiddle>
	 <img class=k_imgbutton src=img/i_new_n.gif onClick="document.edytujtd.more.value=-1" style="cursor:hand;" onmouseover="this.src='img/i_new_a.gif'" onmouseout="this.src='img/i_new_n.gif'" border=0 alt='<?echo label("New page")?>' width=23 height=22 align=absmiddle>
	</td>
   </tr>
<? } ?>
<? if ($C_SHOW_TD_NEXT) {?>
   <tr class=k_form>
    <td align=right><?echo label("Next page")?>: </td>
    <td><input class='k_input' type=text style="width: 195px;" name=next value="<?echo $next?>">
	 <img class=k_imgbutton src=img/i_tree_n.gif onClick="openTree('next',document.edytujtd.next.value,'')" style="cursor:hand;" onmouseover="this.src='img/i_tree_a.gif'" onmouseout="this.src='img/i_tree_n.gif'" border=0 alt='<?echo label("Webpage explorer")?>' width=23 height=22 align=absmiddle>
	 <img class=k_imgbutton src=img/i_new_n.gif onClick="document.edytujtd.next.value=-1" style="cursor:hand;" onmouseover="this.src='img/i_new_a.gif'" onmouseout="this.src='img/i_new_n.gif'" border=0 alt='<?echo label("New page")?>' width=23 height=22 align=absmiddle>
	</td> 
   </tr>
<? } ?>
<? if ($C_SHOW_TD_SIZE) {?>
   <tr class=k_form>
    <td align=right><?echo label("Size")?>: </td>
    <td><input class='k_input' type=text style="width: 250px;" name=size value="<?echo $size?>"></td>
   </tr>
<? } ?>
<? if ($C_SHOW_TD_COS) {?>
   <tr class=k_form>
    <td align=right><?echo label("Number parameter")?>: </td>
    <td><input class='k_input' type=text style="width: 250px;" name=cos value="<?echo $cos?>"></td>
   </tr>
<? } ?>
<? if ($C_SHOW_TD_COSTXT) {?>
   <tr class=k_form>
    <td align=right><?echo label("Text parameter")?>: </td>
    <td><input class='k_input' type=text style="width: 250px;" name=costxt value="<?echo $costxt?>"></td>
   </tr>
<? } ?>

<? if ($C_SHOW_TD_VALID ) {?>
   <tr class=k_form>
    <td align=right><?echo label("Module date activity")?>: </td>
    <td>
		<input class='k_input' type=text style="width: 75px;" name=nd_valid_from 
			value="<?echo strlen($nd_valid_from)?FormatujDate($nd_valid_from):""?>">
		-
		<input class='k_input' type=text style="width: 75px;" name=nd_valid_to 
			value="<?echo strlen($nd_valid_to)?FormatujDate($nd_valid_to):""?>">

	</td>
   </tr>
<? } ?>


   <tr class=k_form>
    <td align=right colspan=2>
    <input class=k_imgbutton type=image src=img/i_save_n.gif onmouseover="this.src='img/i_save_a.gif'" onmouseout="this.src='img/i_save_n.gif'" border=0 width=23 height=22 alt="<?echo label("Save and exit")?>"></td>
   </tr>
  </table>
  <table ID="modadv" style="display:none;visibility:hidden;" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
  <tr><td><?
		if (strlen($MODULE_ADV)) 
		{
			push($INCLUDE_PATH); $INCLUDE_PATH=$MODULE_PATH;
			if (file_exists("$MODULE_PATH/.pre.h")) include("$MODULE_PATH/.pre.h");
 			include ($MODULE_ADV);
			if (file_exists("$MODULE_PATH/.post.h")) include("$MODULE_PATH/.post.h");
			$INCLUDE_PATH=pop();
		}
  ?></td></tr>
  </table>

 </td>
</tr>
<?
	$query="SELECT fullname AS autor FROM passwd 
			WHERE username='$autor'
			AND fullname IS NOT NULL AND fullname<>''";
	parse_str(ado_query2url($query));
	$query="SELECT fullname AS autor_update FROM passwd 
			WHERE username='$autor_update'
			AND fullname IS NOT NULL AND fullname<>''";
	parse_str(ado_query2url($query));

	$ts="";
	if (strlen($autor))
		$ts.=label("Created by")." $autor, ".FormatujDate($nd_create);
	if (strlen($autor_update))
		$ts.="<br>".label("Modified by")." $autor_update, ".FormatujDate($nd_update);

?>

<tr><td valign="Top" class="k_text"><?echo $ts?>&nbsp;<p id="status" class="k_p"></p></td></tr>
</table>

 </td>
 </tr>
 </form>
 </table>
 </td>
</tr>

</table>


<?
if ( is_Array($C_MODULES) ) { ?>

<script>
old_selection=document.edytujtd.module.selectedIndex;
function module_selected()
{
	if (document.edytujtd.module.selectedIndex!=0) document.edytujtd.html.value="";
	if (document.edytujtd.module.value=="-") 
		document.edytujtd.module.selectedIndex=old_selection;	
	else
		old_selection=document.edytujtd.module.selectedIndex;
}
</script>

<? } 


  include("include/helpend.h");
?>


</body>
</html>

<?

	if ($C_HIDE_WYSIWYG)
		echo "<script>
				document.zakLeftWysiwyg.style.display='none';
			    document.all['zakMiddleWysiwyg'].style.display='none';
				document.zakRightWysiwyg.style.display='none';
				</script>";

	if ($C_HIDE_HTML)
		echo "<script>
        document.zakLeftHtml.style.display='none';
        document.all['zakMiddleHtml'].style.display='none';
        document.zakRightHtml.style.display='none';
				</script>";

	if (strlen($MODULE_ADV)) echo "<script> showModAdv(1); </script>";
?>