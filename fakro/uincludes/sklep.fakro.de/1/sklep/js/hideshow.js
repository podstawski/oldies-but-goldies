	ns4 = (document.layers)? true:false;
	ie4 = (document.all)? true:false;
	
	function oc(lift,thisid,bgid,bgiu) {
		if (lift.length) {
			styleObject = getStyleObject(lift);
			if (styleObject.display == 'none')	{
				o_show(lift);
				if (bgiu.length) thisid.style.backgroundImage='url('+bgiu+')';
				thisid.style.borderBottomStyle='none';
			}	
			else {
				o_hide(lift);
				if (bgid.length) thisid.style.backgroundImage='url('+bgid+')';
				thisid.style.borderBottomStyle='solid';
			}	
		}
	};
	
	function o_show(id)
	{
	    if(changeObjectVisibility(id, 'visible','inline'))
			return true;
	    else 
			return false;
	} //show

	function o_hide(id)
	{
		if(changeObjectVisibility(id, 'hidden','none'))
			return true;
	    else
			return false;
	} //hide
	
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
	}

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
	//		alert('ex1='+EX1+',ey1='+EY1+',ex2='+EX2+',ey2='+EY2+'\nmX='+mX+',mY'+mY);
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
	}

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
//		alert ('EX1='+EX1+',EY1='+EY1);
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
	//        txtAttribs += oAttrib.nodeName + '=' + 
	//            oAttrib.nodeValue + ' (' + oAttrib.specified + ')\n'; 
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
