<script>
function wykonaj_jq_migracja(klucz)
{
	document.getElementById('aktywacja_lajli').style.display='none';
        preloader_show();
	$.get('<?=$INCLUDE_PATH?>/migracja.php?klucz='+klucz, function(data) {
			document.getElementById('aktywacja_lajli').style.display='block';
			preloader_hide();
		});
}
</script>

<?
	include_once(dirname(__FILE__).'/db.php');
	include_once(dirname(__FILE__).'/yala.php');

	ini_set('display_errors','On');
	ini_set('error_reporting',7);


	foreach ($_REQUEST AS $k=>$v)
	{
		if (is_string($v)) if (strstr($v,"'")) return;
	}

	$id=0;
	$yala=new YALA($db);

	if (strlen($_REQUEST['hash']) )
	{
		$id=$yala->aktywuj($_REQUEST['hash']);
	}

	
	if (strlen($_REQUEST['klucz']) && strlen($_REQUEST['email'])  && strlen($_REQUEST['pass']) )
	{
		$id=$yala->dodaj($_REQUEST['klucz'],$_REQUEST['email'],'',$_REQUEST['pass']);
	}



	if (strlen($_REQUEST['klucz']) && strlen($_REQUEST['social']) && strlen($_REQUEST['social'])==1 && file_exists(dirname(__FILE__).'/openid-'.$_REQUEST['social'].'.php') )
	{
		include (dirname(__FILE__).'/openid-'.$_REQUEST['social'].'.php');
		$social=new social($_SERVER['HTTP_HOST']);
		
		
		$url=$social->getUrl();

		if (strlen($url))
		{
			if (strstr(strtolower($url),'<script'))
			{
				echo $url;
			}
			else
			{
				if (headers_sent())
				{
					echo "<script>location.href='$url';</script>";
				}
				else
				{
					Header('Location: '.$url);
				}
			}
		}
	}


	if (isset($_REQUEST['openid_ns']))
	{

		require_once(dirname(__FILE__).'/openid.php');
		$social = new LightOpenID($_SERVER['HTTP_HOST']);


		if ($social->mode != 'cancel' && $social->validate())
		{
			

			$openid=$social->identity;
			
			$email=$social->data['openid_ext1_value_contact_email']?:$social->data['openid_ax_value_email']?:$social->data['openid_sreg_email'];

			if (strlen($email)) $id=$yala->dodaj($_COOKIE['yala_klucz'],$email,$openid,'');

		}
	}

	if (isset($_GET['code']) || isset($_GET['verified']))
	{
		require_once(dirname(__FILE__).'/openid-f.php');
		$social=new social($_SERVER['HTTP_HOST']);


		if (strlen($_GET['verified'])) die('ela');

		$me=$social->getUrl();
		echo '<pre>'.print_r($me,true).'</pre>';
	}



	if ($id)
	{
		$sql="SELECT * FROM yala WHERE id=$id";
		parse_str(query2url($sql));

	}


?>
