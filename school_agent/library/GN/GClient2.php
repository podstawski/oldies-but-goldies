<?php
/**
 * @author <piotr.podstawski@gammanet.pl> Piotr Podstawski
 */

class GN_GClient2 extends GN_GClient
{
	    const F_ROOT = 'https://www.googleapis.com/drive/v2/files';
	    const MIME = 'application/json';
	    
	    protected function response($resp)
	    {
			return json_decode($resp->getBody());
			
	    }
	    
	    protected function _puke($co,$die=false)
	    {
			echo '<pre>'.print_r($co,1);
			if ($die) die();
	    }	    
	    
	    
	    public function getFolderByTitle($title, $createIfNotFound = false)
	    {
	    
			$this->_httpClient->setUri(self::F_ROOT.'/root/children?q='.urlencode("title='$title'"));
	    
			$resp=$this->_httpClient->setRawData('',self::MIME)->request('GET');
			$resp=$this->response($resp);
	    
			$ret=null;
			if (count($resp->items)>0)
			{
				    $ret=$resp->items[0];
				    $ret->content = new stdClass();
				    $ret->content->src=$ret->id;
			}
			elseif ($createIfNotFound)
			{
				    $ret=$this->createFolder($title);
			}
			return $ret;
	    }
	    
	    public function getDocumentByTitle($title)
	    {
			return $this->getFolderByTitle($title);
	    }
	    
	    protected function createItem($name,$type)
	    {
			$this->_httpClient->setUri(self::F_ROOT);
			$data=array('title'=>$name,'mimeType'=>'application/vnd.google-apps.'.$type);
			$postdata=json_encode($data);

			$resp=$this->_httpClient->setRawData($postdata,self::MIME)->request('POST');
			$resp=$this->response($resp);
			
			$resp->content = new stdClass();
			$resp->content->src=$resp->id;
			
			return $resp;
			
	    }
	    
	    public function createFolder($title)
	    {
			return $this->createItem($title,'folder');

	    }
	    
	    public function createDocument($title)
	    {
			return $this->createItem($title,'document');
	    }
	    
	    public function getFolderEntry($folder_id)
	    {
			return $this->getDocumentEntry($folder_id);
	    }
	    
	    public function getDocumentEntry($document_id)
	    {
			if (is_object($document_id)) return $document_id;
			
			if (substr($document_id,0,9)=='document:') $document_id=substr($document_id,9);

			$this->_httpClient->setUri(self::F_ROOT.'/'.$document_id);
	    
			$resp=$this->_httpClient->setRawData('',self::MIME)->request('GET');
			$resp=$this->response($resp);
			
			return $resp;
	    }
	    
	    public function putItemToFolder($item,$folder)
	    {
			$this->_httpClient->setUri(self::F_ROOT.'/'.$folder.'/children');
			if (is_object($item)) $item=$item->id;
			$data=array('id'=>$item);
			$postdata=json_encode($data);

			$resp=$this->_httpClient->setRawData($postdata,self::MIME)->request('POST');
			$resp=$this->response($resp);
	    }
	    
	    public function deleteItemFromFolder($itemId,$folderId)
	    {
			if (is_object($itemId)) $itemId=$itemId->id;
			
			$url=self::F_ROOT.'/'.$folderId.'/children/'.$itemId;
			$this->_httpClient->setUri($url);
			$resp=$this->_httpClient->setRawData('',self::MIME)->request('DELETE');
			
	    }

	    
	    public function deleteItemFromRootFolder($itemId)
	    {
			return $this->deleteItemFromFolder($itemId,'root');
	    }    

	    
	    public static function getDocumentLink($document)
	    {
			if (is_object($document) && isset($document->alternateLink) ) return $document->alternateLink;
			
			return $this->getDocumentEntry($document)->alternateLink;
	    }
	    
	    public function getDocumentIdFromObj($doc)
	    {
			return $doc->id;
	    }

	    public function updateFolder($file, $data)
	    {
			return $this->updateFile($file,$data);
	    }
	    
