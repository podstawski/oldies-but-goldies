function popup_url(url, size_w, size_h, scroll) {
	msg=open('http://www.profmetkol.com.pl/'+url,"PROFMETKOL","scrollbars="+scroll+",toolbar=no,directories=no,width="+size_w+",height="+size_h+",menubar=no");
	msg.document.close();
	msg.focus();
};

function popup_page(page, size_w, size_h, scroll)	{
	msg=open(""+page+".php","PROFMETKOLGaleria","scrollbars="+scroll+",toolbar=no,directories=no,width="+size_w+",height="+size_h+",menubar=no");
	msg.document.close();
	msg.focus();
};

function popup_img(url, size_w, size_h, scroll) {
	new_add_w	=	10;
	new_add_h	=	49;
	
	start_size_w = 150;
	start_size_h = 100;
	
	if (size_w!=null && size_h!=null) {
		start_size_w = size_w;
		start_size_h = size_h;
	}
	
	start_popup_left = Math.round((screen.availWidth-start_size_w)/2);
	start_popup_top = Math.round((screen.availHeight-start_size_h)/2);
	
	msg=open("","POPUP","toolbar=no,scrollbars=no,directories=no,menubar=no,status=no,width="+start_size_w+",height="+start_size_h+", top="+start_popup_top+", left="+start_popup_left+"");
	msg.document.close();
	msg.document.write("<HTML><HEAD><TITLE>"+JSTITLE+"</TITLE>\n");
	
	if (size_w==null && size_h==null) {
		msg.document.write("<script language='JavaScript'>\n");
		msg.document.write("function popup_resize(wnd, img) {\n");
		msg.document.write("new_w = Number(img.width)+"+new_add_w+";\n");
		msg.document.write("new_h = Number(img.height)+"+new_add_h+";\n");
		msg.document.write("popup_l = Math.round((screen.availWidth-new_w)/2);\n");
		msg.document.write("popup_t = Math.round((screen.availHeight-new_h)/2);\n");
		msg.document.write("wnd.moveTo(popup_l,popup_t);\n");
		msg.document.write("wnd.resizeTo(new_w,new_h);\n");
		msg.document.write("return;\n");
		msg.document.write("}\n");
		msg.document.write("</script>\n");
	}
	msg.document.write("</HEAD>\n");
	msg.document.write("<BODY BGCOLOR=White style='margin: 0; font-size: 11px; font-family: verdana; vertical-align: center; text-align: center;'>\n");
	msg.document.write("<br><br><b>Proszê czekaæ...</b>\n");
	
	msg.document.write("<IMG SRC='"+url+"' ALT='Zamknij Okno' BORDER='0' onclick='window.close()' ");
	if (size_w==null && size_h==null) msg.document.write(" onload='popup_resize(window,this)' ");
	msg.document.write(" style='cursor: hand; position: absolute; top: 0; left: 0;'>\n");
	
	msg.document.write("</BODY></HTML>\n");
	msg.focus();
};

function popup_flash(flashsrc, size_w, size_h, scroll)	{
	msg=open("","POPUP","scrollbars="+scroll+",toolbar=no,directories=no,width="+size_w+",height="+size_h+",menubar=no");
	msg.document.close();
	msg.document.write("<HTML><HEAD><TITLE>PROFMETKOL</TITLE></HEAD>");
	msg.document.write("<BODY BGCOLOR=White TEXT=#8F82AE TOPMARGIN=0 LEFTMARGIN=0 MARGINHEIGHT=0 MARGINWIDTH=0>");	
	msg.document.write("<OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0 width="+size_w+" height="+size_h+" classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>");
	msg.document.write("<PARAM NAME='Movie' VALUE='"+flashsrc+"'>");
	msg.document.write("<PARAM NAME=\"Src\" VALUE='"+flashsrc+"'>");
	msg.document.write("<EMBED src='"+flashsrc+"' quality=high bgcolor=#000000 width="+size_w+" height="+size_h+" TYPE='application/x-shockwave-flash' PLUGINSPAGE='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'>");
	msg.document.write("</EMBED>");
	msg.document.write("</OBJECT>");
	msg.document.write("</BODY></HTML>");	
	msg.focus();
};


