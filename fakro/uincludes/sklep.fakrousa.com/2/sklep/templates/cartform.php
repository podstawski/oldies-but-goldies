<?
	global $KOSZYK_NEXT;
	$cart_next=$self;
	if ($SYSTEM[koszyk]) $cart_next=$KOSZYK_NEXT;

	$sysmsg_quantity=sysmsg("Quantity","cart");
	$sysmsg_wrong_value=sysmsg("Wrong value","system");
	$sysmsg_article_added_to_offer=sysmsg("Article added to offer","cart");

	$promptform="$SKLEP_INCLUDE_PATH/promptform.php";

	if (file_exists("$SKLEP_INCLUDE_PATH/constant/promptform.php")) $promptform="$SKLEP_INCLUDE_PATH/constant/promptform.php";
	$t=time();
?>



<FORM METHOD="GET" ACTION="<? echo $cart_next; ?>" name="cartForm">
	<INPUT TYPE="hidden" name="action" value="KoszykDodaj">
	<INPUT TYPE="hidden" id="towar_id" name="form[towar_id]">
	<INPUT TYPE="hidden" id="quantity" name="form[quantity]">
	<INPUT TYPE="hidden" name="list[ile]" value="<? echo $LIST[ile];?>">
	<INPUT TYPE="hidden" name="list[sort_f]" value="<? echo $LIST[sort_f];?>">
	<INPUT TYPE="hidden" name="list[sort_d]" value="<? echo $LIST[sort_d];?>">
	<INPUT TYPE="hidden" name="list[start]" value="<? echo $LIST[start];?>">
	<INPUT TYPE="hidden" name="list[szukaj]" value="<? echo $LIST[szukaj];?>">
</FORM>



 <script language="JavaScript">

	var cart_prompt_quantity_<?echo $sid?>=0;


	function setModalXY(x,y,width,height)
	{
		var XY = "";
		if (x != null && y != null) XY = "dialogLeft:" + x + "px; dialogTop:" + y + "px; center:off;";
		if (width != null && height != null) XY += "dialogWidth:" + width + "px; dialogHeight:" + height + "px;";
		return XY;
	}

	function cart_prompt(msg,def)
	{
		w=250;
		h=120;

		cart_prompt_quantity_<?echo $sid?>=0;

		window.showModalDialog('<?echo $promptform?>?cancel=<?echo urlencode(sysmsg('cancel','cart'))?>&t=<?echo $t?>&sid=<?echo $sid?>&msg='+msg+'&def='+def+'&chs='+document.defaultCharset,
			window,setModalXY(event.x-w/2,event.y+30,w,h));

		if (cart_prompt_quantity_<?echo $sid?>==0) return null;

		return cart_prompt_quantity_<?echo $sid?>;
	}

	function addItem2Cart(item,def,jm)
	{
		document.cartForm.towar_id.value = item;		
		if (def == 0) def = 1;
		ilosc = def;
<? if ($SYSTEM[prompt]) { ?>
		ilosc = cart_prompt('<? echo urlencode($sysmsg_quantity);?> ['+jm+']',def);
<? } ?>
		if (ilosc == null) return;
<? if ($SYSTEM[prompt]) { ?>
		ilosc = ilosc.replace(",",".");
<? } ?>
		if (isNaN(ilosc))
		{
			alert('<? echo $sysmsg_wrong_value; ?>');
			return;
		}
		document.cartForm.quantity.value = ilosc;
		document.cartForm.submit();
	}

	var art_add = '<? echo $sysmsg_article_added_to_offer; ?>';

	function calcAddToCart(id,val)
	{
		ilosc = val;
		ilosc = ilosc.replace(",",".");
		if (isNaN(ilosc))
		{
			alert('<? echo $sysmsg_wrong_value; ?>');
			return;
		}
		document.cartForm.towar_id.value = id;		
		document.cartForm.quantity.value = ilosc;
		document.cartForm.submit();
	}

	function goCart()
	{
		location.href='<? echo $cart_next; ?>';
	}

	function chageItemQuantity(id,quant,kwant,iscalc)
	{
		if (quant == 0) quant = 1;

<? if ($SYSTEM[prompt]) { ?>
		quant = cart_prompt('<? echo urlencode($sysmsg_quantity);?>',kwant);
<? } ?>

		if (quant == null) return;
	
		var file_path = '<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?tid='+id+'&tquant='+quant+'&tadd=1&kwant='+kwant+'&randSID='+Math.random();
		loadContent(file_path,'qscript');

	}


	function doKoszyka(towar,ilosc,jm)
	{
		<? 	if ($AUTH[id]>0) { ?>
			addItem2Cart(towar,ilosc,jm);
		<? } else { ?>
			
			var file_path = '<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?tid='+towar+'&tquant='+ilosc+'&tadd=1&kwant='+ilosc+'&randSID='+Math.random();
			loadContent(file_path,'qscript');
		<? } ?>
	}


</script>
