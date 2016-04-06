<?php
require_once 'AbstractCLIController.php';
class ContactsCronController extends AbstractCLIController {
	private $updates = 0;
	private $inserts = 0;

	public function init() {
		date_default_timezone_set('UTC');
	}

	public function contactGroupsAction() {
		$modelContactGroups = new Model_ContactGroups();
		$ids=array();
		foreach ($modelContactGroups->fetchAll() as $contactGroup) {
			$ids[]= $contactGroup->id;
		}
		sort($ids);
		die(implode(' ',$ids));
	}

	protected function googleContactAsText($googleContact) {
		return $googleContact->name . ' (' . $googleContact->id/* . '; ' . join(';', $googleContact->emailAddress)*/ . ')';
	}



	//metoda konstruująca info o mega-kontakcie, którego będziemy rozsyłać wszystkim zainteresowanym
	protected function makeContactInfo($dbContact) {
		$this->debug('making contact info for #' . $dbContact->id, 1);

		$modelUserContacts = new Model_UserContactGroupContacts();
		$userContacts = $modelUserContacts->getByContactID($dbContact->id);
		$ci = array();
		$ci['gc'] = array();
		$ci['do-update'] = false;
		$ci['final-user-contact'] = null;
		$ci['final-gc'] = null;
		$ci['final-gc-id'] = null;

		$googleContacts = array();
		$userContacts = iterator_to_array($userContacts);
		foreach ($userContacts as $userContact) {
			$user = $userContact->getUser();
			$cclient = CRM_Core::getContactsClient($user);
			try {
				$googleContact = $cclient->getContact($userContact->google_contact_id);
				$googleContacts[$userContact->id] = $googleContact;
			} catch (Exception $e) {
				$this->debug('error fetching google contact of #' . $dbContact->id . ' from user ' . $userContact->getUser()->email . ': ' . $e->getMessage(), 2);
				$googleContacts[$userContact->id] = null;
			}
		}

		uasort($userContacts, function($a, $b) use ($googleContacts) {
			$a = $googleContacts[$a->id];
			$b = $googleContacts[$b->id];
			if (empty($a)) {
				return 0;
			}
			if (empty($b)) {
				return 1;
			}
			return $a->updated - $b->updated;
		});

		//weź bazowy wpis z ostatnio wczytanego
		if (!end($userContacts) or !$googleContacts[end($userContacts)->id]) {
			return $ci;
		} else {
			$ci['final-user-contact'] = end($userContacts);
			$ci['final-gc'] = clone($googleContacts[end($userContacts)->id]);
			$ci['final-gc-id'] = $ci['final-gc']->id;
		}

		//pobierz informacje
		$finalEmails = array('xml' => array(), 'strings' => array());
		$finalPhones = array('xml' => array(), 'strings' => array());
		foreach ($userContacts as $k => $userContact) {
			try {
				$googleContact = $googleContacts[$userContact->id];
			} catch (Exception $e) {
				$googleContact = null;
			}
			if (empty($googleContact)) {
				continue;
			}

			if ($googleContact->updated > strtotime($dbContact->date_synchronized)) {
				$ci['do-update'] = true;
			}

			$xml = simplexml_load_string($googleContact->realEntry->getXML());
			$this->debug(
				$this->googleContactAsText($googleContact) . ' from ' . $userContact->getUser()->email . ' (' . 
				date($this->view->translate('common_datetime_format'), $googleContact->updated) . ' vs ' .
				date($this->view->translate('common_datetime_format'), strtotime($dbContact->date_synchronized)) . ')'
			, 2);

			//mergowanie maili w xml
			foreach ($xml->email as $email) {
				$key = CRM_Core::parseEmail((string) $email['address']);
				if (!in_array($key, $finalEmails['strings'])) {
					$finalEmails['xml'] []= $email->saveXML();
					$finalEmails['strings'] []= $key;
				}
			}

			foreach ($xml->phoneNumber as $phone) {
				$key = CRM_Core::parsePhone((string) $phone); //hack
				if (!((string) $phone)) {
					continue;
				}
				if (!in_array($key, $finalPhones['strings'])) {
					$finalPhones['xml'] []= $phone->saveXML();
					$finalPhones['strings'] []= $key;
				}
			}

			$ci['gc'] []= array('userID' => $userContact->getUser()->id, 'googleContact' => $googleContact);
		}

		//upewnienie się że tylko jeden e-mail jest primary
		{
			$primaryEmails = 0;
			foreach ($finalEmails['xml'] as $key => $emailXML) {
				if (strpos($emailXML, 'primary="true"') !== false) {
					$primaryEmails ++;
					//GN_Debug::debug('found new primary address: ' . $emailXML);
					if ($primaryEmails > 1) {
						$finalEmails['xml'][$key] = preg_replace('/ primary="true"/', '', $emailXML);
					}
				}
			}
			$primaryPhones = 0;
			foreach ($finalPhones['xml'] as $key => $phoneXML) {
				if (strpos($phoneXML, 'primary="true"') !== false) {
					$primaryPhones ++;
					//GN_Debug::debug('found new primary address: ' . $phoneXML);
					if ($primaryPhones > 1) {
						$finalPhones['xml'][$key] = preg_replace('/ primary="true"/', '', $phoneXML);
					}
				}
			}
		}

		//przeparsuj xmla
		$xml = $ci['final-gc']->realXML;
		//najpierw usuń wszystkie istniejące telefony i maile
		$xml = preg_replace('/<(gd:)?email([^>]*)>/', '', $xml);
		$xml = preg_replace('/<(gd:)?phoneNumber([^>]*)>([^<]*)<\/(gd:)?phoneNumber>/', '', $xml);
		//teraz doklej to, co trzeba
		$bla = '';
		$bla .= implode('', $finalEmails['xml']);
		$bla .= implode('', $finalPhones['xml']);
		$xml = preg_replace('/(<\/(atom:)?entry>)/', $bla . '\1', $xml);

		$ci['final-gc']->realXML = $xml;
		//$this->debug($xml, 2);
		return $ci;
	}



