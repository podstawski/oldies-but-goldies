<?
	$sid = (int)$_GET["sid"];

	if (strlen($page_id))
	{
		$pole="page_id";
		$wart=$page_id;
		$formaction="index.$KAMELEON_EXT";

	}
	if (strlen($menu_id))
	{
		$pole="menu_id";
		$wart=$menu_id;
		$formaction="menus.$KAMELEON_EXT";
	}

	$page=$referpage+0;

	include("include/userclass.h");
?>
<html>

<head>
    <title>KAMELEON: <?echo label("Link");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/tdedit.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
	<?php
		include_js("jquery-1.4");
		include_js("kameleon");
		include("ajax_variables.php");
	?>
</head>
<body>



<?	if ($CONST_MODE!="express") {?>

<?
   include("include/navigation.h");
?>

<div class="km_toolbar">
  <ul>
    <li><a class="km_icon km_iconi_menu" href="menus.<?echo $KAMELEON_EXT?>?menu=<?echo $menu_id;?>" title="<?echo label("Return to menu no").": $menu_id"?>"><?echo label("Return to menu no").": $menu_id"?></a></li>
    <li><a class="km_icon km_iconi_save" href="javascript:document.getElementById('link').submit()" title="<?echo label("Save menu link")?>"><?echo label("Save menu link")?></a></li>
  </ul>
</div>
<?	}	?>
<div class="formularz">
<form method="post" id="link" action="<?echo $formaction?>" name="LinkProps">
<input type="hidden" name="<?echo $pole?>" value="<?echo $wart?>">
<input type="hidden" name="sid" value="<?echo $sid?>">
<input type="hidden" name="action" value="ZapiszLink">

<?

$display=array();

$query="SELECT * FROM weblink WHERE ver=$ver AND lang='$lang' AND $pole=$wart AND sid=".$sid." AND server=$SERVER_ID";

if ($sid) 
{
	parse_str(ado_query2url($query));
	$href = stripslashes($href);
	$variables = stripslashes($variables);

  if (!$C_HIDE_LINK_ALT) {
  	$display['alt']['arg']['size']='60';
  	$display['alt']['arg']['value']=str_replace('"','&quot;',stripslashes($alt));
  	$display['alt']['label']=label("Link (img) alt");
  }
  
  if (!$C_HIDE_LINK_ALT_TITLE) {
  	$display['alt_title']['arg']['size']='60';
  	$display['alt_title']['arg']['value']=str_replace('"','&quot;',stripslashes($alt_title));
  	$display['alt_title']['label']=label("Title");
  }
  
  if (!$C_HIDE_LINK_IMG) {
  	$display['img']['arg']['size']='50';
  	$display['img']['arg']['id']='_img';
  	$display['img']['arg']['value']=$img;
  	$display['img']['label']=label("Link image");
  	$display['img']['icon']='img/i_image_n.gif|img/i_image_a.gif|otworzGalerie(2,\'_img\')|'.label('Insert or edit image');
  }
  
  if (!$C_HIDE_LINK_IMGA) {
  	$display['imga']['arg']['size']='50';
  	$display['imga']['arg']['id']='_imga';
  	$display['imga']['arg']['value']=$imga;
  	$display['imga']['label']=label("Link active image");
  	$display['imga']['icon']='img/i_image_n.gif|img/i_image_a.gif|otworzGalerie(2,\'_imga\')|'.label('Insert or edit image');
  }
  
	if ($CONST_MODE!="express") 
	{
    if (!$C_HIDE_LINK_HREF) {
      $display['href']['arg']['size']='60';
      $display['href']['arg']['value']=$href;
      $display['href']['label']='HREF';
    }
      
    if (!$C_HIDE_LINK_UFILE_TARGET) {
      $display['ufile_target']['arg']['size']='50';
      $display['ufile_target']['arg']['id']='_ufile_target';
      $display['ufile_target']['arg']['value']=$ufile_target;
      $display['ufile_target']['label']=label("File");
      $display['ufile_target']['icon']='img/i_file_n.gif|img/i_file_a.gif|otworzGalerie(1,\'_ufile_target\')|'.label('Insert or edit file');
    }
    
    if (!$C_HIDE_LINK_VARIABLES) { 
      $display['variables']['arg']['size']='60';
      $display['variables']['arg']['value']=$variables;
      $display['variables']['label']=label("Additional variables");
    }
    
    if (!$C_HIDE_LINK_TARGET) {
      $display['target']['arg']['size']='30';
      $display['target']['arg']['value']=$target;
      $display['target']['label']=label("Target");
    }
    
    if (!$C_HIDE_LINK_SUBMENU_ID) {
      $display['submenu_id']['arg']['size']='10';
      $display['submenu_id']['arg']['value']=$submenu_id;
      $display['submenu_id']['arg']['id']='_submenu_id';
      $display['submenu_id']['label']=label("Sub-menu");
      $display['submenu_id']['more']='<img class="k_imgbutton" src="img/i_new_n.gif" onClick="document.getElementById(\'_submenu_id\').value=-1" style="cursor:hand;" onmouseover="this.src=\'img/i_new_a.gif\'" onmouseout="this.src=\'img/i_new_n.gif\'" border=0 alt="'.label("New menu").'" width="23" height="22" align="absmiddle">';
    }
	}


}



