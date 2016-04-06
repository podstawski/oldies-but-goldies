<?
	$ind = $CIACHO[zstatus];
	$chck[$ind] = "selected";
?>
<FORM METHOD=POST ACTION="">
Status zamówienia <SELECT NAME="zstatus" onChange="document.cookie='ciacho[zstatus]='+this.value">
<option value="">Dowolny</option>
<option value=0 <? echo $chck[0] ?> >Nowe</option>
<option value=1 <? echo $chck[1] ?> >Przyjęte</option>
<option value=-1 <? echo $chck[-1] ?> >Zrealizowane</option>
<option value=-5 <? echo $chck[-5] ?> >Odrzucone</option>
</SELECT>
</FORM>
