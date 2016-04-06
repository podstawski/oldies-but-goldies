<?
	if (!strlen($costxt)) $costxt="UIMAGES";
	$ciacho_name="admin_fe_$costxt";


	$PRE_SELECTED_FILE="";
	if (strlen($FORM[img]))
	{
		$img=explode(":",$FORM[img]);
		if ($img[0]==$costxt)
		{
			$CIACHO[$ciacho_name]=dirname($img[1]);
			$PRE_SELECTED_FILE=basename($img[1]);
		}
	}

	eval("\$dir=\$$costxt;");

	if (!is_dir($dir)) return;

	$subdir=$CIACHO[$ciacho_name];

	if (!is_dir("$dir/$subdir")) @mkdir("$dir/$subdir",0700);

	if (!is_dir("$dir/$subdir")) $subdir="";

	$subdir=ereg_replace("[/]*[^/]+[/]+\.\.","",$subdir);
	if ($subdir=="..") $subdir="";

	$select="";
	$title="";
	$js="kat$sid=new Array();\nkat_idx=0;\n";
	$options="";
	$kat=$dir;
	if (strlen($subdir)) 
	{
		$kat.="/$subdir";
		$options.="<option value=\"..\">[^]</option>";
		$js.="kat${sid}[kat_idx++]=1;\n";
		$title="[<B>$subdir</B>]";
	}

	global $_FILES;

	if ($LIST[dir]==$costxt && file_exists($_FILES[uploaded_file][tmp_name]))
	{
		@move_uploaded_file ($_FILES[uploaded_file][tmp_name],"$dir/".$LIST[subdir].'/'.$_FILES[uploaded_file][name]);
		$PRE_SELECTED_FILE=$_FILES[uploaded_file][name];
	}

	if (strlen($LIST[delfile]) && $LIST[dir]==$costxt && is_writable($LIST[delfile]))
	{
		unlink($LIST[delfile]);
	}

	$dirs=array();
	$files=array();
	if ($dh = opendir($kat)) 
	{
		while (($file = readdir($dh)) !== false) 
		{
			if ($file[0]==".") continue;
			if (is_dir("$kat/$file")) $dirs[]=$file;
			else $files[]=$file;
		}
		closedir($dh);
   }

	sort($dirs);
	sort($files);

   foreach ($dirs AS $d)
   {
	   $options.="<option value=\"$d\">[$d]</option>";
	   $js.="kat${sid}[kat_idx++]=1;\n";
   }

   foreach ($files AS $f)
   {
	   $sel=($PRE_SELECTED_FILE==$f)?"selected":"";
	   $options.="<option value=\"$f\" $sel>$f</option>";
	   $js.="kat${sid}[kat_idx++]=0;\n";
   }

	$select="<select style=\"width:250;\" size=20
				onChange=\"sel${sid}_change(this)\" ondblclick=\"sel${sid}_dbl(this)\">
			$options
			</select>
	";


	$delete_button="<img src=\"$SKLEP_IMAGES/spacer.gif\" id=\"delimg_$sid\">";

	if (is_writable($kat))
	{
		$delete_button="<img src=\"$SKLEP_IMAGES/del.gif\" onclick=\"deleteimg_$sid()\" 
						id=\"delimg_$sid\" style=\"cursor:hand;visibility:hidden\" >";

		$select.="<br><input type=button class=but value='Utwѓrz katalog' onClick='createDir_$sid()'>";
	}
?>
<script>
	<?echo $js?>
</script>
<? echo $title?>
<table width="100%" cellspacing=0 cellpadding=0><tr>
	<td width="50%" valign="top">

		<?echo $select?>
	</td>
	<td width="50%"  valign="top">
		<iframe src="<? if (false && strlen($PRE_SELECTED_FILE)) echo "$kat/$PRE_SELECTED_FILE";?>" 
			id="ifr_<?echo $sid?>" style="width:250;height:330" align=left></iframe>
		<?echo $delete_button?>
	</td>
</table>
<form name="galeria_<?echo $sid?>_form" method="get" action="<?echo $self?>">
<input name="page" type="hidden" value="<?echo $page?>">

</form>

<script>
	function callback_<?echo $sid?>(plik)
	{

	}

	function sel<?echo $sid?>_dbl(sel)
	{
		idx=sel.selectedIndex;
		dir=kat<?echo $sid?>[idx];
		if (dir==1)
		{
			document.cookie='ciacho[<?echo $ciacho_name?>]=<?if (strlen($subdir)) echo "$subdir/";?>'+sel.options[idx].value;
			document.galeria_<?echo $sid?>_form.submit();
		}
		else
		{
			inp=top.opener.document.all[top.opener.galeria_input];
			inp.value='<?echo $costxt?>:<?if (strlen($subdir)) echo "$subdir/"?>'+sel.options[idx].value;
			top.opener.focus();
			window.close();
			
		}
	}

	function createDir_<?echo $sid?>()
	{
		dir=prompt('Nazwa katalogu','');
		if (dir==null) return;

		document.cookie='ciacho[<?echo $ciacho_name?>]=<?if (strlen($subdir)) echo "$subdir/";?>'+dir;
		document.galeria_<?echo $sid?>_form.submit();
	}

	function sel<?echo $sid?>_change(sel)
	{
		idx=sel.selectedIndex;
		dir=kat<?echo $sid?>[idx];
		ifr=document.all['ifr_<?echo $sid?>'];
		if (dir==1) 
		{
			ifr.src='<?echo $SKLEP_IMAGES?>/spacer.gif';
			document.all['delimg_<?echo $sid?>'].style.visibility='hidden';
		}
		else 
		{
			nazwa=sel.options[idx].value;
			na=nazwa.split('.');
			ext=na[na.length-1];
			ext=ext.toLowerCase();
			if (ext=='gif' || ext=='jpg' || ext=='jpeg' || ext=='png')
				ifr.src='<?echo $kat?>/'+sel.options[idx].value;
			ifr.path='<?echo $kat?>/'+sel.options[idx].value;
			document.all['delimg_<?echo $sid?>'].style.visibility='';
		}
	}

	function deleteimg_<?echo $sid?>()
	{
		if (!confirm("Na pewno")) return;
		
		ifr=document.all['ifr_<?echo $sid?>'];

		document.all['df_<?echo $sid?>'].value=ifr.path;
		document.deleteForm_<?echo $sid?>.submit();
	}
</script>


<?
	if (!is_writable($kat)) return;
?>

<form method="post" action="<?echo $self?>" enctype="multipart/form-data">
<input name="list[dir]" type="hidden" value="<?echo $costxt?>">
<input name="uploaded_file" type="file" style="width:340px">
<input name="list[subdir]" type="hidden" value="<?echo $subdir?>">
<input type="submit" value="dodaj" class="but">
</form>

<form method="post" action="<?echo $self?>" name="deleteForm_<?echo $sid?>">
<input name="list[dir]" type="hidden" value="<?echo $costxt?>">
<input name="list[delfile]" id="df_<?echo $sid?>" type="hidden" style="width:340px">
</form>
