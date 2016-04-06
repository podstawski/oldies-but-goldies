<?php

    function unauthorize($info=null)
    {
        $_REQUEST['login_error']=$info;
    }

    if (isset($_REQUEST['openid_ns']))
    {
        require_once(dirname(__FILE__).'/include/class/openid.php');
        $social = new LightOpenID($_SERVER['HTTP_HOST']);


        if ($social->mode != 'cancel' && $social->validate())
        {
            $openid=$social->identity;
            $attributes=$social->getAttributes();
	    
            //echo '<pre>'.print_r($social,true).print_r($attributes,true); die('<pre>'.print_r($social->data,1));
	    
	    
            $email=$attributes['contact/email'];
            $firstName=$attributes['namePerson/first'];
            $lastName=$attributes['namePerson/last'];
            $login=substr(md5(strtolower($email)),0,16);
            
            define ('ADODB_DIR','adodb/');
            include_once("include/request.h");
            
            if (file_exists('const.php')) include_once('const.php'); else include_once('const.h');
            
            include_once("include/adodb.h");
            include_once('include/kameleon.h');
            include_once('include/const.h');
            


	    
	    $local_login='';
	    $local_pass='!';
	    
            if ($AUTH_BY_ACL_PLUGIN)
            {
                $sql="SELECT au_login AS local_login,au_pass AS local_pass FROM acl_user WHERE au_login='$login' OR au_email='$email'";
                parse_str(ado_query2url($sql));
                if (!strlen($local_login))
                {
                    $sql="INSERT INTO acl_user (au_login,au_pass,au_name,au_email) VALUES ('$login','!','$firstName $lastName','$email')";
                    $adodb->execute($sql);
                }
            }
            else
            {
                $sql="SELECT username AS local_login,password AS local_pass FROM passwd WHERE username='$login' OR email='$email'";
                parse_str(ado_query2url($sql));
                
                
                if (!strlen($local_login))
                {
                    $sql="SELECT id AS gid FROM groups WHERE groupname='openid'";
                    parse_str(ado_query2url($sql));
                    if (!$gid)
                    {
                        $sql="INSERT INTO groups (groupname) VALUES ('openid'); $sql";
                        parse_str(ado_query2url($sql));
                    }

                    $sql="INSERT INTO passwd (groupid,username,password,fullname,email) VALUES ($gid,'$login','!','$firstName $lastName','$email')";
                    $adodb->execute($sql);
                }                
            }
	    
            $adodb->addToSession('login.alreadyLogedIn', true, true);
            $adodb->addToSession('login.login',$login,true);
            $adodb->addToSession('login.phash',md5($local_pass),true);	    
	    
	    if (strlen($local_login)) $login=$local_login;
	    
	    
            $_POST['u']=$login;
            $_POST['p']=($local_pass=='!')?'*':$local_pass;
            
	    
	    
            $KAMELEON_MODE=1;
            include_once('include/auth.h');
     
            if (!isset($_REQUEST['login_error']))
            {
		$sql="SELECT lang FROM servers WHERE id=".($SERVER_ID+0);
		parse_str(ado_query2url($sql));
		
		if (strlen($lang)) $adodb->SetCookie("lang",$lang);
		
                $adodb->Close($sysinfo,$persistant_connection);
                if (is_object($auth_acl)) $auth_acl->close();
                header('Location: '.$adodb->session['login.path']);
            }
            
        }
        
        unset($_REQUEST['social']);
    }

    if (strlen($_REQUEST['social'])==1 && file_exists(dirname(__FILE__).'/include/class/openid-'.$_REQUEST['social'].'.php') )
    {
        include (dirname(__FILE__).'/include/class/openid-'.$_REQUEST['social'].'.php');
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
                    Header('Location: '.$url);
                }
        }    
    
        die();
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
  <head>
    <title>Webkameleon Social Login</title>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8" />
    <link type="text/css" rel=stylesheet href="<?echo $CONST_SKINS_DIR?>/kameleon/login.css" />
    <?php
	include_once('include/kameleon.h');
    	include_js('common');
    	include_js('cookies');
    	include_js("jquery-1.4");
	include_js("jquery-ui.min");
    ?>
  </head>
  
  <body>
    
    <div class="loginbox" id="loginbox">
    	<div class="logowanie_social">
        	<div class="komunikat">
        	Proszę czekać, aż administrator przypisze odpowiednie prawa dostępu.
            </div>
        </div> 
    </div>
    
    <pre><?=$_REQUEST['login_error']?></pre>
        <script language="Javascript">
    	a = null;
    	
    	jQueryKam(function() {
        jQueryKam("#loginbox").draggable();
        jQueryKam("#loginbox").css('top',(jQueryKam(window).height()/2-120)+'px');
        jQueryKam("#loginbox").css('left',(jQueryKam(window).width()/2-205)+'px');
      }); 
    </script>  
  </body>
  
</html>
