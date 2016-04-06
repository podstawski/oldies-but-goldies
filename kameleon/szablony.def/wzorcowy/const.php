<?
$CONST_FTP_PASSIVE=1;
$CONST_TOKENS="tokens.php";					//w pliku tokens.h znajduje sie funkcja do wlasnych tokenow dla parsera
$CONST_PARSER_INTEGRATED=1;					//oznacza ze ma używać parsera kameleon - powinno byc zawsze wlaczone
$CONST_PARSER_TOKENS=1;

$CONST_PRE_H='';
$CONST_POST_H='';
$CONST_ACTION_H='';

$DEFAULT_PATH_IMAGES="images/$ver";
$DEFAULT_PATH_UIMAGES="images";
$DEFAULT_PATH_PAGES="php";
$DEFAULT_PATH_INCLUDE="include";
$DEFAULT_PATH_UFILES="att";
$DEFAULT_PATH_PAGES_PREFIX="";

$CONST_LANGS=array("pl",'en');  			// Jakie jezyki pokazuja sie do wyboru

$C_DEBUG_MODE = ($KAMELEON_MODE & !$editmode);
$C_DEBUG_MODE =0;

$C_EDITOR_FORM = 0;
$C_SWF_STYLE = 0;
$CONST_NEXT_PAGE_LINK_FOLLOW=1;

$C_DIRECTORY_INDEX=array('index.php','index.html');

$C_CONTENT_EDITABLE=true;							// Obsluga dodawania pozycji w menu z poziomu belki td 

$CONST_REMOTE_INCLUDES_ARE_HERE=1;			//katalog uincludes zawiera pliki
$C_FORGET_DOCBASE=1;						//nie ma BASE href=...
$C_MULTI_HF=1;								//wraz ze zmiana typu strony - inne naglowki
//$FTP_ALSO_VERSION=array(10);

$C_PAGE_WIDTH			= 980;				//szerokosc strony
$C_PAGE_ALIGN			= "center";			//polozenie strony
$C_PAGE_MENULEFTWIDTH	= "130";			//szerokosc lewego menu
$C_PAGE_MENURIGHTWIDTH	= "130";			//szerokosc prawego menu

//definicja ilosci szpalt (levels)
$TD_POZIOMY[]=array(1,"1. Prawa szpalta górna dziedziczona");
$TD_POZIOMY[]=array(2,"2. Prawa szpalta górna");
$TD_POZIOMY[]=array(3,"3. Prawa szpalta dolna dziedziczona");
$TD_POZIOMY[]=array(4,"4. Prawa szpalta dolna");
$TD_POZIOMY[]=array(5,"5. Lewa szpalta górna dziedziczona");
$TD_POZIOMY[]=array(6,"6. Lewa szpalta górna");
$TD_POZIOMY[]=array(7,"7. Lewa szpalta dolna dziedziczona");
$TD_POZIOMY[]=array(8,"8. Lewa szpalta dolna");
$DEFAULT_TD_LEVEL=2;

//definicja ilosci wierszy nagłówka lub stopki
$TD_POZIOMY_HF[]=array(1,label("Lewa"));
$TD_POZIOMY_HF[]=array(2,label("Prawa")."");
$TD_POZIOMY_HF[]=array(3,label("Srodkowa"));

// definicja szablonów dla stron
$PAGE_TYPY[]=array(0,"0. HOME","body_standard.html");
$PAGE_TYPY[]=array(1,"1. Standard","body_standard.html");

//definicje typów modułów
$TD_TYPY[]=array(0,"0. Standard","td_standard.html");
$TD_TYPY[]=array(1,"1. Pusty","td_plain.html");
$TD_TYPY[]=array(2,"2. Top menu","td_topmenu.html");
$TD_TYPY[]=array(3,"3. Mapa","td_mapa.html");
$TD_TYPY[]=array(4,"4. Sub menu","td_submenu.html");
$TD_TYPY[]=array(5,"5. Slideshow","td_slideshow.html");
$TD_TYPY[]=array(6,"6. Opcje dodatkowe","td_opcje_dodatkowe.html");
$TD_TYPY[]=array(7,"7. Wyszukiwarka","td_search.html");
$TD_TYPY[]=array(8,"8. Galeria","td_gallery.html");
$TD_TYPY[]=array(9,"9. Film","td_film.html");

$TD_TYPY_DXML[0]['naglowek']=array('Nagłówek','width:200px','h1|h2|h3');
$TD_TYPY_DXML[4]['naglowek']=array('Nagłówek','width:200px','h1|h2|h3');
$TD_TYPY_DXML[8]['naglowek']=array('Nagłówek','width:200px','h1|h2|h3');
$TD_TYPY_DXML[9]['naglowek']=array('Nagłówek','width:200px','h1|h2|h3');

//tabela z definicja typow menu
$LINK_TYPY[]=array(0,"0. Link",					"link_standard.html");


// ustawienia kameleona

$C_SHOW_OLD_SUPPORT=0;

$C_SHOW_PAGE_TITLE=1;
$C_SHOW_PAGE_DESCRIPTION=1;
$C_SHOW_PAGE_KEYWORDS=1;
$C_SHOW_PAGE_BGCOLOR=1;
$C_SHOW_PAGE_FGCOLOR=1;
$C_SHOW_PAGE_TBGCOLOR=1;
$C_SHOW_PAGE_TFGCOLOR=1;
$C_SHOW_PAGE_CLASS=1;
$C_SHOW_PAGE_BACKGROUND=1;
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
$C_SHOW_TD_VALID=0;

$C_SHOW_LINK_ALT=1;
$C_SHOW_LINK_ALT_TITLE=1;
$C_SHOW_LINK_IMG=1;
$C_SHOW_LINK_IMGA=1;
$C_SHOW_LINK_HREF=1;
$C_SHOW_LINK_UFILE_TARGET=1;
$C_SHOW_LINK_VARIABLES=1;
$C_SHOW_LINK_TARGET=1;
$C_SHOW_LINK_SUBMENU_ID=1;
$C_SHOW_LINK_FGCOLOR=1;
$C_SHOW_LINK_TYPE=1;
$C_SHOW_LINK_CLASS=1;
$C_SHOW_LINK_DESCRIPTION=1;

$C_HIDE_LINK_ALT=0;
$C_HIDE_LINK_ALT_TITLE=0;
$C_HIDE_LINK_IMG=0;
$C_HIDE_LINK_IMGA=0;
$C_HIDE_LINK_HREF=0;
$C_HIDE_LINK_UFILE_TARGET=0;
$C_HIDE_LINK_VARIABLES=0;
$C_HIDE_LINK_TARGET=0;
$C_HIDE_LINK_SUBMENU_ID=0;
$C_HIDE_LINK_FGCOLOR=0;
$C_HIDE_LINK_TYPE=0;
$C_HIDE_LINK_CLASS=0;
$C_HIDE_LINK_DESCRIPTION=0;

$C_SITECREDITS[]=array("sc_name"=>"implementation","sc_alt"=>"GAMMANET sp. z o.o.","sc_link"=>"http://www.gammanet.pl");
$C_SITECREDITS[]=array("sc_name"=>"CMS","sc_alt"=>"web kameleon","sc_link"=>"http://www.webkameleon.com");
$C_SITECREDITS[]=array("sc_name"=>"graphics design","sc_alt"=>"Tomek Szurkowski","sc_link"=>"http://www.tomszurkowski.com");
?>
