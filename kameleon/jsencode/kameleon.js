// TABLICE WYKORZYSTYWANE
	var km_edited = new Array();
	km_edited['linksid'] = new Array();
	km_edited['tdsid'] = new Array();
	km_edited['tosave'] = 0;

// PRELOADER

	km_preloader_show = function(){
		if (jQueryKam("#km_preloader")) jQueryKam("#km_preloader").remove();
		var prel = jQueryKam("<div></div>");
		prel.attr("id","km_preloader");
		prel.css("z-index","1000000000");
		prel.css("height",jQueryKam(document).height()+"px");
		var msg = jQueryKam("<div></div>");
		msg.addClass("km_preloader_msg");
		msg.html(km_lang["trwa_zapisywanie"]);
		msg.css("top",(Math.round(jQueryKam(window).height()/2)-100+jQueryKam(window).scrollTop())+"px");
		msg.css("left",(Math.round(jQueryKam(document).width()/2)-150)+"px");
		prel.append(msg);
		km_flash_hide();
		jQueryKam("body").append(prel);
	}
	
	km_preloader_hide = function(){
		km_flash_show();
		if (jQueryKam("#km_preloader")) jQueryKam("#km_preloader").remove();
	}
	
	km_flash_hide = function() { 
		jQueryKam("object").each( function () {
			if (jQueryKam(this).css('display')=='block') jQueryKam(this).addClass('km_preloader_display').hide();
		});
	}
	
	km_flash_show = function() {
		jQueryKam(".km_preloader_display").removeClass("km_preloader_display").show();
	}
	
// DROPMENU
	
	km_dropmenu_show = function(ev,type){ // bookmark / lang / server / plugin
		jQueryKam('body').unbind('click.km');
		if (km_droplist['active'].length>0) jQueryKam("#km_"+km_droplist['active']+"_link").removeClass("km_"+km_droplist['active']+"_link_active");
		if (km_droplist['active']==type)
		{
			km_droplist['active']="";
			km_dropmenu_hide();
		}
		else
		{
			jQueryKam(".km_dropmenu_href").removeClass("km_dropmenu_active");
			if (jQueryKam("#km_dropmenu")) jQueryKam("#km_dropmenu").remove(); 
			var to = jQueryKam(ev.target).offset();
			var tamto = jQueryKam("#km_"+type+"_link").offset();
			var div = jQueryKam("<div></div>").attr("id","km_dropmenu").hide().addClass('km_dropmenu_'+type).css({ 'left' : tamto.left+'px', 'top' : (tamto.top+jQueryKam("#km_"+type+"_link").outerHeight())+'px'});
			var ul = jQueryKam("<ul></ul>");
			for (var n=0; n<km_droplist[type].length; n++)
			{
				(function () {
					var c = km_droplist[type][n];
					var href = jQueryKam("<a></a>").addClass(c['css']).attr('title',c['title']).html(c['title']);
					if (c['img'].length>0) { href.css('background-image',"url('"+c['img']+"')"); }
					if (c['href'].length) href.attr('href',c['href']);
					var fun = function () { eval(c['onclick']); };
					if (c['onclick'].length) href.bind('click', fun);
					href.attr('rel','km_droplink');
					var li = jQueryKam("<li></li>").append(href);
					ul.append(li);
				}());
			}
			jQueryKam(div).append(ul);
			km_droplist['active']=type;
			ev.stopPropagation();
			jQueryKam('body').append(div).unbind('click.km').bind('click.km', function(ev) { km_dropmenu_click(ev,type)});
			jQueryKam("#km_dropmenu").slideDown('fast');
			jQueryKam("#km_"+type+"_link").addClass("km_"+type+"_link_active");
		}
	}
	
	km_dropmenu_hide = function(){
		jQueryKam(".km_dropmenu_href").removeClass("km_dropmenu_active");
		jQueryKam("#km_dropmenu").remove();
	}
	
	km_dropmenu_click = function(ev,type){
		jQueryKam('body').unbind('click.km');
		jQueryKam("#km_"+type+"_link").removeClass("km_"+type+"_link_active");
		km_droplist['active']="";
		km_dropmenu_hide();
	}
	
	km_dropmenu_load = function(type){
		jQueryKam.getJSON(km_infos["ajax_link"], { return_link : km_infos["return_link"], page : km_infos["page"], page_link : km_infos["page_link"], action : 'dropmenu_load_'+type }, function(data) {
			if (data.status=='1') 
			{
				km_droplist[type] = new Array();
				if (data.items)
				{
					for (var n=0; n<data.items.length; n++)
					{
						var item = new Array();
						item['title']=data.items[n].title;
						item['img']=data.items[n].img;
						item['href']=data.items[n].href;
						item['onclick']=data.items[n].onclick;
						item['css']=data.items[n].css;
						km_droplist[type].push(item);
					}
				}
				
				if (type=="plugin")
					jQueryKam("#km_plugins_link").css('display','block'); 
					
				if (type=="bookmark")
				{
					if (data.dodany=='1')
						jQueryKam("#km_bookmark_link span").removeClass("km_iconi_bookmark").addClass("km_iconi_bookmark_on");
					else
						jQueryKam("#km_bookmark_link span").removeClass("km_iconi_bookmark_on").addClass("km_iconi_bookmark");
				}
					
			}
		});
	}
	
	km_dropmenu_init = function(){
		if (jQueryKam("#km_plugin_link").length)
		{
			km_dropmenu_load("plugin");
			jQueryKam("#km_plugin_link").bind('click', function(ev){
				km_dropmenu_show(ev,"plugin"); 
			});
		}
		if (jQueryKam("#km_server_link").length)
		{
			km_dropmenu_load("server");
			jQueryKam("#km_server_link").bind('click', function(ev){
				km_dropmenu_show(ev,"server"); 
			}).disableSelection();
		}
		if (jQueryKam("#km_lang_link").length)
		{
			km_dropmenu_load("lang");
			jQueryKam("#km_lang_link").bind('click', function(ev){
				km_dropmenu_show(ev,"lang"); 
			}).disableSelection();
		}
		if (jQueryKam("#km_bookmark_link").length)
		{
			km_dropmenu_load("bookmark");
			jQueryKam("#km_bookmark_link").bind('click', function(ev){
				km_dropmenu_show(ev,"bookmark"); 
			}).disableSelection();
		}
	}
	
