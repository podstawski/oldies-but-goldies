<?
	if (!extension_loaded('ionCube Loader'))
	{
		$_ln='/ioncube/ioncube_loader_'.
			strtolower(substr(php_uname(),0,3)).
			'_'.substr(phpversion(),0,3).'.so';

		$_rd=str_repeat('/..',substr_count($_id=realpath(ini_get('extension_dir')),'/')).
			dirname(__FILE__).'/';

		$_i=strlen($_rd);

		while($_i--)
		{
			if($_rd[$_i]=='/')
			{
				$_lp=substr($_rd,0,$_i).$_ln;
				if(file_exists($_id.$_lp))
				{
					$_ln=$_lp;
					break;
				}
			}
		}
		//@dl($_ln);
	}

	if (function_exists('_il_exec'))
	{
		return _il_exec();
	}

	$library = basename($_ln);

	ob_start();
	phpinfo();
	$tmp = ob_get_contents();
	ob_end_clean();

	preg_match_all("/[a-z,A-Z,0-9,\/\_\-\.]+php.ini/", $tmp, $arr);

	$phpini = $arr[0][0];

	$loader = dirname($SCRIPT_FILENAME)."/loaders/$library";


	if (strlen($library))
	{
		$encoded_title =  "webkameleon has been encoded!<br>";
		if (file_exists($loader))
		{
			$encoded_text  = "There is an appropirate loader in loaders subdirectory: $library<br>";
			$encoded_text .= "To run loader please copy line below into Your <i>$phpini</i> file:<br>";
			$encoded_text .= "<b>zend_extension=$loader</b><br>";
			$encoded_text .= "and restart Your web server:<br>";
		}
		else
		{
			$encoded_text = "webkameleon has been encoded with the 
			<a href=\"http://ioncube.com/encoder/\">ionCube PHP Encoder</a>
			and requires the free <a href=\"http://ioncube.com/loader/\">ionCube PHP Loader</a> 
			to be installed<br><br>";
		}
	}

?>

<html>
<head>
    <title>KAMELEON: AUTH</title>
    <link href="kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-2">
</head>
<body bgcolor="#c0c0c0" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>

<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="0">
<tr>
	<td class=k_td>
<?
	echo "<div align=right class=k_text><A href='http://www.gammanet.pl/' 
		style='TEXT-DECORATION: none' target=_blank><FONT 
		color=#02461f face=Arial size=2><B>gamma</B></FONT><FONT 
		color=#fc7116 face=Arial size=2><B>net</B></FONT></A> - Web Kameleon: 
		<b>$KAMELEON_VERSION</b>&nbsp;&nbsp;</div>";
	echo "<div align=right class=k_text><A href='javascript:licence()' 
		style='TEXT-DECORATION: none; font-size:7pt' >Copyright &copy; 2001 - 2002 
		Gammanet Sp. z o.o. All right reserved</A></div>";
?>
	<br>
	<br>
	<table bgcolor="silver" valign=top align="center" border="3" cellspacing="0" cellpadding="5">
	<tr>
		<td class=k_td>
		<b><?echo $encoded_title;?></b>
		</td>
	</tr>
		<tr>
		<td class=k_td>
		<?echo $encoded_text;?>
		<!-- Podane has³o lub u¿ytkownik s± nieprawid³owe!. Spróbuj <a href="index.php">jeszcze</a> raz.<br>
		You have no rights or you typed wrong username and password! Try <a href="index.php">again</a>. -->
		</td>
	</tr>
	</table>
	<br>
	<br>
	
	</td>
</tr>
</table>

</body>
</html>


<?
	exit;
?>



