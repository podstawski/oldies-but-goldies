<?php
define ('ADODB_DIR','adodb/');
include_once("include/request.h");

if (file_exists('const.php')) include_once('const.php'); else include_once('const.h');

include_once("include/adodb.h");
include_once('include/kameleon.h');
include_once('include/const.h');
include_once("include/request.h");



if (file_exists('include/rev.h') ) 
{
	include('include/rev.h');
	$adodb->SetCookie("KAMELEON_VERSION_REV",$KAMELEON_VERSION_REV);
}


$lang = ( substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2) == 'pl' ) ? 'pl' : 'en';
$CHARSET = 'utf-8';

$kameleon->lang =$lang;
$kameleon->charset = $CHARSET;



if (  $adodb->checkSessionValue('login.path') == true ) 
{
	$path = $adodb->getFromSession('login.path');
}
else
{
	$path = 'index.php';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
  <head>
    <title>Webkameleon Login</title>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="<?echo $ROOT?>/favicon.ico" />
    <link type="text/css" rel=stylesheet href="<?echo $CONST_SKINS_DIR?>/kameleon/login.css" />
    <?php
    	include_js('common');
    	include_js('cookies');
    	include_js("jquery-1.4");
      include_js("jquery-ui.min");
    ?>
    <script type="text/javascript">
    <!--
    
    function checkCookie()
    {
    	if ( noCookies )
    	{
    		style_hide('form1');
    		style_show('noCookies');
    	}
    	
    }
    
    //  ------ check form ------
    function sprawdzForm()
    {
    	var f1 = document.getElementById('form1');
    	var wm = " <?php echo $kameleon->label('Fill the form corectly'); ?>:\n\n";
    	var blad = false;
    
    	// --- entered_login ---
    	var t1 = f1.u;
    	if (t1.value == "" || t1.value == " ") {
    		wm += "- <?php echo $kameleon->label('enter username'); ?>\n";
    		blad = true;
    	}
    
    	// --- entered_password ---
    	var t1 = f1.p;
    	if (t1.value == "" || t1.value == " ") {
    		wm += "- <?php echo $kameleon->label('enter password'); ?>\n";
    		blad = true;
    	}
    
    	// --- check if errors occurred ---
    	if (blad == true) {
    		alert(wm);
    		return false;
    	}
    	else return true;
    }
    
    function form1Submit()
    {
    	if (sprawdzForm() == true) {
    		document.form1.submit();
    	} else {
    		return false;
    	}
    }
    //-->
    </script> 
  </head>
  <body onload="checkCookie()">
    <div class="loginbox" id="loginbox">
	  <?php if (strlen($adodb->getFromSession('login.info'))):?>
      <div class="error">
        <?php echo label($adodb->getFromSession('login.info')); ?>
      </div>
	  <?php endif ?>
      <div class="error" id="noCookies" style="display:none;">
        <?php echo label('To use WebKameleon you need to enable Cookies!'); ?>
      </div>
      <div class="in">
        <form method="post" id="form1" name="form1" onsubmit="return form1Submit();" action="<?php echo $path; ?>" style="display:none;">
        <ul>
          <li class="f"><label for="login"><?php echo $kameleon->label('Login'); ?>:</label><input id="login" type="text" name="u" value="<?php echo $_COOKIE[wku]?>" /></li>
          <li class="f"><label for="pass"><?php echo $kameleon->label('Password'); ?>:</label><input type="password" name="p" autocomplete="off" value="<?php if (strlen($_COOKIE[wkp])>1) echo $_COOKIE[wkp]?>" /></li>
          <li class="b">
            <input type="image" name="submit" alt="Login" src="<?echo $CONST_SKINS_DIR?>/kameleon/img/km_loginpage_sbt.png" />
            <div class="checker">
              <input type="checkbox" value="1" name="r" <?php if (strlen($_COOKIE[wkp])>1) echo 'checked'?> /> <?php echo $kameleon->label('Remember user and password'); ?>
            </div>
          </li>
	  <li class="c">
      	<label>Zaloguj się używając:</label>
		<a class="gmail" href="social.php?social=g">Gmail</a>
		<a class="yahoo" href="social.php?social=y">Yahoo</a>
		<a class="myopenid" href="social.php?social=o">MyOpenid</a>
		<a class="wp" href="social.php?social=w">WP</a>
	  </li>
        </ul>
        </form>
      </div>
      
    </div>
    <script language="Javascript">
    	a = null;
    	function closePage() {
    		a.close();
    	}
    	function testujStroneSP2()
    	{
    		gdziex=screen.width+1000;
    		a=window.open('','test','top=100,width=100,left='+gdziex+',height=100');
    		if (a==null)
    		{
    			alert('<?echo label("WebKameleon requires POP-UPs enabled. Please check the browser configuration !")?>');
    		}
    		else 
    		{
    			setTimeout(closePage, 0);
    		}
    		document.form1.style.display='';
    		document.form1.u.focus();
    	}
    	setTimeout(testujStroneSP2,300);
    	
    	jQueryKam(function() {
        jQueryKam("#loginbox").draggable();
        jQueryKam("#loginbox").css('top',(jQueryKam(window).height()/2-120)+'px');
        jQueryKam("#loginbox").css('left',(jQueryKam(window).width()/2-205)+'px');
      }); 
    </script>  
  </body>
</html>
<?php
	$adodb->clear_sessions($C_AUTLOGOUT);
	$adodb->Close($sysinfo,$persistant_connection);	
?>
