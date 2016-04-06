<?
	$hidden=0;
	if ($CIACHO[opis]!="show" && !$KAMELEON_MODE) $hidden=1;

	$_show=sysmsg("show desc","system");
	$_hide=sysmsg("hide desc","system");

	$desc=$_hide;
	$js="";
	if ($hidden) 
	{
		$desc=$_show;
		$js="<script> hideOrShowDesc(); </script>";
	}
	
?>
<img src="<?echo $SKLEP_IMAGES?>/i_info.gif" align="absMiddle" id="descShowHideImg">
<span id="descShowHide" _show="<? echo $_show?>" _hide="<? echo $_hide?>" 
	style="cursor:hand" onClick="hideOrShowDesc()">
	<?echo $desc ?>
</span>
<? echo $js; ?>
<script>
	function hideNaviDescIfEmpty()
	{
		szp=getObject('szpOpis');
		if (!szp)
		{
			setTimeout(hideNaviDescIfEmpty,100);
			return;
		}
	
		navi_span=getObject('descShowHide');
		navi_img=getObject('descShowHideImg');
		if (szp.innerHTML.length<20)
		{
			navi_span.style.visibility='hidden';
			navi_img.style.visibility='hidden';
		}
	}
	setTimeout(hideNaviDescIfEmpty,200);
</script>
