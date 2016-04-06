<?
unset($tokens);
$title="KAMELEON";
if (is_object($WEBPAGE))
{
    if (strlen($WEBPAGE->background))   $tokens['background']		=" background='$UIMAGES/$WEBPAGE->background'";
    if (strlen($WEBPAGE->bgcolor)) 	    $tokens['bgcolor']			=" bgcolor='#$WEBPAGE->bgcolor'";	//kolor t�a
    if (strlen($WEBPAGE->fgcolor))	    $tokens['textcolor']		=" text='#$WEBPAGE->fgcolor'";		//kolor czcionki
    if (strlen($WEBPAGE->tbgcolor))		$tokens['tablebgcolor']		=" bgcolor='#$WEBPAGE->tbgcolor'";	//kolor tabeli
	if (strlen($WEBPAGE->tfgcolor))		$tokens['tabletextcolor']	=" text='#$WEBPAGE->tfgcolor'";		//kolor czcionki tabeli
	if (strlen($WEBPAGE->class))        $tokens['bodyclass']		=" class='$WEBPAGE->class'";		//domy�lny styl body
    if (strlen($WEBPAGE->title))	    $tokens['title']			=$WEBPAGE->title;
}


if ($editmode)
{
	$ico='ufiles/'.$SERVER_ID.'-att/.root/favicon.ico';
	if (!file_exists($ico)) $ico='root/favicon.ico';

	$tokens['KAMELEON_CSS']=include_css("kameleon.css")."\n\t<link rel=\"shortcut icon\" href=\"$ico\" />
";
}
elseif($C_GEMIUS_SUPPORT)
{
	include("include/gemius.h");
}



$tokens['TEXTSTYLE_CSS']='';

if (file_exists("$SZABLON_PATH/images/textstyle.css"))
	$tokens['TEXTSTYLE_CSS'].="\n\t<link href=\"$IMAGES/textstyle.css\" rel=\"stylesheet\" type=\"text/css\" />";

if (file_exists("$SZABLON_PATH/images/szablon.css"))
	$tokens['TEXTSTYLE_CSS'].="\n\t<link href=\"$IMAGES/szablon.css\" rel=\"stylesheet\" type=\"text/css\" />"; 


while( is_array($CONST_MORE_CSS) && list($type,$file)=each($CONST_MORE_CSS))
	if ($type==$WEBPAGE->type) $tokens['TEXTSTYLE_CSS'].="\n\t<link href=\"$IMAGES/$file\" rel=\"stylesheet\" type=\"text/css\" />"; 


if (file_exists("$KAMELEON_UIMAGES/$DEFAULT_TEXTFILE_CSS"))
	$tokens['TEXTSTYLE_CSS'].="
		<link href=\"$UIMAGES/$DEFAULT_TEXTFILE_CSS\" rel=\"stylesheet\" type=\"text/css\" />";

$rev=$adodb->GetCookie('KAMELEON_VERSION_REV');
if (strlen($rev)) $rev=".$rev"; 
$tokens['KAMELEON_VERSION']=$KAMELEON_VERSION.$rev;




SWITCH ($lang)
{
    CASE 'p': 
	CASE 'i': 
		$meta_lang = 'pl'; BREAK;
    CASE 'e': 
		$meta_lang = 'en'; BREAK;
    CASE 'd': 
		$meta_lang = 'de'; BREAK;
    CASE 'f': 
		$meta_lang = 'fr'; BREAK;
    CASE 'r': 
		$meta_lang = 'ru'; BREAK;
	default: 
		$meta_lang = $lang; break;
}

$tokens['meta_lang']=$meta_lang;
$tokens['CHARSET']=$CHARSET;


//<meta http-equiv=\"Description\" content=\"$WEBPAGE->description\">\n
//<meta http-equiv=\"keywords\" content=\"$WEBPAGE->keywords\">\n

if (strlen($DOCBASE)) 
	$tokens['docbase']="<base href=\"$DOCBASE\" />";
if (strlen($WEBPAGE->description)) 
	$tokens['metadesc']="	<meta name=\"description\" content=\"$WEBPAGE->description\" />\n";
if (strlen($WEBPAGE->keywords)) 
	$tokens['metakey']="	<meta name=\"keywords\" content=\"$WEBPAGE->keywords\" />";


if (strlen($C_PAGE_MENULEFTWIDTH))	$tokens['C_PAGE_MENULEFTWIDTH']=$C_PAGE_MENULEFTWIDTH;
if (strlen($C_PAGE_MENURIGHTWIDTH))	$tokens['C_PAGE_MENURIGHTWIDTH']=$C_PAGE_MENURIGHTWIDTH;
if (strlen($C_PAGE_WIDTH))			$tokens['C_PAGE_WIDTH']=$C_PAGE_WIDTH;
if (strlen($C_PAGE_ALIGN))			$tokens['C_PAGE_ALIGN']=$C_PAGE_ALIGN;
$tokens['IMAGES']=$IMAGES;
$tokens['UIMAGES']=$UIMAGES;




