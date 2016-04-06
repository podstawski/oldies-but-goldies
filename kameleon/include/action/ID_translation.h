<?

$src=explode(":",$clibpage);


$query='';


if (is_array($MENU_ID_TRANSLATION))
{
	foreach(array_keys($MENU_ID_TRANSLATION) AS $src_menu)
	{
		$menu_id=-1;
		include ("include/menu_max.h");
		$MENU_ID_TRANSLATION[$src_menu]=$menu_id;
		$query="UPDATE webtd SET menu_id=$menu_id WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND menu_id=-$src_menu;
				INSERT INTO weblink (server,page_id,menu_id,ver,lang,img,imga,alt,page_target,href,
					pri,fgcolor,type,class,variables,name,hidden,alt_title)
					SELECT $SERVER_ID,page_id,$menu_id,$ver,'$lang',img,imga,alt,-1*page_target,href,
					pri,fgcolor,type,class,variables,name,hidden,alt_title
					FROM weblink WHERE ver=$src[2] AND lang='$src[1]' AND server=$src[0] AND menu_id=$src_menu;		
				";
		if ($adodb->Execute($query)) logquery($query) ;



	}
}






$query='';
while (is_array($PAGE_ID_TRANSLATION) && list($srcpage,$newpage)=each($PAGE_ID_TRANSLATION))
{
	$set=$newpage?$newpage:$srcpage;

	$query.="UPDATE webpage SET next=$set WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND next=-$srcpage;\n";
	$query.="UPDATE webtd SET next=$set WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND next=-$srcpage AND autor='$PHP_AUTH_USER';\n";
	$query.="UPDATE webtd SET more=$set WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND more=-$srcpage AND autor='$PHP_AUTH_USER';\n";
	$query.="UPDATE weblink SET page_target=$set WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND page_target=-$srcpage;\n";
}
$query.="DELETE FROM weblink WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND page_target<0;\n";

if ($adodb->Execute($query)) 
{
	logquery($query) ;
}



