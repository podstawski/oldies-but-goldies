<?php
#http://www.ibm.com/developerworks/xml/library/x-phpgooglecontact/index.html
#https://developers.google.com/google-apps/contacts/v3/#retrieving_all_contact_groups

class ContactsClient {
	private $user;
	private $gclient;
	private $gdata;

	public function __construct(Model_UsersRow $user) {
		$this->user = $user;
		$this->gclient = new GN_GClient($user);
		$client = $this->gclient->getHttpClient();
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);
		$this->gdata = $gdata;
	}



	public function getUser() {
		return $this->user;
	}



	public function createGroup($title) {
		$doc  = new DOMDocument();
		$doc->formatOutput = true;
		$entry = $doc->createElement('atom:entry');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' , 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' , 'xmlns:gd', 'http://schemas.google.com/g/2005');
		$doc->appendChild($entry);

		// add category element
		$category = $doc->createElement('atom:category');
		$category->setAttribute('scheme', 'http://schemas.google.com/g/2005#kind');
		$category->setAttribute('term', 'http://schemas.google.com/contact/2008#group');
		$entry->appendChild($category);

		// add title element
	
		$title=str_replace('&','&amp;',$title);
		$title = $doc->createElement('atom:title', $title);
		$entry->appendChild($title);

		// insert entry
		//var_dump($doc->saveXML());die;
		$entry = $this->gdata->insertEntry($doc->saveXML(), 'https://www.google.com/m8/feeds/groups/' . $this->user->getEmail() . '/full');
		return $this->constructGroup($entry);
	}



	public function createContact($contact) {
		$xml = $contact->realXML;
		$entry = $this->gdata->insertEntry($xml, 'https://www.google.com/m8/feeds/contacts/' . $this->user->getEmail() . '/full');
		return $this->constructContact($entry);
	}



	public function updateContact($contact) {
		$xml = $contact->realXML;
		$entry = $this->gdata->updateEntry($xml, 'https://www.google.com/m8/feeds/contacts/' . $this->user->getEmail() . '/full/' . $contact->id, null, array('If-Match' => '*'));
		return $this->constructContact($entry);
	}



	public function getGroup($id) {
		$query = new Zend_Gdata_Query('https://www.google.com/m8/feeds/groups/' . urlencode($this->user->getEmail()) . '/full/' . $id);
		$entry = $this->gdata->getEntry($query);
		return $this->constructGroup($entry);
	}



	public function getGroupByGroupName($name) {
		$groups = $this->getGroups();
		$id = null;
		foreach ($groups as $group) {
			if ($group->name == $name) {
				$id = $group->id;
			}
		}
		if (!$id) {
			return null;
		}
		return $this->getGroup($id);
	}



	public function getGroups() {
		$query = new Zend_Gdata_Query('https://www.google.com/m8/feeds/groups/default/full?max-results=99999');
		$feed = $this->gdata->getFeed($query);
		return $this->constructGroups($feed);
	}



	public function getContactsByGroupName($name) {
		$groups = $this->getGroups();
		$id = null;
		foreach ($groups as $group) {
			if ($group->name == $name) {
				$id = $group->id;
			}
		}
		if (!$id) {
			return null;
		}
		return $this->getContactsByGroupID($id);
	}



	public function getContactsByGroupID($id, $date = null) {
		$url = 'https://www.google.com/m8/feeds/contacts/' . $this->user->getEmail() . '/full?max-results=99999&group=' . urlencode('https://www.google.com/m8/feeds/groups/' . $this->user->getEmail() . '/base/' . $id);
		if (!empty($date)) {
			$url .= '&updated-min=' . urlencode($date);
		}
		$query = new Zend_Gdata_Query($url);
		$feed = $this->gdata->getFeed($query);
		return $this->constructContacts($feed);
	}



	public function getContacts() {
		$query = new Zend_Gdata_Query('https://www.google.com/m8/feeds/contacts/default/full?max-results=99999');
		$feed = $this->gdata->getFeed($query);
		return $this->constructContacts($feed);
	}



	public function getContact($id) {
		$query = new Zend_Gdata_Query('https://www.google.com/m8/feeds/contacts/default/full/' . $id);
		$entry = $this->gdata->getEntry($query);
		return $this->constructContact($entry);
	}



	public function getContactPhoto($id) {
		$uri = 'https://www.google.com/m8/feeds/photos/media/default/' . $id;
		$requestData = $this->gdata->prepareRequest('GET', $uri, array(), null);
		try {
			$response = $this->gdata->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], $requestData['data'], $requestData['contentType'], null);
		} catch (Exception $e) {
			return null;
		}
		return array($response->getHeader('content-type'), $response->getBody());
	}



	public function putContactPhoto($id, $mime, $photoData) {
		$uri = 'https://www.google.com/m8/feeds/photos/media/default/' . $id;
		$requestData = $this->gdata->prepareRequest('PUT', $uri, array('If-Match' => '*'), $photoData, $mime);
		$response = $this->gdata->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], $requestData['data'], $requestData['contentType'], null);
	}



	public function getContactByContactName($name) {
		$contacts = $this->getContacts();
		$id = null;
		foreach ($contacts as $contact) {
			if ($contact->name == $name) {
				$id = $contact->id;
			}
		}
		if (!$id) {
			return null;
		}
		return $this->getContact($id);
	}



	protected function constructGroup($entry) {
		$obj = new stdClass;
		$obj->id = (string) $entry->id;
		$obj->id = substr($obj->id, strrpos($obj->id, '/') + 1);
		$obj->name = (string) $entry->title;
		$obj->link = (string) $entry->getLink('self')->getHref();
		$obj->updated = strtotime((string) $entry->updated);
		$obj->isSystem = 0;
		foreach ($entry->extensionElements as $extension) {
			if ($extension->rootElement == 'systemGroup') {
				$obj->isSystem = 1;
			}
		}
		return $obj;
	}



	protected function constructGroups($feed) {
		$results = array();
		foreach ($feed as $entry) {
			$group =  self::constructGroup($entry);
			if (!$group->isSystem) {
				$results []= $group;
			}
		}
		return $results;
	}



	protected function constructContact($entry) {
		$xml = simplexml_load_string($entry->getXML());
		$obj = new stdClass;
		$obj->realXML = $entry->getXML();
		$obj->realEntry = $entry;
		$obj->id = (string) $entry->id;
		$obj->id = substr($obj->id, strrpos($obj->id, '/') + 1);
		$obj->link = (string) $entry->getLink('self')->getHref();
		$obj->name = (string) $entry->title;
		$obj->orgName = (string) $xml->organization->orgName;
		$obj->orgTitle = (string) $xml->organization->orgTitle;
		$obj->emailAddress = array();
		$obj->updated = strtotime($entry->updated->text);
		foreach ($xml->email as $e) {
			$obj->emailAddress[] = (string) $e['address'];
		}
		$obj->phoneNumber = array();
		foreach ($xml->phoneNumber as $p) {
			$obj->phoneNumber[] = (string) $p;
		}
		$obj->website = array();
		foreach ($xml->website as $w) {
			$obj->website[] = (string) $w['href'];
		}
		return $obj;
	}



	protected function constructContacts($feed) {
		$results = array();
		foreach ($feed as $entry) {
			$results []= self::constructContact($entry);
		}
		return $results;
	}
}