// ZAPISYWANIE TREŚCI

	km_edited_add = function(sid,type){
		jQueryKam("#km_editsave").show();
		window.onbeforeunload = null;
		jQueryKam(window).bind('beforeunload', function() {
			return "Zmiany nie zostały zapisane";
		});
		if (!km_inarray(sid,km_edited[type])) {
			km_edited[type].push(sid);
			km_edited['tosave']=km_edited['linksid'].length+km_edited['tdsid'].length;
		}
	}

	km_edited_remove = function(sid,type){
		var key = km_position_in_array(sid,km_edited[type]);
		if (key) km_edited[type].splice(key,1);
		km_edited['tosave']-=1;
		km_edited_checksaves();
	}

	km_edited_save = function(){
		km_preloader_show();
		if (km_edited['linksid'].length>0){
			for (var i=0;i<km_edited['linksid'].length;i++)
				km_content_alt_save(km_edited['linksid'][i]);
		}
		if (km_edited['tdsid'].length>0){
			for (var i=0;i<km_edited['tdsid'].length;i++)
				km_content_title_save(km_edited['tdsid'][i]);
		}
		km_edited_checksaves();
	}
	
	km_edited_checksaves = function(){
		if (km_edited['tosave']==0){
			jQueryKam("#km_editsave").hide();
			km_preloader_hide();
			window.onbeforeunload = null;
			return true;
		}
		else
			return false;
	}
	
	km_cursor_position = function(editableDiv){
		var caretPos = 0, containerEl = null, sel, range;
	    if (window.getSelection) {
	        sel = window.getSelection();
	        if (sel.rangeCount) {
	            range = sel.getRangeAt(0);
	            if (range.commonAncestorContainer.parentNode == editableDiv) {
	                caretPos = range.endOffset;
	            }
	        }
	    }
	    else if (document.selection && document.selection.createRange) {
	        range = document.selection.createRange();
	        if (range.parentElement() == editableDiv) {
	            var tempEl = document.createElement("span");
	            editableDiv.insertBefore(tempEl, editableDiv.firstChild);
	            var tempRange = range.duplicate();
	            tempRange.moveToElementText(tempEl);
	            tempRange.setEndPoint("EndToEnd", range);
	            caretPos = tempRange.text.length;
	        }
	    }
	    return caretPos;
	}
	
	km_edited_keypress = function(ev){
		var str = ev.target.innerHTML;
		var pos = km_cursor_position(ev.target);
		if (ev.which==8 && pos>=0)
		{
			var newstr=str.substring(0,pos-1)+str.substring(pos);
			ev.target.innerHTML=newstr;
			ev.target.focus();
			if (newstr.length>0)
			{
				pos-=1;
				if (pos>newstr.length) pos-=1;
				if (document.selection) {
					sel = document.selection.createRange();
					sel.moveStart('character', pos);
					sel.select();
				}
				else {
					sel = window.getSelection();
					sel.collapse(ev.target.firstChild, pos);
				}
			}
		}
	}

