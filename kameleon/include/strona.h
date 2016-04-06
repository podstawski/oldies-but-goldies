<?    
  	include("include/userclass.h");

?>
<html>
<head>
    <title>KAMELEON: <?echo label("Page setup");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/property.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
    <?
    include_js('tree');
    ?>
</head>
<body>
  <script>
  function zmiana_numeru_quest(page,newpage)
  {
      if (page==newpage)
      {
  	document.PageProperties.newid.value = page;
  	return;
      }
  
      if (confirm("<?echo label("Are you sure you want to change page number from");?>: "+page+" <?echo label("to");?>: "+newpage+"."))
      {	
          document.PageProperties.newid.value = newpage;
      }
      else
  	document.PageProperties.newid_prompt.value = page;
  }
  
  function saveLink(targety,action)
  { }
  </script>

<?
    include("include/navigation.h");
?>
<div class="km_toolbar">
  <ul>
    <li><a class="km_icon km_iconi_save" href="javascript:document.forms['PageProperties'].submit()" title="<?=label("Save")?>"><?=label("Save")?></a></li>
  </ul>		
</div>

<? 
	$form_action=strstr($HTTP_REFERER,'consistency.php')?$HTTP_REFERER:'index.php';

?>
<form method=post action="<?echo $form_action?>" name='PageProperties'>
 <input type=hidden name=page value="<?echo $page?>">
 <input type=hidden name=newid value="<?echo $page?>">
 <input type=hidden name=action value="ZapiszStrone">