class UserIMAP {
	public static function fromUserIMAP(UserIMAP $imap) {
		return new UserIMAP($imap->getUser());
	}

	private $imap;
	private $user;

	public function __construct(Model_UsersRow $user) {
		$options = Zend_Registry::get('oauth_options');
		$this->user = $user;

		$marketplace = $user->getDomain()->marketplace;

		// Retrieve mail using Access Token
		$config = new Zend_Oauth_Config();
		if ($marketplace) {
			$vendor = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('googlevendor');
			if ((bool) $vendor['enabled'] == false) {
				throw new GN_GClient_Exception('cannot use 2-legged oauth, googlevendor is not enabled');
			}
			$options['consumerKey']    = $vendor['consumerKey'];
			$options['consumerSecret'] = $vendor['consumerSecret'];
			$config->setToken(new Zend_Oauth_Token_Access());
		} else {
			if (!$user->getAccessToken()) {
				throw new CRM_EmptyTokenException($user);
			}
			$config->setToken($user->getAccessToken());
		}
		$config->setOptions($options);
		$config->setRequestMethod('GET');
		$url = 'https://mail.google.com/mail/b/' .
			$user->getEmail() .
			'/imap/';
		if ($marketplace) {
			$urlWithXoauth = $url . '?xoauth_requestor_id=' . urlencode($user->getEmail());
		}

		$httpUtility = new Zend_Oauth_Http_Utility();

		/**
		 * Get an unsorted array of oauth params,
		 * including the signature based off those params.
		 */
		if ($marketplace) {
			$params = $httpUtility->assembleParams($url, $config, array('xoauth_requestor_id' => $user->getEmail()));
		} else {
			$params = $httpUtility->assembleParams($url, $config);
		}

		/**
		 * Sort parameters based on their names, as required
		 * by OAuth.
		 */
		ksort($params);

		/**
		 * Construct a comma-deliminated,ordered,quoted list of
		 * OAuth params as required by XOAUTH.
		 *
		 * Example: oauth_param1="foo",oauth_param2="bar"
		 */
		$first = true;
		$oauthParams = '';
		foreach ($params as $key => $value) {
			// only include standard oauth params
			if (strpos($key, 'oauth_') === 0) {
				if (!$first) {
					$oauthParams .= ',';
				}
				$oauthParams .= $key . '="' . urlencode($value) . '"';
				$first = false;
			}
		}

		/**
		 * Generate SASL client request, using base64 encoded
		 * OAuth params
		 */
		if ($marketplace) {
			$initClientRequest = 'GET ' . $urlWithXoauth . ' ' . $oauthParams;
		} else {
			$initClientRequest = 'GET ' . $url . ' ' . $oauthParams;
		}
		$initClientRequestEncoded = base64_encode($initClientRequest);

		/**
		 * Make the IMAP connection and send the auth request
		 */
		$imap = new Zend_Mail_Protocol_Imap('imap.gmail.com', 993, 'SSL');
		$authenticateParams = array('XOAUTH', $initClientRequestEncoded);
		$response = $imap->requestAndResponse('AUTHENTICATE', $authenticateParams);
		$this->imap = $imap;
		if (!$response) {
			throw new CRM_IMAPException('Authorization error');
		}
	}

