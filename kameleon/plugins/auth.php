<?
// AUTORYZACJA U¯YTKOWNIKA

function unauthorize($info)
{
	global $adodb, $SCRIPT_NAME, $QUERY_STRING;

	$path = $SCRIPT_NAME;

	if (strlen($QUERY_STRING))
	{
		$path .= '?' . $QUERY_STRING;
	}
	
	//Komunikat do formularza logowania idzie przez sesje
	$adodb->addToSession('login.info',$info,true);
	$adodb->addToSession('login.path',$path,true);
	$adodb->delSessionVar('login.alreadyLogedIn');

	//Jak nie ma loginu ma sie za³adowaæ formularz logowania
	header("Location: ../../login.php");
	$adodb->Close();
	exit();
}


$query="SELECT count(*) AS c FROM label";
parse_str(ado_query2url($query));

if ($c<200) 
{
	if (file_exists("../changes/label.txt")) $fl=file("../changes/label.txt");

	if (is_array($fl)) for ($i=0;$i<count($fl);$i++)
	{
		$linia=$fl[$i];
		$linia=ereg_replace("\n","",$linia);
		$linia=ereg_replace("\r","",$linia);
		$linia=explode(";",$linia);

		$lab_label=addslashes(trim($linia[1]));
		$lab_value=addslashes(trim($linia[2]));
		$lab_lang=trim($linia[3]);

		$query="INSERT INTO label (label,value,lang)
			SELECT '$lab_label','$lab_value','$lab_lang'
			WHERE 1 NOT IN (SELECT 1 FROM label 
					WHERE label='$lab_label'
					AND lang='$lab_lang')";

		if (!$adodb->Execute($query))
		{
			$adodb->debug=1;
			$adodb->Execute($query);
			$adodb->debug=0;
			break;
		}
		
	}
}


$_auth=explode("@",$_POST['u']);


$query="SELECT count(*) AS c FROM passwd WHERE 1";
parse_str(ado_query2url($query));


if ( $adodb->checkSessionValue('login.alreadyLogedIn') == true )
{
	$_auth[0] = $adodb->getFromSession('login.login');
	$_auth[1] = $adodb->getFromSession('login.server');
}
else if ( empty($_POST['u']) || empty($_POST['p']) )
{
	if ( !$c )
	{
		unauthorize( "Try default"." kameleon:gammanet" );		
	}
	else
	{
		unauthorize( "Enter user and password" );
	}

}

$USERNAME		= $PHP_AUTH_USER		= $_auth[0];
$SERVER			= $_auth[1];
$PHP_AUTH_PW	= $_POST['p'];

if (!$c) 
{
	if ( $adodb->checkSessionValue('login.alreadyLogedIn') !== true) 
	{
		if ($USERNAME!="kameleon" || $PHP_AUTH_PW!="gammanet")
			unauthorize("Try default"." kameleon:gammanet");

		if ( $USERNAME=="kameleon" && $PHP_AUTH_PW=="gammanet") 
		{
			$adodb->addToSession('login.alreadyLogedIn', true, true);
			$adodb->addToSession('login.login',$USERNAME,true);
			$adodb->addToSession('login.server',$SERVER,true);
			$adodb->addToSession('login.phash',md5($PHP_AUTH_PW),true);
		}
	}
}
else
{
	if ($USERNAME == 'kameleon')
	{
		$adodb->delSessionVar('login.alreadyLogedIn');
	}


	$query="SELECT * FROM passwd WHERE username='$USERNAME'";
	parse_str(ado_query2url($query));

	//echo "$PHP_AUTH_PW<br>$password <br>".crypt($PHP_AUTH_PW,$password);

	if ( $adodb->checkSessionValue('login.alreadyLogedIn') !== true  
		|| $adodb->getFromSession('login.phash') != md5($password) )
	{

		if ( $password!=$PHP_AUTH_PW 
			&& $password!=crypt($PHP_AUTH_PW,$password) 
			|| !strlen($password) )
		{
			unauthorize("User and password don't match");
		}
		else
		{
			$adodb->addToSession('login.alreadyLogedIn', true, true);
			$adodb->addToSession('login.login',$USERNAME,true);
			$adodb->addToSession('login.server',$SERVER,true);
			$adodb->addToSession('login.phash',md5($password),true);
		}
	}

	if (strlen($ulang)>0) $kameleon->setlang($ulang);
}

// skóra dla kameleona
if (!strlen($skin)) $skin="kameleon";
$kameleon->user=$KAMELEON;
$kameleon->user[skin]=$skin;

//$REMOTE_USER=$USERNAME;
