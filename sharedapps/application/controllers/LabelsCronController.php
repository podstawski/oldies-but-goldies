<?php
require_once 'AbstractCLIController.php';
class LabelsCronController extends AbstractCLIController {
	public function labelsAction() {
		$modelLabels = new Model_Labels();
		$ids=array();
		foreach ($modelLabels->fetchAll() as $label) {
			$ids[]=$label->id;
		}
		sort($ids);
		die(implode(' ',$ids));
	}

	protected function getMessageRow($messageID,$userID) {
		$modelMessages = new Model_Messages();

		$messageRow = $modelMessages->getByMessageID($messageID);
		if ($messageRow === null) {
			$this->debug('New message '.$messageID,2);
			$messageRow = $modelMessages->createRow();
			$messageRow->message_id = $messageID;
			$messageRow->user_id = $userID;
			$messageRow->save();
		}
		return $messageRow;
	}

	protected function getMessageLabelRow($messageID,$labelID) {
		$modelMessageLabels = new Model_MessageLabels();
		$messageLabelRow = $modelMessageLabels->getByMessageAndLabelID($messageID, $labelID);

		if (!$messageLabelRow) {
			$messageLabelRow=$modelMessageLabels->createRow();
			$messageLabelRow->message_id = $messageID;
			$messageLabelRow->label_id = $labelID;
			$messageLabelRow->save();
		}

		return $messageLabelRow;
	}

	protected function getFullMail($imap,$imapID) {
		if (0) {
			/* sets "read" flag */
			$tmp = $imap->fetch(array('BODY[TEXT]', 'RFC822.HEADER', 'FLAGS'), $imapID);
			$mail = new CRM_Mail(array(
				'raw' => $tmp['RFC822.HEADER'] . "\r\n" . $tmp['BODY[TEXT]'],
				'flags' => $tmp['FLAGS']));
		} else {
			/* doesn't set "read" flag */
			$tmp = $imap->fetch(array('BODY.PEEK[TEXT]', 'BODY.PEEK[HEADER]', 'FLAGS'), $imapID);
			$mail = new CRM_Mail(array(
				'raw' => $tmp['BODY[HEADER]'] . "\r\n" . $tmp['BODY[TEXT]'],
				'flags' => $tmp['FLAGS']));
		}
		return $mail;
	}

	protected function getFullMailDate($fullMail) {
		//szukamy w formacie rfc 2822 w nagłówkach maila
		preg_match('/^Date: (.*)$/mi', $fullMail->getRaw(), $matches);
		if (empty($matches[1])) {
			return null;
		}
		//zwracamy w formacie rfc2060 date_time, czyli używanym w append() w imap
		$x = array_map('trim', explode(' ', $matches[1]));
		return $x[1] . '-' . $x[2] . '-' . $x[3] . ' ' . $x[4] . ' ' . $x[5];
	}

	protected function saveMessageAppendIfNeeded($userLabel,$labelName,$messageID,$fullmail) {
		$modelUserMessages = new Model_UserMessages();

		$user = $userLabel->getUser();

		if ($fullmail) {
			try {
				$imap=$_SERVER['imap'][$user->id];
				$date = $this->getFullMailDate($fullmail);
				$this->debug('Appending message '.$messageID.' to '.$user->email."'s folder",3);
				$flags = array();
				if (in_array(Zend_Mail_Storage::FLAG_FLAGGED, $fullmail->getFlags())) {
					$flags []= Zend_Mail_Storage::FLAG_FLAGGED;
				}
				$imap->append($userLabel->local_name?:$labelName, $fullmail->getRaw(), $flags, $date);
			}
			catch (Exception $e) {
				$this->debug('Exception: ' . $e->getMessage(), 3);
				return;
			}
		}

		$subMessageRow = $modelUserMessages->createRow();
		$subMessageRow->user_id = $userLabel->user_id;
		$subMessageRow->message_id = $messageID;
		$subMessageRow->save();

		return $subMessageRow;

	}

