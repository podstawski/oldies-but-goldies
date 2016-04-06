<?
global $WEBPAGE, $FL_FORM;
include ($INCLUDE_PATH."./fun.php");
include ($INCLUDE_PATH."./funxml.php");

/*DEFINICJA OBIEKTOW*/
$top = new XMLTD;
//$top->menu_id = 3;

$left = new XMLTD;
$left->plain = "TEST";
//$left->menu_id = 3;

$right = new XMLTD;
$right->img = $UIMAGES."/".$background;
/*END DEFINICJA OBIEKTOW*/

/*DEFINICJA XML*/
$xml_file = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml_file.= "<page>";
/*TOP MENU*/
$xml_file.= "<top>";
	$xml_file.= $top->td2xml();
$xml_file.= "</top>";
/*TOP MENU*/

/*MIDDLE*/
$xml_file.= "<middle>";
	$xml_file.= "<left>";
	$xml_file.= $left->td2xml();
	$xml_file.= "</left>";
	$xml_file.= "<right>";
	$xml_file.= $right->td2xml();
	$xml_file.= "</right>";
$xml_file.= "</middle>";
/*END MIDDLE*/

/*BOTTOM*/
$xml_file.= "<bottom>";
//	$xml_file.= $bottom->td2xml();
$xml_file.= "</bottom>";
/*BOTTOM*/

$xml_file.= "</page>";
$xml_file = iso2utf($xml_file);
/*END DEFINICJA XML*/

//echo $xml_file;
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?><page><top><menu><element><alt>Vidella</alt><url>8.php</url><img>../../../uimages/sb/sp.gif</img></element><element><alt>Inspiracje</alt><url>2.php</url><img>../../../uimages/sb/sp.gif</img><menu><element><alt>stwĂłrz swoje wnÄtrze</alt><url>20.php</url></element><element><alt>gadĹźety</alt><url>19.php</url></element></menu></element><element><alt>Projektanci</alt><url>3.php</url><img>../../../uimages/sb/sp.gif</img></element><element><alt>Produkt</alt><url>4.php</url><img>../../../uimages/sb/sp.gif</img><menu><element><alt>karnisze</alt><url>15.php</url></element><element><alt>rolety</alt><url>16.php</url></element><element><alt>dekoracja sufitu</alt><url>28.php</url></element></menu></element><element><alt>Sklepy</alt><url>5.php</url><img>../../../uimages/sb/sp.gif</img><menu><element><alt>gdzie kupiÄ</alt><url>22.php</url></element><element><alt>dla partnerĂłw handlowych</alt><url>23.php</url></element></menu></element><element><alt>Press Room</alt><url>6.php</url><img>../../../uimages/sb/sp.gif</img></element><element><alt>Kontakt</alt><url>7.php</url><img>../../../uimages/sb/sp.gif</img></element><element><alt>Firma</alt><url>67.php</url><img>../../../uimages/sb/sp.gif</img></element></menu></top><middle><left><plain>Vidella to produkty najwyĹźszej jakoĹci, prezyjazne domowi, pozwalajÄce na dobre</plain><menu><element><alt>KARNISZE</alt><url>15.php</url><img>../../../uimages/sb/head/podstrona_menu_1.jpg</img><imga>../../../uimages/sb/head/podstrona_menu_1b.jpg</imga></element><element><alt>ROLETY</alt><url>16.php</url><img>../../../uimages/sb/head/podstrona_menu_2.jpg</img><imga>../../../uimages/sb/head/podstrona_menu_2b.jpg</imga></element><element><alt>DEKORACJE SUFITU</alt><url>28.php</url><img>../../../uimages/sb/head/podstrona_menu_3.jpg</img><imga>../../../uimages/sb/head/podstrona_menu_3b.jpg</imga></element></menu></left><right><img>../../../uimages/head/partnerzy.jpg</img></right></middle><bottom></bottom></page>";
?>