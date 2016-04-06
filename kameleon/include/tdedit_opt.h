<?

$C_SHOW_TD_INSIDE = $C_SHOW_TD_MENU | $C_SHOW_TD_MENU | $C_SHOW_TD_HTML | $C_SHOW_TD_API;
$C_SHOW_TD_DESIGN = $C_SHOW_TD_BGIMG | $C_SHOW_TD_BGCOLOR | $C_SHOW_TD_ALIGN | $C_SHOW_TD_VALIGN | $C_SHOW_TD_CLASS | $C_SHOW_TD_WIDTH;
$C_SHOW_TD_POS_TYPE = $C_SHOW_TD_TYPE | C_SHOW_TD_LEVEL | $C_SHOW_TD_IMG;
$C_SHOW_TD_NAVIGATION = $C_SHOW_TD_MORE | $C_SHOW_TD_NEXT | $C_SHOW_TD_SIZE | $C_SHOW_TD_COS | $C_SHOW_TD_COSTXT;

if (!$C_SWF_STYLE ) $swfstyle=0;

$display=array();


if ($C_SHOW_TD_INSIDE)
{
	$display['__inside__']['title']=label('Inside');
}

if ($C_SHOW_TD_MENU ) 
{
	$select='<select class="k_select" style="width: 250px;" size="1" name="menu_id">';
	$select.='<option class="k_option" value="0">'.label('Select menu').'</opiton>';
	$select.='<option class="k_option" value="-1">'.label('New menu').'</opiton>';
	if ($menu_id) $select.='<option class="k_option" style="background-color:Silver" value="'.$menu_id.'" selected>menu '.$menu_id.'</opiton>';
	ob_start();
	include ("include/menu_options.h");
	$select.=ob_get_contents();
	ob_end_clean();
	$select.='</select>';


	$display['menu_id']['label']=label('Menu');
	$display['menu_id']['input']=$select;
}

if ($C_SHOW_TD_HTML )
{
	$display['html']['arg']['style']='width: 220px';
	$display['html']['arg']['id']='_html';
	$display['html']['arg']['value']=$html;
	if (is_Array($C_MODULES)) $display['html']['arg']['onChange']='document.edytujtd.module.selectedIndex=0; old_selection=0';
	$display['html']['label']=label('Include file');
	$display['html']['icon']='img/ftp_inc_n.gif|img/ftp_inc_n.gif|otworzGalerie(3,\'_html\')|'.label('Insert php file');


}
else
	$display['html']['hidden']=$html;

if ($C_SHOW_TD_API  && !$swfstyle)
{
	$display['api']['label']=label('Include api');
	$display['api']['input']=CreateFormField(array("",1,"select",api,$api,$APIS));
}
else
	$display['api']['hidden']=$api;



if ($C_SHOW_TD_HTML && is_Array($C_MODULES) ) 
{

	$select='<select class="k_select" style="width: 250px;" name=module onChange="module_selected()">';
	$select.='<option class=k_option value="">'.label('Select module').'</opiton>';
	for ($_m=0;$_m<count($C_MODULES);$_m++)
	{	
		$m_name=$C_MODULES[$_m];
		if (!is_Object($MODULES->$m_name->files)) continue;
		$m_upname=strtoupper($C_MODULES[$_m]).":";
	
		$select.="<option class=k_option value='-'>$m_upname</opiton>\n";
		foreach ($MODULES->$m_name->files AS $m_key=>$m_val)
		{
			$_s="";
			$_v="@$m_name/".$m_val->file;
			$_o=kameleon_global($m_val->label);
			if ($module==$_v) $_s=" selected";
			$select.="<option$_s  class=k_option value='$_v'>&nbsp; $_o</opiton>\n";		

		}

		if (file_exists($f_name)) 
		{
			ob_start();
			include($f_name);
			$select.=ob_get_contents();
			ob_end_clean();
		}
		
	}
	$select.='</select>';

	$display['module']['label']=label('Include kameleon module');
	$display['module']['input']=$select;
}

