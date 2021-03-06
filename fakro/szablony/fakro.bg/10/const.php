<?

$CONST_SWF_JS=1;

$CONST_FTP_PASSIVE=1;
$CONST_TOKENS="tokens.php";					//w pliku tokens.h znajduje sie funkcja do wlasnych tokenow dla parsera
$CONST_PARSER_INTEGRATED=1;					//oznacza ze ma u�ywa� parsera kameleon - powinno byc zawsze wlaczone
$CONST_PARSER_TOKENS=1;

$CONST_MORE_TOKENS=array('sklep_tokens'=>'sklep_tokens.php');
$CONST_MORE_CSS=array(2=>'sklep/textstyle.css',50=>'sklep/textstyle.css');
$CONST_MORE_JS=array(2=>'sklep/scripts.js',50=>'sklep/scripts.js');

$CONST_PRE_H='pre.php';
$CONST_POST_H='post.php';
$CONST_ACTION_H='action.php';

$DEFAULT_PATH_IMAGES="img/\$ver";
$DEFAULT_PATH_UIMAGES="img";
$DEFAULT_PATH_PAGES="html$lang";
$DEFAULT_PATH_INCLUDE="include";
$DEFAULT_PATH_UFILES="att";
$DEFAULT_PATH_PAGES_PREFIX="print/";

$C_EDITOR_FORM = 1;
$C_SWF_STYLE = 1;
$CONST_NEXT_PAGE_LINK_FOLLOW=1;

//-----------------------------------------------------------------
$CONST_LANGS=array("bg");
$C_DIRECTORY_INDEX=array('index.php','index.html');
$C_SHOW_OLD_SUPPORT = 1;
//-----------------------------------------------------------------

$C_DEBUG_MODE = ($KAMELEON_MODE & !$editmode);
$C_DEBUG_MODE =0;

$CONST_REMOTE_INCLUDES_ARE_HERE=1;			//katalog uincludes zawiera pliki
$C_FORGET_DOCBASE=1;						//nie ma BASE href=...
$C_MULTI_HF=0;								//wraz ze zmiana typu strony - inne naglowki
//$FTP_ALSO_VERSION=array(10);

$C_PAGE_WIDTH			= 750;				//szerokosc strony
$C_PAGE_ALIGN			= "center";			//polozenie strony
$C_PAGE_MENULEFTWIDTH	= "130";			//szerokosc lewego menu
$C_PAGE_MENURIGHTWIDTH	= "130";			//szerokosc prawego menu

//definicja ilosci szpalt (levels)
$TD_POZIOMY[]=array(4,"4. Lewa szpalta dziedziczona");
$TD_POZIOMY[]=array(1,"1. Lewa szpalta");
$TD_POZIOMY[]=array(7,"7. Lewa szpalta dolna dziedziczona");
$TD_POZIOMY[]=array(5,"5. Srodkowa szpalta dziedziczona");
$TD_POZIOMY[]=array(2,"2. Srodkowa szpalta");
$TD_POZIOMY[]=array(8,"8. Srodkowa szpalta dolna dziedziczona");
$TD_POZIOMY[]=array(6,"6. Prawa szpalta dziedziczona");
$TD_POZIOMY[]=array(3,"3. Prawa szpalta");
$TD_POZIOMY[]=array(9,"9. Prawa szpalta dolna dziedziczona");
$DEFAULT_TD_LEVEL=2;

//definicja ilosci wierszy nag��wka lub stopki
$TD_POZIOMY_HF[]=array(1,label("Top level"));
$TD_POZIOMY_HF[]=array(2,label("Level")." 2");
$TD_POZIOMY_HF[]=array(3,label("Level")." 3");
$TD_POZIOMY_HF[]=array(4,label("Level")." 4");
$TD_POZIOMY_HF[]=array(5,label("Level")." 5");
$TD_POZIOMY_HF[]=array(6,label("Bottom level"));

// definicja szablon�w dla stron
$PAGE_TYPY[]=array(0,"0. HOME","body_home.html");
$PAGE_TYPY[]=array(1,"1. Standard","body_standard.html");
$PAGE_TYPY[]=array(2,"2. SKLEP","body_standard.html");

$PAGE_TYPY[]=array(50,"50. Sklep/kartoteka","sklep/body_kartoteka.html");

