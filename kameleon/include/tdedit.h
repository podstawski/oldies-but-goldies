<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>KAMELEON: <? echo label("WebKameleon Editor"); ?></title>
    <link href="<?php echo $kameleon->user[skinpath]; ?>/kameleon.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $SZABLON_PATH . '/images/textstyle.css'; ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo $UIMAGES . '/textstyle.css'; ?>" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/calendar.css" title="win2k-cold-1" />
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/tdedit.css" />
	<script type="text/javascript">
		var identyfiersArray=new Array();
	
		var km_infos = new Array();
		km_infos["ajax_link"]='<?php echo str_replace("tdedit.php","ajax.php",$SCRIPT_NAME); ?>';
		km_infos["page_link"]='<?php echo $SCRIPT_NAME."?page=".$page; ?>';
		km_infos["page"]='<?php echo $page; ?>';
		km_infos["return_link"]='<?php echo base64_encode($_SERVER["REQUEST_URI"]); ?>';	
		var km_droplist = new Array();
		km_droplist['active']="";
		
	</script>
	<?php
	switch($lang)
	{
		case "p":
		case "i":
		case "pl":
			$clang = "pl";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "d":
		case "de":
			$clang = "de";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "f":
		case "fr":
			$clang = "fr";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "r":
		case "ru":
			$clang = "ru";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "t":
			$clang = "cs";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "s":
			$clang = "es";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "h":
			$clang = "hu";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "g":
			$clang = "gr";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;


		default: 
			$clang="en";
			$ctimeFormat = '%d-%m-%Y %H:%M';
	}

	include_js("jquery-1.4");
	include_js("kameleon");
	include_js("calendar");
	include_js("calendar-$clang");
	include_js("calendar-setup");
	?>
	<script src="codemirror/lib/codemirror.js" type="text/javascript"></script>
	<link rel="stylesheet" href="codemirror/lib/codemirror.css">
	<script src="codemirror/mode/javascript/javascript.js"></script>
	<link rel="stylesheet" href="codemirror/theme/default.css">
</head>

<body>

<?
  if ($oldeditormode)
  {
	include('include/tdedit_old.h');
	return;
  }

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
  $stylecontent = @fread( $fd, filesize( $filename ) );
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
	TDEDIT_WIDTH='1200'; 
	TEXTSTYLE='<?echo $filename;?>';
	UFILES='<? echo "$my_host/$UFILES"?>';
	label_prompt_cols='<? echo label("Issue number of columns");?>';
	label_prompt_rows='<? echo label("Issue number of rows");?>';
	label_prompt_width='<? echo label("Issue table width (%)");?>';
	label_prompt_border='<? echo label("Issue table border width");?>';

	// Specjalna warto�c na potrzeby sk�r dla edytora
	var skinName = '<?php echo $kameleon->user['skin']?>';


	
</SCRIPT>


<?	 include ("include/tree_js.h"); ?>



<? 
  if ($editmode) 
  {
    if ($CONST_MODE!="express") include("include/navigation.h");
  }
?>

<?
	if (strtolower($pri)=="max")
	{
		$query="SELECT max(pri) AS _pri FROM webtd
			WHERE  server=$SERVER_ID AND ver=$ver 
			AND lang='$lang' AND page_id=$page_id";
		parse_str(ado_query2url($query));		
		$pri=$_pri;
	}
	
	  
  include('include/clib.h');
?>


  
<form style="margin: 0; padding:0;" method="post" action="tdedit.<?echo $KAMELEON_EXT?>#<?echo $hash?>" name="edytujtd" id="edytujtd_id"> 
  <input type="hidden" name="page_id" value="<?echo $page_id?>">
  <input type="hidden" name="page" value="<?echo $page?>">
  <input type="hidden" name="pri" value="<?echo $pri?>">
  <input type="hidden" name="sid" value="<?echo $sid?>" />
  <input type="hidden" name="hash" value="<?echo $hash?>">
  <input type="hidden" name="td_width" value="<?echo $td_width?>">
  <input type="hidden" name="km_action" value="ZapiszTD" id="edytujtd_action">
  