if ( $C_SHOW_TD_STATICINCLUDE && !$swfstyle)
{
	$display['staticinclude']['arg']['value']=1;
	$display['staticinclude']['label']=label('Files included during publication');
	if ($staticinclude) $display['staticinclude']['arg']['checked']='checked';
	$display['staticinclude']['arg']['type']='checkbox';
	$display['staticinclude']['arg']['class']='k_cbx';
}
else
	$display['staticinclude']['hidden']=$staticinclude;


if ($C_SHOW_TD_HTML  ) 
{ 
	$display['ob']['label']=label('Content analyzer + {VAR} replacement');
	$display['ob']['input']='	<input type="checkbox" class="k_cbx" name="obtd[1]" value="1" '.(($ob&1)?'checked':'').'> start &nbsp;&nbsp;
								<input type="checkbox" class="k_cbx" name="obtd[2]" value=2 '. (($ob&2)?'checked':'').'> stop';
	
}


if ($C_SHOW_TD_INSIDE)
{
	$display['__web20__']['title']=label('WEB 2.0');
	
	$display['web20']['label']=label('Select WEB 2.0 module');
	$display['web20']['input']='<select class="k_select" name="web20" id="id_select_web20"><option name="web20_module" value="">'.$kameleon->label('Choose').'</option></select>';
	$display['web20']['after']='<span id="id_options_web20"></span>';
}



if ($C_SHOW_TD_DESIGN)
{
	$display['__design__']['title']=label('Design');
}


if($C_SHOW_TD_BGIMG) 
{
	$display['bgimg']['arg']['style']='width: 220px';
	$display['bgimg']['arg']['id']='_bgimg';
	$display['bgimg']['arg']['value']=$bgimg;
	$display['bgimg']['label']=label($swfstyle?'Macromedia SWF file':'Background image');
	$display['bgimg']['icon']='img/i_image_n.gif|img/i_image_a.gif|otworzGalerie(2,\'_bgimg\')|'.label($swfstyle?'Insert or edit file':'Insert or edit image');
}

if($C_SHOW_TD_BGCOLOR && $C_SHOW_OLD_SUPPORT)
{
	$display['bgcolor']['arg']['size']='10';
	$display['bgcolor']['arg']['value']=$bgcolor;
	$display['bgcolor']['arg']['id']='_bgcolor';
	$display['bgcolor']['label']=label("Background color");
	$display['bgcolor']['icon']='img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'_bgcolor\')';

}

if($C_SHOW_TD_ALIGN && $C_SHOW_OLD_SUPPORT)
{
	$display['align']['label']=label("Horizontal align");
	$display['align']['input']=CreateFormField(array("",1,"select","align",$align,$aligns));
}

if ($C_SHOW_TD_VALIGN  && !$swfstyle)
{
	$display['valign']['label']=label("Vertical align");
	$display['valign']['input']=CreateFormField(array("",1,"select","valign",$valign,$valigns));
}

if($C_SHOW_TD_WIDTH  && !$swfstyle)
{
	$display['width']['arg']['size']='10';
	$display['width']['arg']['value']=$width;
	$display['width']['label']=label("Width").' '. label("Pixel").' '.label("or").' %';
}

if($C_SHOW_TD_WIDTH  && $swfstyle)
{
	$display['widthXheight']['label']=label("Width").' x '.label("Height");
	$display['widthXheight']['input']='<input class="k_input" type="text" size="5" name="width" value="'.$width.'"> x <input class="k_input" type="text" size="5" name="size" value="'.$size.'">';
}