	public function getIMAP() {
		return $this->imap;
	}

	public function getUser() {
		return $this->user;
	}
}


class CRM_Core
{
	public static function renamePath(UserIMAP $imap, $oldPath, $newPath) {
		if (strpos($newPath, '/') !== false) {
			self::createPath($imap, dirname($newPath));
		}
		#error_log('Renaming ' . $oldPath . ' to ' . $newPath);
		$imapClone = UserIMAP::fromUserIMAP($imap);
		$storage = new Zend_Mail_Storage_Imap($imapClone->getIMAP());
		$storage->renameFolder($oldPath, $newPath);
	}

	public static function strtolower($txt) {
		return self::imapEnc(mb_convert_case(self::imapDec($txt), MB_CASE_LOWER));
	}

	public static function imapDec($str) {
		return mb_convert_encoding($str, 'utf-8', 'utf7-imap');
	}

	public static function imapEnc($str) {
		return mb_convert_encoding($str, 'utf7-imap', 'utf-8');
	}



	public static function addAutocompleteEmail($user, $email) {
		$email = strtolower($email);
		$emails = self::getCachedAutocomplete($user);
		$emails[$email] = array('e-mail' => $email, 'src' => 'manual');
		GN_SessionCache::set('autocomplete', $emails);
		return $emails;
	}