// STRONA

	km_page_visible = function(pagesid){
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { pagesid : pagesid, action : 'page_visible' }, function(data) {
			if (data.status=='1') 
			{
				if (data.hidden=='1') jQueryKam(".km_page_visible").removeClass("km_iconi_visible").addClass("km_iconi_invisible");
				else jQueryKam(".km_page_visible").removeClass("km_iconi_invisible").addClass("km_iconi_visible");
				km_preloader_hide();
			}
			else alert('problem zapisu');
		});
	}
	
	km_page_sitemap = function(pagesid){
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { pagesid : pagesid, action : 'page_sitemap_visible' }, function(data) {
			if (data.status=='1') 
			{
				if (data.nositemap=='1') jQueryKam(".km_page_sitemap_visible").removeClass("km_iconi_sm").addClass("km_iconi_nsm");
				else jQueryKam(".km_page_sitemap_visible").removeClass("km_iconi_nsm").addClass("km_iconi_sm");
				km_preloader_hide();
			}
			else alert('problem zapisu');
		});
	}
	
	km_bookmark = function(){
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { page : km_infos["page"], action : 'bookmark_addremove' }, function(data) {
			if (data.status=='1') 
			{
				km_preloader_hide();
				km_dropmenu_load("bookmark");
			}
			else alert('problem zapisu');
		});
	}
	

// MODUŁY

	km_module_drag = function(level,tdsid,kolejka)
	{
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { level : level, tdsid : tdsid, kolejka : kolejka, action : 'module_drag', page_id : km_infos["page"]  }, function(data) {
			if (data.status=='1') km_preloader_hide();
			else alert('problem zapisu');
		});
	}
	
	km_module_delete = function(tdsid, page)
	{
		if (confirm(km_lang["czy_na_pewno_skasowac"]))
		{
			km_preloader_show();
			if (page==undefined) page=km_infos["page"];
			jQueryKam.getJSON(km_infos["ajax_link"], { tdsid : tdsid, action : 'module_delete', page_id : page }, function(data) {
				if (data.status=='1')
				{
					jQueryKam("#km_dragbox_"+tdsid).remove();
				}
				else alert('problem zapisu');
				km_preloader_hide();
			});
		}
	}
	
	km_module_visible = function(tdsid){
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { tdsid : tdsid, action : 'module_visible' }, function(data) {
			if (data.status=='1') 
			{
				if (data.hidden=='1') jQueryKam("#km_td_"+tdsid+" .km_modul_visible").removeClass("km_icontd_visible_on").addClass("km_icontd_visible_off");
				else jQueryKam("#km_td_"+tdsid+" .km_modul_visible").removeClass("km_icontd_visible_off").addClass("km_icontd_visible_on");
				km_preloader_hide();
			}
			else alert('problem zapisu');
		});
	}