	    public function updateFile($file, $data)
	    {
			if (is_object($file)) $file=$file->id;
			
			$url=self::F_ROOT.'/'.$file;
			$this->_httpClient->setUri($url);
			
			$postdata=json_encode($data);
			$resp=$this->_httpClient->setRawData($postdata,self::MIME)->request('PUT');
			
			return $resp;
	    }
	    
	    
	    public function deleteDocument($file)
	    {
			if (is_object($file))
			{
				    if (!isset($file->id)) return;
				    $file=$file->id;
			}
			$url=self::F_ROOT.'/'.$file;
			$this->_httpClient->setUri($url);
			
			$resp=$this->_httpClient->setRawData('',self::MIME)->request('DELETE');
			
	    }
	    public function deleteFolder($folder)
	    {
			return $this->deleteDocument($folder);
	    }
	    
	    public static function getDocumentID($document)
	    {
			return $document->id;
	    }
	    
	    
	    public function copyDocument($src_id,$title)
	    {
			$url=self::F_ROOT.'/'.$src_id.'/copy';
			$this->_httpClient->setUri($url);
			
			$data=array('title'=>$title);
			$postdata=json_encode($data);
			$resp=$this->_httpClient->setRawData($postdata,self::MIME)->request('POST');
			
			$resp=$this->response($resp);
			
			return $resp;
			
	    }
	    
	    public function writersCanShare($obj,$may_share=true)
	    {
			if (is_object($obj)) $obj=$obj->id;
			
			$resp=$this->updateFile($obj,array('writersCanShare'=>$may_share));
			

	    }
	    
	    public function addPermisionToObj($obj,$email,$acl,$more_acl=null,$notify=true,$message=null)
	    {
			if (is_object($obj)) $obj=$obj->id;
			
			$url=self::F_ROOT.'/'.$obj.'/permissions';
			$this->_httpClient->setUri($url);
			
			
			$data=array('sendNotificationEmails'=>$notify, 'role'=>$acl, 'type'=>'user', 'value'=>$email);
			if ($more_acl) $data['additionalRoles']=array($more_acl);
			if (!is_null($message)) $data['emailMessage']=$message;
			$postdata=json_encode($data);
			
			
			$resp=$this->_httpClient->setRawData($postdata,self::MIME)->request('POST');
			
			$resp=$this->response($resp);
			
			return $resp;			
			
	    }
	    
	    public function revokeAllPermisions($obj,$email)
	    {
			if (is_object($obj)) $obj=$obj->id;
			
			$url=self::F_ROOT.'/'.$obj.'/permissions';
			$this->_httpClient->setUri($url);
			
			$resp=$this->_httpClient->setRawData('',self::MIME)->request('GET');
			$resp=$this->response($resp);			
	
			if (isset($resp->items)) foreach ($resp->items AS $item)
			{
				    if ($item->role=='owner') continue;
				    $this->_httpClient->setUri($url.'/'.$item->id);
				    $this->_httpClient->setRawData('',self::MIME)->request('DELETE');
				    
			}		
	    }
	    
	    public function getFilePublishDate($obj)
	    {
			return $obj->createdDate;
			
	    }
	    public function getFileUpdateDate($obj)
	    {
			return $obj->modifiedDate;
	    }
	    
	    public function getFileUpdateAuthor($obj)
	    {
			return $obj->lastModifyingUserName;
	    }
	    
	    public function getFileOwner($obj)
	    {
			return $obj->ownerNames[0];
	    }
	    
	    public function anyOneWithLinkCanEdit($obj,$acl='writer')
	    {
			if (is_object($obj)) $obj=$obj->id;
			$url=self::F_ROOT.'/'.$obj.'/permissions';
			$this->_httpClient->setUri($url);
			
			$data=array('sendNotificationEmails'=>false, 'role'=>$acl, 'type'=>'anyone', 'value'=>'me', 'withLink'=>true);
			$postdata=json_encode($data);
			$resp=$this->_httpClient->setRawData($postdata,self::MIME)->request('POST');
			
			$resp=$this->response($resp);
			
			return $resp;				
	    }
	    
}
