<?
    if (!($WEBTD->type)) $WEBTD->type=0;
	
//	unset($parser_tokens);

	$td_class="";$td_bgimg="";$td_img="";$td_align="";$td_valign="";$td_bgcolor="";
	$td_width=" width=\"100%\"";

    if ($WEBTD->class) 		$td_class	= " class=\"$WEBTD->class\"";
    if ($WEBTD->bgimg)		$td_bgimg	= " background=\"$UIMAGES/$WEBTD->bgimg\"";
	if ($WEBTD->img)		$td_img		= " <img border=\"0\" src=\"$UIMAGES/$WEBTD->img\" alt=\"\" />";
    if ($WEBTD->align)		$td_align	= " align=\"$WEBTD->align\"";
    if ($WEBTD->valign)		$td_valign	= " valign=\"$WEBTD->valign\"";
    if ($WEBTD->bgcolor)	$td_bgcolor	= " bgcolor=\"#$WEBTD->bgcolor\"";
	if ($WEBTD->width)		$td_width	= " width=\"$WEBTD->width\"";
    
	$td_morename = label("more");
	if (strlen($WEBTD->costxt))
	{
		parse_str($WEBTD->costxt);
		if (strlen($ctx_morename))		$td_morename = $ctx_morename;
		$ctx_morename = "";
	}
	if ($WEBTD->more) 
		$td_more_href = kameleon_href("","",$WEBTD->more);
	$td_more="";
    if ($WEBTD->more && strlen($WEBTD->plain) && $WEBTD->more!=$page)
    	$td_more="<a href=\"".kameleon_href("","",$WEBTD->more)."\">".$td_morename."</a>";


	

	$parser_tokens['td_title']=$WEBTD->title;
	$parser_tokens['td_class']=$td_class;
	$parser_tokens['td_bgimg']=$td_bgimg;
	$parser_tokens['td_img']=$td_img;
	$parser_tokens['td_align']=$td_align;
	$parser_tokens['td_valign']=$td_valign;
	$parser_tokens['td_bgcolor']=$td_bgcolor;
	$parser_tokens['td_width']=$td_width;
	$parser_tokens['td_more_href']=$td_more_href;
	$parser_tokens['td_more']=$td_more;
	$parser_tokens['td_pri']=$WEBTD->pri;
	$parser_tokens['td_plain']=$WEBTD->plain;
	if (strlen($WEBTD->js)) $parser_tokens['td_plain'].='
<script language="javascript">
'.$WEBTD->js.'
</script>
';


	$parser_tokens['td_menu']=$WEBTD->menu_id;
	$parser_tokens['td_include']=$WEBTD->html;
	$parser_tokens['td_api']=$WEBTD->api;

	$parser_tokens['td_valid_from']=strlen($WEBTD->nd_valid_from)?formatujDate($WEBTD->nd_valid_from):"";
	$parser_tokens['td_valid_to']=strlen($WEBTD->nd_valid_to)?formatujDate($WEBTD->nd_valid_to):"";

	

	
	$parser_template=kameleon_template($SZABLON_PATH,$TD_TYPY,$WEBTD->type+0);
	$WEBTD->parser_template=$parser_template;

	$parser_start="%SECTION_WEBTD_BEGIN%";
	$parser_end="%SECTION_WEBTD_END%";


	parser($parser_start,$parser_end,$parser_template,$parser_tokens);

