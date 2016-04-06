<?
 $action="";
 $katalog=$UIMAGES;
 if ($BASIC_RIGHTS) $katalog.="/$USERNAME";
 
 if (strlen($lista))
 {
	unlink("$katalog/$lista");
 }
 $path.="/".$curdir;
?>