ns4 = (document.layers)? true:false;
ie4 = (document.all)? true:false;

function oc(lift) {
	if (lift.length) {
		styleObject = getStyleObject('div_mn'+lift);
		if (styleObject.display == 'none')	{
			o_show('div_mn'+lift);
		}	
		else {
			o_hide('div_mn'+lift);
		}	
		
		styleObjectPL = getStyleObject('div_pl'+lift);
		if (styleObjectPL.borderBottomStyle == 'none')	{
			styleObjectPL.borderBottomStyle = 'solid';
		}	
		else {
			styleObjectPL.borderBottomStyle = 'none';
		}	
	}
};

function o_show(id)
{
    if(changeObjectVisibility(id, 'visible','block'))
		return true;
    else 
		return false;
}; //show

function o_hide(id)
{
	if(changeObjectVisibility(id, 'hidden','none'))
		return true;
    else
		return false;
}; //hide

function show(id,e)
{
	e.cancelBubble=true;
    if(changeObjectVisibility(id, 'visible','inline'))
	{
		hideShowCovered(id);
		return true;
	}
    else 
		return false;
    
	return;
};

function hide(id,e)
{
	if (ie4)
	{
		el=document.all[id];
		var p = getAbsolutePos(el);
		var EX1 = p.x;
		var EX2 = el.offsetWidth + EX1;
		var EY1 = p.y;
		var EY2 = el.offsetHeight + EY1;
		mX=event.clientX;
		mY=event.clientY;
		if ((mX>EX1+1 && mX<EX2) && (mY>EY1 && mY<EY2))	return false;
		hideShowCovered('winda_out');
	}

	e.cancelBubble=true;
    if(changeObjectVisibility(id, 'hidden','none'))
	{
		return true;
	}
    else
		return false;
};

function getStyleObject(objectId) {
	// cross-browser function to get an object's style object given its id
	if(document.getElementById && document.getElementById(objectId)) {
	// W3C DOM
	return document.getElementById(objectId).style;
	} else if (document.all && document.all(objectId)) {
	// MSIE 4 DOM
	return document.all(objectId).style;
	} else if (document.layers && document.layers[objectId]) {
	// NN 4 DOM.. note: this won't find nested layers
	return document.layers[objectId];
	} else {
	return false;
	}
} // getStyleObject

function getObject(objectId) {
	// cross-browser function to get an object's style object given its id
	if(document.getElementById && document.getElementById(objectId)) {
	// W3C DOM
	return document.getElementById(objectId);
	} else if (document.all && document.all(objectId)) {
	// MSIE 4 DOM
	return document.all(objectId);
	} else if (document.layers && document.layers[objectId]) {
	// NN 4 DOM.. note: this won't find nested layers
	return document.layers[objectId];
	} else {
	return false;
	}
} // getObject


function changeObjectVisibility(objectId, newVisibility,newDisplay) {
	// get a reference to the cross-browser style object and make sure the object exists
	var styleObject = getStyleObject(objectId);
	if(styleObject)
	{
		styleObject.visibility = newVisibility;
		styleObject.display = newDisplay;
		return true;
	} 
	else
	{
		//we couldn't find the object, so we can't change its visibility
		return false;
	}
} // changeObjectVisibility

