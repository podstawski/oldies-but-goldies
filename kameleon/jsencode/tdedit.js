
page_number=0;

function init()
{
	plainToFrame();
	frames.edytor.focus();
}




function ustawKolor(typ,atr,mode)
{
	if (advanced.style.visibility=='hidden')
	{
		var stext = frames.edytor.document.selection.createRange();
		if (atr == null)
		{
			stext.execCommand(typ,mode);
		}
		else
		{
			stext.execCommand(typ, mode, atr);
		}  
			stext.select();
			frames.edytor.focus();
		}
		else
		{
			document.edytujtd.bgcolor.value=atr;
			document.edytujtd.focus();
		}
}


function showWysiwyg()
{
	document.getElementById('advanced').style.display='none';
	document.getElementById('modadv').style.display='none';
	document.getElementById('zakladka_js').style.display='none';
	document.getElementById('wysiwyghtml').style.display='block';
}

function showAdvanced()
{
	document.getElementById('wysiwyghtml').style.display='none';
	document.getElementById('modadv').style.display='none';
	document.getElementById('zakladka_js').style.display='none';
	document.getElementById('advanced').style.display='block';
}

function showJavascript()
{
	document.getElementById('wysiwyghtml').style.display='none';
	document.getElementById('modadv').style.display='none';
	document.getElementById('advanced').style.display='none';
	document.getElementById('zakladka_js').style.display='block';
}

function showModAdv(init)
{  
	document.getElementById('wysiwyghtml').style.display='none';
	document.getElementById('advanced').style.display='none';
	document.getElementById('zakladka_js').style.display='none';
	document.getElementById('modadv').style.display='block';

	if (init) return;

	showModFun();
}


function ZapiszZmiany()
{
	document.getElementById('edytujtd').action.value='ZapiszTD';
	document.getElementById('edytujtd').submit();
}

function ZapiszZmianyZamknij()
{
	window.clipboardData.setData("Text",document.getElementById('edytujtd').plain.value);
	window.close();
}

function wstawObrazek(img)
{
	if (pole_obrazka!='')
	{
		document.all[pole_obrazka].value=img;
		pole_obrazka='';
	}
	else
	{
		frames.edytor.focus();
		img=UIMAGES+'/'+img;
		var stext = frames.edytor.document.selection.createRange();
		stext.execCommand('insertimage',false, img);
		stext.select();
	}
}

function wstawPhp( href )
{
 if (pole_obrazka!='')
 {
   document.all[pole_obrazka].value=href;
   pole_obrazka='';
 }
}

function wstawPlik(href)
{
	//alert('wstawPlik: ' + href);
   	var stext = frames.edytor.document.selection.createRange();

	typ=stext.queryCommandEnabled('insertimage',true,'');
	sel=frames.edytor.document.selection.type;
	if (typ==true && sel=='Control')
	{
		obrazek=stext.item(0);
	}	

	href=UFILES+'/'+href;
	stext.execCommand('createlink',false,href);
	stext.select();
	frames.edytor.focus();

	if (typ==true && sel=='Control')
	{
		obrazek.style.visibility='hidden';
		obrazek.style.visibility='visible';
	}
}



function saveId(key,val)
{
	//alert (pole+'='+val);
	if (pole!='')
		document.all[pole].value=val;
	else
	{

		var stext = frames.edytor.document.selection.createRange();
	
		typ=stext.queryCommandEnabled('insertimage',true,'');
		sel=frames.edytor.document.selection.type;
		if (typ==true && sel=='Control')
		{
			obrazek=stext.item(0);
			html=obrazek.parentElement.outerHTML;
		}
		else
			html=stext.htmlText;
	
		//wykryj numer strony
		re = new RegExp("kameleon:inside_link\\(([0-9|a-z|:|\$]+)\\)");
		re_arr = re.exec(html);
		if (re_arr!=null)
		{
			page_number=re_arr[1];
		}
	//	page=prompt(label,page_number);
		page=val;
	
		if (page!=null)
		{
			page_number=page;
	//		stext.execCommand('RemoveFormat');
			stext.execCommand('createlink',false,'kameleon:inside_link('+page+')');
			stext.select();
			frames.edytor.focus();
			if (typ==true && sel=='Control')
			{
				obrazek.style.visibility='hidden';
				obrazek.style.visibility='visible';
			}
		}
	}
}
