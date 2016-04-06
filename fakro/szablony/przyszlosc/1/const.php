<?
$CONST_FTP_PASSIVE=1;
$CONST_TOKENS="tokens.php";					//w pliku tokens.h znajduje sie funkcja do wlasnych tokenow dla parsera
$CONST_PARSER_INTEGRATED=1;					//oznacza ze ma u?ywa? parsera kameleon - powinno byc zawsze wlaczone
$CONST_PARSER_TOKENS=1;

$CONST_PRE_H='pre.php';
$CONST_POST_H='post.php';
$CONST_ACTION_H='action.php';

$DEFAULT_PATH_IMAGES="images/\$ver";
$DEFAULT_PATH_UIMAGES="uimages";
$DEFAULT_PATH_PAGES="strony/\$ver/\$lang";
$DEFAULT_PATH_INCLUDE="include";
$DEFAULT_PATH_UFILES="att/\$ver";
$DEFAULT_PATH_PAGES_PREFIX="";

//-----------------------------------------------------------------
$CONST_LANGS=array("pl");
$C_DIRECTORY_INDEX=array('index.php','index.html');
$C_SHOW_OLD_SUPPORT = 1;
//-----------------------------------------------------------------

$C_DEBUG_MODE = ($KAMELEON_MODE & !$editmode);
$C_DEBUG_MODE =0;

$C_EDITOR_FORM = 1;
$C_SWF_STYLE = 0;
$CONST_NEXT_PAGE_LINK_FOLLOW=1;

$C_DIRECTORY_INDEX=array('index.php','index.html');

$CONST_REMOTE_INCLUDES_ARE_HERE=1;  //katalog uincludes zawiera pliki
$C_FORGET_DOCBASE=1;                //nie ma BASE href=...
$C_MULTI_HF=1;                      //wraz ze zmiana typu strony - inne naglowki
//$FTP_ALSO_VERSION=array(10);

$C_PAGE_WIDTH			      = 750;      //szerokosc strony
$C_PAGE_ALIGN			      = "center"; //polozenie strony
$C_PAGE_MENULEFTWIDTH   = "130";    //szerokosc lewego menu
$C_PAGE_MENURIGHTWIDTH  = "130";    //szerokosc prawego menu

//definicja ilosci szpalt (levels)
$TD_POZIOMY[]=array(4,"4. Lewa szpalta dziedziczona");
$TD_POZIOMY[]=array(1,"1. Lewa szpalta");
$TD_POZIOMY[]=array(7,"7. Lewa szpalta dolna dziedziczona");
$TD_POZIOMY[]=array(5,"5. Srodkowa szpalta dziedziczona");
$TD_POZIOMY[]=array(2,"2. Srodkowa szpalta");
$TD_POZIOMY[]=array(6,"6. Prawa szpalta dziedziczona");
$TD_POZIOMY[]=array(3,"3. Prawa szpalta");
$DEFAULT_TD_LEVEL=2;

//definicja ilosci wierszy nag??wka lub stopki
$TD_POZIOMY_HF[]=array(1,"1. Poziom TopLeft");
$TD_POZIOMY_HF[]=array(2,"2. Poziom TopRight");
$TD_POZIOMY_HF[]=array(3,"3. Poziom TopCenter");
$TD_POZIOMY_HF[]=array(4,"4. Poziom BottomLeft");    // +
$TD_POZIOMY_HF[]=array(5,"5. Poziom BottomCenter");
$TD_POZIOMY_HF[]=array(6,"6. Poziom BottomRight");   // +

// definicja szablon?w dla stron
$PAGE_TYPY[]=array(0,"0. HOME","body_home.html");
$PAGE_TYPY[]=array(1,"1. Standard","body_standard.html");

