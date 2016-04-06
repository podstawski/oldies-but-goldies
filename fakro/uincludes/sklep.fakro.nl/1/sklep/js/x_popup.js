function popup_url(url, size_w, size_h, scroll) {
	msg=open('http://www.profmetkol.com.pl/'+url,"PROFMETKOL","scrollbars="+scroll+",toolbar=no,directories=no,width="+size_w+",height="+size_h+",menubar=no");
	msg.document.close();
	msg.focus();
};

function popup_page(page, size_w, size_h, scroll)	{
	msg=open(page,"PROFMETKOLGaleria","scrollbars="+scroll+",toolbar=no,directories=no,width="+size_w+",height="+size_h+",menubar=no");
	msg.document.close();
	msg.focus();
};

function popup_img(url, size_w, size_h, scroll) {
	new_size_w = size_w;
	new_size_h = size_h+25;
	msg=open("","PROFMETKOLObrazek","scrollbars="+scroll+",toolbar=no,directories=no,width="+new_size_w+",height="+new_size_h+",menubar=no");
	msg.document.close();
	msg.document.write("<HTML><HEAD><TITLE>PROFMETKOL</TITLE></HEAD>");
	msg.document.write("<BODY BGCOLOR=White TEXT=#8F82AE TOPMARGIN=0 LEFTMARGIN=0 MARGINHEIGHT=0 MARGINWIDTH=0>");
	msg.document.write("<CENTER><IMG SRC='"+url+"' ALT='Zamknij Okno' BORDER='0' onclick='window.close()' style='cursor: hand;'><BR><BR>");
	msg.document.write("</BODY></HTML>");
	msg.resizeTo(new_size_w,new_size_h);
	msg.focus();
};

function popup_flash(flashsrc, size_w, size_h, scroll)	{
	msg=open("","PROFMETKOL","scrollbars="+scroll+",toolbar=no,directories=no,width="+size_w+",height="+size_h+",menubar=no");
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

function sc(logo) {
	size_w = 350;
	size_h = 250;
	new_size_w = size_w;
	new_size_h = size_h+25;
	msg=open("","SiteCredits","scrollbars=no,toolbar=no,directories=no,width="+new_size_w+",height="+new_size_h+",menubar=no");
	msg.document.close();
	msg.document.write("<HTML><HEAD><TITLE>PROFMETKOL</TITLE></HEAD>");
	msg.document.write("<BODY TEXT=#163F17 TOPMARGIN=0 LEFTMARGIN=0 MARGINHEIGHT=0 MARGINWIDTH=0>");	
	msg.document.write("<table cellspacing='0' cellpadding='0' border=0 width=350>");
	msg.document.write("<tr><td style='padding: 0px 12px 0px 12px; height: 80px;'>");
	if (logo.length)	msg.document.write("<img src='"+logo+"' hspace=0 vspace=0 border=0>");
	msg.document.write("</td></tr>");
	msg.document.write("<tr><td style='padding: 12px; height: 170px; background-color: #CFEFBE; font-family: verdana; font-size: 11px;'>");
	msg.document.write("<p><b>Project co-ordination</b><br>Gammanet sp. z o.o. | www.gammanet.pl </p>");
	msg.document.write("<p><b>Art direction, photo illustration & graphics design</b><br>Tomek Szurkowski | www.tomszurkowski.com</p>");
	msg.document.write("<p><b>Content Management System</b><br>web kameleon | www.webkameleon.com </p>");
	msg.document.write("</td></tr>");
	msg.document.write("</table>");
	msg.resizeTo(new_size_w,new_size_h);
	msg.focus();
};
