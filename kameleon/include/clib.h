<?

	$clib_js='';
	$clib=$adodb->GetCookie('clib');
	foreach($_COOKIE AS $c=>$v)
		if (substr($c,0,4)=='clib')
		{
			$co=substr($c,4);
			if (!$co) continue;
			if (strlen($v) && strlen($co))
			{
				foreach (explode(',',$v) AS $_v) 
				{
					if ($co=='td' || $co=='mask')
					{
						$title='';
						$sql="SELECT title,page_id AS pid,uniqueid FROM webtd WHERE sid=".abs($_v);
						parse_str(ado_query2url($sql));
						if (!strlen($title)) $title=label('noname');
						$title=strip_tags($title);
            $title.=" [$pid]";
					}
					if ($co=='page')
					{
						$title='';
						if (strstr($_v,'page'))
						{
							$p=explode('-',$_v);
							$sql="SELECT title,id AS pid,sid AS _v FROM webpage WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver AND id=".$p[1];

						}
						else
						{
							$sql="SELECT title,id AS pid FROM webpage WHERE sid=".$_v;
						}
						parse_str(ado_query2url($sql));
						if (!strlen($title)) $title=label('noname');
						$title=strip_tags($title);
						$title.=" [$pid]";
					}
					if ($co=='area')
					{
						$a=explode(':',$_v);
						$title='';
						$sql="SELECT count(*) AS ile FROM webtd WHERE server=$a[0] AND lang='$a[1]' AND ver=$a[2] AND page_id=$a[3]";
						parse_str(ado_query2url($sql));
				    
						$title=$a[3]%2?label('Header'):label('Footer');		
						$title = strip_tags($title);
            $title.=" [".label('modules').": $ile]";
					}

					$klucz = ($co=='mask') ? $uniqueid : $_v ;
					$clib[$co][$klucz]=stripslashes($title);
				}
				$clib_js.="document.cookie='$c=';\n";
			}
			
		}
	$adodb->SetCookie('clib',$clib);

	//echo '<pre>';print_r($clib);echo '</pre>';
	
	if (is_array($clib)) foreach ($clib AS $co=>$a)
	{
		$clib_js.="kameleonCliboard['$co']=new Array;\n";
		
		$i=0;
		foreach (array_reverse($a,true) AS $k=>$v)
		{
			
			$clib_js.="kameleonCliboard['$co'][$i]=new Array;\n";
			$clib_js.="kameleonCliboard['$co'][$i]['k']='$k';\n";
			$clib_js.="kameleonCliboard['$co'][$i]['t']='".addslashes($v)."';\n";
			$i++;
		}
	}

	//echo '<pre>';print_r($clib); echo "\n$clib_js\n".'</pre>';
?>


<form style="margin:0; padding: 0;" name="paste" method="get" action="<?echo $PHP_SELF?>">
 <input type="hidden" name="page_id">
 <input type="hidden" name="paste">
 <input type="hidden" name="page" value="<?echo $page?>">	 
 <input type="hidden" name="referer" value="<?echo $referer?>">	
 <input type="hidden" name="ref_menu" value="<?echo $ref_menu?>">	
 <input type="hidden" name="ref_tree" value="0">	
 <input type="hidden" name="action">
</form>	


<div id="km_pastediv" style="display:none; z-index: 100001"></div>

<script language="JavaScript">

var kameleonCliboard = new Array;
<?=$clib_js?>

