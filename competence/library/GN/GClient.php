<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_GClient extends Zend_Gdata_Gapps
{
    /**
     * @var GN_Model_DomainRow
     */
    protected $_domainRow;

    /**
     * @var bool
     */
    protected $_twoLegged;

    /**
     * @var GN_Model_IDomainUser
     */
    protected $_domainUser;

	protected $_mode;

	const MODE_AUTO = null;
	const MODE_DOMAIN = 1;
	const MODE_DOMAIN_TWO_LEGGED = 2;
	const MODE_DOMAIN_THREE_LEGGED = 3;
	const MODE_USER = 4;

    /**
     * @param GN_Model_IDomainUser $user
     * @param bool $twoLegged
     */
    public function __construct(GN_Model_IDomainUser $user, $mode = self::MODE_AUTO, $nonBlocking = false) {
		$domain  = $user->getDomain();
		$options = Zend_Registry::get('oauth_options');

		if ($mode == self::MODE_AUTO) {
			$mode = self::MODE_DOMAIN;
			if (in_array('getAccessToken', get_class_methods($user))) {
				$token = $user->getAccessToken();
				if (!empty($token)) {
					$mode = self::MODE_USER;
				}
			}
		}

		if ($mode == self::MODE_DOMAIN) {
			if ($domain->marketplace) {
				$mode = self::MODE_DOMAIN_TWO_LEGGED;
			} else {
				$mode = self::MODE_DOMAIN_THREE_LEGGED;
			}
		}

		if ($mode == self::MODE_USER) {
			$token = $user->getAccessToken();
		} elseif ($mode == self::MODE_DOMAIN_THREE_LEGGED) {
			$token = $domain->getAccessToken();
		} elseif ($mode == self::MODE_DOMAIN_TWO_LEGGED) {
			$vendor = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('googlevendor');

			if ((bool) $vendor['enabled'] == false) {
				throw new GN_GClient_Exception('cannot use 2-legged oauth, googlevendor is not enabled');
			}

			$options['consumerKey']    = $vendor['consumerKey'];
			$options['consumerSecret'] = $vendor['consumerSecret'];

			$token = new Zend_Oauth_Token_Access();

		}

	    $httpClient = new GN_GClient_HttpClient($options);
	    if (isset($token) and empty($token)) {
		    throw new GN_GClient_EmptyTokenException($user);
	    }
		
	    $httpClient->setToken($token);
	    $httpClient->setRequestor($user);
	    $httpClient->setTwoLegged($mode == self::MODE_DOMAIN_TWO_LEGGED);

	    if ($nonBlocking) {
		    $httpClient->setNonBlocking(true);
	    }

	    parent::__construct($httpClient, $domain->domain_name);

	    $this->_domainRow  = $domain;
	    $this->_domainUser = $user;
	    $this->_mode       = $mode;
	    $this->_twoLegged  = $mode == self::MODE_DOMAIN_TWO_LEGGED;
    }

	    public function setNonBlocking($nb=true)
	    {
			//$this->_httpClient=clone($this->_httpClient);
			$this->_httpClient->setNonBlocking($nb);
	    }
    
    
	public function getMode() {
		return $this->_mode;
	}

    /**
     * @return GN_Model_IDomainUser
     */
    public function getUser()
    {
        return $this->_domainUser;
    }

    /**
     * @return bool
     */
    public function isTwoLegged()
    {
        return $this->_twoLegged;
    }

    /**
     * @param Zend_Gdata_Spreadsheets_SpreadsheetEntry|Zend_Gdata_Spreadsheets_WorksheetEntry $document
     * @return string
     * @throws GN_GClient_Exception
     */
    public static function getDocumentID($document)
    {
        if ($document instanceof Zend_Gdata_Spreadsheets_SpreadsheetEntry) {
            $tmp = $document->getLink('alternate')->getHref();
            list (, $tmp) = explode('key=', $tmp);
		if ($pos=strpos($tmp,'&')) $tmp=substr($tmp,0,$pos);
            return $tmp;
        }
        if ($document instanceof Zend_Gdata_Spreadsheets_WorksheetEntry) {
            $tmp = explode('/', (string) $document->getId());
            $tmp = end($tmp);
            return $tmp;
        }
	if ($document instanceof Zend_Gdata_Docs_DocumentListEntry) {
			$tmp = explode('/', (string) $document->getId());
			$tmp = end($tmp);
			return $tmp;
	}
	if (in_array('getId', get_class_methods($document))) {
			$tmp = explode('/', (string) $document->getId());
			$tmp = end($tmp);
			return $tmp;
	}
        if (Zend_Uri::check($document) == false) {
            throw new GN_GClient_Exception('invalid document');
        }
        return null;
    }

	public static function getDocumentLink($document)
	{
        if ($document instanceof Zend_Gdata_Spreadsheets_SpreadsheetEntry) {
            $tmp = $document->getLink('alternate')->getHref();
            return $tmp;
        }
        if ($document instanceof Zend_Gdata_Spreadsheets_WorksheetEntry) {
            $tmp = $document->getLink('alternate')->getHref();
            return $tmp;
        }
		if ($document instanceof Zend_Gdata_Docs_DocumentListEntry) {
            $tmp = $document->getLink('alternate')->getHref();
			return $tmp;
		}
        if (Zend_Uri::check($document) == false) {
            throw new GN_GClient_Exception('invalid document');
        }
        return null;
	}

    /**
     * @param string $title
     * @return string
     */
    public function createSpreadsheet($title)
    {
        $spreadsheet = new Zend_Gdata_Spreadsheets_SpreadsheetEntry();
        $spreadsheet->setTitle(new Zend_Gdata_App_Extension_Title($title));
        $spreadsheet->setCategory(array(new Zend_Gdata_App_Extension_Category('http://schemas.google.com/docs/2007#spreadsheet', 'http://schemas.google.com/g/2005#kind')));

        $spreadsheetEntry = $this->insertEntry($spreadsheet, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI, 'Zend_Gdata_Spreadsheets_SpreadsheetEntry');
        return $this->getDocumentID($spreadsheetEntry);
    }

    /**
     * @param string $title
     * @return string
     */
	public function createDocument($title) {
		$document = new Zend_Gdata_Docs_DocumentListEntry();
		$document->setTitle(new Zend_Gdata_App_Extension_Title($title));
		$document->setCategory(array(new Zend_Gdata_App_Extension_Category('http://schemas.google.com/docs/2007#document', 'http://schemas.google.com/g/2005#kind')));

		$documentEntry = $this->insertEntry($document, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI, 'Zend_Gdata_Docs_DocumentListEntry');
		return $this->getDocumentID($documentEntry);
	}

    /**
     * @param string $title
     * @param string $parentPath
     * @return entry
     */
    public function createFolder($title, $parentPath = null) {
		$folder = new Zend_Gdata_Entry();
		$folder->setTitle(new Zend_Gdata_App_Extension_Title($title));
		$folder->setCategory(array(new Zend_Gdata_App_Extension_Category('http://schemas.google.com/docs/2007#folder', 'http://schemas.google.com/g/2005#kind')));

		$uri = Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI;
		if ($parentPath != null) {
			$uri .= '/' . $parentPath;
		}
		$folderEntry = $this->insertEntry($folder, $uri);
		//nie możemy użyć getDocumentID ponieważ zend nie ma klasy osobnej dla folderów
		$tmp = explode('/', $folderEntry->content->src);
		$folderId = end($tmp);
		return $folderEntry;
    }


	public function getFolderByTitle($title, $createIfNotFound = false) {
		$docsClient = new Zend_Gdata_Docs($this->_httpClient);
		foreach ($docsClient->getFeed('https://docs.google.com/feeds/folders/private/full/folder:root?showfolders=true') as $entry) {
			$isFolder = false;
			foreach ($entry->category as $category) {
				if (strpos($category->term, 'folder') !== false) {
					$isFolder = true;
				}
			}
			if (!$isFolder) {
				continue;
			}
			if ($entry->title->text == $title) {
				return $entry;
			}
		}
		if ($createIfNotFound) {
			return $this->createFolder($title);
		}
		return false;
	}

	
	public function getFolderEntry($folder_uri)
	{
	    $docsClient = new Zend_Gdata_Docs($this->_httpClient);
	    $entry=$docsClient->getEntry($folder_uri);
	    return $entry;	    
	}
	
	public function updateFolder($uri, $data)
	{
	    $docsClient = new Zend_Gdata_Docs($this->_httpClient);

	    $uri = Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI  . '/' . end(explode('/', $uri));
  
	    $xmlbody='';
	    foreach($data AS $k=>$v) $xmlbody.="<$k>$v</$k>";
	    $xml='<?xml version="1.0" encoding="UTF-8"?>
			<entry xmlns="http://www.w3.org/2005/Atom">
				    <category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/docs/2007#folder"/>
				    '.$xmlbody.'
			</entry>';  

	    $requestData = $docsClient->prepareRequest('PUT', $uri, array('If-Match' => '*'),$xml);
	  

	    $docsClient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], $requestData['data'], $requestData['contentType'], null);
    	    
	}
	
	public function updateFile($uri, $data)
	{

	}

	public function getDocumentEntry($documentID) {
		$docsClient = new Zend_Gdata_Docs($this->_httpClient);
		foreach ($docsClient->getDocumentListFeed()->entries as $entry) {
			if (urldecode(self::getDocumentID($entry)) == urldecode($documentID)) {
				    	    
				    return $entry;
			}
		}
		return null;
	}

	public function getDocumentsList($location = null) {
		if ($location === null) {
			$location = Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI;
		}
        return $this->getFeed($location, 'Zend_Gdata_Docs_DocumentListFeed');
	}

    /**
     * @param  string $spreadsheetID
     * @return Zend_Gdata_Spreadsheets_SpreadsheetEntry
     */
    public function getSpreadsheetEntry($spreadsheetID)
    {
        $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
        $query->setDocumentType('spreadsheets');
        $query->setSpreadsheetKey($spreadsheetID);

        return $this->getEntry($query, 'Zend_Gdata_Spreadsheets_SpreadsheetEntry');
    }

    /**
     * @param string $spreadsheet
     * @return GN_GClient
     */
    public function deleteSpreadsheet($spreadsheetID)
    {
        $spreadsheetEntry = $this->getSpreadsheetEntry($spreadsheetID);
        if ($spreadsheetEntry->getLink('edit') == null) {
            throw new GN_GClient_Exception('cannot delete spreadsheet');
        }
        $spreadsheetEntry->delete();
        return $this;
    }

    /**
     * @param Zend_Gdata_Spreadsheets_DocumentQuery|string $location
     * @return Zend_Gdata_Spreadsheets_SpreadsheetFeed
     */
    public function getSpreadsheetsList($location = null)
    {
        if ($location == null) {
            $location = Zend_Gdata_Spreadsheets::SPREADSHEETS_FEED_URI;
        }
        return $this->getFeed($location, 'Zend_Gdata_Spreadsheets_SpreadsheetFeed');
    }

    /**
     * @return array
     */
    public function getSpreadsheetsListData()
    {
        $tmp = array();
        /**
         * @var Zend_Gdata_Spreadsheets_SpreadsheetEntry $spreadsheet
         */
        foreach ($this->getSpreadsheetsList()->getEntry() as $spreadsheet) {
            $tmp[$this->getDocumentID($spreadsheet)] = (string) $spreadsheet->getTitle();
        }
        return $tmp;
    }

    /**
     * @param string $title
     * @param bool $createIfNotFound
     * @return string
     */
    public function getSpreadsheetId($title, $createIfNotFound = false)
    {
        $found = false;
        foreach ($this->getSpreadsheetsListData() as $spreadsheetID => $spreadsheetTitle) {
            if ($spreadsheetTitle == $title) {
                $found = true;
                break;
            }
        }

        if ($found)
            return $spreadsheetID;

        if ($createIfNotFound)
            return $this->createSpreadsheet($title);

        return null;
    }

    /**
     * @param string $spreadsheetID
     * @param string $title
     * @param int $rows
     * @param int $cols
     * @return string
     */
    public function createWorksheet($spreadsheetID, $title, $rows = 50, $cols = 10)
    {
        $worksheet = new Zend_Gdata_Spreadsheets_WorksheetEntry();
        $worksheet->setTitle(new Zend_Gdata_App_Extension_Title($title));
        $worksheet->setCategory(array(new Zend_Gdata_App_Extension_Category('http://schemas.google.com/docs/2007#spreadsheet', 'http://schemas.google.com/g/2005#kind')));
        $worksheet->setRowCount(new Zend_Gdata_Spreadsheets_Extension_RowCount($rows));
        $worksheet->setColumnCount(new Zend_Gdata_Spreadsheets_Extension_ColCount($cols));

        $worksheetEntry = $this->insertEntry($worksheet, 'https://spreadsheets.google.com/feeds/worksheets/' . $spreadsheetID . '/private/full', 'Zend_Gdata_Spreadsheets_WorksheetEntry');
        return $this->getDocumentID($worksheetEntry);
    }

    /**
     * @param string $spreadsheetID
     * @param string $worksheetID
     * @param bool $isTitle
     * @return Zend_Gdata_Spreadsheets_WorksheetEntry
     */
    public function getWorksheetEntry($spreadsheetID, $worksheetID, $isTitle = false)
    {
        /**
         * @var Zend_Gdata_Spreadsheets_WorksheetEntry $entry
         */
        foreach ($this->getSpreadsheetEntry($spreadsheetID)->getWorksheets()->getEntry() as $entry) {
            if (($isTitle == false && $this->getDocumentID($entry) == $worksheetID) || ($isTitle == true && (string) $entry->getTitle() == $worksheetID)) {
                return $entry;
            }
        }
        return null;
    }

    /**
     * @param $spreadsheetID
     * @param $worksheetID
     * @param bool $isTitle
     * @return Zend_Gdata_Spreadsheets_WorksheetFeed
     */
    public function getWorksheetFeed($spreadsheetID, $worksheetID, $isTitle = false)
    {
        $worksheetEntry = $this->getWorksheetEntry($spreadsheetID, $worksheetID, $isTitle);
        if ($worksheetEntry) {
            $spreadsheetEntry = $this->getSpreadsheetEntry($spreadsheetID);
            return $this->getFeed($spreadsheetEntry->getLink(Zend_Gdata_Spreadsheets::WORKSHEETS_FEED_LINK_URI)->href, 'Zend_Gdata_Spreadsheets_WorksheetFeed');
        }
        return null;
    }

    /**
     * @param string $spreadsheetID
     * @return Zend_Gdata_Spreadsheets_WorksheetFeed
     */
    public function getWorksheetsList($spreadsheetID)
    {
        return $this->getSpreadsheetEntry($spreadsheetID)->getWorksheets();
    }

    /**
     * @param string $spreadsheetID
     * @return array
     */
    public function getWorksheetsListData($spreadsheetID)
    {
        $tmp = array();
        /**
         * @var Zend_Gdata_Spreadsheets_WorksheetEntry $worksheet
         */
        foreach ($this->getWorksheetsList($spreadsheetID)->getEntry() as $worksheet) {
            $tmp[$this->getDocumentID($worksheet)] = (string) $worksheet->getTitle();
        }
        return $tmp;
    }

    /**
     * @param string $spreadsheetID
     * @param string $worksheetID
     * @return array
     */
    public function getWorksheetData($spreadsheetID, $worksheetID)
    {
        $data = array();
        $worksheetEntry = $this->getWorksheetEntry($spreadsheetID, $worksheetID);
        /**
         * @var Zend_Gdata_Spreadsheets_ListFeed $listFeed
         * @var Zend_Gdata_Spreadsheets_ListEntry $listEntry
         * @var Zend_Gdata_Spreadsheets_Extension_Custom $customEntry
         */
        $listFeed = $this->getFeed($worksheetEntry->getLink(Zend_Gdata_Spreadsheets::LIST_FEED_LINK_URI)->href, 'Zend_Gdata_Spreadsheets_ListFeed');
        foreach ($listFeed->getEntry() as $listEntry) {
            $tmp = array();
            foreach ($listEntry->getCustom() as $customEntry) {
                $tmp[$customEntry->getColumnName()] = $customEntry->getText();
            }
            $data[] = $tmp;
        }
        return $data;
    }
    
    
    public function putItemToFolder($item,$folder)
    {
	    $docsClient = new Zend_Gdata_Docs($this->_httpClient);
	    $docsClient->insertEntry($item, $folder);
	    
    }
    
    public function deleteItemFromRootFolder($itemId)
    {
	    $docsClient = new Zend_Gdata_Docs($this->_httpClient);
	    $uri = Zend_Gdata_Docs::DOCUMENTS_FOLDER_FEED_URI . '/folder%3Aroot/' . $itemId;
	    $requestData = $docsClient->prepareRequest('DELETE', $uri, array('If-Match' => '*'));
	    $docsClient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null);	    
    }
    
    public function getDocumentIdFromObj($doc)
    {
	    if (!is_object($doc)) return($doc);
    }
    
    public function deleteDocument($document)
    {
	    if (method_exists($document,'delete')) $document->delete();
    }
    
    public function deleteFolder($folder)
    {
	    $docsClient = new Zend_Gdata_Docs($this->_httpClient);
	    $uri = $folder;
	    //zamień adres - api v2 używa do usuwania folderów adresu api _dokumentów_ a nie folderów (api v3)
	    $uri = Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI  . '/' . end(explode('/', $uri));
	    $requestData = $docsClient->prepareRequest('DELETE', $uri, array('If-Match' => '*'));
	    $docsClient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null);
	    
    }
    
	    public function copyDocument($src_id,$title)
	    {
			$documentCopy = new Zend_Gdata_Docs_DocumentListEntry();
			$documentCopy->setCategory(array(new Zend_Gdata_App_Extension_Category("http://schemas.google.com/docs/2007#document", "http://schemas.google.com/g/2005#kind")));
			$documentCopy->setTitle(new Zend_Gdata_App_Extension_Title($title), null);
			$documentCopy->setId(new Zend_Gdata_App_Extension_Id($src_id));
			$this->insertEntry($documentCopy, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI, 'Zend_Gdata_Docs_DocumentListEntry');	    
	    }
    
    
    	    public function getDocumentByTitle($title)
	    {		
			$query = new Zend_Gdata_Docs_Query();
			$query->setTitle($title);
			$query->setTitleExact('true');
			return $this->getEntry($query, 'Zend_Gdata_docs_DocumentListEntry');			
	    }
	    
	    
	    public function addPermisionToObj($obj,$email,$acl,$more_acl=null,$notify=true,$message=null)
	    {
			$docsClient = new Zend_Gdata_Docs($this->_httpClient);
			$docsClient->updateAcl($obj, $email, $acl,$more_acl);			
	    }


	    public function revokeAllPermisions($obj,$email)
	    {
			
			$docsClient = new Zend_Gdata_Docs($this->_httpClient);
			$docsClient->deleteAcl($obj,$email);
			
			return;
			$headers = array(
				'GData-Version' => '3.0',
				'X-Upload-Content-Length' => '0',
			);
			$xml = '';
			$uri = "https://docs.google.com/feeds/default/private/full/" . $objID . '/acl/default';
		
			die('sraj');
			$docsClient->performHttpRequest('DELETE', $uri, $headers, $xml, 'application/atom+xml');			
		
	    }
    
	    public function anyOneWithLinkCanEdit($objID,$acl='writer',$more_acl=null)
	    {

			$more=$more_acl?'<gAcl:additionalRole value="'.$more_acl.'"/>':'';
			$headers = array(
				'GData-Version' => '3.0',
				'X-Upload-Content-Length' => '0',
			);
			$xml = '<?xml version="1.0" encoding="UTF-8"?' . '>' .
				'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gAcl="http://schemas.google.com/acl/2007">' .
				'<category term="http://schemas.google.com/acl/2007#accessRule" scheme="http://schemas.google.com/g/2005#kind"/>' .
				'<gAcl:withKey key="batman"><gAcl:role value="'.$acl.'"/>'.$more.'</gAcl:withKey>' .
				'<gAcl:scope type="default"/>' .
				'</entry>';
			$uri = "https://docs.google.com/feeds/default/private/full/" . $objID . '/acl';
			$docsClient->performHttpRequest('POST', $uri, $headers, $xml, 'application/atom+xml');			
			
	    }
	    
	    public function getFilePublishDate($f)
	    {
			return $f->published->text;
	    }
	    
	    public function getFileUpdateDate($f)
	    {
			return $f->updated->text;
	    }
	    
	    
	    public function getFileUpdateAuthor($f)
	    {
			try {
				$xml = $f->getService()->getHttpClient()->getLastResponse()->getBody();
				$xml = preg_replace('/<(\/)?[a-z]*:/', '<\1', $xml); //usuń namespace, inaczej simplexml nie przeczyta
				$xml = simplexml_load_string($xml);
				$author = (string)$xml->entry[0]->lastModifiedBy->name;
			} catch (Exception $e) {

				$author = null;
			}

			return $author;
	    }
	    
	    public function getFileOwner($f)
	    {
			try {
				$xml = $f->getService()->getHttpClient()->getLastResponse()->getBody();
				$xml = preg_replace('/<(\/)?[a-z]*:/', '<\1', $xml); //usuń namespace, inaczej simplexml nie przeczyta
				$xml = simplexml_load_string($xml);
				$author = (string)$xml->entry[0]->author->name;
			} catch (Exception $e) {

				$author = null;
			}

			return $author;
	    }
	    
	    public function writersCanShare($obj,$may_share=true)
	    {
			$docId=self::getDocumentID($obj);
	    
			$docsClient = new Zend_Gdata_Docs($this->_httpClient);
			$headers = array(
				'GData-Version' => '3.0',
				'X-Upload-Content-Length' => '0',
				'If-Match' => '*',
			);
			$xml = '<?xml version="1.0" encoding="UTF-8"?' . '>' .
				'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:docs="http://schemas.google.com/docs/2007">' .
				'<docs:writersCanInvite value="'.($may_share?'true':'false').'" />' .
				'<docs:writersCanShare value="'.($may_share?'true':'false').'" />' .
				'</entry>';
			$uri = "https://docs.google.com/feeds/default/private/full/" . $docId;
			$docsClient->performHttpRequest('PUT', $uri, $headers, $xml, 'application/atom+xml');
			
			
	    }
	    
	    
}