if ($C_SHOW_TD_COSTXT && $swfstyle)
{
	parse_str($xml);
	foreach ($SWF_OBJECT_PARAMS AS $name=>$values)
	{
		$_name='swf_'.$name;
		$display[$_name]['label']=label($_name);

		$v=explode('|',$values);
		if (!strlen($values)) $display[$_name]['input'] = '<input class="k_input" type="text" style="width: 200px;" name="_swf['.$name.']" value="'.$_swf[$name].'">';
		if (strtolower($values)=='true|false' || strtolower($values)=='false|true')
		{
			$checked='';
			
			if (!strlen($_swf[$name]) && strtolower($v[0])=='true') $checked=' checked';
			if (strtolower($_swf[$name]) == 'true') $checked=' checked';
			
			$display[$_name]['input'] = '<input'.$checked.' class="k_checkbox" type="checkbox" name="_swf['.$name.']" value="true">';
		}
		elseif (strlen($values))
		{
			$select = '<select class="k_select" style="width: 200px" name="_swf['.$name.']">';
			foreach ($v AS $val)
			{
				$sel=($_swf[$name]==$val) ? ' selected':'';
				$token='swf_'.$name.'_'.$val;
				$lab=label($token);
				if ($lab==$token) $lab=$val;
				$select.='<option'.$sel.' value="'.$val.'">'.$lab.'</option>';
			}
			$select.='</select>';

			$display[$_name]['input'] = $select;
		}
	}
}



if ($C_SHOW_TD_CLASS)
{
	$display['class']['label']=label("Class name");
	$display['class']['input']=CreateFormField(array("",1,"select","class",$class,$USERCLASS));
}

if($C_SHOW_TD_BGIMG && !$swfstyle)
{
	$display['fetch_images_during_save']['arg']['value']=1;
	$display['fetch_images_during_save']['label']=label('Fetch images if pasted from remote HTML');
	$display['fetch_images_during_save']['arg']['type']='checkbox';
	$display['fetch_images_during_save']['arg']['class']='k_cbx';
}

if ($C_SHOW_TD_POS_TYPE)
{
	$display['__tdpos__']['title']=label('Type and position');
}

if ($C_SHOW_TD_TYPE)
{
	$display['type']['label']=label("Type");
	$display['type']['input']=CreateFormField(array("",1,"select",type,$type,$TD_TYPY));
}

if($C_SHOW_TD_LEVEL)
{
	$display['level']['label']=label("Level");
	$poziomy=($page_id<0 && is_Array($TD_POZIOMY_HF)) ? $TD_POZIOMY_HF : $TD_POZIOMY;
	$display['level']['input']=CreateFormField(array("",1,"select","level",$level,$poziomy));
}

if($C_SHOW_TD_IMG) 
{
	$display['img']['arg']['style']='width: 220px';
	$display['img']['arg']['value']=$img;
	$display['img']['arg']['id']='_img';
	$display['img']['label']=label('Title image');
	$display['img']['icon']='img/i_image_n.gif|img/i_image_a.gif|otworzGalerie(2,\'_img\')|'.label('Insert or edit image');
}

if ($C_SHOW_TD_NAVIGATION)
{
	$display['__navi__']['title']=label('Navigation');
}

if ($C_SHOW_TD_MORE)
{
	$display['more']['arg']['style']='width: 95px';
	$display['more']['arg']['value']=$more;
	$display['more']['arg']['id']='_more';
	$display['more']['label']=label('More');
	$display['more']['icon']='img/i_tree_n.gif|img/i_tree_a.gif|openTree(\'_more\',\'_more\',\'\')|'.label("Webpage explorer");
	$display['more']['more']='<img class="k_imgbutton" src="img/i_new_n.gif" onClick="document.getElementById(\'_more\').value=-1" style="cursor:hand;" onmouseover="this.src=\'img/i_new_a.gif\'" onmouseout="this.src=\'img/i_new_n.gif\'" border="0" alt="'.label("New page").'" width="23" height="22" align="absmiddle">';
}

if ($C_SHOW_TD_NEXT)
{
	$display['next']['arg']['style']='width: 95px';
	$display['next']['arg']['value']=$next;
	$display['next']['arg']['id']='_next';
	$display['next']['label']=label('Next page');

	$display['next']['icon']='img/i_tree_n.gif|img/i_tree_a.gif|openTree(\'_next\',\'_next\',\'\')|'.label("Webpage explorer");
	$display['next']['more']='<img class="k_imgbutton" src="img/i_new_n.gif" onClick="document.getElementById(\'_next\').value=-1" style="cursor:hand;" onmouseover="this.src=\'img/i_new_a.gif\'" onmouseout="this.src=\'img/i_new_n.gif\'" border="0" alt="'.label("New page").'" width="23" height="22" align="absmiddle">';
}

