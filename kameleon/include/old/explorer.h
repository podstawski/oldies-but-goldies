<?
	if ($C_MIKOLAJ_EXPERIMENTAL)
	{
		include("include/explorer_mikolaj.h");
		return;
	} 

	//22-09-2003, Robert Posiadala, przenioslem style do kameleon.css aby mozliwe bylo ich nadpisywanie
	//ustawienie parametrow, do muliti tez
	if (strlen($lng))
		$lang=$lng;
	else
		$lng=$lang;

	if ($multi)
	{
		$SQL="SELECT DISTINCT lang AS l FROM webpage WHERE server=$SERVER_ID AND prev=-1";
		$langres=pg_Exec($db,$SQL);
		for ($i=0;$i<pg_numrows($langres);$i++)
		{
			parse_str(pg_ExplodeName($langres,$i));
			if ($lng==$l)
				$lang_name="<b>".label($l)."</b>";
			else
				$lang_name=label($l);
			if ($i)
				echo " | <a class=k_text href=\"$PHP_SELF?node=-1&multi=1&lng=$l\">$lang_name</a>";
			else
				echo "<a class=k_text href=\"$PHP_SELF?node=-1&multi=1&lng=$l\">$lang_name</a>";

		}
	}
	if (strlen($referer))
		$node=$referer;
	$params="lng=$lang&page=$page&node=$node&TreeFollowLink=$TreeFollowLink&TreeDontShowPageNumber=$TreeDontShowPageNumber&referer=$referer";
?>
<script>
var node_html="";
var node=-1;
var parent;

var node_visibility_hidden,	node_nostitemap;
var node_visibility="",node_visibility_a="";
var node_visibility_img='img/i_visible_n.gif';
var node_visibility_imga='img/i_visible_a.gif';
var node_visibility_hidden_img='img/i_invisible_n.gif';
var node_visibility_hidden_imga='img/i_invisible_a.gif';

var node_sitemap="",node_sitemap_a="";
var node_sitemap_img='img/i_sm_n.gif';
var node_sitemap_imga='img/i_sm_a.gif';
var node_sitemap_hidden_img='img/i_nsm_n.gif';
var node_sitemap_hidden_imga='img/i_nsm_a.gif';

var node_nostitemap_img;

var lang='<?echo $lang?>';
var nodea;
var active_node=null;

function expnode(n,lng)
{
	node_html="";
	node=n;
	parent=document.all[lng+'_'+n].p;
	
	d= new Date();
	sec=d.getUTCMilliseconds();

//	przestaw plusik na minusik
	plusminus=document.all['img_'+n+'_'+lng];
	if (plusminus!=null)
	{
		re = /plus/;
		txt=plusminus.src;
		if (txt.match(re))
		{
			plusminus.src=txt.replace(re,'minus');
			exp_tree.src='explorer-main.php?lng='+lang+'&node=-1&parent='+n+'&t='+sec+'<?echo $params?>';
		}
		else
		{
			re = /minus/;
			txt=plusminus.src;
			if (txt.match(re))
			{
				plusminus.src=txt.replace(re,'plus');
				document.all[lng+'_'+n].innerHTML='';
			}
		}
		//	podœwietl aktywn¹ a ukryj t¹ która by³a podœwietlona
		if (active_node!=null)
		{
			active_node.style.backgroundColor='#FFFFFF';
			active_node.borderColor='';
		}
		active_node=document.all['m_'+n+'_'+lng];
		active_node.style.backgroundColor='#c0c0c0';
		active_node.borderColor='#000000';
		active_node.scrollIntoView(true);
	}
}

function expnode_data(obj)
{
	var o;
	o=document.all[obj];
//	alert(o);
	if  (o!=null)
	{
//		alert('in');
		o.innerHTML='';
		o.innerHTML=node_html;
	}
	return false;
}

