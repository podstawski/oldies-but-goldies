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
