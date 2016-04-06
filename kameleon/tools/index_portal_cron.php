<?


if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');

define ('ADODB_DIR',"../adodb/");
include ("../include/adodb.h");

include ("../include/fun.h");
include ("../include/search.h");

$KEY="";
$INDEX_PAGE="";
$VERSION=1;
$LANG="p";

$dbapi=$db;

if (!$db) 
{
	echo "Baza danych ma problem natury egzystencjalnej. \n";
	return;
}
if (!$dbapi) 
{
	echo "Baza danych API ma problem natury egzystencjalnej. \n";
	return;
}

//*************************************
$SQL =" SELECT u_params,servername FROM search_ustawienia";
$res_i=$adodb->Execute($SQL);
$len=$res_i->RecordCount();
//echo "serwisow: $len\n";
for ($p=0;$p<$len;$p++)
{
	parse_str(ado_ExplodeName($res_i,$p));
	parse_str($u_params);

	$VERSION=$u_ver;
	$LANG=$u_lang;
	$KEY=$servername;
	$PAGE_RESULT=$page_result;

	//by robson
//	$u_status=1;
	if ($u_status==1)
	{
		;
	}
	else
	{
	   if ($u_status==0)
    		 continue;
	   else
	   {
			$data=getdate(time());
	    	$wday=$data["wday"];
	    	if ($wday!=0)  continue;
	   }
	 }
	// tu trzeba odpalic indeksacje
	$dzis=date("d-m-Y");
	$godz=date("H:i:s");
	echo "$dzis $godz ";
	//$cmd="$PHP_EXE index_portal.php -S$KEY $PHP_SUFFIX";
	$cmd="$PHP_EXE index_portal.php -S$KEY ";
	exec($cmd);
	$dzis=date("d-m-Y");
	$godz=date("H:i:s");
	echo " $dzis $godz $KEY\n";
}
?>