	//metoda wywoływana w sytuacji kiedy wiemy, że zaraz updatniemy komuś kontakt -- możemy pozwolić sobie na dodatkowe requesty do google
	protected function extendContactInfo($ci) {
		if (empty($ci['final-gc'])) {
			return;
		}
		//nie pobieraj informacji 2x
		if (isset($ci['extended'])) {
			return;
		}
		$ci['extended'] = true;
		//pobierz zdjęcie
		$user = $ci['final-user-contact']->getUser();
		$cclient = CRM_Core::getContactsClient($user);
		$result = $cclient->getContactPhoto($ci['final-gc-id']);
		if ($result) {
			list ($mime, $data) = $result;
			$ci['final-gc-photo-mime'] = $mime;
			$ci['final-gc-photo-data'] = $data;
		}

		return $ci;
	}



	//metoda "naprawiająca" finalny kontakt przed włożeniem, używając info (i ew. bazowego kontaktu)
	protected function finalizeGoogleContact($finalGC, $userInfo, $sourceGC) {
		$user = $userInfo['userContactGroup']->getUser();
		$gc = $finalGC;

		//napraw maile
		$gc->realXML = preg_replace('/google\.com([^"]+)\/[^"\/]*%40[^"\/]*(["\/])/', 'google.com\1/' . urlencode($user->getEmail()) . '\2', $gc->realXML);
		//czas ostatniego update
		$gc->realXML = preg_replace('/updated>([^<]+)<\//', 'updated>' . date('c') . '</', $gc->realXML);
		//usuń całe info o uczestnictwie w grupach
		$gc->realXML = preg_replace('/<groupMembershipInfo[^>]*>/', '', $gc->realXML);
		//dodaj id synchronizowanej grupy
		$bla = '<groupMembershipInfo xmlns="http://schemas.google.com/contact/2008" deleted="false" href="http://www.google.com/m8/feeds/groups/' . urlencode($user->getEmail()) . '/base/' . $userInfo['googleGroupID'] . '"/>';
		$gc->realXML = preg_replace('/(<\/(atom:)?entry>)/', $bla . '\1', $gc->realXML);

		//jeśli dodatkowo wiemy, że chcemy updatować już jakiś istniejący id, to musimy zrobić parę rzeczy więcej
		if ($sourceGC !== null) {
			//sfałszuj id kontaktu w xmlu, żeby updatowanie się nie psuło
			$gc->realXML = preg_replace('/contacts\/([^"><]*?)\/[0-9a-f]{7,}"/', 'contacts/\1/' . $sourceGC->id . '"', $gc->realXML); //napraw idki kontaktu
			$gc->id = $sourceGC->id;

			//doklej info o grupach które już były
			preg_match_all('/(<groupMembershipInfo[^>]*\/>)/', $sourceGC->realXML, $matches);
			$bla = join('', $matches[0]);
			$gc->realXML = preg_replace('/(<\/(atom:)?entry>)/', $bla . '\1', $gc->realXML);
		}

		return $gc;
	}




