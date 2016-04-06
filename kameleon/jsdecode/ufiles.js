var objHref;

function previewtime()
{
	if (preview_img.readyState=="complete")
	{
		html=document.all['p_opis'].innerHTML;
		html=html+"<br>"+preview_img.width+" x "+preview_img.height;
		document.all['p_opis'].innerHTML=html;

		if (preview_img.width>160)
		{
			preview_img.width=160;
		}
	}
	else
	{
		setTimeout(previewtime,20);
	}

}
function bodytime()
{
	if (frames.view.document.readyState=="complete")
	{
		frames.view.document.body.innerHTML="<img id='img_id' src='/'>";
		frames.view.document.body.innerHTML="<img id='img_id' src='"+objHref+"'>";
		preview_img=frames.view.document.all['img_id'];
		setTimeout(previewtime,20);
	}
	else
	{
		setTimeout(bodytime,20);
	}
}



function tryedit(obj,allow)
{
	edit=document.getElementById('edit_file_img');
	edit.style.display='none';

	if (obj.selectedIndex==-1) return;
	if (!rozmiary.length) return;
	if (allow==0) return;
	
	row=rozmiary[obj.selectedIndex].toString();
	cols=row.split(',');
	dir=cols[1];

	if (dir==0)
	{
		edit.style.display='';
	}
}

function edit_file(galeria,edytor)
{
	lista=document.getElementById('select_lista');

	e=edytor.split(';');
	if (e.length>1)
	{
		edytor='';

		v=lista.value.split('.');
		ext=v[v.length-1].toLowerCase();
		
		for (i=0;i<e.length;i++)
		{
			para=e[i].split(':');
			if (para[0].indexOf(ext)>=0) edytor=para[1];
		}

		
	}

	if (edytor=='') return;
	window.open(edytor+'?plik='+lista.value+'&galeria='+galeria,'_blank','directories=no,menubar=no,resizable=yes,toolbar=no,width=700,height=500');
}


function preview(obj)
{
	var t = Math.random();

	if ( document.all['final_button'] != null)
		if (document.all['final_button']._value != null)
		{
			document.all['final_button'].value=document.all['final_button']._value;
		}


	if (obj.selectedIndex==-1) return;
	if (!rozmiary.length) return;

	
	row=rozmiary[obj.selectedIndex].toString();
	cols=row.split(',');
	dir=cols[1];

	var href=UFILES+'/'+obj[obj.selectedIndex].value;
	var hrefA=href+'?'+t;

	if (dir==0)
	{
		p_opis.innerText=label3+' '+cols[0]+' '+label4+'\n'+label5+' '+cols[2]; 
		if(document.all.preview_mode)
		if (document.all.preview_mode.checked) 
		{
			for (i=href.length;i;i--)
			{
				if (href.substr(i,1)=='.')
				{
					ext=href.substr(i+1);
					break;
				}
			}

			if (ext.length && 
				(ext.toLowerCase()=="gif" || ext.toLowerCase()=="jpg" || ext.toLowerCase()=="jpeg" || ext.toLowerCase()=="png") )
			{
				document.all.view.src="empty.php";
				objHref=hrefA;
				setTimeout(bodytime,20);
			}
			else
			{
				if (ext.length &&
					ext.toLowerCase()=="zip" )
				{
					document.all['final_button']._value=document.all['final_button'].value;
					document.all['final_button'].value=label_unzip;
					document.all.view.src="empty.php";
				}
				else
				{
					document.all.view.src=hrefA;  
				}
			}

		}
	}
	else
	{
		p_opis.innerText=label6+' '+cols[0]; 
		document.all.view.src="empty.php";
	}
	  
}

function PreviewMode(obj)
{
	if (document.all.preview_mode.checked)
	{
		document.all.view.style.visibility="visible";
		preview(document.all.lista);
	}
	else
	{
		document.all.view.style.visibility="hidden";
	}
}


function resize(obj)
{
	return;
	if (obj.selectedIndex==-1) return;
	if (!rozmiary.length) return;



	row=rozmiary[obj.selectedIndex].toString();


	cols=row.split(',');
	dir=cols[1];
	if (dir==1) return;


	document.all.view.style.visibility='hidden';
	href=UFILES+'/'+obj[obj.selectedIndex].value;
	img=document.createElement('<img src='+href+'>');
	img.src=href;
	w=img.width;
	h=img.height;
	if (w>h)
		max=w;
	else
		max=h;
	zoom=150;
	if (max>zoom)
	{
		if (w==max)
		{
			document.galeria.view.width=zoom;
			hp=(h*zoom)/w;
			hp=Math.round(hp);
			document.galeria.view.height=hp;
		}
		else
		{
			document.galeria.view.height=zoom;
			wp=(w*zoom)/h;
			wp=Math.round(wp);
			document.galeria.view.width=wp;
		}
	}
	else
	{
		document.galeria.view.width=w;
		document.galeria.view.height=h;
	}
	document.all.view.style.visibility='visible';
}


function deleteFile(obj,label1,label2)
{
	sel=obj.selectedIndex;
	if (sel==-1) 
		alert (label1);
	else
	{

		row=rozmiary[sel].toString();
		cols=row.split(',');
		dir=cols[1];
		if (confirm(label2))
		{
			document.galeria.action.value='UsunPlik';
			document.galeria.submit();		
		}
	}
}

function SetDir(obj)
{
	sel=obj.selectedIndex;
	if (sel==-1) return;

	row=rozmiary[sel].toString();
	cols=row.split(',');
	dir=cols[1];

	if (dir==1)
	{
		dir_name=cols[0]; 
		document.galeria.newdir.value=dir_name;
		document.galeria.submit();
	}
	else
	{
		document.galeria.action.value='Download';
		document.galeria.submit();
		document.galeria.action.value='';
	}
}

function newDir(msg)
{
    kat=prompt(msg,'newdir');
	if (kat==null) return;
    if (kat.length>0)
    {
	       //document.galeria.action.value='DodajKatalogFiles';
    	   document.galeria.newdir.value=kat;
	       document.galeria.submit();
    }
}

function dirUp()
{
	document.galeria.newdir.value='..';
	document.galeria.submit();
}
