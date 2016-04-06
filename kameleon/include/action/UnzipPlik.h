<?
 $action="";

/*
$EXE=explode(' ',$CONST_UNZIP_EXE);
$EXE=$EXE[0];

 if (!strlen($EXE) || !is_executable($EXE) ) 
	$error=label("Can not find UNZIP file or the file is not executable");

*/
 if (strlen($error)) return;



 include("include/ufiles_const.h");
 $katalog=$rootdir;
 if ($BASIC_RIGHTS) $katalog.="/$USERNAME";


 if (strlen($lista))
 {
	$plik_do_rozpakowania="$katalog/$lista";

	$pwd=getcwd();
	$katalog=dirname($plik_do_rozpakowania);
	$plik=basename($plik_do_rozpakowania);

	chdir($katalog);
	$cmd=str_replace('{file}',$plik,CONST_UNZIP_EXE);

	//echo getcwd()."<br>$cmd";
	exec($cmd);
	unlink($plik);
	chdir($pwd);
 }

