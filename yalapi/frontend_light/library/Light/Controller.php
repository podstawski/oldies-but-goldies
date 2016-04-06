<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
session_start();
require_once(dirname(__FILE__) . '/../GN/LightOpenID.php');
require_once(dirname(__FILE__) . '/../GN/User.php');

class Light_Controller extends Zend_Controller_Action
{
	private $requests;
	private $_yalaOptions;

	public function init()
	{
		$this->requests = array();
		$this->_yalaOptions = $this->getInvokeArg('bootstrap')->getOption('yala');

		if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn'])
		{
			$loggedIn = false;

			if (isset($_SESSION['Zend_Auth']) and isset($_SESSION['Zend_Auth']['storage']))
			{
				$storage = $_SESSION['Zend_Auth']['storage'];
				if (isset($storage->id) and $storage->id)
				{
					//logowanie frontend heavy...
					$hash = $this->_yalaOptions['json_hash'];
					$email = $storage->email;
					$firstName = $storage->first_name;
					$lastName = $storage->last_name;
					$loggedIn = true;
					$source = 'heavy';
				}
			}

			else
			{
				//logowanie frontend light...
				$social = new LightOpenID($_SERVER['HTTP_HOST']);
				if (!isset($social->data['openid_mode']) or !$social->data['openid_mode'] or !$social->validate())
				{
					//przekierowanie do googli, kiedy nie mamy info o userze
					$social->identity = 'https://www.google.com/accounts/o8/id';
					$social->required = array
					(
						'namePerson/first',
						'namePerson/last',
						'contact/email'
					);
					$url = $social->authUrl();
					header('Location: ' . $url);
					die();
				}
				else
				{
					//dostaliśmy info o userze od googli.
					$attributes = $social->getAttributes();
					$hash = $this->_yalaOptions['json_hash'];
					$email = $attributes['contact/email'];
					$firstName = $attributes['namePerson/first'];
					$lastName = $attributes['namePerson/last'];
					$loggedIn = true;
					$source = 'light-google';
				}
			}

			if ($loggedIn)
			{
				//dostaliśmy info potrzebne do zalogowania skądkolwiek.
				$_SESSION['source'] = $source;
				$_SESSION['firstName'] = $firstName;
				$_SESSION['lastName'] = $lastName;
				$_SESSION['email'] = $email;
				$_SESSION['sig'] = GN_User::getSig($email, $hash);
				$_SESSION['loggedIn'] = true;
				$_SESSION['acl'] = $this->makeRequest('/acl');
				header('Location: ' . $this->view->url(array('action' => 'index', 'controller' => 'index'), null, true));
			}
		}
	}

	/**
	 * @param string $url
	 * @param array $postData
	 * @return array
	 */
	protected function makeRequest($url, array $getData = array(), array $postData = array(), $type = false)
	{
		//prepare request
		$ch = curl_init();
		$startTime = microtime(true);
		$realType = $type;

		//prepare url
		$url = $this->_yalaOptions['url'] . $url;
		if (isset($getData['id']))
		{
		    $url .= '/' . $getData['id'];
		    unset($getData['id']);
		}

		//prepare custom headers
		$inputHeaders = array
		(
			'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
			'Connection' => 'close',
		);

		//add session / login information
		$getData['email'] = $_SESSION['email'];
		$getData['sig'] = $_SESSION['sig'];

		//load cookies
		if (isset($_SESSION['cookies']))
		{
			$inputCookies = $_SESSION['cookies'];
			//$inputHeaders['Cookie'] = $inputCookies;
			curl_setopt($ch, CURLOPT_COOKIE, $inputCookies);
			unset ($getData['email']);
			unset ($getData['sig']);
		}
		else
		{
			$inputCookies = null;
		}

		//use POST
		if (!empty($postData))
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
			if ($type === false)
			{
				$realType = 'POST';
			}
		}
		//use GET
		if (!empty($getData))
		{
			$url .= '?' . http_build_query($getData);
		}
		if ($realType === false)
		{
			$realType = 'GET';
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $realType);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $inputHeaders);

		//execute the request
		$midTime = microtime(true);
		$response = curl_exec($ch);
		$endTime = microtime(true);

		//get basic information
		$outputStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$info = curl_getinfo($ch);

		//get output headers
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$outputHeaderLines = explode("\r\n", substr($response, 0, $headerSize));
		array_shift($outputHeaderLines);
		$outputHeaders = array();
		foreach ($outputHeaderLines as $line)
		{
			if ($line == '')
			{
				continue;
			}
			list($key, $value) = explode(':', $line, 2);
			$key = str_replace('- ', '-', ucwords(str_replace('-', '- ', $key)));
			if (isset ($outputHeaders[$key]))
			{
				if (!is_array($outputHeaders[$key]))
				{
					$tmp = $outputHeaders[$key];
					$outputHeaders[$key] = array();
					$outputHeaders[$key] []= $tmp;
				}
				$outputHeaders[$key] []= $value;
			}
			else
			{
				$outputHeaders[$key] = $value;
			}
		}

		//get output body
		$outputRaw = substr($response, $headerSize);
		if ($outputRaw == '')
		{
			$outputJSON = array();
		}
		else
		{
			$outputJSON = json_decode($outputRaw, true);
		}

		//close CURL
		curl_close($ch);

		//save cookies
		$outputCookies = array();
		foreach ($outputHeaderLines as $line)
		{
			if (!preg_match('/Set-Cookie: ?([^;]+);?/i', $line, $matches))
			{
				continue;
			}
			$outputCookies []= $matches[1];
		}
		if (!empty($outputCookies))
		{
			$outputCookies = join('; ', $outputCookies);
			$_SESSION['cookies'] = $outputCookies;
		}
		else
		{
			$outputCookies = null;
		}

		$ret = array
		(
			'type' => $realType,
			'inputCookies' => $inputCookies,
			'outputCookies' => $outputCookies,
			'timePreparation' => $midTime - $startTime,
			'timeExecution' => $endTime - $midTime,
			'curlInfo' => $info,
			'inputURL' => $url,
			'inputPOST' => $postData,
			'inputGET' => $getData,
			'inputHeaders' => $inputHeaders,
			'outputHeaders' => $outputHeaders,
			'outputStatusCode' => $outputStatusCode,
			'outputRaw' => $outputRaw,
			'outputJSON' => $outputJSON
		);

		/*if (!is_array($outputJSON))
		{
			header('Content-Type: text/plain; charset=utf-8');
			echo "Unexpected response format\n";
			print_r($ret);
			die();
		}*/

		$this->view->getHelper('debug')->addRequest($ret);

		return $ret;
	}
}	

?>