$parser_template = kameleon_template($SZABLON_PATH,$PAGE_TYPY,$WEBPAGE->type);



$tokens['KAMELEON_META_NEXT']='';
if (strlen($WEBPAGE->next))
{
	if ($KAMELEON_MODE) 
	{
		$tokens['KAMELEON_META_NEXT'].="<script language=\"JavaScript\" type=\"text/javascript\">";
		if (!$editmode) $tokens['KAMELEON_META_NEXT'].="location.href='".kameleon_href('','',$WEBPAGE->next)."';\n";
		$tokens['KAMELEON_META_NEXT'].="</script>\n";
	}
	else
		$tokens['KAMELEON_META_NEXT'].="<"."? header(\"Location: ".kameleon_href('','',$WEBPAGE->next)."\") ?".">";
}

$tokens['KAMELEON_META_ADD']="<meta name=\"WebKameleonId\" content=\"server=$SERVER_ID; page=$page; ver=$ver; lang=$lang\" />\n";
$tokens['WEBPAGE_DATE']="<meta name=\"ftpdate\" content=\"".date("r")."\" />";

$tokens['WEBPAGE_JS']='';

$tokens['WEBPAGE_JS'].="\n\t<script language=\"JavaScript\" type=\"text/javascript\">";
$tokens['WEBPAGE_JS'].="\n\t\tvar JSTITLE = \"".addslashes(stripslashes($WEBPAGE->title))."\";";
$tokens['WEBPAGE_JS'].="\n\t\tvar JSCLOSE = \"".addslashes(stripslashes(label('Close window')))."\";";
$tokens['WEBPAGE_JS'].="\n\t\tvar JSWAIT = \"".addslashes(stripslashes(label('Please wait')))."\";";
$tokens['WEBPAGE_JS'].="\n\t\tvar JSUIMAGES = \"".$UIMAGES."\";";
$tokens['WEBPAGE_JS'].="\n\t\tvar JSIMAGES = \"".$IMAGES."\";";
$tokens['WEBPAGE_JS'].="\n\t</script>";

$tokens['WEBPAGE_SITECREDITS'] ="";
$tokens['WEBPAGE_SITECREDITS'].="\n\t<script language=\"JavaScript\" type=\"text/javascript\">";
$tokens['WEBPAGE_SITECREDITS'].="\n\tSITECREDITS = new Array();";
for ($sc=0;$sc<count($C_SITECREDITS);$sc++)
{
	$tokens['WEBPAGE_SITECREDITS'].="\n\tSITECREDITS[".$sc."]=Array(\"".label($C_SITECREDITS[$sc][sc_name])."\",\"".$C_SITECREDITS[$sc][sc_alt]."\",\"".$C_SITECREDITS[$sc][sc_link]."\");";
}
$tokens['WEBPAGE_SITECREDITS'].="\n\t</script>";

$popupjspath=$KAMELEON_MODE?'jsencode/popupimg.enc':"$PAGE_PATH/popupimg.enc.js";

//AM #9
$tokens['WEBPAGE_JS'].="\n\t<script language=\"Javascript\" type=\"text/javascript\" src=\"$popupjspath\">";
//$plik=@fopen('jsencode/popupimg.enc','r');
//while (!@feof ($plik)) $tokens['WEBPAGE_JS'].=@fread($plik, 512);
//@fclose($plik);
$tokens['WEBPAGE_JS'].="</script>";



$katalog="$SZABLON_PATH/images/js";
$handle=@opendir($katalog);
$jsy=array();

while ($handle && ($file = readdir($handle)) !== false) 
{
	if ($file[0]==".") continue;
	if (is_file("$katalog/$file")) $jsy[]="$IMAGES/js/$file"; 

}


@closedir($handle); 
@sort($jsy);
foreach ($jsy AS $js) $tokens['WEBPAGE_JS'].="\n\t<script src=\"$js\" type=\"text/javascript\"></script>";


if (is_array($CONST_MORE_JS)) foreach ($CONST_MORE_JS AS $type=>$file)
	if ($type==$WEBPAGE->type || $type=='*') 
		$tokens['WEBPAGE_JS'].="\n\t<script src=\"$IMAGES/$file\" type=\"text/javascript\"></script>";


$parser_start="%SECTION_PAGE_HEADER_BEGIN%";
$parser_end="%SECTION_PAGE_HEADER_END%";

$WEBPAGE->parser_template=$parser_template;
include_once ("include/parser.h");

parser($parser_start,$parser_end,$parser_template,$tokens);

//include("include/helpbegin.h");

