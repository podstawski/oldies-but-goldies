<?
	$CONST_IMG_EDITOR="editorObrazkowKameleona_";
	$init_gallery_cmd='';

	switch ($galeria)
	{
		case 1: // CARTMAN: NIE WYKORZYSTANE
			$rootdir=$UFILES;
			$callback="wstawPlik";
			$default_preview=0;
			$preview_denied=1;
			$final_button=label("Create hyperlink to file");
			$cookiename="cookiefpath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=0;
			$backwithdir=0; // czy ma zwracać z $rootdir czy nie 1-tak, 2-nie
			break;
			
		case 2: // UIMAGES - KAMELEON - OPCJE STRONY / MODUŁU
		default:
			$rootdir=$UIMAGES;
			$callback="wstawObrazek";
			$default_preview=1;
			$preview_denied=0;
			$final_button=label("Insert image");
			$cookiename="cookieipath";
			if (function_exists('gd_info')) $edit_allowed=1;
			if (function_exists('gd_info')) $edytor='ImageManager/editor.php';
			$ckeditor=0;
			$backwithdir=0;
			break;

		case 3: // INCLUDE - KAMELEON - OPCJE MODUŁU
			eval("\$rootdir=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
			$callback="wstawPhp";
			$default_preview=0;
			$preview_denied=1;
			$final_button=label("Include file");
			$cookiename="cookiefpath";
			$edit_allowed=1;
			$edytor='fileedit.php';
			$ckeditor=0;
			$backwithdir=0;
			break;
			
		case 4: // UIMAGES - KAMELEON - PRZEGLĄDARKA
			$rootdir=$UIMAGES;
			$callback="";
			$default_preview=1;
			$preview_denied=0;
			$final_button="";
			$cookiename="cookieipath";
			if (function_exists('gd_info')) $edit_allowed=1;
			if (function_exists('gd_info')) $edytor='ImageManager/editor.php';
			$ckeditor=0;
			$backwithdir=0;
			break;

		case 5: // IMAGES(SZABLON) - KAMELEON - PRZEGLĄDARKA
			eval("\$rootdir=\"$SZABLON_PATH\";");
			$callback="";
			$default_preview=0;
			$preview_denied=0;
			$final_button="";
			$cookiename="cookiephppath";
			$edit_allowed=1;
			$edytor='h,php,css,js,xml,txt:fileedit.php'.(function_exists('gd_info') ? ';gif,jpg,jpeg,png:ImageManager/editor.php' : '');
			$ckeditor=0;
			$backwithdir=0;
			break;

	  case 6: // ROOT - KAMELEON - PRZEGLĄDARKA
			$rootdir=$UFILES.'/.root';
			$callback="";
			$default_preview=0;
			$preview_denied=1;
			$final_button="";
			$cookiename="cookiefrpath";
			$edit_allowed=1;
			$edytor='h,php,css,js,xml,txt:fileedit.php'.(function_exists('gd_info') ? ';gif,jpg,jpeg,png:ImageManager/editor.php' : '');
			$ckeditor=0;
			$backwithdir=0;
			$init_gallery_cmd="system('cp root/* $rootdir');";
			break;
			
		case 7: // UIMAGES - CKEDITOR - POWIĘKSZENIE OBRAZKA
			$rootdir=$UIMAGES;
			$callback="";
			$default_preview=0;
			$preview_denied=1;
			$final_button="";
			$cookiename="cookieipath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=2;
			$backwithdir=1;
			break;
			
		case 8: // UIMAGES - CKEDITOR - WSTAW OBRAZEK
			$rootdir=$UIMAGES;
			$callback="";
			$default_preview=0;
			$preview_denied=1;
			$final_button="";
			$cookiename="cookieipath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=1;
			$backwithdir=1;
			break;
			
		case 9: // UFILES - CKEDITOR - WSTAW ZAŁĄCZNIK
			$rootdir=$UFILES;
			$callback="";
			$default_preview=0;
			$preview_denied=1;
			$final_button="";
			$cookiename="cookiefpath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=3;
			$backwithdir=1;
			break;

		case 10: // WYMYSŁ PUDLA ;)
			$rootdir=$UFILES;
			$callback="wstawPlik";
			$default_preview=0;
			$preview_denied=1;
			$final_button="";
			$cookiename="cookieufpath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=0;
			$backwithdir=0;
			break;
		
		case 11: // UIMAGES - CKEDITOR - WSTAW LINK DO OBRAZKA
			$rootdir=$UIMAGES;
			$callback="";
			$default_preview=0;
			$preview_denied=1;
			$final_button="";
			$cookiename="cookieipath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=4;
			$backwithdir=1;
			break;
			
		case 12: // UIMAGES - CKEDITOR - WSTAW FLASH
			$rootdir=$UIMAGES;
			$callback="";
			$default_preview=0;
			$preview_denied=1;
			$final_button="";
			$cookiename="cookieipath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=5;
			$backwithdir=1;
			break;
		case 13: // HTML SAVE AND RESTORE - KAMELEON - PRZEGLĄDARKA
			$rootdir=$UFILES.'/.html';
			$callback="wstawPhp";
			$default_preview=0;
			$preview_denied=1;
			$final_button=label("Restore HTML");			
			$cookiename="cookiefhpath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=0;
			$backwithdir=0;
			$init_gallery_cmd="";
			break;

		case 14: // HTML SAVE AND RESTORE - KAMELEON - PRZEGLĄDARKA
			$rootdir=$UFILES.'/.html';
			$callback="wklej_area";
			$default_preview=0;
			$preview_denied=1;
			$final_button=label("Paste area");			
			$cookiename="cookiefhpath";
			$edit_allowed=0;
			$edytor='';
			$ckeditor=0;
			$backwithdir=0;
			$init_gallery_cmd="";
			break;

	}

?>