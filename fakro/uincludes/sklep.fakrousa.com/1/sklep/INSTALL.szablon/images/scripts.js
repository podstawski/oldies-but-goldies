
function list_table_onclick()
{
	obj=event.srcElement;
	table=obj.parentNode.parentNode.parentNode;

	if (obj.tagName=="TD") 
	{

		tr=obj.parentNode;

		if (tr.dbid==null) return;

		if (table.selectedId==tr.dbid)
		{
			show_selected_item();
			return;
		}

		tr._className=tr.className;
		tr.className='tr_sel';

		if (table.selectedTr != null)
		{
			table.selectedTr.className=table.selectedTr._className;
		}

		table.selectedTr=tr;
		table.selectedId=tr.dbid;
		list_selected_item();
	}
}

function list_table_ondblclick()
{
	obj=event.srcElement;
	table=obj.parentNode.parentNode.parentNode;

	if (obj.tagName=="TH") 
	{
		if (obj.sort==null) return;

		sd=0;
		sf=obj.sort;

		if (obj.sort==table.sort)
			sd=(table.sortd)?0:1;
		else
			sd=0;

		
		table.sortd=sd;
		table.sort=obj.sort;

		cmd="list_sort('"+sf+"',"+sd+");";
		eval(cmd);
	}

	list_table_onclick();
}


function list_table_init_obj(obj,sort_field,sort_dir)
{
	tbody=obj.childNodes[0];
	trs=tbody.childNodes;
	if (trs[0] == null) return;

	ths=trs[0].childNodes;

	obj.onclick=list_table_onclick;
	obj.ondblclick=list_table_ondblclick;


	for (i=0; i<ths.length; i++)
	{
		th=ths[i];
		if (th.sort==null) continue;

		img=IMAGES+"/";
		if (sort_field==th.sort) 
		{
			img+="sort";
			img+=sort_dir;
			img+=".gif";
			obj.sort=th.sort;
			obj.sortd=sort_dir;
		}
		else img+="spacer.gif";
		img_tag="<IMG align='right' width=10 height=9 src='"+img+"'>";
		th.innerHTML = img_tag + th.innerHTML;
	}

	
	for (i=1; i<trs.length; i++)
	{
		tr=trs[i];
		if (tr.className.length) continue;
		tr.className = (i%2) ? "tr_even" : "tr_odd";
		if (obj.selectedId==tr.dbid) 
		{
			tr._className=tr.className;
			obj.selectedTr=tr;
			tr.className = "tr_sel";
		}
		tdks=tr.childNodes;
		
		for (td=0; td<tdks.length; td++)
		{
			if (tdks[td].innerHTML.length==0) tdks[td].innerHTML='&nbsp;'
		}
	}
}

function list_table_init(table_id,sort_field,sort_dir)
{
	obj=getObject(table_id);
	list_table_init_obj(obj,sort_field,sort_dir)
}


function list_sort(pole,kier)
{
	document.list_sort_form.s_field.value=pole;
	document.list_sort_form.s_dir.value=kier;
	document.list_sort_form.submit();
}


function list_prompt_item(_action,id,txt)
{
	if (!confirm(txt)) return;
	document.list_sort_form.s_action.value=_action;
	document.list_sort_form.s_id.value=id;
	document.list_sort_form.s_ile.value=0;
	document.list_sort_form.submit();
}

function list_delete_item(_action,id)
{
	list_prompt_item(_action,id,"Czy jeste¶ pewien, ¿e chcesz usun±æ");
}


function list_more(more,id)
{
	document.list_fwd_form.action=more;

	document.list_fwd_form.s_id.value=id;
	document.list_fwd_form.submit();
}



function kartoteka_popup(url,type)
{
	var screenTop;
	var screenLeft;

	if (screenTop == null)
		screenTop = 0;
	if (screenLeft == null)
		screenLeft = 0;

	_top = screenTop+30;
	if (top.opener == null)
	{
		_top = screenTop-50;
	}
	_left = screenLeft+30;

	a=open(url,type,'toolbar=0,location=0,directories=0,\
                        status=0,menubar=0,scrollbars=0,resizable=1,\
                        width=594,height=545,top='+_top+',left='+_left+'');
	a.focus();
}


function popupBookmarkClick(sid)
{

	for (i=0;i<popupWindows.length;i++)
	{
		s=popupWindows[i];
		bm=getObject('mbid_'+s);
		div=getObject('popupWindowId_'+s);
		bm.className='popup_bookmark_normal';
		div.style.display='none';
	}

	bm=getObject('mbid_'+sid);
	div=getObject('popupWindowId_'+sid);
	bm.className='popup_bookmark_clicked';
	div.style.display='inline';

	document.cookie='ciacho['+KARTOTEKA_CIACHO_NAZWA+']='+sid;
}


function hideOrShowDesc()
{
        but=getObject('descShowHide');
        szp=getObject('szpOpis');

        if (!szp)
        {
                setTimeout(hideOrShowDesc,100);
                return;
        }

        if (szp.style == null)
                return;

        if (szp.style.display=="none")
        {
                but.innerHTML=but._hide;
                szp.style.display='';
                ciacho='show';
        }
        else
        {
                but.innerHTML=but._show;
                szp.style.display='none';
                ciacho="hide";
        }
        document.cookie='ciacho[opis]='+ciacho;

}
function hideOrShow()
{
        obj=getObject('katTree');
        lab=getObject('katLabel');

        if (obj.style == null)
                return;


        if (obj.style.display=="none")
        {
                lab.innerHTML=lab._hide;
                obj.style.display='';
                hideShowCovered('katTree');
                ciacho='show';
        }
        else
        {
                hideShowCovered('katTree');
                lab.innerHTML=lab._show;
                obj.style.display='none';
                ciacho="hide";
        }
        document.cookie='ciacho[kategoria]='+ciacho;
}
function labelHeight()
{
        return;
}
var KALKULATOR="";
function kalkulator(id)
{

        if (KALKULATOR.length==0) return;
        k=KALKULATOR.split(':');

        _top = Math.round(screen.height / 2) - Math.round(k[1] / 2);
        _left = Math.round(screen.width / 2) - Math.round(k[0] / 2);
        param='width='+k[0]+',height='+k[1]+',top='+_top+',left='+_left;
        href=k[2].split("?");
        sign=(href.length==2)?'&':'?';
        a=open(k[2]+sign+'list[id]='+id,'kalkulator',param);
        a.document.close();
        a.focus();
}

var OPIS_LINK="";
function opisProduktu(link)
{
        wys = 350;
        szer = 500;
        _top = Math.round(screen.height / 2) - Math.round(wys / 2);
        _left = Math.round(screen.width / 2) - Math.round(szer / 2);
        param='width='+szer+',height='+wys+',top='+_top+',left='+_left;
        b=open(OPIS_LINK+link,'Produkt',param);
        b.document.close();
        b.focus();
}