if ($C_SHOW_TD_SIZE  && !$swfstyle)
{
	$display['size']['arg']['style']='width: 95px';
	$display['size']['arg']['value']=$size;
	$display['size']['label']=label('Size');
}

if ($C_SHOW_TD_COS )
{
	$display['cos']['arg']['style']='width: 95px';
	$display['cos']['arg']['value']=$cos;
	$display['cos']['label']=label('Number parameter');
}

if ($C_SHOW_TD_COSTXT )
{
	$display['costxt']['arg']['style']='width: 250px';
	$display['costxt']['arg']['value']=$costxt;
	$display['costxt']['label']=label('Text parameter');
}

if ($kameleon->current_server->accesslevel >0 )
{
	$display['accesslevel']['arg']['style']='width: 50px';
	$display['accesslevel']['arg']['value']=$accesslevel;
	$display['accesslevel']['label']=label('Access level');
	$display['accesslevel']['arg']['onChange']='checkAccessLevel(this,'.(0+$kameleon->current_server->accesslevel).')';
}


if ($C_SHOW_TD_VALID )
{
	$display['__valid__']['title']=label('Module date activity');

	$display['nd_valid_from']['label']=label("Valid from");
	$display['nd_valid_from']['arg']['style']='width: 150px';
	$display['nd_valid_from']['arg']['id']='nd_valid_from_id';
	$display['nd_valid_from']['arg']['value']=strlen($nd_valid_from)?FormatujDate($nd_valid_from, 'd-m-Y H:i'):'';
		
	$display['nd_valid_to']['label']=label("Valid to");
	$display['nd_valid_to']['arg']['style']='width: 150px';
	$display['nd_valid_to']['arg']['id']='nd_valid_to_id';
	$display['nd_valid_to']['arg']['value']=strlen($nd_valid_to)?FormatujDate($nd_valid_to, 'd-m-Y H:i'):'';

	$display['nd_valid_to']['more']='
									<script language="Javascript">
									
									Calendar.setup({
										inputField     :    "nd_valid_from_id",  
										ifFormat       :    "'.$ctimeFormat.'", 
										showsTime      :    true,
										align          :    "Tl",           
										timeFormat     :    "24"
									});

									Calendar.setup({
										inputField     :    "nd_valid_to_id",  
										ifFormat       :    "'.$ctimeFormat.'",      
										showsTime      :    true,
										align          :    "Tl",           
										timeFormat     :    "24"
									}); 
									
									</script>
										';
	
}


if ($C_SHOW_TD_COSTXT )
{
	$display['__save__']['title']=label('Save and restore html');
	
	$display['save_plain']['arg']['value']=1;
	$display['save_plain']['label']=label('Save HTML to repository');
	$display['save_plain']['arg']['type']='checkbox';
	$display['save_plain']['arg']['class']='k_cbx';
	
	$display['restore_plain']['arg']['style']='width: 320px';
	$display['restore_plain']['arg']['id']='_restore_plain';
	$display['restore_plain']['label']=label('Restore HTML from file');
	$display['restore_plain']['icon']='img/ftp_inc_n.gif|img/ftp_inc_n.gif|otworzGalerie(13,\'_restore_plain\')|'.label('Explore restore HTMLs');	
}


$_TD_TYPY_DXML=array();
if (is_array($TD_TYPY_DXML[$type+0])) $_TD_TYPY_DXML=array_merge($_TD_TYPY_DXML,$TD_TYPY_DXML[$type+0]);
if (is_array($TD_TYPY_DXML['*'])) $_TD_TYPY_DXML=array_merge($_TD_TYPY_DXML,$TD_TYPY_DXML['*']);


if (count($_TD_TYPY_DXML)) 
{
	$display['__user__']['title']=label('User variables');
	$anything=false;

	$d_xml_a=unserialize(base64_decode($d_xml));
	foreach ($_TD_TYPY_DXML AS $name=>$v)
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
			if(strlen($v[5])) $display[$token]['more']=$v[5];
		}
	}

	if (!$anything) unset($display['__user__']);
	
}



