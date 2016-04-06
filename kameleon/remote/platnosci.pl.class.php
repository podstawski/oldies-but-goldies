<?

	class PLATNOSCI_PL
	{
		var $pos_id,$key1,$key2;
		var $curl;
		var $encoding;
		var $url='https://www.platnosci.pl/paygw';
		
		function PLATNOSCI_PL($pos_id,$k1,$k2,$encoding,$path_to_curl=false)
		{
			if (strlen($path_to_curl)) $this->curl=$path_to_curl;
			elseif (function_exists('curl_init')) $this->curl=1;

			$this->pos_id=$pos_id;
			$this->key1=$k1;
			$this->key2=$k2;

			$this->encoding=$encoding;

		}

		function _post($url,$vars)
		{
			//$url="https://www.tui.pl/test.php";

			foreach ($vars AS $k=>$v)
			{
				if (strlen($parameters)) $parameters.='&';
				$parameters.=$k.'='.urlencode($v);
			}

			if ($this->curl==1)
			{

				$ch=curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_TIMEOUT, 20);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded"));
				curl_setopt($ch, CURLOPT_POST, 1);
				
				curl_setopt($ch, CURLOPT_POSTFIELDS,$parameters);

				ob_start();
				curl_exec($ch);
				if (!curl_errno($ch)) $wynik=ob_get_contents();
				ob_end_clean();
				curl_close($ch);
			}

			if (is_executable($this->curl))
			{
				$cmd=$this->curl.' -d "'.$parameters.'" '.$url;
				$prg=popen($cmd,'r');
				$wynik = '';
				while (!@feof($prg)) $wynik .= @fgets($prg, 1024);
				@pclose($prg);
			}

			if (!$this->curl)
			{
				$server=eregi_replace("https://([^/]+)/.*","\\1",$url);
				$server_script=eregi_replace("https://[^/]+(/.*)","\\1",$url);
				echo $server;
				$fp = @fsockopen('ssl://' . $server, 443, $errno, $errstr, 30);
				if (!$fp) return;

				$header = 'POST ' . $server_script . ' HTTP/1.0' . "\r\n" . 'Host: ' . $server . "\r\n" .'Content-Type: application/x-www-form-urlencoded' . "\r\n" .'Content-Length: ' . strlen($parameters) . "\r\n" .'Connection: close' . "\r\n\r\n";

				@fputs($fp, $header . $parameters);
				$wynik = '';
				while (!@feof($fp)) $wynik .= @fgets($fp, 1024);
				@fclose($fp);


			}
			
			
			return $wynik;
		}


		function get($session_id)
		{
			$vars['pos_id']=$this->pos_id;
			$vars['session_id']=$session_id;
			$ts=time();

			$vars['ts']=$ts;
			$vars['sig']=md5( $this->pos_id . $session_id . $ts . $this->key1 );

			$url=$this->url.'/'.$this->encoding.'/Payment/get/txt';
			$wynik=$this->_post($url,$vars);
			
		

			foreach (explode("\n",$wynik) AS $line)
			{
				$pos=strpos($line,':');
				if ($pos) $ret[substr($line,0,$pos)]=trim(substr($line,$pos+1));
			}

			return ($ret);			
		}

		function verify($_REQUEST)
		{
			$sig=md5($_REQUEST[pos_id] . $_REQUEST[session_id] . $_REQUEST[ts] . $this->key2);
			return ($_REQUEST[sig]==$sig);
		}

	}


?>