// LINKI MENU

	km_link_drag = function(linksid,kolejka)
	{
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { menu_id : km_infos["menu_id"], linksid : linksid, kolejka : kolejka, action : 'link_pos' }, function(data) {
			if (data.status=='1') km_preloader_hide();
			else alert('problem zapisu');
		});
	}

	km_link_target = function(linksid,target)
	{
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { menu_id : km_infos["menu_id"], target : target, linksid : linksid, action : 'link_target' }, function(data) {
			if (data.status=='1') km_preloader_hide();
			else alert('problem zapisu');
		});
	}

	km_link_delete = function(linksid)
	{
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { menu_id : km_infos["menu_id"], linksid : linksid, action : 'link_delete' }, function(data) {
			if (data.status=='1') 
			{
				jQueryKam("#link_"+linksid).remove();	
			}
			km_preloader_hide();
		});
	}

	km_link_visible = function(linksid){
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { menu_id : km_infos["menu_id"], linksid : linksid, action : 'link_visible' }, function(data) {
			if (data.status=='1') 
			{
				if (data.hidden=='1') jQueryKam("#link_"+linksid+" .link_visible").removeClass("km_icontd_visible_on").addClass("km_icontd_visible_off");
				else jQueryKam("#link_"+linksid+" .link_visible").removeClass("km_icontd_visible_off").addClass("km_icontd_visible_on");
				km_preloader_hide();
			}
			else alert('problem zapisu');
		});
	}

	km_link_add = function(tdsid){
		km_preloader_show();
		jQueryKam.getJSON(km_infos["ajax_link"], { tdsid : tdsid, action : 'link_add', page_id : km_infos["page"] }, function(data) {
			if (data.status=='1') 
			{
				document.location.href=km_infos["page_link"];
			}
			else alert('problem zapisu');
		});
	}

// EDYCJA KONTENTU
	km_content_activate = function(){
		jQueryKam(".km_title_edit").bind('mousedown',function(e){
			if (e.which==3){
				e.stopPropagation();
				km_content_title_edit(jQueryKam(this));
			}
			else
				e.preventDefault();
		}).bind("contextmenu", function(e){ return false; });
		jQueryKam(".km_alt_edit").bind('mousedown', function(e){
			if (e.which==3){
				e.stopPropagation();
				km_content_alt_edit(jQueryKam(this));
			}
			else
				e.preventDefault();
		}).bind("contextmenu", function(e){ return false; });

		
		jQueryKam(".km_alt_edit").bind('keypress', km_edited_keypress );
		jQueryKam("#km_editsave a").bind('click', km_edited_save);
	}
	
	km_content_title_edit = function(title){
		title.attr("contenteditable",true);
		t = title.attr("id").split("km_title_");
		sid = t[1];
		km_edited_add(sid,"tdsid");
	}
	
	km_content_alt_edit = function(title){
		title.attr("contenteditable",true);
		t = title.attr("id").split("km_alt_");
		sid = t[1];
		km_edited_add(sid,"linksid");
	}
	
	km_content_title_save = function(sid){
		jQueryKam("#km_title_"+sid).removeAttr("contenteditable");
		var text = jQueryKam("#km_title_"+sid).html();
		jQueryKam.getJSON(km_infos["ajax_link"], { tdsid : sid, txt : text, action : 'contenttitle_save', page_id : km_infos["page"]  }, function(data) {
			if (data.status=='1') km_edited_remove(sid,"tdsid");
			else alert('problem zapisu');
		});
	}
	
	km_content_alt_save = function(sid){
		jQueryKam("#km_alt_"+sid).removeAttr("contenteditable");
		var text = jQueryKam("#km_alt_"+sid).html();
		jQueryKam.getJSON(km_infos["ajax_link"], { linksid : sid, txt : text, action : 'contentalt_save', page_id : km_infos["page"]  }, function(data) {
			if (data.status=='1') km_edited_remove(sid,"linksid");
			else alert('problem zapisu');
		});
	}

// FUNKCJE

	km_inarray = function(evar, earray){
		if (earray.length==0) return false;
		for (key in earray) {
			if (earray[key] == evar) {
				return true;
			}
		}
		return false;
	}
	
	km_position_in_array = function (evar, earray){
		for (key in earray) {
			if (earray[key] == evar) {
				return key;
			}
		}
		return false;
	}


// INICJALIZACJA
	jQueryKam(document).ready(function(){
		km_content_activate();
		km_dropmenu_init();

		
	});