//definicje typ�w modu��w
$TD_TYPY[]=array(0,"0. Standard","td_standard.html");
$TD_TYPY[]=array(1,"1. Standard2","td_standard2.html");
$TD_TYPY[]=array(3,"3. PRODUKT","td_produkt.html");
$TD_TYPY[]=array(6,"6. SKLEP","td_sklep.html");
$TD_TYPY[]=array(2,"2. ADMIN pl","td_plain.html");
$TD_TYPY[]=array(4,"4. ADMIN plo","td_plainonly.html");
$TD_TYPY[]=array(5,"5. Include only","td_inc.html");

$TD_TYPY[]=array(50,"50. Sklep/kartoteka","sklep/td_kartoteka.html");

//tabela z definicja typow menu
$LINK_TYPY[]=array(0,"0. Link",					"link_standard.html");
$LINK_TYPY[]=array(1,"1. Lista ul",				"link_ul.html");
$LINK_TYPY[]=array(2,"2. Lista ol",				"link_ol.html");
$LINK_TYPY[]=array(3,"3. Lista pozioma",		"link_horizontal.html");
$LINK_TYPY[]=array(4,"4. Lista pionowa",		"link_vertical.html");
$LINK_TYPY[]=array(5,"5. Select",					"link_select.html");
$LINK_TYPY[]=array(6,"6. FLASH",					"link_flash.html");
$LINK_TYPY[]=array(7,"7. Lista pozioma z kreska",	"link_horizontal_kreska.html");
$LINK_TYPY[]=array(8,"8. GALERIA",	"link_galeria.html");
$LINK_TYPY[]=array(9,"9. GALERIA2",	"link_galeria2.html");// definicje za��cznych us�ug


if ($KAMELEON_MODE)
{
	$APIS[]=array("",label("Choose"));
	//$APIS[]=array("news",label("News"));
	$APIS[]=array("search",label("Search engine"));
	//$APIS[]=array("ogloszenia",label("Hyde Park"));
	//$APIS[]=array("ksiega",label("Guest book"));
	//$APIS[]=array("forum",label("Forum"));
	//$APIS[]=array("kontakt",label("Contact formular"));
	//$APIS[]=array("polecam",label("Inform friends"));
	//$APIS[]=array("counter",label("Counter"));
	$APIS[]=array("kameleon:sitemap",label("Site map"));
	
	$APIS[]=array("tip",label("Test tip"));
	$APIS[]=array("sendform_otrs",label("Test send form otrs"));
}

// ustawienia kameleona
$C_SHOW_PAGE_TITLE=1;
$C_SHOW_PAGE_DESCRIPTION=1;
$C_SHOW_PAGE_KEYWORDS=1;
$C_SHOW_PAGE_BGCOLOR=1;
$C_SHOW_PAGE_FGCOLOR=1;
$C_SHOW_PAGE_TBGCOLOR=1;
$C_SHOW_PAGE_TFGCOLOR=1;
$C_SHOW_PAGE_CLASS=1;
$C_SHOW_PAGE_BACKGROUND=0;
$C_SHOW_PAGE_TYPE=1;
$C_SHOW_PAGE_NEXT=1;
$C_SHOW_PAGE_PREV=1;
$C_SHOW_PAGE_FILENAME=1;
$C_SHOW_PAGE_MENU_ID=1;
$C_SHOW_PAGE_SUBMENU_ID=1;

$C_SHOW_TD_TITLE=1;
$C_SHOW_TD_MENU=1;
$C_SHOW_TD_HTML=1;
$C_SHOW_TD_API=1;
$C_SHOW_TD_BGIMG=1;
$C_SHOW_TD_BGCOLOR=1;
$C_SHOW_TD_ALIGN=1;
$C_SHOW_TD_VALIGN=1;
$C_SHOW_TD_CLASS=1;
$C_SHOW_TD_WIDTH=1;
$C_SHOW_TD_TYPE=1;
$C_SHOW_TD_LEVEL=1;
$C_SHOW_TD_IMG=1;
$C_SHOW_TD_MORE=1;
$C_SHOW_TD_NEXT=1;
$C_SHOW_TD_SIZE=1;
$C_SHOW_TD_COS=1;
$C_SHOW_TD_COSTXT=1;
$C_SHOW_TD_STATICINCLUDE=1;
$C_SHOW_TD_VALID=1;
?>