<?
	if ($editmode)
	{
		$query="SELECT * FROM webtd
			WHERE  server=$SERVER_ID AND ver=$ver 
			AND lang='$lang' AND page_id=$page_id AND sid=".$_REQUEST["sid"];
		parse_str(ado_query2url($query));
		
	}
	

	$strpslashes_needed = ini_get('magic_quotes_gpc') ;

	if ($strpslashes_needed) $title=stripslashes($title);
	if ($strpslashes_needed) $plain=stripslashes($plain);
	if ($strpslashes_needed) $costxt=stripslashes($costxt);
	if (ereg("<[^>]+=\\\\\"[^>]*>",$plain)) $plain=stripslashes($plain);


	$dn=dirname($SCRIPT_NAME);
	if ($dn=="/") $dn="";

	//$plain=ereg_replace("($EREG_REPLACE_KAMELEON_UIMAGES)","$dn/\\1",$plain);
  $plain=ereg_replace("($EREG_REPLACE_KAMELEON_UIMAGES)","\\1",$plain);
	
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

	include_js("tdedit",false);

  echo "<div class=\"km_toolbar\">";
  
  
	
	if ($editmode) $zapisz_zmiany="ZapiszZmiany()";
	else $zapisz_zmiany="ZapiszZmianyZamknij()";
  $save_href_id=$helpmode?"id=\"help_td_save\"":"";
	
	echo "<div class=\"km_toolbar_right\"><ul>";
	  
	  if ($CONST_MODE!="express") 
    {
  	    echo "
        <li><a href=\"javascript:".$zapisz_zmiany."\" ".$save_href_id."\" class=\"km_icon km_icontdedit_save\" title=\"".label("Save and exit")."\">".label("Save and exit")."</a></li><li class=\"km_sep\"></li>
        <li><a href=\"javascript:showWysiwyg()\" class=\"km_icon km_icontdedit_wysiwyg\" title=\"".label("WYSIWYG")."\">".label("WYSIWYG")."</a></li><li class=\"km_sep\"></li>";
    }
  
    if ($editmode && $CONST_MODE!="express") 
    {
    	echo "<li><a href=\"javascript:showJavascript()\" class=\"km_icon km_icontdedit_js\" title=\"".label("JavaScript")."\">".label("JavaScript")."</a></li><li class=\"km_sep\"></li>";
      echo "<li><a href=\"javascript:showAdvanced()\" class=\"km_icon km_icontdedit_opcje\" title=\"".label("Options")."\">".label("Options")."</a></li><li class=\"km_sep\"></li>";
    }
  
    if (strlen($MODULE_ADV) && $CONST_MODE!="express")
    {
      echo "<a href=\"javascript:showModAdv(0)\" class=\"k_zakl01\>&nbsp;";
      echo strlen($MODULE_NAME)?$MODULE_NAME:label("Module options");
      echo "&nbsp;</a>";
    }
    
    if ($CONST_MODE!="express")
    {
      if (strlen($menu_id)>0) echo "<li><a class=\"km_icon km_icontd_menu\" href=\"menus.".$KAMELEON_EXT."?menu=".$menu_id."\" title=\"".label('Menu').":".$menu_id."\">".label('Menu').": <u><b>".$menu_id."</b></u></a></li><li class=\"km_sep\"></li>"; 
      if (strlen($html)>0) echo "<li><span class=\"km_icon km_icontdedit_include\" title=\"".$html."\">".$html."</span></li><li class=\"km_sep\"></li>";
    }
  
  
  
  $scheme = ($_SERVER [ "SERVER_PORT" ] == 80 ? "http" : "https");  
  $wwwpath = dirname($scheme."://".$_SERVER [ "HTTP_HOST" ].$_SERVER [ "SCRIPT_NAME" ]);  
  
  
  
  echo "
    </div>";
    
  if ($C_SHOW_TD_TITLE && $editmode) 
	{ 
		$input_title_id=$helpmode?"id=\"help_title_input\"":"";
		if ($CONST_MODE!="express")
		{
      echo "<div class=\"km_toolbar_left\">".label("Title").": <input style=\"width: 300px;\" class=\"k_input\" type=\"text\" name=\"title\" value=\"".ereg_replace("\"","&quot;",$title)."\" ".$input_title_id." /></div>";
		}	
	}
	
	echo "</div>
  
  <div id=\"wysiwyghtml\" class=\"km_wysiwyg\">
  <div id=\"toolbar_place\"></div>
  <div class=\"km_editor\">
  <script type=\"text/javascript\" src=\"editor/ckeditor.js\"></script>
  <textarea cols=\"80\" id=\"editor1\" name=\"plain\" rows=\"10\">".str_replace(array("<",">"),array("&lt;","&gt;"),$plain)."</textarea>
  </div>
  ";

  if ($kameleon->lang == "i" || $kameleon->lang == "pl") $jezyk = "pl";
  else $jezyk = "en";

	$extracss="";
	if (sizeof($C_EDITOR_EXTRACSS)>0)
	{
		for ($i=0;$i<sizeof($C_EDITOR_EXTRACSS);$i++) $extracss.=",'".str_replace(array("{UIMAGES}","{IMAGES}"),array($UIMAGES,$IMAGES),$C_EDITOR_EXTRACSS[$i])."'";
	}


  echo "
  <script type=\"text/javascript\">
	//<![CDATA[

    jQueryKam(window).resize(function() {
      jQueryKam(document).ready(function() {
        var w = jQueryKam(\"#cke_editor1\").css(\"width\");
        if (w!=undefined) CKEDITOR.instances.editor1.resize(w,(jQueryKam(window).height()-258));
      });
    });



    CKEDITOR.replace( 'editor1',
    {
        baseHref : '".$wwwpath."/',
        userImages : '".$UIMAGES."',
        sharedSpaces :
				{
					top : 'toolbar_place'
				},
				resize_minWidth : ".(1+$_GET["td_width"]).",
				resize_maxWidth : (jQueryKam(window).width()),
				resize_dir : 'horizontal',
				width : ".(1+$_GET["td_width"]).",
        customConfig : 'kameleon_config.js',
        height : (jQueryKam(window).height()-290),
        language : '".$jezyk."_".strtolower($CHARSET)."',
        stylesCombo_stylesSet : 'kameleon_styles:".$wwwpath."/kameleon_css.php',
        contentsCss : ['".$wwwpath."/".$IMAGES."/textstyle.css', '".$wwwpath."/".$IMAGES."/szablon.css', '".$kameleon->user[skinpath]."/ckeditor.css'".$extracss."] ,
        schowek : kameleonCliboard,   
        //ignoreEmptyParagraph : false,    
        on :
        {
            instanceReady : function( ev )
            {
                this.dataProcessor.writer.setRules( 'p',
                {
                    indent : false,
                    breakBeforeOpen : true,
                    breakAfterOpen : false,
                    breakBeforeClose : false,
                    breakAfterClose : true
                });
            }
        }
    });
    ";
    ?>

<?
echo "
		
	//]]>
	</script>
  ";
  

		?>
		<?if (!$editmode){?>
		<script type="text/javascript">
			document.getElementById('editor1').value=window.clipboardData.getData("Text");
		</script>			
		<?}?>

  </div>
<?
  include('include/tdedit_opt.h');
?>


</form>

<?
if ( is_Array($C_MODULES) ) { ?>

<script language="javascript">
old_selection=document.edytujtd.module.selectedIndex;
function module_selected()
{
	if (document.edytujtd.module==null) return;
	
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

<script language="Javascript">
	function ZapiszZmianyNieWychodzac()
	{
		document.getElementById('edytujtd_action').value='ZapiszTD';
		document.getElementById('edytujtd_id').setAttribute("action",'tdedit.php');
		document.getElementById('edytujtd_id').submit();
	}
	function ZapiszZmiany()
	{
		document.getElementById('edytujtd_action').value='ZapiszTD';
		document.getElementById('edytujtd_id').setAttribute("action",'index.<?echo $KAMELEON_EXT?>#<?echo $hash?>');
    	document.getElementById('edytujtd_id').submit();
	}


	function checkAccessLevel(obj,limit)
	{
		if (obj.value>limit)
		{
			alert('<?echo addslashes(label('You are not permited to set access level'))?> - '+obj.value);
			obj.value=limit;
		}
	}

</script>

</body>

<?

	if ($C_HIDE_WYSIWYG)
		echo "<script language='javascript'>
				document.zakLeftWysiwyg.style.display='none';
			    document.all['zakMiddleWysiwyg'].style.display='none';
				document.zakRightWysiwyg.style.display='none';
				</script>";

	if (strlen($MODULE_ADV)) echo "<script language='javascript'> showModAdv(1); </script>";
?>

</html>


