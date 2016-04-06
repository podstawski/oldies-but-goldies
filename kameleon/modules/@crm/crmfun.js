	function decHex(dec)
	{
		ar=new Array ('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		if (dec>255 || dec<0) return 'FF';
		dd=Math.floor(dec/16);
		pp=dec%16;
		hex=''+ar[dd]+''+ar[pp]+'';
		return hex;
	}
	function obj_bgcolor(obj)
	{
		if (obj.style.backgroundColor.length) return obj.style.backgroundColor;
		if (obj.className.length) 
		{
			for (i=document.styleSheets.length-1;i>=0;i--)
			{
				var o=document.styleSheets(i);
				var r=o.rules;
//				alert(r.length);
				for (j=0;j<r.length ;j++)
				{
					styl=o.rules(j);
					if ("." + obj.className == styl.selectorText)
					{
						if (styl.style.backgroundColor.length)
						{
							return styl.style.backgroundColor;
						}
						
					}
				}
			}
		}
		 
		
		if (obj.bgColor.length) return obj.bgColor;
		tag=obj.tagName.toLowerCase();
		if (tag!="body") return obj_bgcolor(obj.parentNode);
		return 'white';
	}

	function obj_acolor(sC)
	{
		if (sC.substr(0,1)!="#" ) return "white black black white";

		procent=0.3;

		fR=0.0+parseInt(sC.substr(1,2),16);
		fG=0.0+parseInt(sC.substr(3,2),16);
		fB=0.0+parseInt(sC.substr(5,2),16);


		iR_light=Math.round(fR*(1+procent)); 
		iR_dark=Math.round(fR*(1-procent));
		iG_light=Math.round(fG*(1+procent)); 
		iG_dark=Math.round(fG*(1-procent));
		iB_light=Math.round(fB*(1+procent)); 
		iB_dark=Math.round(fB*(1-procent));

		sLight='#'+decHex(iR_light)+decHex(iG_light)+decHex(iB_light);
		sDark='#'+decHex(iR_dark)+decHex(iG_dark)+decHex(iB_dark);

	
		//alert(sC);
		//alert(sLight);
		//alert(sDark);

		return sLight+" "+sDark+" "+sDark+" "+sLight;
	}

	function today(obj)
	{
		t = new Date();
		m=String(t.getMonth()+1);
		if (m.length==1) m="0"+m;
		d=String(t.getDate());
		if (d.length==1) d="0"+d;
		wynik=d+ "-" + m + "-" +t.getYear();
		obj.value=wynik;
	}
