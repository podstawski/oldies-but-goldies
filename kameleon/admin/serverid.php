<?
//autor: Robert
//data: 04-01-2002
//modyfikacja: 18-08-2003, kompatybilnoœæ z kameleon 4.04
//modyfikacja: 21-10-2002
//ver 3.27

include ("../const.h");
	$CONST_LICENSE_NAME="";
	$CONST_LICENSE_VALID="";
	$CONST_LICENSE_SERVERS=0;
	$CONST_LICENSE_SECRET="klj7G#h^*de@#^*jhjf";
	if (strlen($CONST_LICENSE_KEY))
	{
		$_md5=$md5=substr($CONST_LICENSE_KEY,0,strpos($CONST_LICENSE_KEY,"g"));
		$_rest=substr($CONST_LICENSE_KEY,strpos($CONST_LICENSE_KEY,"g")+1);
		$_rmd5=md5($_rest.$CONST_LICENSE_SECRET);
		if ($_md5==$_rmd5)
		{
			$_rest=base64_decode($_rest);
			$CONST_LICENSE_NAME=substr($_rest,strpos($_rest,":")+1);
			$pos=strpos($CONST_LICENSE_NAME,":");
			if ($pos)
			{
				$CONST_LICENSE_HOST=substr($CONST_LICENSE_NAME,$pos+1);
				$CONST_LICENSE_NAME=substr($CONST_LICENSE_NAME,0,$pos);
			}

			$unix=substr($_rest,0,strpos($_rest,":"))+0;
			$CONST_LICENSE_VALID=date("20y-m-d",$unix);
			$d=$CONST_LICENSE_VALID;
			$CONST_LICENSE_SERVERS=$unix-mktime(1,0,0,substr($d,5,2),substr($d,8,2),substr($d,0,4));
			if (time()>$unix) $CONST_LICENSE_INVALID=1;
		}
	
		unset($_md5); unset($_rest); unset($_rmd5);
	}
	unset($CONST_LICENSE_SECRET);
	unset($CONST_LICENSE_KEY);

define ('ADODB_DIR',"../adodb/");
include ("../include/adodb.h");
include ("../include/fun.h");

$dbapi=$db;

include("../include/kameleon.h");
include ("../include/const.h");

include("include/auth.h");
include("include/update.h");


$id=-1;
if (!strlen($nazwa)) return $id;
if (strlen($error)) return $id;


//to jest niewykorzystywane
if ($command=="getid")
{
	$query = " SELECT id from servers WHERE nazwa='$nazwa'";
	parse_str(query2url($query));
	echo $id;
}
else
	if ($command=="getuser")
	{
		$username="";
		$query = " SELECT username FROM passwd WHERE username='$nazwa'";
		parse_str(query2url($query));
		if (strlen($username))
			echo $username;
		else
			echo $id;
	}
//koniec tego co niewykorzystane

?>
