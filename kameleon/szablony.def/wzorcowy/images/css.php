<?
header('Content-type: text/css');
$path = $_REQUEST["t"].str_replace("../","",base64_decode($_REQUEST["p"]));
$path = ".";
echo "
body { background-image: url('".$path."/szablon/body_bg.png'); }
.logo { background-image: url('".$path."/szablon/logo.png'); }
.top_menu, .submenu h2, .formularz .btn_gray { background-image: url('".$path."/szablon/gr_gray.png');} 
.submenu li { background-image: url('".$path."/szablon/gr_gray_li.png');} 
.submenu li ul li { background-image: url('".$path."/szablon/gr_gray_sub.png');} 
.submenu li.active a, .submenu li ul li.active a { background-image: url('".$path."/szablon/dot.png');} 
.top_menu .active, .tabela th, .formularz .btn_blue { background-image: url('".$path."/szablon/gr_blue.png');}
.body_footer { background-image: url('".$path."/szablon/footer_bg.png');}
.body_footer .left .mapa { background-image: url('".$path."/szablon/mapa.png');}
.mapa .parent .plus	{ background-image: url('".$path."/szablon/mapa_plus.png');}
.mapa .parent .minus { background-image: url('".$path."/szablon/mapa_minus.png');}
.mapa .parent .no { background-image: url('".$path."/szablon/mapa_no.png');}
.slideshow .navi .items, .slideshow .navi .items a { background-image: url('".$path."/slideshow/navi_pas.png');} 
.opcje_dodatkowe .drukuj { background-image: url('".$path."/szablon/i_drukuj.png');}
.opcje_dodatkowe .polec { background-image: url('".$path."/szablon/i_polec.png');}
.opcje_dodatkowe .dodaj { background-image: url('".$path."/szablon/i_dodaj.png');}
.formularz .msg_ok { background-image: url('".$path."/szablon/msg_ok.png'); }
.formularz .msg_error { background-image: url('".$path."/szablon/msg_error.png'); }
";

?>