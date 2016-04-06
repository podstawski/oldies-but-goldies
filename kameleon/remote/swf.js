obj=document.getElementById('ta_swf_'+sid);
if (obj!=null)
{
	if (sid!='applet')
	{
		embed_idx=obj.value.toLowerCase().indexOf('<embed');
		embed=obj.value.substr(embed_idx);

		var FO = {majorversion:"6", build:"40" };

		embed=embed.replace(/[ \r\n]+/gi,' ');

		for(space=embed.indexOf(' ');space>0;space=embed.indexOf(' '))
		{
			embed=embed.substr(space+1);
			equal=embed.indexOf('=');
			param=embed.substr(0,equal);
			quote=embed.substr(equal+1,1);
			if (param.length==0) continue;
			value=embed.substr(equal+2);
			nextquote=value.indexOf(quote);
			value=value.substr(0,nextquote);

			if (param.toLowerCase()=='src') param='movie';
			if (param.toLowerCase()=='type') continue;

			str2eval='FO.'+param+'="'+value+'"';
			eval(str2eval);
		}
	}
	did='di_swf_'+sid;
	if (sid!='applet' && typeof(UFO)=="object" && document.getElementById(did)!=null ) UFO.create(FO,did);
	else 
	{
		var html=obj.value;

		htmla=html.split('\n');
		for (i=0;i<htmla.length;i++)
		{
			document.write(htmla[i]);
			document.write("\n");
		}
	}
}