	public static function getCachedAutocomplete($user) {
		if (GN_SessionCache::isFresh('autocomplete')) {
			return GN_SessionCache::get('autocomplete');
		}

		$emails = array();

		//dodaj maile z kontaktów
		$cclient = CRM_Core::getContactsClient($user);
		foreach ($cclient->getContacts() as $contact) {
			foreach ($contact->emailAddress as $email) {
				//filtruj gmail, google i domenę usera
				list ($uname, $domain) = explode('@', $email);
				if (strtolower($domain) != strtolower($user->getDomain()->domain_name) and strtolower($domain) != 'gmail.com' and strtolower($domain) != 'google.com') {
					continue;
				}
				$emails[$email] = array('e-mail' => $email, 'src' => 'contacts');
			}
		}

		//dodaj maile z którymi user jest zlabelowany
		$modelLabels = new Model_Labels();
		$labels = $modelLabels->getByUserID($user->id);
		foreach ($labels as $label) {
			foreach ($label->getUserLabels() as $userLabel) {
				$email = $userLabel->getUser()->email;
				$emails[$email] = array('e-mail' => $email, 'src' => 'db');
			}
		}
		$modelContactGroups = new Model_ContactGroups();
		$contactGroups = $modelContactGroups->getByUserID($user->id);
		foreach ($contactGroups as $contactGroup) {
			foreach ($contactGroup->getUserContactGroups() as $userContactGroup) {
				$email = $userContactGroup->getUser()->email;
				$emails[$email] = array('e-mail' => $email, 'src' => 'db');
			}
		}

		//dodaj maile z domeny
		try {
			$client = new GN_GClient($user, GN_GClient::MODE_DOMAIN);
			foreach ($client->retrieveAllUsers() as $userEntry) {
				if ($userEntry->login->suspended) {
					continue;
				}
				$email = $userEntry->login->username;
				if (strpos($email, '@') === false) {
					$email .= '@' . $user->getDomain()->domain_name;
				}
				$emails[$email] = array('e-mail' => $email, 'src' => 'domain');
			}
		} catch (Exception $e) {
			#GN_Debug::debug($e->getMessage());
		}

		//usuń siebie
		$emails = array_filter($emails, function($e) use ($user) { return $e['e-mail'] != $user->email; });

		//posortuj
		if (!empty($emails)) {
			uasort($emails, function($a, $b) {
				return strcmp($a['e-mail'], $b['e-mail']);
			});
		}

		GN_SessionCache::set('autocomplete', $emails, 86400);
		return $emails;
	}

	public  static function getCachedContactGroups($user) {
		if (GN_SessionCache::isFresh('contacts' . $user->id)) {
			$results = GN_SessionCache::get('contacts' . $user->id);
		} else {
			$modelContactGroups = new Model_ContactGroups();
			$modelUserContactGroups = new Model_UserContactGroups();

			$cclient = CRM_Core::getContactsClient($user);
			$results = array();
			foreach ($cclient->getGroups() as $result) {
				$key = $result->name;
				$row = null;
				$row = $modelUserContactGroups->getByGoogleGroupNameAndUserID($result->name, $user->id);
				$results[$key] = array($row, $result);
				
				if ($user->disabled) {
					$user->disabled=null;
					$user->save();
				}				
				
			}

			foreach ($modelUserContactGroups->getByUserID($user->id) as $userContactGroup) {
				$key = $userContactGroup->getName();
				if (!isset($results[$key])) {
					$results[$key] = array($userContactGroup, null);
				} else {
					$results[$key][0] = $userContactGroup;
				}
			}

			if (!empty($results)) {
				usort($results, function($a, $b) {
					list($la, $fa) = $a;
					list($lb, $fb) = $b;
					$ca = $la ? $la->getName() : $fa->name;
					$cb = $lb ? $lb->getName() : $fb->name;
					return strcmp($ca, $cb);
				});
			}

			GN_SessionCache::set('contacts' . $user->id, $results, 300);
		}
		return $results;
	}