function expmenu(n,lng)
{
	node=n;
	parent=document.all[lng+'_'+n].p;
	active_node=document.all['m_'+n+'_'+lng];
	node_visibility_hidden=document.all[lng+'_'+n].h;
	node_sitemap=document.all[lng+'_'+n].s;
	lang=lng;

	if 	(node_visibility_hidden=='1')
	{
		node_visibility=node_visibility_hidden_img;
		node_visibility_a=node_visibility_hidden_imga;
		document.all['menu_eye'].src=node_visibility;
	}
	else
	{
		node_visibility=node_visibility_img;
		node_visibility_a=node_visibility_imga;
		document.all['menu_eye'].src=node_visibility;
	}

	if 	(node_sitemap=='1')
	{
		node_sitemap=node_sitemap_hidden_img;
		node_sitemap_a=node_sitemap_hidden_imga;
		document.all['menu_sitemap'].src=node_sitemap;
	}
	else
	{
		node_sitemap=node_sitemap_img;
		node_sitemap_a=node_sitemap_imga;
		document.all['menu_sitemap'].src=node_sitemap;
	}

	oWorkItem=event.srcElement;

	if(oMenu.style.display=="none" || oMenu.style.display=="")
	{
		oMenu.style.top=event.clientY+document.body.scrollTop;
		oMenu.style.left=event.clientX+document.body.scrollLeft;
		oMenu.style.display="block";
		oMenu.style.visibility="visible";
		return false;
	}
	else
	{
		oMenu.style.display="none";
		oMenu.style.visibility="hidden";
	}
	return false;

}
function expmenu_hide()
{
	oMenu.style.display="none";
	oMenu.style.visibility="hidden";
}


function expdoit(page,action)
{
	expmenu_hide();
	if (page=='')
	{
		page=node;
	}
	document.zmiany.referer.value=parent;

	if ((action=="UsunStrone" || action=="UsunLink" || action=="UsunTD") 
		&& !confirm("<?echo label("Are you sure you want to delete");?> ?")) return;

	switch (action)
	{
		case "DodajStrone": 
			document.zmiany.page.value=-1;	
			document.zmiany.page_id.value=-1;	
			document.zmiany.referer.value=node;
			document.zmiany.action.value=action;	
			document.zmiany.submit();
			break;
		case "visibility":
		case "SiteMapHideShow":
			document.zmiany.page.value=page;	
			document.zmiany.action.value=action;	
			document.zmiany.submit();
			break;
		case "EdytujStrone":
			href='strona.<?echo $KAMELEON_EXT?>';
			document.editpage.page.value=page;	
			document.editpage.setreferpage.value=page;	
			document.editpage.action=href;	
			document.editpage.submit();

			break;
		case "copypage":
			skopiuj(page,'page');
			break;
		case "pastepage":
			document.zmiany.referer.value=page;
			document.zmiany.page.value=-1;
			wklej(-1,'page');
			break;
		case "ReTitle":
			oldtitle=document.all[lang+'_'+node].t;;
			newtitle=prompt('<?echo label("Title")?>',oldtitle);
			if (newtitle!=null)
			{	
				document.zmiany.title.value=newtitle;	
				document.zmiany.table.value="page";	
				document.zmiany.page.value=page;	
				document.zmiany.action.value=action;	
				document.zmiany.submit();
			}
			break;
		case "UsunStrone":
			document.zmiany.page_id.value=page;	
			document.zmiany.page.value=page;	
			document.zmiany.action.value=action;	
			document.zmiany.submit();
			break;

		default:
			document.zmiany.page_id.value=page;	
			document.zmiany.action.value=action;	
			document.zmiany.submit();
	}
}

function skopiuj(obj,co)
{
	clib="<?echo "$SERVER_ID:$lang:$ver"?>:"+obj;
	ciacho="clib"+co+"="+clib;
	document.cookie=ciacho;
	alrt="";
	if (co=="td") alrt="<?echo label("Module was copied to kameleon cliboard")?>";
	if (co=="page") alrt="<?echo label("Page was copied to kameleon cliboard")?>";
	if (co=="area") alrt="<?echo label("Area was copied to kameleon cliboard")?>";	
	
	if (alrt.length)
	{
		document.cookie=ciacho;
//		alert (ciacho);
		alert (alrt);
	}
}
function wklej(obj,co)
{
	var kukis_value,kukis_key,kukis_arr,i,err;

	ciacho='clib'+co+'';
	ciacha=document.cookie;
	//cookie zawiera spacje wiec trzeba je usunac
	re = / /g;
	ciacha=ciacha.replace(re,'');
	
	arr=ciacha.split(';');
	err=1;
	for (i=0;i<arr.length;i++)
	{
		kukis=arr[i];
		kukis_arr=kukis.split('=');

		kukis_key=kukis_arr[0];
		kukis_value=kukis_arr[1];
		if (kukis_key==ciacho) err=0;
	}
	if (err)
	{ 
		alert ('<? echo label("Nothing found in kameleon cliboard") ?>');
		return;
	}

	document.zmiany.page_id.value=obj;
	document.zmiany.action.value="Wklej_"+co;
	document.zmiany.submit();
}