function hideShowCovered(id) {
	if (ie4)
		el=document.all[id];
	else
		el=document.layers[id];

	var tags = new Array("applet", "iframe", "select");
	var res;

	var p = getAbsolutePos(el);
	var EX1 = p.x;
	var EX2 = el.offsetWidth + EX1;
	var EY1 = p.y;
	var EY2 = el.offsetHeight + EY1;
	for (var k = tags.length; k > 0; ) {
		var ar = document.getElementsByTagName(tags[--k]);
		var cc = null;

		for (var i = ar.length; i > 0;) {
			cc = ar[--i];

			p = getAbsolutePos(cc);
			var CX1 = p.x;
			var CX2 = cc.offsetWidth + CX1;
			var CY1 = p.y;
			var CY2 = cc.offsetHeight + CY1;

			if ((CX1 > EX2) || (CX2 < EX1) || (CY1 > EY2) || (CY2 < EY1)) {
				cc.style.visibility = "visible";
				res=1;

			} else {
				cc.style.visibility = "hidden";
				res=0;
			}
		}
	}
	return res;
};

function getAbsolutePos(el) {
	var r = { x: el.offsetLeft, y: el.offsetTop };
	if (el.offsetParent) {
		var tmp = getAbsolutePos(el.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}
	return r;
};

function getAttrib(oElem,name)
{
	var val='';
	var oAttribs = oElem.attributes;
	for (var i = 0; i < oAttribs.length; i++)
	{
		var oAttrib = oAttribs[i];
		if (name==oAttrib.nodeName)
		{
			val=oAttrib.nodeValue;
			break;
		}
	}
	return  val;
}
function setAttrib(oElem,name,val)
{
	var oAttribs = oElem.attributes;
	for (var i = 0; i < oAttribs.length; i++)
	{
		var oAttrib = oAttribs[i];
		if (name==oAttrib.nodeName)
		{
			oAttrib.nodeValue=val;
			break;
		}
	}
}

function sc(logo) {
	size_w = 378;
	size_h = 270;
	new_size_w = size_w;
	new_size_h = size_h+25;
	msg=open("","SiteCredits","scrollbars=yes,toolbar=no,directories=no,width="+new_size_w+",height="+new_size_h+",menubar=no");
	msg.document.close();
	msg.document.write("<HTML><HEAD><TITLE>Site Credits</TITLE></HEAD>");
	msg.document.write("<BODY TEXT=#163F17 link=#163F17 TOPMARGIN=0 LEFTMARGIN=0 MARGINHEIGHT=0 MARGINWIDTH=0>");	
	msg.document.write("<table cellspacing='0' cellpadding='0' border=0 width=350>");
	msg.document.write("<tr><td style='padding: 0px 12px 0px 12px; height: 80px;'>");
	if (logo.length)	msg.document.write("<img src='"+logo+"' hspace=0 vspace=0 border=0>");
	msg.document.write("</td></tr>");
	msg.document.write("<tr><td style='padding: 12px; height: 170px; background-color: #CFEFBE; font-family: verdana; font-size: 11px;'>");
	msg.document.write("<p><b>Project co-ordination</b><br>Gammanet sp. z o.o. | <a href='http://www.gammanet.pl' target='_blank' style='color: #163F17;'>www.gammanet.pl</a></p>");
	msg.document.write("<p><b>Art direction, photo illustration & graphics design</b><br>Tomek Szurkowski | <a href='http://www.tomszurkowski.com' target='_blank' style='color: #163F17;'>www.tomszurkowski.com</a></p>");
	msg.document.write("<p><b>Content Management System</b><br>web kameleon | <a href='http://www.webkameleon.com' target='_blank' style='color: #163F17;'>www.webkameleon.com</a></p>");
	msg.document.write("</td></tr>");
	msg.document.write("</table>");
	msg.resizeTo(new_size_w,new_size_h);
	msg.focus();
}

// Do dynamicznego podmieniania script.src we wszystkich przegl¹darkach
function loadContent(file,id)
{
  var head = document.getElementsByTagName('head').item(0)
//  var scriptTag = document.getElementById(id);
//  if(scriptTag) head.removeChild(scriptTag);
//  nie wszêdzie dzia³a :( Camel
  script = document.createElement('script');
  alert(head+' '+script);
  script.src = file;
	script.type = 'text/javascript';
	script.id = 'loadScript';
	head.appendChild(script)
}