	public static function getCachedImapFolders($user) {
		if (GN_SessionCache::isFresh('folders' . $user->id)) {
			$results = GN_SessionCache::get('folders' . $user->id);
		} else {
			$imap = self::getIMAP($user)->getIMAP();
			$storage = new Zend_Mail_Storage_Imap($imap);
			$imapFolders = $storage->getFolders();

			$modelLabels = new Model_Labels();
			$modelUserLabels = new Model_UserLabels();

			$results = array();
			$iterator = new RecursiveIteratorIterator($imapFolders, RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $localName => $folder) {
				$key = $folder->getGlobalName();
				if (substr($folder, 0, 7) == '[Gmail]') {
					continue;
				}
				if ($folder == 'INBOX') {
					continue;
				}
				$userLabel = $modelUserLabels->getByNameAndUserID($folder, $user->id);
				$results[$key] = array($userLabel, $folder);
				
				if ($user->disabled) {
					$user->disabled=null;
					$user->save();
				}
			}

			foreach ($modelUserLabels->getByUserID($user->id) as $userLabel) {
				$key = $userLabel->getName();
				if (!isset($results[$key])) {
					$results[$key] = array($userLabel, null);
				} else {
					$results[$key][0] = $userLabel;
				}
			}

			if (!empty($results)) {
				usort($results, function($a, $b) {
					list($la, $fa) = $a;
					list($lb, $fb) = $b;
					$ca = $la ? $la->getName() : $fa->getGlobalName();
					$cb = $lb ? $lb->getName() : $fb->getGlobalName();
					return strcmp($ca, $cb);
				});
			}

			GN_SessionCache::set('folders' . $user->id, $results, 300);
		}
		return $results;
	}



	public static function selectCreatedPath($storage, $path) {
		$folders = new RecursiveIteratorIterator($storage->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
		$all=array();
		foreach ($folders AS $folder)
		{
			$all[self::strtolower($folder->getGlobalName())]=$folder->getGlobalName();
		}

		if (isset($all[self::strtolower($path)]))
		{
			$storage->selectFolder($all[self::strtolower($path)]);
			return $all[self::strtolower($path)];
		}

		try {
			$dir=dirname($path);
			if ($dir=='.' || empty($dir)) $dir=null;
			else $storage->selectFolder($dir);
			$storage->createFolder(basename($path),$dir);

			$ret=$path;
			return $path;
		}
		catch (Exception $e)
		{
			$dir=self::selectCreatedPath($storage,$dir);
			$storage->createFolder(basename($path),$dir);

			$ret=$dir.'/'.basename($path);
		}

		$storage->selectFolder($ret);
		return $ret;
	}

	public static function getIMAP(Model_UsersRow $user) {
		return new UserImap($user);
	}

	public static function getContactsClient(Model_UsersRow $user) {
		return new ContactsClient($user);
	}

	public static function parsePhone($str) {
		return preg_replace('/^0+/', '', preg_replace('/[^0-9]/', '', $str));
	}

	public static function parseEmail($str) {
		$str = str_replace(' ', '', trim(strtolower($str)));
		if (!preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i', $str)) {
			return false;
		}
		return $str;
	}

	public static function getNowUTC() {
		$old = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$ret = date('c');
		date_default_timezone_set($old);
		return $ret;
	}

	public static function getGoogleContactIdent($googleContact) {
		$emails = array();
		$phones = array();

		$xml = simplexml_load_string($googleContact->realXML);
		foreach ($xml->email as $e) {
			$emails []= CRM_Core::parseEmail((string) $e['address']);
		}
		foreach ($xml->phoneNumber as $p) {
			$phones []= CRM_Core::parsePhone((string) $p);
		}
		/*$emails = $googleContact->emailAddress;
		if (!is_array($emails)) {
			$emails = array($emails);
		}
		$emails = array_map(function($str) { return CRM_Core::parseEmail($str); }, $emails);

		$phones = $googleContact->phoneNumber;
		if (!is_array($phones)) {
			$phones = array($phones);
		}
		$phones = array_map(function($str) { return CRM_Core::parsePhone($str); }, $phones);*/

		$ident = array_merge($emails, $phones);
		$ident = array_map(function($str) { return md5($str); }, $ident);
		return $ident;
	}
}