function skopiuj(obj,co,title,menu)
{
	if (co=='td' && menu!=null)
	{
		obj=obj*-1;
	}

	ciacho='clib'+co+'';
	ciacha=document.cookie;
	re = / /g;
	ciacha=ciacha.replace(re,'');
	arr=ciacha.split(';');

	ciacho_val='';

	for (i=0;i<arr.length;i++)
	{
		kukis=arr[i];
		kukis_arr=kukis.split('=');

		kukis_key=kukis_arr[0];
		kukis_value=kukis_arr[1];
		if (kukis_key==ciacho) 
		{
			if (typeof(kukis_arr[1])!='undefined') ciacho_val=kukis_value;
		}
	}	

	if (ciacho_val.length>0) ciacho_val+=',';
	

	if (co=='area') obj='<?="$SERVER_ID:$lang:$ver"?>:'+obj;
	ciacho_val+=obj;

	ciacho="clib"+co+"="+ciacho_val;
	document.cookie=ciacho;
	alrt="";
	if (co=="td") alrt="<?echo label("Module was copied to kameleon cliboard")?>";
	if (co=="page") alrt="<?echo label("Page was copied to kameleon cliboard")?>";
	if (co=="area") alrt="<?echo label("Area was copied to kameleon cliboard")?>";
	if (co=="mask") alrt="<?echo label("The module identifier was copied")?>";	
	
	
	
	if (title!=null)
	{
		a=kameleonCliboard[co];
		if (typeof(a)=='undefined') 
		{
			kameleonCliboard[co] = new Array;
			a=kameleonCliboard[co];
		}

		ac=a.length;
		kameleonCliboard[co][ac] = new Array;

		for (i=ac;i>0;i--)
		{
			kameleonCliboard[co][i]['k']=kameleonCliboard[co][i-1]['k'];
			kameleonCliboard[co][i]['t']=kameleonCliboard[co][i-1]['t'];
		}

		kameleonCliboard[co][0]['k']=obj;
		kameleonCliboard[co][0]['t']=title;

	}


	if (alrt.length)
	{
		alert (alrt);
	}
	
	if (co=="area")
	{
		if (confirm('<?=label('Do you want to archive the area a file ?')?>'))
		{
			document.paste.paste.value=obj;
			document.paste.action.value='Skopiuj_obszar';
			document.paste.submit();
		}
	}
}

function wklej_area(plik)
{
	document.paste.action.value='Wklej_obszar';
	document.paste.paste.value=plik;
	document.paste.submit();
}

function wklej(obj,co,wklej)
{
	if (co=='farea')
	{
		document.paste.page_id.value=obj;
		
		a=open('ufiles.php?galeria=14','galeryjka',"toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width=840,height=420");
		
		return;
	}
	
	
	a=kameleonCliboard[co];

	if (typeof(a)=='undefined') 
	{
		alert ('<? echo label("Nothing found in kameleon cliboard") ?>');
		return;
	}

	if (a.length==0)
	{
		alert ('<? echo label("Nothing found in kameleon cliboard") ?>');
		return;
	}

	if (a.length==1 || wklej!=null)
	{
		if (wklej==null) wklej=a[0]['k'];
		//alert(wklej);

		if (wklej<0)
		{
			if (!confirm('<? echo label("Module has menu, should it be pasted as well") ?>?'))
			{
				wklej=wklej*-1;;
			}

		}

		document.paste.page_id.value=obj;
		document.paste.action.value="Wklej_"+co;
		if (document.paste.ref_tree.value==1) document.paste.action.value="Wklej_tree";
		document.paste.paste.value=wklej;
		document.paste.submit();
	}
  else
  {
    html='<div class="km_schowek_header"><? echo label("Multicliboard") ?><img src="<?php echo $kameleon->user[skinpath]; ?>/img/multischowek/close.gif" alt="<? echo label("Close") ?>" onclick="km_close_multischowek()" /></div>';
    html+='<div class="km_schowek_items"><ul>';
  	for (i=0;i<a.length;i++)
  	{
  		html+='<li><a href="javascript:wklej(\''+obj+'\',\''+co+'\',\''+a[i]['k']+'\')">'+a[i]['t']+'</a></li>';
  	}
  	html+='</ul></div>';
  	
  	document.getElementById('km_pastediv').innerHTML=html;
  	document.getElementById('km_pastediv').style.display='block';
  	jQueryKam(function() { 
    	jQueryKam("#km_pastediv").draggable({ handle: '.km_schowek_header' });
    });
  	return false;
  }
}

function km_close_multischowek()
{
  document.getElementById('km_pastediv').style.display='none';
}

</script>