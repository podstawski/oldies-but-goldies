<?
$KEY="";
$INDEX_PAGE="";
$VERSION="";
$LANG="";
$CHANGES=0;
if ($argc<2)
{
	echo "Syntax: $argv[0] -Sserver_name [-Vversion] [-Llang] [-Ppage_number] [-R] [-C{days}]

	Description:
	server_name  - server name in Web Kameleon
	version      - version number
	lang         - {p|i|e|d|...} modyfied in const.php in szablony
	page_number  - page number in kameleon
	-R           - drop and create new search index, it takes sometimes a few hours
	days         - index only updated pages in last number of days, default is 0,
	               example: -C2 index pages changed in last 2 days

	";
	return;
}

for ($i=1;$i<$argc;$i++)
{
	$par=$argv[$i];

	if ($par[0]!="-") continue;
	switch ($par[1])
	{
        case 'L':
            $LANG=substr($par,2);
            break;
  
		case 'V':
			$VERSION=substr($par,2);
			break;
		case 'S':
			$KEY=substr($par,2);
			break;
		case 'P':
			$INDEX_PAGE=substr($par,2);
			if (strpos($INDEX_PAGE,'-')) $INDEX_PAGE=explode('-',$INDEX_PAGE);
			else $INDEX_PAGE+=0;
			break;
		case 'R':
			$REINDEX=1;
			break;
		case 'C':
			$CHANGES=0+substr($par,2);
			
			break;

	}
	
}

chdir("..");
if (file_exists('const.php')) include_once('const.php'); else include_once('const.h');
define ('ADODB_DIR',"adodb/");
include("include/request.h");
include("include/adodb.h");
include_once ("include/fun.h");
include ("include/search.h");


if (!is_Object($adodb)) 
{
	echo "Baza danych ma problem natury egzystencjalnej. \n";
	return;
}

//*************************************
$SQL =" SELECT u_params,servername FROM search_ustawienia WHERE servername='$KEY'";
$res=$adodb->Execute($SQL);
$len=$res->RecordCount();
if ($len==1)
{
	parse_str(ado_ExplodeName($res,0));
	parse_str($u_params);

	if ($u_status==1)
	{
		$params="u_status=0&u_lang=$u_lang&u_ver=$u_ver&page_result=$page_result&u_button_img=$u_button_img&u_index_type=$u_index_type";
    	$SQL = " UPDATE search_ustawienia SET u_params='$params'";
	    $SQL.= " WHERE servername='$KEY'";
	    $res=$adodb->Execute($SQL);
	}
	if ($u_index_type==1)
		$operator="=";
	else
		$operator="<=";

	// robson
	if ($REINDEX)
	{
		$u_status=1;
		$operator="=";
	}
	if (!strlen($VERSION)) $VERSION=$u_ver;
	if (!strlen($LANG)) $LANG=$u_lang;
	$KEY=$servername;
	$PAGE_RESULT=$page_result;

//	echo "$u_status, $VERSION, $LANG , $KEY\n";return;
	$SQL =" SELECT id AS server FROM servers WHERE nazwa='$KEY'";
	$res=$adodb->Execute($SQL);
	parse_str(ado_ExplodeName($res,0));

	$adodb->Close();

//	Ladowanie srodowiska kameleona i ustawien szablonu
//	tak aby mozna bylo okreslic 

	$mybasename="index";
	$GENERATE_ONLY_WEBPAGE_OBJECT=1;
	$SERVER_ID=$server;
	$lang=$LANG;
	$page=$PAGE_RESULT;
	$ver=$VERSION;

	//error_reporting(0);
	include ("index.php");
	include ("include/adodb.h");

	$dbapi=$db;
	$page+=0;
	$totalwords=0;
	$t=time();


	$query =" SELECT DISTINCT id AS page FROM webpage ";
	$query.=" WHERE server=$server AND lang='$LANG' AND ver $operator $VERSION 
			AND (hidden<>1 OR hidden IS NULL) 
			AND (nositemap<>1 OR nositemap IS NULL)";
	if ($CHANGES) 
	{
		$nd_upd=time()-3600*24*$CHANGES-3600;
		$query.=" AND nd_update>=$nd_upd";
	}

	if (is_array($INDEX_PAGE)) $query.=" AND id>=$INDEX_PAGE[0] AND id<=$INDEX_PAGE[1]";
	elseif ($INDEX_PAGE) $query.=" AND id=$INDEX_PAGE";


	$result=$adodb->Execute($query);
	if (!$result)
	{
		$adodb->debug=1;
		$adodb->Execute($query);
		return;

	}
	$count=$result->RecordCount();

	for ($i=0;$i<$count;$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		if (strlen($page)>0) $strony[]=$page;
	}
	

	if (is_array($strony)) sort($strony);


	//jesli indeksacja jednokrotna to usuwamy caly indeks i budujemy go od nowa
	$api_warning="";
	if ($u_status==1)
	{
		deleteDesc("",$KEY,$LANG,$VERSION);
		deleteIndex("",$KEY,$LANG,$VERSION);
		$api_warning= "<font color=red>".label("Index was deleted and rebuilded!")."</font><br>\n";
		$CHANGES=0;
	}

	$lp=0;
	$msgPage="";
	for ($i=0;$i<count($strony);$i++)
	{
		if ($strona!=$strony[$i])
		{
			$strona=$strony[$i];
			$ile_slow=index_page($adodb,$strona,$LANG,$VERSION, $KEY, $CHANGES);
			if (!strlen($ile_slow))
			{
				$msgPage.= "$lp. page $strona caused error ($ile_slow words found)<br>\n";
				break;
			}
			$lp++;
			$msgPage.= "$lp. page $strona was successfuly indexed ($ile_slow words found)<br>\n";	
		}
	}

	$t=time()-$t;

	$min=sprintf("%02d",0+round(($t/60)*100)/100);
	$sec=sprintf("%02d",$t % 60);
	$sec_msg="$min:$sec";


	$lang=$LANG;

	$dzis=date("d-m-Y");
	$godz=date("H:i:s");
	$msg= "\n$api_warning\n".label("Site").": $KEY<br>\n".label("Version").": $VERSION<br>\n".label("Language").": ".label($LANG)."<br>\n";
	$msg.=label("Date").", ".label("time").": $dzis, $godz<br>\n".label("Summary").": $sec_msg ".label("seconds").", $totalwords ".label("words").", $lp ".label("pages")." ";
	$msg.="<br>\n".label("Page list").":<br>\n$msgPage";

	$SQL = " UPDATE search_ustawienia SET u_msg='$msg'";
	$SQL.= " WHERE servername='$KEY'";
	$res=$adodb->Execute($SQL);
	echo $msg;
}
$adodb->Close();

?>