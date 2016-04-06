<?php
global $GLOBAL_PRE_DONE;
$_C_REMOTE_HTML_INCLUDED=1;

$more=(strlen($WEBTD->more))?$WEBTD->more:$page;
$next=(strlen($WEBTD->next))?$WEBTD->next:$page;

$next=urlencode(kameleon_href("","",$next));
$more=urlencode(kameleon_href("","",$more));

$self=urlencode(kameleon_href("","",$page));
$tit=urlencode($WEBTD->title);

$_costxt=urlencode($WEBTD->costxt);

$param="more=$more&cos=$WEBTD->cos&next=$next&size=$WEBTD->size&class=$WEBTD->class&costxt=$_costxt&title=$tit&pagetype=$WEBPAGE->type&self=$self&tree=$WEBPAGE->tree&sid=$WEBTD->sid";

if ($WEBPAGE->next) 
{
	$param.="&nextpage=".urlencode(kameleon_href("","",$WEBPAGE->next));
	
}
if ($WEBPAGE->prev) 
{
	$param.="&prevpage=".urlencode(kameleon_href("","",$WEBPAGE->prev));
}

if (!$GLOBAL_PRE_DONE || $WEBTD->staticinclude || $KAMELEON_MODE )
{
	$param.="&page=$page&ver=$ver&lang=$lang&IMAGES=$IMAGES&UIMAGES=$UIMAGES&INCLUDE_PATH=$INCLUDE_PATH";
}

$_html="$WEBTD->html";

if ($_html[0]=="@" && is_Object($obj=html_txt2html_obj($_html)) ) 
{
	if (strlen($obj->file_inc))
	{
		$_html=ereg_replace($obj->file,$obj->file_inc,$_html);
	}
}


if ( $CONST_REMOTE_INCLUDES_ARE_HERE 
	&& ($WEBTD->staticinclude || $KAMELEON_MODE ) ) 
{
	kameleon_include($_html,$param);
}
else
{
	
	if ($_html[0]=="@")
	{
		$param.="&INCLUDE_PATH=$INCLUDE_PATH/".dirname($_html);
		$param.="&MODULE_PATH=$INCLUDE_PATH/".dirname($_html);
		$restore="parse_str(\"INCLUDE_PATH=$INCLUDE_PATH\");";
	}
	else
	{
		$restore='';
		$param.="&INCLUDE_PATH=$INCLUDE_PATH";
	}

	$slash=strlen($INCLUDE_PATH)?"/":"";
	$param.="&html=$_html";
	if (!ereg("\.(ht|x)m[l]*\$",$WEBPAGE->file_name)) echo "<?php parse_str(\"$param\"); \n include(\"$INCLUDE_PATH$slash$_html\"); $restore ?>";
}
