<?
	//do koszyka
	$ico_basket_org 		= $SKLEP_IMAGES."/i_koszyk.gif";
	$ico_basket_src 		= $UIMAGES."/system/i_koszyk.gif";
	if (file_exists($ico_basket_src)) {
		$ico_basket_size = getimagesize($ico_basket_src);
	}
	else {
		$ico_basket_src = $ico_basket_org;
		$ico_basket_size = getimagesize($ico_basket_org);
	}
	$ico_basket_add_src = $ico_basket_src;
	$ico_basket_add_size= $ico_basket_size[3];
	$ico_basket_add_alt	= sysmsg("Add to cart","buttons");

	
	//zobacz produkt
	$ico_show_org 		= $SKLEP_IMAGES."/i_zobacz.gif";
	$ico_show_src 		= $UIMAGES."/system/i_zobacz.gif";
	if (file_exists($ico_show_src)) {
		$ico_show_size = getimagesize($ico_show_src);
	}
	else {
		$ico_show_src = $ico_show_org;
		$ico_show_size = getimagesize($ico_show_org);
	}
	$ico_show_src = $ico_show_src;
	$ico_show_size= $ico_show_size[3];
	$ico_show_alt	= sysmsg("Show this product","buttons");
	
	//zamѓw	
	$ico_order_org 		= $SKLEP_IMAGES."/i_zamow.gif";
	$ico_order_src 		= $UIMAGES."/system/i_zamow.gif";

	if (file_exists($ico_order_src)) {
		$ico_order_size = getimagesize($ico_order_src);
	}
	else {
		$ico_order_src = $ico_order_org;
		$ico_order_size = getimagesize($ico_order_org);
	}
	$ico_order_src = $ico_order_src;
	$ico_order_size= $ico_order_size[3];
	$ico_order_alt	= sysmsg("Order this product","buttons");
	
	//usuё
	$ico_delete_org 		= $SKLEP_IMAGES."/i_delete.gif";
	$ico_delete_src 		= $UIMAGES."/system/i_delete.gif";
	if (file_exists($ico_delete_src)) {
		$ico_delete_size = getimagesize($ico_delete_src);
	}
	else {
		$ico_delete_src = $ico_delete_org;
		$ico_delete_size = getimagesize($ico_delete_org);
	}
	$ico_delete_src = $ico_delete_src;
	$ico_delete_size= $ico_delete_size[3];
	$ico_delete_alt	= sysmsg("Delete this","buttons");	


	//lista
	$ico_list_org 		= $SKLEP_IMAGES."/i_lista.gif";
	$ico_list_src 		= $UIMAGES."/system/i_lista.gif";
	if (file_exists($ico_list_src)) {
		$ico_list_size = getimagesize($ico_list_src);
	}
	else {
		$ico_list_src = $ico_list_org;
		$ico_list_size = getimagesize($ico_list_org);
	}
	$ico_list_src = $ico_list_src;
	$ico_list_size= $ico_list_size[3];
	$ico_list_alt	= sysmsg("Show list","buttons");	

	//ulubione
	$ico_favorite_org 		= $SKLEP_IMAGES."/i_ulubione.gif";
	$ico_favorite_src 		= $UIMAGES."/system/i_ulubione.gif";
	if (file_exists($ico_favorite_src)) {
		$ico_favorite_size = getimagesize($ico_favorite_src);
	}
	else {
		$ico_favorite_src = $ico_favorite_org;
		$ico_favorite_size = getimagesize($ico_favorite_org);
	}
	$ico_favorite_src = $ico_favorite_src;
	$ico_favorite_size= $ico_favorite_size[3];
	$ico_favorite_alt	= sysmsg("Add to favorite","buttons");	
	
	//Acrobat read
	$ico_acroread_org 		= $SKLEP_IMAGES."/i_acroread.gif";
	$ico_acroread_src 		= $UIMAGES."/system/i_acroread.gif";
	if (file_exists($ico_acroread_src)) {
		$ico_acroread_size = getimagesize($ico_acroread_src);
	}
	else {
		$ico_acroread_src = $ico_acroread_org;
		$ico_acroread_size = getimagesize($ico_acroread_org);
	}
	$ico_acroread_src = $ico_acroread_src;
	$ico_acroread_size= $ico_acroread_size[3];
	$ico_acroread_alt	= sysmsg("Print to AcrobatReader","buttons");	
	
	//Powrѓt
	$ico_back_org 		= $SKLEP_IMAGES."/i_back.gif";
	$ico_back_src 		= $UIMAGES."/system/i_back.gif";
	if (file_exists($ico_back_src)) {
		$ico_back_size = getimagesize($ico_back_src);
	}
	else {
		$ico_back_src = $ico_back_org;
		$ico_back_size = getimagesize($ico_back_org);
	}
	$ico_back_src = $ico_back_src;
	$ico_back_size= $ico_back_size[3];
	$ico_back_alt	= sysmsg("Back","buttons");	
	

	//Zapytaj
	$ico_ask_org 		= $SKLEP_IMAGES."/i_zapytaj.gif";
	$ico_ask_src 		= $UIMAGES."/system/i_zapytaj.gif";
	if (file_exists($ico_ask_src)) {
		$ico_ask_size = getimagesize($ico_ask_src);
	}
	else {
		$ico_ask_src = $ico_ask_org;
		$ico_ask_size = getimagesize($ico_ask_org);
	}
	$ico_ask_src = $ico_ask_src;
	$ico_ask_size= $ico_ask_size[3];
	$ico_ask_alt	= sysmsg("Ask","buttons");	
	
	
?>
