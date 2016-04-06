<?php
        session_start();
//        echo '<pre>';print_r($_SESSION);die();

        $ch = curl_init();
        $url=$_GET['url'];
	
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);

    
        if (is_array($_POST) && count($_POST))
        {
            $post=http_build_query($_POST);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        }
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        if (isset($_SESSION['curl_session'])) {
	echo '<p>USTAWIAM CIASTKO NA ' . $_SESSION['curl_session'] . '</p>';
            curl_setopt($ch, CURLOPT_COOKIE, $_SESSION['curl_session']);
        }

        $t0=microtime(true);       
        $response = curl_exec($ch);
        //$response=file_get_contents($url);
        $t1=microtime(true);
        
        $delta=$t1-$t0;

        if (!isset($_SESSION['curl_session']) or !$_SESSION['curl_session']) {
            $pattern = "#Set-Cookie: (.*?; path=.*?)\n#";
            preg_match_all($pattern, $response, $matches);
            array_shift($matches);
            $_SESSION['curl_session'] = implode("\n", $matches[0]);
	    echo '<p>DOSTAŁEM CIASTKO POSTACI ' . $_SESSION['curl_session'] . '</p>';
        }
        
        
        Header('Content-type: text/html; charset=utf=8');
        
        echo '<form><input type="text" style="width:90%" name="url" value="'.$url.'" id="_url"/>
                <input type="submit" value="go!"/>
                <input type="button" value="*" onclick="document.getElementById(\'_url\').value=\'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/debug_test.php\'"/>
                <a href="'.$url.'" target="_blank">LINK</a>
                </form>
                '.round(1000*$delta,1).' msek
                <hr size=1/><pre>';
        
        print_r($response);