	public function synchronizeContact($userInfo, $dbContact, $ci) {
		$userContactGroup = $userInfo['userContactGroup'];
		$this->debug('Synchronizing contact #' . $dbContact->id . ' to ' . $userContactGroup->getUser()->email, 1);
		$user = $userContactGroup->getUser();
		$userGoogleContacts = array();
		foreach ($ci['gc'] as $gc) {
			if ($gc['userID'] == $userContactGroup->user_id) {
				$userGoogleContacts []= $gc['googleContact'];
			}
		}

		$modelUserContactGroupContacts = new Model_UserContactGroupContacts();
		$cclient = CRM_Core::getContactsClient($user);

		if (empty($ci['final-gc'])) {
			$this->debug('Final Google Contact is empty, skipping...', 2);
			return;
		}
		$finalGoogleContact = clone($ci['final-gc']);

		if (empty($userGoogleContacts)) {
			$everSynchronized = false;
			foreach ($userContactGroup->getUserContacts() as $userContact) {
				if (!empty($userContact->google_contact_id) and $userContact->contact_id == $dbContact->id) {
					#var_dump($user->getEmail() . ' - ' . $userContact->google_contact_id);
					$everSynchronized = true;
				}
			}

			if ($everSynchronized) {
				$this->debug('it appears user has deleted the contact, adding skipped', 2);
			} else {
				//przygotuj więcej info jak już wiemy nie tylko co, ale też komu chcemy synchronizować
				$this->debug('adding new contact', 2);
				$ci = $this->extendContactInfo($ci);
				$localGoogleContact = $this->finalizeGoogleContact($finalGoogleContact, $userInfo, null);

				try {
					$resultingGoogleContact = $cclient->createContact($localGoogleContact);
					$this->inserts ++;
					if (!empty($ci['final-gc-photo-data'])) {
						$cclient->putContactPhoto($resultingGoogleContact->id, $ci['final-gc-photo-mime'], $ci['final-gc-photo-data']);
					}

					//włożenie kontaktu db
					$row = $modelUserContactGroupContacts->createRow();
					$row->contact_id = $dbContact->id;
					$row->google_contact_id = $resultingGoogleContact->id;
					$row->user_contact_group_id = $userContactGroup->id;
					$row->save();
				} catch (Exception $e) {
					$this->debug('Exception: ' . $e->getMessage(), 3);
					$this->debug('XML', 3);
					$this->debug($finalGoogleContact->realXML, 3);
				}
			}
		} elseif ($ci['do-update']) {

			foreach ($userGoogleContacts as $googleContact) {
				$this->debug('updating existing contact: ' . $this->googleContactAsText($googleContact), 2);
				if ($googleContact->id == $ci['final-gc-id']) {
					$this->debug('skipped because target == master', 2);
				} else {
					//przygotuj więcej info jak już wiemy nie tylko co, ale też komu chcemy synchronizować
					$ci = $this->extendContactInfo($ci);
					$localGoogleContact = $this->finalizeGoogleContact($finalGoogleContact, $userInfo, $googleContact);

					try {
						$resultingGoogleContact = $cclient->updateContact($localGoogleContact);
						if (!empty($ci['final-gc-photo-data'])) {
							$cclient->putContactPhoto($finalGoogleContact->id, $ci['final-gc-photo-mime'], $ci['final-gc-photo-data']);
						}
						$this->debug('contact updated on: ' . date($this->view->translate('common_datetime_format'), $resultingGoogleContact->updated). ', last db write: '.date($this->view->translate('common_datetime_format'), strtotime($dbContact->date_synchronized)), 2);
						$this->updates ++;
					} catch (Exception $e) {
						$this->debug('Exception: ' . $e->getMessage(), 3);
						$this->debug('XML', 3);
						$this->debug($finalGoogleContact->realXML, 3);
					}
				}
			}
		}
		$ident = CRM_Core::getGoogleContactIdent($finalGoogleContact);
		$dbContact->updateIdent($ident);
	}