?>
<div id="zakladka_js" style="display: none">
	<textarea id="tdjs_area" name="tdjs" style="width:100%; height:300px"><?=stripslashes($js)?></textarea>
</div>
<script type="text/javascript">
jQueryKam(document).ready(function() {

	var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('tdjs_area'), {
		mode:  "javascript",
		height: (jQueryKam(window).height()-154)+"px",
		tabMode : "shift"
	});

	jQueryKam(".CodeMirror-scroll").css('height', (jQueryKam(window).height()-154)+"px");
});
</script>
<div id="advanced" style="display:none">

	<?
		echo display_opt($display);

		if (is_object($auth_acl) && file_exists(dirname(__FILE__).'/../plugins/acl/kameleon/matrix.php') )
		{
			include(dirname(__FILE__).'/../plugins/acl/kameleon/const.php');
			require_once(dirname(__FILE__).'/../plugins/acl/kameleon/fun.php');

			unset($_SERVER['tree']);
			$_SERVER['acl']=&$auth_acl;

			$RESOURCE=TD_RESOURCE;
			$RESOURCE_ID=$sid;
			if ( acl_hasRight($RESOURCE_ID,PAGE_GRANT_RIGHT,$RESOURCE))
			{
				echo '<h2>'.label('ACL').':</h2>';
				$acl=&$auth_acl;
				$lang=$kameleon->lang;

				$exclude_rights=array(PROOF_RIGHT,FTP_RIGHT,'insert');
				include(dirname(__FILE__).'/../plugins/acl/kameleon/matrix.php');
			}
		}
	
	?>


<div class="km_toolbar">
  <ul>
    <li><a class="km_icon km_iconi_save" href="javascript:ZapiszZmiany()" title="<?=label("Save and exit")?>"><?=label("Save and exit")?></a></li>
  </ul>		
</div>

  
  <div id="modadv" style="display:none;">
    <table border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
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
  </div>

 </td>
</tr>
</table>
</div>
<?
	$autor_update=$kameleon->userFullName($autor_update);
	$autor=$kameleon->userFullName($autor);
	

	$ts="";
	if (strlen($autor)) $ts.=label("Created by")." $autor, ".FormatujDate($nd_create, 'd-m-Y H:i');
	if (strlen($autor_update)) $ts.="<br>".label("Modified by")." $autor_update, ".FormatujDate($nd_update, 'd-m-Y H:i');


	

	include_js("galerie",false);
?>
<script type="text/javascript">

<? if ($C_SHOW_TD_INSIDE){ ?>

function web20selectChanged()
{
	options=document.getElementById('id_options_web20');
	module=document.getElementById('id_select_web20').value;
	if (module.length>0)
	{
		link='include/web20?module='+module+'&options=<?=$web20?>';
		jQueryKam.getJSON(link,function(data){
			options.innerHTML=data;
		});
	}
	else
	{
		options.innerHTML='';
	}
	

}

jQueryKam(function($)
{
	var web20='';
	<?
		$op=unserialize(base64_decode($web20));
		if (isset($op['module'])) echo "web20='".$op['module']."';";
	?>

	jQueryKam.getJSON('include/web20',function(data){
		select=document.getElementById('id_select_web20');
		index=0;
		for(var i in data)
		{	
			select.options[select.length] = new Option(data[i].title, data[i].module);
			if (data[i].module==web20) index=parseInt(i)+1;
		}
		
		if (index>0)
		{
			select.selectedIndex=index;
			web20selectChanged();
		}
	});
    	
	
	
	jQueryKam('#id_select_web20').bind('change', web20selectChanged);
	
});

<? } ?>

function saveLink(targety,action)
{ }
</script>

<div class="km_footer" style="background-image: url(img/nkam/bg_head.jpg);">
  <div class="km_left">
    <?echo $ts?>
  </div>
  <div class="km_right">
    <img src="img/include.gif" title="<?echo label('Copy module identifier')?>" align="absMiddle" style="cursor:pointer" onclick="skopiuj('<?echo $sid;//$uniqueid?>','mask');">
    <?echo label('Module identifier')?>: <b><?echo $uniqueid?></b>
  </div>
</div>
