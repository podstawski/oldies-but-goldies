if (apiClassName.length) 
{
	classFound=0;
	ssh=document.styleSheets;

	for (i=ssh.length-1;i>=0;i--)
	{
		var o=document.styleSheets(i);
		var r=o.rules;
				
		if (apiClassDebug) alert(r.length);
		for (j=0;j<r.length ;j++)
		{
			styl=o.rules(j);
			if ("." + apiClassName == styl.selectorText)
			{
				classFound=1;
				break;
			}
		}
		if (classFound) break;
	}

	if (apiClassDebug && classFound) alert("Class "+apiClassName+" found. ");	
	if (apiClassDebug && !classFound) alert("Class "+apiClassName+" not found, attaching "+apiClassFile);

	if (!classFound) document.writeln('<link href="'+apiClassFile+'" rel="stylesheet" type="text/css">');
}