	protected function propagateMessages($label,$imap,$storage,$imapIds,$userID) {
		$modelUserMessages = new Model_UserMessages();

		if (empty($imapIds)) {
			return;
		}

		foreach ($imapIds as $imapId) {
			$flags = array();


			//$ala=$imap->fetch(array('BODY[HEADER.FIELDS (MESSAGE-ID)]','X-GM-THRID'),$imapId);
			//die(var_dump($ala));

			$header = $imap->fetch('RFC822.HEADER',$imapId);
			$mail = new Zend_Mail_Message(array('handler' => $storage, 'id' => $imapId, 'headers' => $header, 'flags' => $flags));

			$messageRow = $this->getMessageRow($mail->messageID,$userID);
			$this->getMessageLabelRow($messageRow->id, $label->id);

			$x_gm=$imap->fetch(array('X-GM-THRID','X-GM-MSGID'),$imapId);
			$thrid=$x_gm['X-GM-THRID'];
			$googleid=$x_gm['X-GM-MSGID'];


			$fullMail = null;
			$flags = null;
			foreach ($label->getUserLabels(true) as $subUserLabel) {
				$subMessageRow = $modelUserMessages->getByUserAndMessageID($subUserLabel->user_id, $messageRow->id);



				if (is_null($subMessageRow)) {
					if (empty($fullMail)) {
						$fullMail = $this->getFullMail($imap, $imapId);
					}


					$this->saveMessageAppendIfNeeded($subUserLabel,$label->name,$messageRow->id, ($subUserLabel->user_id != $userID)?$fullMail:null);

				}
				elseif ($subUserLabel->user_id == $userID) if (empty($subMessageRow->thrid) || $thrid!=$subMessageRow->thrid || empty($subMessageRow->googleid) || $googleid!=$subMessageRow->googleid )
				{
					$subMessageRow->thrid = $thrid;
					$subMessageRow->googleid = $googleid;
					$subMessageRow->save();
				}

			}
		}

	}

	
	protected function addAllUserMessage($userId,$imapId,$labelId)
	{
		$modelUserAllMessages = new Model_UserAllMessages();

		$userAllMessage = $modelUserAllMessages->createRow();
		$userAllMessage->user_id = $userId;
		$userAllMessage->imap_id = $imapId;
		$userAllMessage->label_id = $labelId;
		$userAllMessage->save();
		
	}
	
	
	public function migrateAction() {
		//header('Content-Type: text/plain');
		$p = $this->_request->getParams();
		if ($p['label'] + 0 == 0) { //?
			die("No label ID\n");
		}

		$start=$this->microtime_float();

		if (isset($p['noflush']) && $p['noflush']) $this->flush=false;

		$modelMessages = new Model_Messages();
		$modelLabels = new Model_Labels();
		$modelUserLabel = new Model_UserLabels();
		$modelUserMessages = new Model_UserMessages();
		$modelUserAllMessages = new Model_UserAllMessages();

		$label=$modelLabels->find($p['label'])->current();

		if (!$label)
		{
			die('No label with ID='.$p['label']."\n");
		}

		
		while ($label->getRelatedLabelsActionCount()>0 ) {
			$this->debug("Waiting for users not to interfere " . $label->name);
			sleep(10);
		}
		
		$label->start();
		
		$this->debug("Starting label '".CRM_Core::imapDec($label->name)."'");

		$users=array();
		foreach ($label->getUserLabels(true) AS $userLabel) {
			$u = array();
			$user = $userLabel->getUser();
			$u['userLabel'] = $userLabel;
			$u['user'] = $user;
			$users []= $u;

		}

		if (count($users) == 1) {
			$this->debug('Skipping synchronization because got only 1 user');
		} else {
			foreach ($users as $k => $u) {
				extract($u);
				$this->debug("Label lookup ".$user->email,1);
				$mylabelname = $label->name;
				if (!empty($userLabel->local_name)) {
					$mylabelname = $userLabel->local_name;
					$this->debug("User's label name: ".CRM_Core::imapDec($mylabelname), 1);
				}
				try {
					$userImap = CRM_Core::getIMAP($user);
					
					if ($user->disabled) {
						$user->disabled=null;
						$user->save();
					}
					
				} catch (CRM_IMAPException $e) {
					$user->disabled=date('c');
					$user->save();
					/*
					CRM_History::observe($user, 'labels-disable', array(
						'label-id' => $userLabel->getLabel()->id,
						'user-label-id' => $userLabel->id,
						'user-label-name' => CRM_Core::imapDec($userLabel->getName()),
						'user-mp' => $userLabel->getUser()->getDomain()->marketplace,
						'user-domain' => $userLabel->getUser()->getDomain()->domain_name,
						'user-name' => $userLabel->getUser()->name,
						'user-id' => $userLabel->user_id,
						'user-email' => $userLabel->getUser()->email,
					));
					*/
					$this->debug('Invalid token/marketplace error for user ' . $user->email . ' (message: ' . $e->getMessage() . '); skipping', 1);
					unset($users[$k]);
					continue;
				} catch (GN_GClient_EmptyTokenException $e) {
					$this->debug($e->getMessage());
					unset($users[$k]);
					continue;
				} catch (CRM_EmptyTokenException $e) {
					$this->debug($e->getMessage());
					unset($users[$k]);
					continue;
				}
				$imap = $userImap->getIMAP();
				$u['imap']=$imap;
				$storage = new Zend_Mail_Storage_Imap($imap);
				$u['storage']=$storage;

				$_SERVER['imap'][$user->id] = $imap;


				$xlist = $imap->requestAndResponse('LIST', $imap->escapeString('', '*'));


				$allmail = '';
				foreach ($xlist AS $x) {
					if (isset($x[1]) && $x[1][count($x[1])-1]=='\\All') {
						$allmail=$x[count($x)-1];
					}
				}

				$u['allmail']=$allmail;

				try {
					$storage->selectFolder($mylabelname);
					$targetLabel=$mylabelname;
				} catch (Exception $e) {
					$targetLabel = CRM_Core::selectCreatedPath($storage, $mylabelname);
				}

				if ($targetLabel!=$mylabelname)
				{
					$userLabelRow=$modelUserLabel->getByID($userLabel->id);
					$userLabelRow->local_name=$targetLabel;
					$userLabelRow->save();
				}
				$u['mylabelname']=$targetLabel;
				
				$users[$k]=$u;
			}

			foreach ($users AS $u) {
				foreach($u AS $k=>$v) $$k=$v;

				$this->debug("Sync user ".$user->email,1);

				if ($userLabel->start==date('Y-m-d'))
				{
					$since=time()-90*24*3600;
					$search = 'SINCE '.date('d-M-Y',$since);
	
					$imapIds = $imap->search(array($search));
					$this->debug('Message count since '.date('d-m-Y',$since).': '.count($imapIds),2);
	
					$this->propagateMessages($label,$imap,$storage,$imapIds,$userLabel->user_id);
				}


				if (!$allmail) continue;

				$storage->selectFolder($allmail);
				$imapIds = $imap->search(array('SINCE '.date('d-M-Y',time()-1*24*3600)));

				$this->debug('AllMail count since '.date('d-m-Y',time()-1*24*3600).': '.count($imapIds),2);


				
				$allImapdIds=array();
				foreach($imapIds AS $imapId) {
					
					if ($modelUserAllMessages->getByUserAndImapID($user->id,$imapId,$label->id))
					{
						continue;
					}


					try {
						$google=$imap->fetch(array('X-GM-LABELS','X-GM-THRID'),$imapId);
					} catch (Exception $e) {
						continue 2;
					}

					$thrid=$google['X-GM-THRID'];

					if (is_array($google['X-GM-LABELS'])) foreach ($google['X-GM-LABELS'] AS $_label) {
						if ($mylabelname == $_label)
						{
							$allImapdIds[]=$imapId;
							$this->addAllUserMessage($user->id,$imapId,$label->id);
							continue 2;
						}
						if (strtolower($_label)=='\\\\draft')
						{
							continue 2;
						}
					}

					if ($modelUserMessages->getByLabelAndUserAndThrID($label->id,$userLabel->user_id,$thrid)) {
						$this->addAllUserMessage($user->id,$imapId,$label->id);
						$allImapdIds[]=$imapId;
					}

				}
				$this->debug('Unseen and applicable mail count: '.count($allImapdIds),2);
				$this->propagateMessages($label,$imap,$storage,$allImapdIds,$userLabel->user_id);
			}
		}

		$label->finish();

		$end=$this->microtime_float();
		$delta=round($end-$start,2);
		$this->debug('End label \''.CRM_Core::imapDec($label->name).'\' in '.$delta.' sec.');


		die();
	}
}
