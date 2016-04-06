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
<img src="<?echo $SKLEP_IMAGES?>/i_info.gif" align="absMiddle">
<span id="descShowHide" _show="<? echo $_show?>" _hide="<? echo $_hide?>" 
	style="cursor:hand" onClick="hideOrShowDesc()">
	<?echo $desc ?>
</span>
<? echo $js; ?>