</script>


<span id="<?echo $lang?>_-1" onClick="expmenu_hide();"></span>
<script id="exp_tree" src="explorer-main.<?echo $KAMELEON_EXT?>?<?echo $params;?>" onreadystatechange="expnode_data('<?echo $lang?>_'+node);"></script>


<form name=zmiany method=post action="<?echo $SCRIPT_NAME?>">
 <input type=hidden name=action value="">
 <input type=hidden name=page value="0">
 <input type=hidden name=page_id value="">
 <input type=hidden name=title value="">
 <input type=hidden name=table value="">
 <input type=hidden name=referer value="<?echo $referer?>">
</form>

<form name=editpage method=get action="">
 <input type=hidden name=page value="0">
 <input type=hidden name=page_id value="">
 <input type=hidden name=setreferpage value="">
</form>

<div id="oMenu" class="ex_menu"><table id="toolbar" border="0" cellspacing="0" bgcolor="#c0c0c0">
	<tr>
	
		<td><img onclick="expdoit('','DodajStrone')" border=0 src=img/i_new_n.gif align=middle onmouseover="this.src='img/i_new_a.gif'" onmouseout="this.src='img/i_new_n.gif'" width=23 height=22 align=absmiddle alt="<? echo label("Add new page")?>"></td>
		<td><img onclick="expdoit('','ReTitle')" border=0 src=img/i_title_n.gif align=middle onmouseover="this.src='img/i_title_a.gif'" onmouseout="this.src='img/i_title_n.gif'" width=23 height=22 align=absmiddle alt="<?echo label("Edit title")?>"></td>
		<td><img onclick="expdoit('','EdytujStrone')" border=0 src=img/i_property_n.gif align=middle onmouseover="this.src='img/i_property_a.gif'" onmouseout="this.src='img/i_property_n.gif'" width=23 height=22 align=absmiddle alt="<?echo label("Edit page")?>"></td>

		<td><img onclick="expdoit('','visibility')" id="menu_eye" border=0 src="" align=middle onmouseover="this.src=node_visibility_a;" onmouseout="this.src=node_visibility" width=23 height=22 align=absmiddle alt="<?echo label("Page invisible")?>"></td>
		<td><img onclick="expdoit('','SiteMapHideShow')" id="menu_sitemap" border=0 src="" align=middle onmouseover="this.src=node_sitemap_a" onmouseout="this.src=node_sitemap" width=23 height=22 align=absmiddle alt="<?echo label("Page invisible in sitemap")?>"></td>

		<td><img onclick="expdoit('','copypage')" border=0 src=img/i_copy_n.gif align=middle onmouseover="this.src='img/i_copy_a.gif'" onmouseout="this.src='img/i_copy_n.gif'" width=23 height=22 align=absmiddle alt="<?echo label("Copy page")?>"></td>
		<td><img onclick="expdoit('','pastepage')" border=0 src=img/i_paste_n.gif align=middle onmouseover="this.src='img/i_paste_a.gif'" onmouseout="this.src='img/i_paste_n.gif'" width=23 height=22 align=absmiddle alt="<?echo label("Paste page")?>"></td>
		
		<td><img onclick="expdoit('','UsunStrone')" border=0 src=img/i_delete_n.gif align=middle onmouseover="this.src='img/i_delete_a.gif'" onmouseout="this.src='img/i_delete_n.gif'" width=23 height=22 align=absmiddle alt="<?echo label("Delete page")?>"></td>
	</tr>
</table></div>