<?
	$query="SELECT * FROM webpage  WHERE  ver=$ver AND id=$page AND lang='$lang' AND server=$SERVER_ID LIMIT 1";
	parse_str(ado_query2url($query));

	$C_SHOW_PAGE_PROPERITIES=$C_SHOW_PAGE_TITLE | $C_SHOW_PAGE_DESCRIPTION | $C_SHOW_PAGE_KEYWORDS;
	$C_SHOW_PAGE_COLORS=$C_SHOW_PAGE_BGCOLOR | $C_SHOW_PAGE_FGCOLOR | $C_SHOW_PAGE_TBGCOLOR 
		| $C_SHOW_PAGE_TFGCOLOR | $C_SHOW_PAGE_KEY
		| $C_SHOW_PAGE_CLASS | $C_SHOW_PAGE_BACKGROUND | $C_SHOW_PAGE_TYPE;



	$display=array();

	$display['tree']['hidden']=$tree;
	
	if ($C_SHOW_PAGE_PROPERITIES ) $display['__props__']['title']=label('Page setup');

	if ($C_SHOW_PAGE_TITLE )
	{
		$display['title']['arg']['size']='80';
		$display['title']['arg']['value']=stripslashes($title);
		$display['title']['label']=label("Page title");

		$display['title_short']['arg']['size']='40';
		$display['title_short']['arg']['value']=stripslashes($title_short);
		$display['title_short']['label']=label("Page title - short");
	}

	if ($C_SHOW_PAGE_DESCRIPTION )
	{
		$display['description']['label']=label("Description");
		$display['description']['input']='<textarea class="k_textarea" name="description" cols="80" rows="10">'.stripslashes($description).'</textarea>';
	}
	if ($C_SHOW_PAGE_KEYWORDS )
	{
		$display['keywords']['label']=label("Keywords");
		$display['keywords']['input']='<textarea class="k_textarea" name="keywords" cols="80" rows="10">'.stripslashes($keywords).'</textarea>';

	}

	if ($C_SHOW_PAGE_COLORS ) $display['__colors__']['title']=label('Page colors and styles');

	if ($C_SHOW_PAGE_BGCOLOR  && $C_SHOW_OLD_SUPPORT) 
	{
		$display['bgcolor']['arg']['size']='8';
		$display['bgcolor']['arg']['value']=$bgcolor;
		$display['bgcolor']['arg']['id']='_bgcolor';
		$display['bgcolor']['label']=label("Background color");
		$display['bgcolor']['icon']='img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'_bgcolor\')';
	}

	if ($C_SHOW_PAGE_FGCOLOR  && $C_SHOW_OLD_SUPPORT) 
	{
		$display['fgcolor']['arg']['size']='8';
		$display['fgcolor']['arg']['value']=$fgcolor;
		$display['fgcolor']['arg']['id']='_fgcolor';
		$display['fgcolor']['label']=label("Font color");
		$display['fgcolor']['icon']='img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'_fgcolor\')';
	}

	if ($C_SHOW_PAGE_TBGCOLOR  && $C_SHOW_OLD_SUPPORT) 
	{
		$display['tbgcolor']['arg']['size']='8';
		$display['tbgcolor']['arg']['value']=$tbgcolor;
		$display['tbgcolor']['arg']['id']='_tbgcolor';
		$display['tbgcolor']['label']=label("Main table background color");
		$display['tbgcolor']['icon']='img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'_tbgcolor\')';
	}

	if ($C_SHOW_PAGE_TFGCOLOR  && $C_SHOW_OLD_SUPPORT) 
	{
		$display['tfgcolor']['arg']['size']='8';
		$display['tfgcolor']['arg']['value']=$tfgcolor;
		$display['tfgcolor']['arg']['id']='_tfgcolor';
		$display['tfgcolor']['label']=label("Main table font color");
		$display['tfgcolor']['icon']='img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'_tfgcolor\')';
	}

	if ($C_SHOW_PAGE_CLASS )
	{
		$display['class']['label']=label("Class");
		$display['class']['input']=CreateFormField(array("",1,"select","class",$class,$USERCLASS));
	}

	if ($C_SHOW_PAGE_BACKGROUND ) 
	{
		$display['background']['arg']['size']='60';
		$display['background']['arg']['value']=$background;
		$display['background']['arg']['id']='_background';
		$display['background']['label']=label('Background image');	
		$display['background']['icon']='img/i_image_n.gif|img/i_image_a.gif|otworzGalerie(2,\'_background\')|'.label('Insert or edit image');
	}


	if ($C_SHOW_PAGE_KEY )
	{
		$display['pagekey']['arg']['size']='45';
		$display['pagekey']['arg']['value']=$pagekey;
		$display['pagekey']['label']=label("Page key");
	}

	if (C_SHOW_PAGE_TYPE )
	{
		$display['type']['label']=label("Type");
		$display['type']['input']=CreateFormField(array("",1,"select",type,$type,$PAGE_TYPY));
	}

	if ($CONST_MODE!="express")  $display['__navi__']['title']=label('Page navigations and menus');

	if ($C_SHOW_PAGE_PREV ) 
	{
		$display['prev_prev']['hidden']=($prev>=0)?$prev:'';
		$display['prev']['arg']['size']='5';
		$display['prev']['arg']['value']=($prev>=0)?$prev:'';
		$display['prev']['arg']['id']='km_prev';
		$display['prev']['label']=label("Parent page");
		$display['prev']['more']='<img class="k_imgbutton" src="img/i_tree_n.gif" onclick="openTree(\'km_prev\',document.PageProperties.prev.value,\'\')" style="cursor:hand" onmouseover="this.src=\'img/i_tree_a.gif\'" onmouseout="this.src=\'img/i_tree_n.gif\'" border="0" alt="'.label('Webpage explorer').'" width="23" height="22" align="absmiddle">';
	}


	if ($C_SHOW_PAGE_NEXT ) 
	{
		$display['next']['arg']['size']='5';
		$display['next']['arg']['value']=$next;
		$display['next']['arg']['id']='km_next';
		$display['next']['label']=label("Next page");
		$display['next']['more']='<img class="k_imgbutton" src="img/i_tree_n.gif" onclick="openTree(\'km_next\',document.PageProperties.next.value,\'\')" style="cursor:hand" onmouseover="this.src=\'img/i_tree_a.gif\'" onmouseout="this.src=\'img/i_tree_n.gif\'" border="0" alt="'.label('Webpage explorer').'" width="23" height="22" align="absmiddle"><img class="k_imgbutton" src="img/i_new_n.gif" onclick="document.getElementById(\'km_next\').value=-1" style="cursor:hand;" border="0" alt="'.label("New page").'" width="23" height="22" align="absmiddle">';
	}

	if ($C_SHOW_PAGE_MENU_ID  && $C_SHOW_OLD_SUPPORT)
	{
		$display['menu_id']['arg']['size']='5';
		$display['menu_id']['arg']['value']=$menu_id;
		$display['menu_id']['label']=label("Main menu");
	}

	if ($C_SHOW_PAGE_SUBMENU_ID  && $C_SHOW_OLD_SUPPORT)
	{
		$display['submenu_id']['arg']['size']='5';
		$display['submenu_id']['arg']['value']=$submenu_id;
		$display['submenu_id']['label']=label("Sub-menu");
	}

	if ($C_SHOW_PAGE_FILENAME )
	{
		$display['file_name']['arg']['size']='80';
		$display['file_name']['arg']['value']=$file_name;
		$display['file_name']['label']=label("File name");
	}

	if ($CONST_MODE!="express")
	{
		$display['newid_prompt']['arg']['size']='5';
		$display['newid_prompt']['arg']['value']=$page;
		$display['newid_prompt']['label']=label("Change page number");
		$display['newid_prompt']['arg']['onChange']='zmiana_numeru_quest('.$page.',this.value)';
	}


	$_PAGE_TYPY_DXML=array();
	if (is_array($PAGE_TYPY_DXML[$type+0])) $_PAGE_TYPY_DXML=array_merge($_PAGE_TYPY_DXML,$PAGE_TYPY_DXML[$type+0]);
	if (is_array($PAGE_TYPY_DXML['*'])) $_PAGE_TYPY_DXML=array_merge($_PAGE_TYPY_DXML,$PAGE_TYPY_DXML['*']);
	


	if (count($_PAGE_TYPY_DXML)) 
	{
		$display['__user__']['title']=label('User variables');
		$anything=false;

		$d_xml_a=unserialize(base64_decode($d_xml));
		foreach ($_PAGE_TYPY_DXML AS $name=>$v)
		{
			if (is_array($display[$name])) 
			{
				$display[$name]['label']=$v[0];
				if (strlen($v[1])) $display[$name]['arg']['style']=$v[1];
			}
			else
			{
				$anything=true;
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

		if (!$anything) unset($display['__user__']);
		
	}



	echo display_opt($display);

	if (is_object($auth_acl) && file_exists(dirname(__FILE__).'/../plugins/acl/kameleon/matrix.php') )
	{
		include(dirname(__FILE__).'/../plugins/acl/kameleon/const.php');
		require_once(dirname(__FILE__).'/../plugins/acl/kameleon/fun.php');

		$_SERVER['tree']=$tree;
		$_SERVER['acl']=&$auth_acl;
		if (acl_hasPageRight($page,PAGE_GRANT_RIGHT))
		{
			echo '<h2>'.label('ACL').':</h2>';
			$acl=&$auth_acl;
			$lang=$kameleon->lang;
			$RESOURCE=PAGE_RESOURCE;
			$RESOURCE_ID=$page;
			include(dirname(__FILE__).'/../plugins/acl/kameleon/matrix.php');
		}
	}

  ?>
</form>

<div class="km_toolbar">
  <ul>
    <li><a class="km_icon km_iconi_save" href="javascript:document.forms['PageProperties'].submit()" title="<?=label("Save")?>"><?=label("Save")?></a></li>
  </ul>		
</div>

<?
	include_js("galerie",false);
?>

  <script>
     function saveId(key,val)
    {
        document.getElementById(pole).value=val;
    }

    function NewKameleonRight()
    {
		  userwin=open("users.php?callback_fun=NewACLuser","users","toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=560,height=350");
    }
  </script>
</body>
</html>