	public function migrateAction() {
		//header('Content-Type: text/plain');
		$p = $this->_request->getParams();
		if ($p['contact-group'] + 0 == 0) {
			die("No contact group ID\n");
		}

		$start = $this->microtime_float();
		$modelContacts = new Model_Contacts();
		$modelContactGroups = new Model_ContactGroups();
		$modelUserContactGroups = new Model_UserContactGroups();
		$modelUserContactGroupContacts = new Model_UserContactGroupContacts(); //brawo dla nazwy...

		if (isset($p['noflush']) && $p['noflush']) {
			$this->flush = false;
		}


		$contactGroup = $modelContactGroups->find($p['contact-group'])->current();
		
		
		
		while ($contactGroup->getRelatedContactGroupsActionCount()>0 ) {
			$this->debug("Waiting for users not to interfere " . $contactGroup->name);
			sleep(10);
		}
		
		
		$contactGroup->start();
		
		
		
		$this->debug("Phase 0: starting contact group " . $contactGroup->name);
		$users = array();
		$fetchAll = false; //defaultowo, tylko najnowsze
		foreach ($contactGroup->getUserContactGroups(true) as $tmpUserContactGroup) {
			$userContactGroup = $modelUserContactGroups->getByID($tmpUserContactGroup->id); #pobierz jeszcze raz z db żeby nie było read-only (patrz: join w ContactGroupsRow)
			$u = array();
			$u['userContactGroup'] = $userContactGroup;
			$u['user'] = $userContactGroup->getUser();
			$users[$u['user']->email]= $u;
			$this->debug("User's contact group name: " . $userContactGroup->getName(), 1);
			if ($userContactGroup->is_new) {
				$fetchAll = true;
				$this->debug('This is new user', 2);
			}
			if ($userContactGroup->fetch_all) {
				$fetchAll = true;
				$this->debug('This is user has fetch_all flag set', 2);
			}
		}
		
		
		if ($fetchAll) {
			$contactsMinDate = null; //zresetuj userom
			$this->debug('Going to fetch all contacts', 1);
		} else {
			$contactsMinDate = date('c', time() - 3600 );
			$this->debug('Going to fetch contacts newer than ' . $contactsMinDate, 1);
		}

		


		if (count($users) == 1) {
			$this->debug('Skipping synchronization because got only 1 user');
		} else {
			$this->debug("Phase 1: fetching info from Google");
			foreach ($users as $u) {
				extract($u);
				if ($userContactGroup->is_new) {
					$userContactGroup->is_new = 0;
					$userContactGroup->save();
					$u['userContactGroup'] = $userContactGroup;
				}

				try {
					$cclient = CRM_Core::getContactsClient($user);
					if ($user->disabled) {
						$user->disabled = null;
						$user->save();
					}
				} catch (Exception $e) {

					$user->disabled=date('c');
					$user->save();
					
					/*
					CRM_History::observe($user, 'contacts-disable', array(
						'contact-group-id' => $contactGroup->id,
						'contact-name' => $contactGroup->name,
						'user-mp' => $user->getDomain()->marketplace,
						'user-domain' => $user->getDomain()->domain_name,
						'user-name' => $user->name,
						'user-id' => $user->id,
						'user-email' => $user->email,
					));
					*/
					
					
					$this->debug('Invalid token/marketplace error for user ' . $user->email . ' (message: ' . $e->getMessage() . '); skipping', 1);
					continue;
				}
				//utwórz użytkownikowi grupę kontaktów
				try {
						
					$googleGroup = $cclient->getGroupByGroupName($userContactGroup->getName());
				} catch (Exception $e) {

					$user->disabled=date('c');
					$user->save();
					
					CRM_History::observe($user, 'contacts-disable', array(
						'contact-group-id' => $contactGroup->id,
						'contact-name' => $contactGroup->name,
						'user-mp' => $user->getDomain()->marketplace,
						'user-domain' => $user->getDomain()->domain_name,
						'user-name' => $user->name,
						'user-id' => $user->id,
						'user-email' => $user->email,
					));
					
					
					
					$this->debug('Invalid token/marketplace error for user ' . $user->email . ' (message: ' . $e->getMessage() . '); skipping', 1);
					continue;
				}
				
					
				
				if (empty($googleGroup)) {
					$googleGroup = $cclient->createGroup($userContactGroup->getName());
				}
				$u['googleGroupID'] = $googleGroup->id;
				$u['googleContacts'] = $cclient->getContactsByGroupID($googleGroup->id, $contactsMinDate);
				$users[$user->email] = $u;

				$this->debug('Contacts of ' . $u['user']->email . ' in group ' . $userContactGroup->getName() . ' (' . count($u['googleContacts']) . '):', 1);
				foreach ($u['googleContacts'] as $k => $googleContact) {
					$this->debug(($k + 1) . '. ' . $this->googleContactAsText($googleContact) . '(last updated: ' . date('c', $googleContact->updated) . ')', 2);
				}
			}



			//faza druga: wyciągamy info od userów i wrzucamy do db, o ile tam jeszcze jej nie ma
			$dbContacts = array();
			$this->debug('Phase 2: making db entries');
			foreach ($users as $u) {
				extract($u);
				$this->debug($user->email, 1);

				if (isset($googleContacts)) foreach ($googleContacts as $k => $googleContact) {
					//najpierw pobierz lub utwórz nowy kontakt z tabeli contact groups.
					$ident = CRM_Core::getGoogleContactIdent($googleContact);

					//nie dało się niczego powiedzieć o kontakcie (brak maili i telefonów)
					if (empty($ident)) {
						continue;
					}

					//sprawdź, czy user już był synchronizowany z danym kontaktem.
					$oldUserContactRow = $modelUserContactGroupContacts->getByUserContactGroupAndGoogleContact($userContactGroup->id, $googleContact);
					$everSynchronized = $oldUserContactRow != null;

					$select = $modelContacts
						->select(true)
						->join('contact_emails', 'contact_id = contacts.id', array())
						->where('email IN(?)', $ident)
						->where('contact_group_id = ?', $contactGroup->id)
						;
					$contactRow = $modelContacts->fetchRow($select);

					//znaleźliśmy kontakt, pobieramy
					if ($contactRow) {
						$select = $modelContacts->select()->where('id = ?', $contactRow->id);
						$contactRow = $modelContacts->fetchRow($select);
						$this->debug($this->googleContactAsText($googleContact). ' found db #' . $contactRow->id, 2);
					}

					if ($contactRow) {
						$oldContactRow = $contactRow->getUserContactByUserID($user->id);
						if (!empty($oldContactRow) and $oldContactRow->google_contact_id != $googleContact->id) {
							$this->debug($this->googleContactAsText($googleContact) . ': found different google id in #' . $contactRow->id . ' (' . $oldContactRow->google_contact_id . ')', 2);

							$unlink = null;
							$modelContactEmails = new Model_ContactEmails();
							$dbIdent = $modelContactEmails->getByContactID($contactRow->id);
							foreach ($dbIdent as $dbIdentRow) {
								if (in_array($dbIdentRow->email, $ident)) {
									$this->debug('Found same e-mail/phone:' . $dbIdentRow->email . ', splitting skipped', 3);
									$unlink = false;
									break;
								}
							}
							if ($unlink !== false) {
								$this->debug('No matching e-mail/phones found. Checking further.', 3);
								$unlink = false;
								$oldGoogleContact = null;
								foreach ($googleContacts as $subGoogleContact) {
									if ($subGoogleContact->id == $oldContactRow->google_contact_id) {
										$oldGoogleContact = $subGoogleContact;
									}
								}

								if (!empty($oldGoogleContact)) {
									$this->debug('User still has google contact with id from db', 3);
									$oldIdent = CRM_Core::getGoogleContactIdent($oldGoogleContact);
								} else {
									$this->debug('Couldn\'t find google contact ' . $googleContact->id, 3);
									$oldIdent = array();
									$unlink = true;
								}

								foreach ($dbIdent as $dbIdentRow) {
									if (!in_array($dbIdentRow->email, $oldIdent)) {
										$unlink = true;
										$this->debug('E-mail/phone ' . $dbIdentRow->email . ' is not in this google contact', 4);
									}
								}

								if ($unlink) {
									$this->debug('Going to add this contact as new', 3);
									foreach ($dbIdent as $dbIdentRow) {
										if (!in_array($dbIdentRow->email, $oldIdent)) {
											$this->debug('Removing e-mail/phone ' . $dbIdentRow->email . ' for #' . $contactRow->id, 4);
											$dbIdentRow->delete();
										}
									}

									$contactRow = $modelContacts->createRow();
									$contactRow->contact_group_id = $contactGroup->id;
									$contactRow->save();
									$this->debug($this->googleContactAsText($googleContact). ' added db #' . $contactRow->id, 3);

									foreach ($ident as $i) {
										$subrow = $modelContactEmails->createRow();
										$subrow->contact_id = $contactRow->id;
										$subrow->email = $i;
										$subrow->save();
									}

									$everSynchronized = false;
								} else {
									$this->debug('Google contact has all e-mails/phones from db, ignoring', 3);
								}
							}

						}
					}

					//nie udało się znaleźć kontaktu -- tworzymy nowy
					if (!$contactRow) {
						$contactRow = $modelContacts->createRow();
						$contactRow->contact_group_id = $contactGroup->id;
						$contactRow->save();
						$this->debug($this->googleContactAsText($googleContact). ' added db #' . $contactRow->id, 2);

						$modelContactEmails = new Model_ContactEmails();
						foreach ($ident as $i) {
							$subrow = $modelContactEmails->createRow();
							$subrow->contact_id = $contactRow->id;
							$subrow->email = $i;
							$subrow->save();
						}
					}

					if (!$everSynchronized) {
						//a tu wrzuć do UserContactGroupContacts nowy wiersz (link na "globalny" kontakt w labelu dla danego usera.)
						$row = $modelUserContactGroupContacts->createRow();
						$row->contact_id = $contactRow->id;
						$row->google_contact_id = $googleContact->id;
						$row->user_contact_group_id = $userContactGroup->id;
						$row->save();
						$this->debug($this->googleContactAsText($googleContact). ' added user db #' . $row->id, 2);
					}

					$dbContacts[$contactRow->id] = $contactRow;
				}
			}


			//faza trzecia: patrzymy, kto jest powyżej czasu ostatniej aktualizacji i dla takich osób synchronizujemy ich dane z pozostałymi.
			$this->debug('Phase 3: merging all known information (in memory)');
			$contactInfo = array();
			foreach ($dbContacts as $dbContact) {
				$contactInfo[$dbContact->id] = $this->makeContactInfo($dbContact);
			}


			//faza czwarta: wysyłamy finalnie zmergowane contactinfo do userów
			$this->debug('Phase 4: finally, synchronizing contacts');
			foreach ($dbContacts as $dbContact) {
				$ci = $contactInfo[$dbContact->id];
				foreach ($users as $u) {
					$this->synchronizeContact($u, $dbContact, $ci);
					if ($u['userContactGroup']->fetch_all) {
						$u['userContactGroup']->fetch_all = 0;
						$u['userContactGroup']->save();
					}
				}

				//tutaj oznacz glboalny kontakt z "labela" z tabeli contacts jako zupdatowany (umieść go w db jeśli go wcześniej tam nie było.)
				$dbContact->date_synchronized = CRM_Core::getNowUTC();
				$dbContact->save();
			}
		}
		
		$contactGroup->finish();
			

		$end = $this->microtime_float();
		$delta = round($end - $start, 2);
		$this->debug('Total insertions: ' . $this->inserts);
		$this->debug('Total updates: ' . $this->updates);
		$this->debug('End contact group ' . $contactGroup->name . ' in ' . $delta . ' sec.');
		die();
	}
}