if ($CONST_MODE!="express")
{
  if (!$C_HIDE_LINK_FGCOLOR && $C_SHOW_OLD_SUPPORT) {
  	$display['fgcolor']['arg']['size']='10';
  	$display['fgcolor']['arg']['value']=$fgcolor;
  	$display['fgcolor']['arg']['id']='_fgcolor';
  	$display['fgcolor']['label']=label("Font color");
  	$display['fgcolor']['icon']='img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'_fgcolor\')';
  }
  
  if (!$C_HIDE_LINK_TYPE) {
  	$display['type']['label']=label("Type");
  	$display['type']['input']=CreateFormField(array("",1,"select",type,$type,$LINK_TYPY));
  }
  
  if (!$C_HIDE_LINK_CLASS) {
  	$display['class']['label']=label("Class");
  	$display['class']['input']=CreateFormField(array("",1,"select","class",$class,$USERCLASS));
  }
  
	if ($pri && !$C_HIDE_LINK_DESCRIPTION)
	{
		$display['description']['label']=label("Description");
		$display['description']['input']='<textarea class="k_textarea" name="description" cols="80" rows="10">'.stripslashes($description).'</textarea>';
	}
}

$link_template=kameleon_template($SZABLON_PATH,$LINK_TYPY,$type+0);
$php=ereg_replace('html$','php',$link_template);
if (file_exists($php)) include($php);



$_LINK_TYPY_DXML=array();
if (is_array($LINK_TYPY_DXML[$type+0])) $_LINK_TYPY_DXML=array_merge($_LINK_TYPY_DXML,$LINK_TYPY_DXML[$type+0]);
if (is_array($LINK_TYPY_DXML['*'])) $_LINK_TYPY_DXML=array_merge($_LINK_TYPY_DXML,$LINK_TYPY_DXML['*']);

if (count($_LINK_TYPY_DXML) && $pri) 
{
	$d_xml_a=unserialize(base64_decode($d_xml));
	foreach ($_LINK_TYPY_DXML AS $name=>$v)
	{
		if (is_array($display[$name])) 
		{
			$display[$name]['label']=$v[0];
			if (strlen($v[1])) $display[$name]['arg']['style']=$v[1];
		}
		else
		{
			$token='_d_xml['.$name.']';
			$display[$token]['label']=$v[0];

			if (strlen($v[2]))
			{
				$display[$token]['input']=str2input($v[2],$token,$d_xml_a[$name],$v[1]);
			}
			else
			{
				if (strlen($v[1])) $display[$token]['arg']['style']=$v[1];
				$display[$token]['arg']['value']=$d_xml_a[$name];
			}

			if(strlen($v[4])) $display[$token]['icon']=$v[4];
		}
	}
	
}



echo display_opt($display);


if (is_object($auth_acl) && file_exists(dirname(__FILE__).'/../plugins/acl/kameleon/matrix.php') )
{
	include(dirname(__FILE__).'/../plugins/acl/kameleon/const.php');
	require_once(dirname(__FILE__).'/../plugins/acl/kameleon/fun.php');

	
	if ($pri) 
	{
		unset($_SERVER['tree']);
		$tree=':'.$menu_id.':';
	}
	else 
		$_SERVER['tree']='';
	
	$_SERVER['acl']=&$auth_acl;
	
	$RESOURCE=MENU_RESOURCE;
	$RESOURCE_ID=$menu_id;
	if ($pri) $RESOURCE_ID=$sid;

	if ( acl_hasRight($RESOURCE_ID,PAGE_GRANT_RIGHT,$RESOURCE))
	{
		echo '<h2 class="section_name">'.label('ACL').':</h2>';
		$acl=&$auth_acl;
		$lang=$kameleon->lang;

		$exclude_rights=array(PROOF_RIGHT,FTP_RIGHT);
		include(dirname(__FILE__).'/../plugins/acl/kameleon/matrix.php');
	}
}



include_js("galerie",false);

?>
<script type="text/javascript">
function wstawPlik(img)
{
	document.getElementById(galeriaPole).value = img;
}
</script>

</form>
</div>

</body>
</html>
