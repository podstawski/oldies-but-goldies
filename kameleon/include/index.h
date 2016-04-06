<?
	if ($navigationhidden)
	{
		$navigationhidden=false;
		$adodb->SetCookie("navigationhidden",$navigationhidden);
	}


	if ($editmode && !strlen($page)) 
		if (is_array($kameleon->current_server->trans)) 
			if ($kameleon->current_server->trans[$lang][ver]=="$ver")
				if (in_array($kameleon->user[username],$kameleon->current_server->trans[$lang][users]))
				{
					include('include/webtrans.h');
					return;
				}

	$page+=0;


	$kameleon_invitation="&laquo; ";
	$kameleon_invitation.=($first_entrence)?label("Please click the image to start editing !"):label("Please click the image to continue editing !");

	if (!$first_entrence && !$helpmode) $kameleon_invitation="";

	
		

	eval("\$INCLUDE_PATH=\"$DEFAULT_PATH_INCLUDE\";");
	$identity_sysinfo='';

	if (is_Object($WEBPAGE))
	{
		$_SERVER['WEBPAGE']=&$WEBPAGE;

		if (strlen($WEBPAGE->file_name)<2 && $C_SHOW_PAGE_FILENAME )
		{

			if ($WEBPAGE->ver==$ver) $identity_sysinfo=label('This page has no filename - it is not recommended by google');
			$tree_question=label('Rename whole tree?');
			$identity_sysinfo_link='index.php?page='.$page.'&action=ZapiszStroneNazwa';
			$identity_sysinfo_more='onclick="if (confirm(\''.$tree_question.'\')) this.href+=\'Tree\';"';
			$WEBPAGE->file_name="$PATH_PAGES/$page.$SERVER->file_ext";
		}
		$file_name=$WEBPAGE->file_name;

		if (!strlen($WEBPAGE->file_name)) $WEBPAGE->file_name=$file_name;
		
		$INCLUDE_PATH=kameleon_relative_dir($WEBPAGE->file_name,$INCLUDE_PATH);
		
		if ($C_FORGET_DOCBASE && !$KAMELEON_MODE)
		{
			$DOCBASE="";
			$UIMAGES=kameleon_relative_dir($WEBPAGE->file_name,$UIMAGES);
			$IMAGES=kameleon_relative_dir($WEBPAGE->file_name,$IMAGES);
			$UFILES=kameleon_relative_dir($WEBPAGE->file_name,$UFILES);
		}
		if ($C_FORGET_DOCBASE) $DOCBASE="";
		
	}



	
	if ($GENERATE_ONLY_WEBPAGE_OBJECT==1) return;

	include ("include/remote_pre.h");

	include ("include/remote_action.h");

	if (!$CONST_PARSER_INTEGRATED && file_exists("$SZABLON_PATH/pagebegin.h")) 
	{
		include("$SZABLON_PATH/pagebegin.h");
	}
	if ($CONST_PARSER_INTEGRATED)
	{	
		include("include/pagebegin.h");
	}


    
	if ($editmode) 
	{
		include_js("lang/pl");
		include_js("jquery-1.4");
		include_js("jquery-ui.min");
		include_js("draging");
		include_js("kameleon");
		include('include/clib.h');
		
		include("ajax_variables.php");
	?>

<script type="text/javascript">

function showMenu()
{
	alert('<?php echo label("Please wait");?>');
	void(0);
}

function tdedit(page_id,sid,hash,tab_name)
{
	document.getElementById('km_tdeditpass').page_id.value=page_id;
	document.getElementById('km_tdeditpass').sid.value=sid;
	document.getElementById('km_tdeditpass').hash.value=hash;
	document.getElementById('km_tdeditpass').td_width.value=document.getElementById(tab_name).offsetWidth;
	document.getElementById('km_tdeditpass').submit();
}


function menu(page)
{
	m=prompt("<?echo label("Issue the menu number");?>","");
	if (m+0>0 ) zmiana(page+":"+m,"DodajTDMenu");	
}

function zmiana(page,action)
{
	if ((action=="UsunStrone" || action=="UsunLink" || action=="UsunTD") 
		&& !confirm("<?echo label("Are you sure you want to delete");?> ?")) return;

	if (action=="SkopiujTD" || action=="SkopiujHF") 
	{
		pagedest=prompt("<?echo label("To what page");?>","<?echo $page?>");
		if (pagedest==null) return;
		//if ( pagedest==<?echo $page?> ) return;


		page+=":"+pagedest;
	}

	document.zmiany.page_id.value=page;	
	document.zmiany.action.value=action;	
	document.zmiany.submit();
}

function doMenuChange( page_id, sid, server, atrybut, wartosc )
{
	//alert('page_id='+page_id+', sid='+sid+', server='+server+', atrybut='+atrybut+', wartosc='+wartosc);
	document.MenuChange.sid.value=sid;
	document.MenuChange.page_id.value=page_id;
	document.MenuChange.server.value=server;
	document.MenuChange.atrybut.value=atrybut;	
	document.MenuChange.wartosc.value=wartosc;	
	document.MenuChange.submit();
}

function addMenu(menu_id, page, page_id, sid, server)
{
	//alert(menu_id+' '+page_id+' '+sid+' '+server);
	document.addMenu.menu_id.value=menu_id;
	document.addMenu.page.value=page;
	document.addMenu.page_id.value=page_id;
	document.addMenu.sid.value=sid;
	document.addMenu.server.value=server;
	document.addMenu.submit();	
}

function elementMove(direction, page, page_id, pri)
{
	document.elementMove.page.value=page;
	document.elementMove.wart.value=page_id;
	document.elementMove.pri.value=pri;
	document.elementMove.dir.value=direction;
	document.elementMove.submit();	
}

function changeVisibility(page, page_id, pri, action)
{
	document.changeVisibility.page.value=page;
	document.changeVisibility.page_id.value=page_id;
	document.changeVisibility.pri.value=pri;
	document.changeVisibility.action.value=action;
	document.changeVisibility.submit();	
}

function zmiana_strony(page)
{
	document.forms['gotopage'].page.value=page;
	document.forms['gotopage'].submit();
}

function zmiana_menu(menu)
{
	document.gotomenu.menu.value=menu;
	document.gotomenu.submit();
}


function kopia_strony(page)
{
	document.copypage.pagesrc.value=page;
	document.copypage.submit();

}



function wk_show(id)	
{
	document.all[id].style.visibility = 'visible';	
	document.all[id].style.display = 'inline';	
	document.all['c'+id].style.filter="progid:DXImageTransform.Microsoft.Alpha(Opacity=40, FinishOpacity=10, Style=0, StartX=0, FinishX=100, StartY=0, FinishY=100)";
}
function wk_hide(id)
{
	document.all[id].style.visibility = 'hidden';	
	document.all[id].style.display = 'none';	
	document.all['c'+id].style.filter="";

}

function getAbsObjPos(el) 
{
	var r = { x: el.offsetLeft, y: el.offsetTop };
	if (el.offsetParent)
	{
		var tmp = getAbsObjPos(el.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}
	return r;
}

function kameleon_mouse_event(e, obj,field,value,onoroff)
{
	//event.cancelBubble=true;

	p=getAbsObjPos(obj);
	p.h=obj.clientHeight;
	p.w=obj.clientWidth;

	p.x2=p.x+p.w;
	p.y2=p.y+p.h;
	//ex=event.clientX;
	//ey=event.clientY;
	
	// START
	posx=0;posy=0;
  var ev=(!e)?window.event:e;//IE:Moz
  if (ev.pageX) //Moz
  {
    posx=ev.pageX+window.pageXOffset;
    posy=ev.pageY+window.pageYOffset;
  }
  else if(ev.clientX) //IE
  {  
    if(document.documentElement)//IE 6+ strict mode
    {
      posx = ev.clientX + document.documentElement.scrollLeft;
      posy = ev.clientY + document.documentElement.scrollTop;
    }
    else if(document.body)//Other IE
    {
      posx = ev.clientX + document.body.scrollLeft;
      posy = ev.clientY + document.body.scrollTop;
    }
  }
  else
  {
    return false;
  }
  ex=posx;
  ey=posy;
  // STOP
  
  
	ok=0;
	
	if (onoroff==1) if (ex>=p.x && ex<=p.x2 && ey>=p.y && ey<=p.y2 ) ok=1;
	if (onoroff==0) if ( ex<p.x || ex>p.x2 || ey<p.y || ey>p.y2 ) ok=1;

	if (ok==1) 
	{	
		e='obj.'+field+'=value;';
		eval(e);
	}
}


function svn_start(html)
{
	prt='<?php echo label("Any other file besides")?>: '+html;
	files=prompt(prt,'');

	if (files==null) return;

	document.svn_start_form.svn_files.value=html+':'+files;
	document.svn_start_form.submit();
}

function svn_end(what)
{
		a=open('svn.<?echo $KAMELEON_EXT?>?what='+what+'&page=<?echo $page?>','SVN',
			"toolbar=0,location=0,directories=0,\
			status=1,menubar=0,scrollbars=1,resizable=0,\
			width=400,height=400");
		a.focus();
}

</script>
<?php
// JS potrzebny do menu na belkach
include_js("jsdomenu",false);
//
?>


<form name=zmiany style="margin:0; padding: 0;" method=post action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=action value="">
 <input type=hidden name=page value="<?echo $page?>">
 <input type=hidden name=page_id value="">
</form>


<form style="margin:0; padding: 0;" name=svn_start_form method=post action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=action value="SvnStart">
 <input type=hidden name=page value="<?echo $page?>">
 <input type=hidden name=svn_files value="">
</form>

<form style="margin:0; padding: 0;" name="MenuChange" method="post" action="<?echo $SCRIPT_NAME?>">
 <input type=hidden name=sid value="">
 <input type=hidden name=atrybut value="">
 <input type=hidden name=wartosc value="">
 <input type=hidden name=page_id value="">
 <input type=hidden name=page value="<?echo $page?>">
 <input type=hidden name=server value="">
 <input type=hidden name=action value="ZapiszTDshort">
 <input type=hidden name=lang value="<?php echo $lang;?>">
</form>

<form style="margin:0; padding: 0;" name=addMenu method=post action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=menu_id value="">
 <input type=hidden name=page value="<?echo $page; ?>">
 <input type=hidden name=page_id value="">
 <input type=hidden name=sid value="">
 <input type=hidden name=ver value="<?php echo $ver;?>">
 <input type=hidden name=server value="">
 <input type=hidden name=lang value="<?php echo $lang;?>">
 <input type=hidden name=action value="ZapiszBelkaMenu">
</form>

<form style="margin:0; padding: 0;" name=changeVisibility method=get action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=page value="">
 <input type=hidden name=page_id value="">
 <input type=hidden name=pri value="">
 <input type=hidden name=action value="">
</form>


<form style="margin:0; padding: 0;" name=elementMove method=post action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=table value="td">
 <input type=hidden name=pole value="page_id">
 <input type=hidden name=page value="<?echo $page?>">
 <input type=hidden name=page_id value="">
 <input type=hidden name=pri value="">
 <input type=hidden name=dir value="">
 <input type=hidden name=wart value="">
 <input type=hidden name=action value="Move">
</form>


<form style="margin:0; padding: 0;" name=gotopage method=post action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=page value="">
</form>

<form style="margin:0; padding: 0;" name=gotomenu method=post action='menus.<?echo $KAMELEON_EXT?>'>
 <input type=hidden name=menu value="">
</form>

<form style="margin:0; padding: 0;" name=copypage method=post action=<?echo $SCRIPT_NAME?>>
 <input type=hidden name=pagesrc>
 <input type=hidden name=page value="<?echo $page?>">	
 <input type=hidden name=referer value="<?echo $referer?>">	
 <input type=hidden name=ref_menu value="<?echo $ref_menu?>">	
 <input type=hidden name=action value=SkopiujStroneWTejWersji>
</form>	




<form style="margin:0; padding: 0;" name="tdeditpass" method="get" id="km_tdeditpass" action="tdedit.<?echo $KAMELEON_EXT?>">
 <input type=hidden name=page_id>
 <input type=hidden name=sid>
 <input type=hidden name=hash>
 <input type=hidden name=page value="<?echo $page?>">	 
 <input type=hidden name=setreferpage value="<?echo $page?>">	 
 <input type=hidden name=td_width>
</form>	

<form style="margin: 0; padding: 0;" method="post" name="kameleon_tddrag" action="<?echo $SCRIPT_NAME?>">
  <input type="hidden" name="page" value="<?echo $page?>" />
  <input type="hidden" name="action" value="MoveDrag" />
  <input type="hidden" name="kolejka" value="" />
  <input type="hidden" name="levelek" value="" />
  <input type="hidden" name="tdsid" value="" />
</form>

<div id="km_hinter"></div>

<?
	
	if (is_array($CONST_LANGS_RELATED[$lang]) && is_object($WEBPAGE)) include ("include/langs_related.php");

	} // end of editmode

	
	if ($C_MULTI_HF && $WEBPAGE->type>0 && $SERVER->header>-1*$MULTI_HF_STEP )
	{
		$SERVER->header-=$MULTI_HF_STEP*$WEBPAGE->type;
		$SERVER->footer-=$MULTI_HF_STEP*$WEBPAGE->type;						
	}


	$border=$editmode?1:0;

	$marquee_color="Red";
	if (!strlen($kameleon_warrning)) 
	{
		$kameleon_warrning = $kameleon_invitation;
		$marquee_color="Black";
	}
	$marquee="&nbsp;";
	if (strlen($kameleon_warrning))
		$marquee="<marquee hspace=15 truespeed Scrolldelay=30 behavior=\"slide\" 
					Scrollamount=5
					STYLE=\"color:$marquee_color; font-weight:Bold; font-size:15px ;
					filter:progid:DXImageTransform.Microsoft.Shadow(color=#e0e0e0, strength=1, direction=135)\">
					$kameleon_warrning </marquee>";

	if ($KAMELEON_MODE)
	{
		
		if ($editmode) 
		{
			if ($CONST_MODE!="express")
			{
				include("include/navigation.h");
				include("include/page-options.h");
			}
			
		}
		else
		{   
           	$seteditmode=1;
            echo "
            <div style=\"background-color: #b8b8b8; padding: 4px 7px; overflow: hidden; background-image: url('".$kameleon->user[skinpath]."/img/km_toolbar_bg.jpg');\">
              <ul style=\"display: block; float: left; list-style:none; margin:0; padding:0;\">
                <li style=\"display: block; margin: 0; padding: 4px 0;\"><a href=\"$SCRIPT_NAME?page=$page&seteditmode=1\" style=\"display: block; background-image: url('".$kameleon->user[skinpath]."/img/km_sprite.png'); height: 24px; width: 28px; background-position: -338px -934px; text-indent: -300px; overflow: hidden;\" title=\"".label("Show edit mode")."\">".label("Show edit mode")."</a></li>
              </ul>
            </div>
            ";
        } 
		

		if ($CONST_MODE=="express")
		{
			;
		}
		else
		{
			echo "<div class=\"km_ruler_x\"></div>";
		}
	}
	
	
	if ($KAMELEON_MODE) if (!$kameleon->checkRight('write','page',$page)) $editmode=0;
	

	//rozdzielenie naglowka i stopki oraz zachowanie tego co bylo (RP)

	if ($CONST_PARSER_INTEGRATED)
	{
		
		
		$page_parts[]=array($SERVER->header,"include/header.h");
		$page_parts[]=array($page,"include/body.h");
		$page_parts[]=array($SERVER->footer,"include/footer.h");
		
	}
	else
	{
		if (file_exists("$SZABLON_PATH/header.h")) 
		{
			if ($SERVER->header) $page_parts[]=array($SERVER->header,"$SZABLON_PATH/header.h");
		}
		else
		{
			if ($SERVER->header) $page_parts[]=array($SERVER->header,"$SZABLON_PATH/header-footer.h");
		}

		$page_parts[]=array($page,"$SZABLON_PATH/body.h");

		if (file_exists("$SZABLON_PATH/footer.h")) 
		{
			if ($SERVER->footer) $page_parts[]=array($SERVER->footer,"$SZABLON_PATH/footer.h");
		}
		else
		{
			if ($SERVER->footer) $page_parts[]=array($SERVER->footer,"$SZABLON_PATH/header-footer.h");
		}
	}

	for ($page_parts_index=0;$page_parts_index<count($page_parts) && is_Object($WEBPAGE);$page_parts_index++)
	{
	   $page_part=$page_parts[$page_parts_index];
	   $page_id=$page_part[0];

		$this_editmode=0;
		if ( ($hf_editmode && $editmode && $page_parts_index!=1) || (!$hf_editmode && $editmode && $page_parts_index==1) ) $this_editmode=1;

		if ($CONST_MODE=="express" && $editmode)
		{
			$this_editmode=1;
		}

	   if (is_object($WEBPAGE) && file_exists($page_part[1])) 
	   {
			if ( $this_editmode ) 
			{
				$query="SELECT max(pri) as maxpri FROM webtd
					WHERE page_id=$page_id AND server=$SERVER_ID
					AND lang='$lang' AND ver=$ver";
				parse_str(ado_query2url($query));
				if ($log_also_select) logquery($query);
				$maxpri+=1;
				
				$query="SELECT count(*) AS c_this_ver FROM webtd
					WHERE page_id=$page_id AND server=$SERVER_ID
					AND lang='$lang' AND ver=$ver";
				parse_str(ado_query2url($query));

				$insert_td=label("Insert module");
				$hash="kameleon_td${page_parts_index}_$maxpri";
	
				$copy="";
				$c=kameleon_td_count($page_id,$ver,$lang,0);

				if ( $page_id<0 && $c )
					$copy="<li><a class=\"km_icon km_iconi_copy_m\" href=\"#\" onclick=\"skopiuj('".$page_id."','area')\" title=\"".label("Copy area")."\">".label("Copy area")."</a></li>";
	
				if ( $page_id<0 && !$c_this_ver )
					$copy.="<li><a class=\"km_icon km_iconi_paste_m\" href=\"#\" onclick=\"wklej('".$page_id."','area')\" title=\"".label("Paste area")."\">".label("Paste area")."</a></li>";
				if ( $page_id<0 && !$c_this_ver )
					$copy.="<li><a class=\"km_icon km_iconi_paste_m\" href=\"#\" onclick=\"wklej('".$page_id."','farea')\" title=\"".label("Paste area from file")."\">".label("Paste area from file")."</a></li>";

	
				if ( $page_id>=0 || $c )
					$copy.="<li><a class=\"km_icon km_iconi_paste_m\" href=\"#\" onclick=\"wklej('".$page_id."','td')\" title=\"".label("Paste module")."\">".label("Paste module")."</a></li>";
		
				$buttons="
        <div class=\"km_section_btn\">
          <ul>
            <li><a class=\"km_icon km_iconi_new_m\" href=\"$SCRIPT_NAME3?page=$page&page_id=$page_id&action=DodajTD&hash=$hash#$hash\" title=\"".$insert_td."\">".$insert_td."</a></li>
            ".$copy."
          </ul>
        </div>";
								
				if ($ver != $WEBPAGE->ver )
					$buttons="<div class=\"km_section_alert\">".label("No area in this version")."</div>";

				if ($CONST_MODE=="express")
				{
				}
				else
				{
					echo "<div class=\"km_section\" id=\"page_part_".$page_part[1]."\">".$buttons."\n";
				}
			}
			include($page_part[1]);
			if ($this_editmode && $CONST_MODE!="express") echo "</div>\n";	
	    }
	}

	if (!$CONST_PARSER_INTEGRATED && file_exists("$SZABLON_PATH/pageend.h")) 
	{
		include("$SZABLON_PATH/pageend.h");
	}
	if ($CONST_PARSER_INTEGRATED)
	{
		include("include/pageend.h");
	}

	include ("include/remote_post.h");
	
  if ($editmode) 
  { 
    include ("include/jsdomenu.h");
  }
  