//definicje typ?w modu??w
$TD_TYPY[]=array(0,"0. Standard","td_std1.html");
$TD_TYPY[]=array(1,"1. Standard2","td_std2.html");
$TD_TYPY[]=array(3,"3. FLASH","td_flash.html");
$TD_TYPY[]=array(2,"2. ADMIN pl","td_plain.html");
$TD_TYPY[]=array(4,"4. ADMIN plo","td_plainonly.html");
$TD_TYPY[]=array(5,"5. NEWS","td_news.html"); //+
$TD_TYPY[]=array(6,"6. PRESS","td_news.html");
$TD_TYPY[]=array(7,"7. SPEC","td_spec.html"); // +


//tabela z definicja typow menu
$LINK_TYPY[]=array(0,"0. Link",					"link_standard.html");
$LINK_TYPY[]=array(1,"1. Lista ul",				"link_ul.html");
$LINK_TYPY[]=array(2,"2. Lista ol",				"link_ol.html");
$LINK_TYPY[]=array(3,"3. Lista pozioma",		"link_horizontal.html");
$LINK_TYPY[]=array(4,"4. Lista pionowa",		"link_vertical.html");
$LINK_TYPY[]=array(5,"5. Select",					"link_select.html");
$LINK_TYPY[]=array(6,"6. FLASH",					"link_flash.html");
$LINK_TYPY[]=array(7,"7. Lista pozioma z kreska",	"link_horizontal_kreska.html"); // +
$LINK_TYPY[]=array(8,"8. GALERIA",				"link_galeria.html");
$LINK_TYPY[]=array(9,"9. SLAJD",				"link_slajd.html");
$LINK_TYPY[]=array(10,"10. RAPORTY i NEWSY",	"link_raporty.html");

if ($KAMELEON_MODE) {
    $APIS[]=array("ogloszenia_fakro",label("Hyde Park - fakro"));
}


// ustawienia kameleona
$C_SHOW_PAGE_TITLE=1;
$C_SHOW_PAGE_DESCRIPTION=1;
$C_SHOW_PAGE_KEYWORDS=1;
$C_SHOW_PAGE_BGCOLOR=0;
$C_SHOW_PAGE_FGCOLOR=0;
$C_SHOW_PAGE_TBGCOLOR=0;
$C_SHOW_PAGE_TFGCOLOR=0;
$C_SHOW_PAGE_CLASS=0;
$C_SHOW_PAGE_BACKGROUND=1;
$C_SHOW_PAGE_TYPE=1;
$C_SHOW_PAGE_NEXT=1;
$C_SHOW_PAGE_PREV=1;
$C_SHOW_PAGE_FILENAME=1;
$C_SHOW_PAGE_MENU_ID=0;
$C_SHOW_PAGE_SUBMENU_ID=0;

$C_SHOW_TD_TITLE=1;
$C_SHOW_TD_MENU=1;
$C_SHOW_TD_HTML=1;
$C_SHOW_TD_API=0;
$C_SHOW_TD_BGIMG=1;
$C_SHOW_TD_BGCOLOR=0;
$C_SHOW_TD_ALIGN=0;
$C_SHOW_TD_VALIGN=0;
$C_SHOW_TD_CLASS=1;
$C_SHOW_TD_WIDTH=0;
$C_SHOW_TD_TYPE=1;
$C_SHOW_TD_LEVEL=1;
$C_SHOW_TD_IMG=0;
$C_SHOW_TD_MORE=1;
$C_SHOW_TD_NEXT=1;
$C_SHOW_TD_SIZE=1;
$C_SHOW_TD_COS=0;
$C_SHOW_TD_COSTXT=1;
$C_SHOW_TD_STATICINCLUDE=0;
$C_SHOW_TD_VALID=1;

$C_SITECREDITS[]=array("sc_name"=>"implementation","sc_alt"=>"GAMMANET sp. z o.o.","sc_link"=>"http://www.gammanet.pl");
$C_SITECREDITS[]=array("sc_name"=>"CMS","sc_alt"=>"web kameleon","sc_link"=>"http://www.webkameleon.com");
$C_SITECREDITS[]=array("sc_name"=>"graphics design","sc_alt"=>"Tomek Szurkowski","sc_link"=>"http://www.tomszurkowski.com");
?>
