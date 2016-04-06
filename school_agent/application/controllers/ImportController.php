<?php
require_once 'AbstractController.php';

class ImportController extends AbstractController
{
    public function indexAction()
    {
        $this->_redirectExit('index', 'dashboard');
    }

	private function ignoreGroup($groupName) {
		return strpos($groupName, '*') !== false;
	}

	private function ignoreUser($userName) {
		return strpos($userName, '*') !== false;
	}

    /**
     * @param string $text
     * @return string
     */
    private function encodeToAscii($text)
    {
	
	$acc =	'É	Ê	Ë	š	Ì	Í	ƒ	œ	µ	Î	Ï	ž	Ð	Ÿ	Ñ	Ò	Ó	Ô	Š	£	Õ	Ö	Œ	¥	Ø	Ž	§	À	Ù	Á	Ú	Â	Û	Ã	Ü	Ä	Ý	';
	$str =	'E	E	E	s	I	I	f	o	m	I	I	z	D	Y	N	O	O	O	S	L	O	O	O	Y	O	Z	S	A	U	A	U	A	U	A	U	A	Y	';

	$acc.=	'Å	Æ	ß	Ç	à	È	á	â	û	Ĕ	ĭ	ņ	ş	Ÿ	ã	ü	ĕ	Į	Ň	Š	Ź	ä	ý	Ė	į	ň	š	ź	å	þ	ė	İ	ŉ	Ţ	Ż	æ	ÿ	';
	$str.=	'A	A	S	C	a	E	a	a	u	E	i	n	s	Y	a	u	e	I	N	S	Z	a	y	E	i	n	s	z	a	p	e	I	n	T	Z	a	y	';
	
	$acc.=	'Ę	ı	Ŋ	ţ	ż	ç	Ā	ę	Ĳ	ŋ	Ť	Ž	è	ā	Ě	ĳ	Ō	ť	ž	é	Ă	ě	Ĵ	ō	Ŧ	ſ	ê	ă	Ĝ	ĵ	Ŏ	ŧ	ë	Ą	ĝ	Ķ	ŏ	';
	$str.=	'E	l	n	t	z	c	A	e	I	n	T	Z	e	a	E	i	O	t	z	e	A	e	J	o	T	i	e	a	G	j	O	t	e	A	g	K	o	';
	
	$acc.=	'Ũ	ì	ą	Ğ	ķ	Ő	ũ	í	Ć	ğ	ĸ	ő	Ū	î	ć	Ġ	Ĺ	Œ	ū	ï	Ĉ	ġ	ĺ	œ	Ŭ	ð	ĉ	Ģ	Ļ	Ŕ	ŭ	ñ	Ċ	ģ	ļ	ŕ	Ů	';
	$str.=	'U	i	a	G	k	O	u	i	C	g	k	o	U	i	c	G	L	O	u	i	C	g	l	o	U	o	c	G	L	R	u	n	C	g	l	r	U	';
	
	$acc.=	'ò	ċ	Ĥ	Ľ	Ŗ	ů	ó	Č	ĥ	ľ	ŗ	Ű	ô	č	Ħ	Ŀ	Ř	ű	õ	Ď	ħ	ŀ	ř	Ų	ö	ď	Ĩ	Ł	Ś	ų	Đ	ĩ	ł	ś	Ŵ	ø	đ	';
	$str.=	'o	c	H	L	R	u	o	C	h	l	r	U	o	c	H	L	R	u	o	D	h	l	r	U	o	d	I	L	S	c	D	i	l	s	W	o	d	';
	
	$acc.=	'Ī	Ń	Ŝ	ŵ	ù	Ē	ī	ń	ŝ	Ŷ	Ə	ú	ē	Ĭ	Ņ	Ş	ŷ';
	$str.=	'I	N	S	w	u	E	i	n	s	Y	e	u	e	I	N	S	y';
	
	$acc.=	'Б	б	В	в	Г	г	Д	д	Ё	ё	Ж	ж	З	з	И	и	Й	й	К	к	Л	л	М	м	Н	н	П	п	О	о	Р	р	С	с	Т	т	У	у	Ф	ф	Х	х	Ц	ц	Ч	ч	Ш	ш	Щ	щ	Ъ	Ы	ы	Ь	Э	э	Ю	ю	Я	я';
	$str.=	'B	b	W	w	G	g	D	d	Yo	yo	Z	z	Z	z	I	i	N	n	K	k	L	l	M	m	H	h	P	p	O	o	P	p	S	s	T	t	U	u	f	F	Ch	h	C	c	C	c	Sz	sz	S	s	-	Y	y	-	E	e	Iu	iu	Ia	ia';
	
    
    
        return str_replace(explode("\t", $acc), explode("\t", $str), $text);
	
	
        return str_replace(array('ą', 'ć', 'ę', 'ł', 'ó', 'ń', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ó', 'Ń', 'Ś', 'Ź', 'Ż'), str_split('acelonszzACELONSZZ'), $text);
    }

    /**
     * @param array $input
     * @return array
     */
    private function removeDuplicateGroupMembers(array $input)
    {
        $output = array();
        for ($i = 0; $i < count($input); $i++) {
            $duplicate = false;
            for ($j = $i + 1; $j < count($input); $j++) {
                if ($input[$i] == $input[$j]) {
                    $duplicate = true;
                    break;
                }
            }
            if (!$duplicate) {
                array_push($output, $input[$i]);
            }
        }
        return $output;
    }

    /**
     * @param array $input
     * @return array
     */
    private function removeWildcardGroups(array $input)
    {
        $output = array();
        foreach ($input as $x) {
			if ($this->ignoreGroup($x['group'])) {
                continue;
            }
            $output [] = $x;
        }
        return $output;
    }

    /**
     * @param string $name
     * @param string $domain
     * @return string
     */
    private function generateGroupEmail($name, $domain)
    {
		if (strpos($name, '@') !== false) {
			return $name;
		}
        return sprintf
        (
            '%s@%s',
            strtolower($this->encodeToAscii($name)),
            strtolower($domain)
        );
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $domain
     * @return string
     */
    private function generateUserEmail($firstName, $lastName, $domain)
    {
        return sprintf(
            '%s.%s@%s',
            str_replace(' ', '.', strtolower($this->encodeToAscii($firstName))),
            str_replace(' ', '.', strtolower($this->encodeToAscii($lastName))),
            strtolower($domain)
        );
    }

    //tłumacz klucz wg listy definicji na jakieś rozpoznawalne przez nas
    //jeśli nie pasuje do żadnej definicji - null
    private function translateText($key, $definitions)
    {
        $key = $this->encodeToAscii($key);
        $key = strtolower($key);
        foreach ($definitions as $replacement => $aliases) {
            foreach ($aliases as $alias) {
                $alias = strtolower($alias);
				$alias = str_replace(' ', '', $alias);
				$alias = str_replace(str_split('ąćęłóńśźżĄĆĘŁÓŃŚŹŻ'), str_split('acelonszzACELONSZZ'), $alias);
                if (strpos($key, $alias) === 0) {
                    return $replacement;
                }
            }
        }
        return null;
    }

    //przetłumacz klucze we wszystkich kolumnach pojedynczego wiersza
    private function translateColumns(array $columns, array $definitions)
    {
        $newColumns = array();
        foreach ($columns as $key => $text) {
            $newKey = $this->translateText($key, $definitions);
            if ($newKey !== null) {
                $newColumns[$newKey] = $text;
            }
        }
        return $newColumns;
    }

    //przetłumacz kolumny wszystkich wierszy
    private function translateRows(array $rows, array $definitions)
    {
        foreach ($rows as $i => $row) {
            $rows[$i] = $this->translateColumns($row, $definitions);
        }
        return $rows;
    }


    public function getSimilarityPercentage(array $a, array $b)
    {
        $num1 = 100 - count(array_diff($a, $b)) * 100 / max(1, count($a));
        $num2 = 100 - count(array_diff($b, $a)) * 100 / max(1, count($b));
        return min($num1, $num2);
    }


	public function firstPhaseAction()
	{
		$this->_helper->layout->disableLayout();
		if (!$this->_hasParam('process-id') or !ClassGroup_Process::confirmProcessId($this->_getParam('process-id'))) {
			die($this->view->translate('misc_wrong_process'));
		}
		$progressID = $this->_getParam('progress-id');
		ClassGroup_Process::discardProcessId();
		ClassGroup_Session::stop();
		ClassGroup_Progress::detachBrowser();
		ClassGroup_Progress::start($progressID, 0, 1);

		try {
            if (!$this->_hasParam('spreadsheet')) {
                $this->finishError($this->view->translate('import_no_spreadsheet_specified_error'));
                return;
            }
            $spreadsheetId = $this->_getParam('spreadsheet');

			$this->addDebug('init spreadsheet');

            //zainicjuj spreadsheet
            $gclient = new GN_GClient($this->user);
            $domainName = $gclient->getDomain();
            $gapps = new ClassGroup_Gapps($gclient);
            $found = false;
            foreach ($gapps->getSpreadsheetList() as $spreadsheet) {
                if ($spreadsheet['id'] == $spreadsheetId) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->finishError($this->view->translate('import_wrong_spreadsheet_error', $spreadsheetId));
                return;
            }
            $worksheetIds = $gclient->getWorksheetsListData($spreadsheetId);
            foreach ($worksheetIds as $worksheetId => $worksheetTitle) {
				if ($this->ignoreGroup($worksheetTitle)) {
					continue;
				}
                if (strpos($worksheetTitle, ' ') !== false) {
                    $this->finishError($this->view->translate('import_worksheet_invalid_chars_error', $worksheetTitle));
                }
            }

            //zainicjuj co mamy zrobić
            $data = array
            (
                'spreadsheet-id' => $spreadsheetId,
                'spreadsheet-ops' => array(),
                'groups' => array(),
                'users' => array(),
            );

            //utwórz info o progressie
			$this->addDebug('retrieving google users');
            $googleUsers = $gapps->getUsers();
			$this->addDebug('retrieving google groups');
            $googleGroups = $gapps->getGroups();

			$this->addDebug('analyzing groups');
            //utwórz info co zrobić z grupami
            //przeiteruj po spreadsheetach
            foreach ($worksheetIds as $worksheetId => $worksheetTitle) {
                $group = array
                (
                    'name' => $worksheetTitle,
                    'action' => 'create',
                    'members' => array(),
                    'owners' => array(),
                );
                list ($groupId, $email) = $gapps->resolveName(
                    $this->generateGroupEmail($group['name'], $domainName)
                );
                $group['e-mail'] = $email;
                $group['id'] = $groupId;
                $data['groups'][$group['e-mail']] = $group;
            }

			$this->addDebug('analyzing users');
            //utwórz info co zrobić z userami - przeiteruj po arkuszach
            $definitions = array
            (
                'first-name' => array($this->view->translate('spreadsheet_header_first_name')),
                'last-name' => array($this->view->translate('spreadsheet_header_last_name')),
                'e-mail' => array($this->view->translate('spreadsheet_header_email')),
                'password' => array($this->view->translate('spreadsheet_header_password')),
                'owner-of' => array($this->view->translate('spreadsheet_header_ownership')),
            );
            foreach ($worksheetIds as $worksheetId => $worksheetTitle) {
                $worksheet = $gclient->getWorksheetData($spreadsheetId, $worksheetId);
                $worksheet = $this->translateRows($worksheet, $definitions);
                //przeiteruj po wierszach
                foreach ($worksheet as $rowIndex => $row) {
					if (!isset($row['e-mail'])) {
						$this->finishError($this->view->translate('import_missing_column_error', $worksheetTitle, $this->view->translate('spreadsheet_header_email')));
						return;
					} elseif (!isset($row['first-name'])) {
						$this->finishError($this->view->translate('import_missing_column_error', $worksheetTitle, $this->view->translate('spreadsheet_header_first_name')));
						return;
					} elseif (!isset($row['last-name'])) {
						$this->finishError($this->view->translate('import_missing_column_error', $worksheetTitle, $this->view->translate('spreadsheet_header_last_name')));
						return;
					} elseif (!isset($row['password'])) {
						$this->finishError($this->view->translate('import_missing_column_error', $worksheetTitle, $this->view->translate('spreadsheet_header_password')));
						return;
					} elseif (!isset($row['owner-of'])) {
						$this->finishError($this->view->translate('import_missing_column_error', $worksheetTitle, $this->view->translate('spreadsheet_header_ownership')));
						return;
					}
                    $user = array
                    (
                        'e-mail' => $row['e-mail'],
                        'first-name' => $row['first-name'],
                        'last-name' => $row['last-name'],
                        'password' => $row['password'],
                        'action' => 'create',
                    );

                    //jeśli jest to wildcard-meta user, to nie można go tworzyć ani usuwać, duh!
					if ($this->ignoreUser($user['e-mail'])) {
                        $user['action'] = 'do-nothing';

                        //w przeciwnym razie wypadałoby zrobić parę rzeczy
                    } else {
                        //generowanie e-maili
                        $makeUnique = false;
                        if (empty($user['e-mail'])) {
                            if (empty($user['first-name']) or empty($user['last-name'])) {
                                $this->finishError($this->view->translate('import_cannot_guess_email_error', $rowIndex + 1, $worksheetTitle));
                                return;
                            } else {
                                $user['e-mail'] = $this->generateUserEmail($user['first-name'], $user['last-name'], $domainName);
                            }
                            $user['e-mail-generated'] = true;
                            $makeUnique = true;
                        } elseif (strpos($user['e-mail'], '@') === false) {
                            $user['e-mail'] .= '@' . $domainName;
                            $user['e-mail-generated'] = true;
                            $makeUnique = true;
                        }

                        //zadbaj o to by generowany e-mail był unikalny
                        if ($makeUnique) {
                            $realMail = $user['e-mail'];
                            $newMail = $user['e-mail'];
                            $suffix = 1;
                            while (true) {
                                $ok = true;
                                //istnieje już taki user z wcześniejszego spreadsheetu?
                                if (isset($data['users'][$newMail])) {
                                    $ok = false;
                                }
                                //istnieje taki user w googlach?
                                foreach ($googleUsers as $googleUser) {
                                    if ($googleUser['e-mail'] == $newMail) {
                                        $ok = false;
                                    }
                                }
                                if ($ok) {
                                    $user['e-mail'] = $newMail;
                                    $user['e-mail-generated'] = true;
                                    break;
                                }
                                $newMail = str_replace('@', $suffix . '@', $realMail);
                                $suffix++;
                            }
                        }

                        //walidacja domeny
                        list ($userName, $userDomainName) = explode('@', $user['e-mail']);
                        if ($userDomainName != $domainName) {
                            $user['action'] = 'do-nothing';
                        }

                        //generowanie imienia i nazwiska
                        if (empty($user['first-name']) or empty($user['last-name'])) {
                            $index = strpos($userName, '.');
                            if ($index !== false) {
                                $user['first-name'] = ucfirst(substr($userName, 0, $index));
                                $user['last-name'] = ucfirst(substr($userName, $index + 1));
                            } else {
                                $user['first-name'] = ucfirst($userName);
                                $user['last-name'] = ucfirst($userName);
                            }
                            $user['first-name-generated'] = true;
                            $user['last-name-generated'] = true;
                        }

                        //generowanie haseł
                        if (empty($user['password'])) {
                            $user['password'] = '';
                            $alpha = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
                            foreach (range(1, 8) as $i) {
                                $user['password'] .= $alpha[mt_rand() % count($alpha)];
                            }
                            $user['password-generated'] = true;
                        }
                    }

                    if (isset($data['users'][$user['e-mail']])) {
                        $user['member-of'] = $data['users'][$user['e-mail']]['member-of'];
                        $user['owner-of'] = $data['users'][$user['e-mail']]['owner-of'];
                    } else {
                        $user['member-of'] = array();
                        $user['owner-of'] = array();
                    }

                    $email = $this->generateGroupEmail($worksheetTitle, $userDomainName);
                    list ($groupId) = $gapps->resolveName($email);
                    //członkostwo w grupach
                    $group = array(
                        'id' => $groupId,
                        'e-mail' => $email,
                        'name' => $worksheetTitle,
                    );
                    $user['member-of'] [] = $group;

                    //właścicielstwo grup
                    foreach (array_filter(array_map(function($x)
                    {
                        return trim($x);
                    }, explode(',', empty($row['owner-of']) ? '' : $row['owner-of']))) as $groupName) {
                        $email = $this->generateGroupEmail($groupName, $userDomainName);
                        list ($groupId) = $gapps->resolveName($email);
                        $group = array
                        (
                            'id' => $groupId,
                            'e-mail' => $email,
                            'name' => $groupName,
                        );
                        $user['owner-of'] [] = $group;
                    }

                    //zapamiętaj lokalizację (lub lokalizacje!) usera w worksheecie
                    if (isset($data['users'][$user['e-mail']])) {
                        $user['locations'] = $data['users'][$user['e-mail']]['locations'];
                    } else {
                        $user['locations'] = array();
                    }
                    $user['locations'] [] = array
                    (
                        'spreadsheet-id' => $spreadsheetId,
                        'worksheet-id' => $worksheetId,
                        'row-index' => $rowIndex + 2
                    );
                    $data['users'][$user['e-mail']] = $user;
                }
            }

			$this->addDebug('done analyzing users');

            if (!$this->validateData($data)) {
                $this->_redirectExit('index', 'dashboard');
            }

            $totalProgress = 1 + count($googleGroups);
            foreach ($googleUsers as $googleUser) {
                if (!isset($data['users'][$googleUser['e-mail']])) {
                    $totalProgress++;
                }
            }
            ClassGroup_Progress::start($progressID, 0, $totalProgress);
            ClassGroup_Progress::step($progressID);

			$this->addDebug('merging group and user information');

            foreach ($data['users'] as $user) {
                foreach (array_merge($user['owner-of'], $user['member-of']) as $group) {
                    if (!isset($data['groups'][$group['e-mail']])) {
                        $data['groups'][$group['e-mail']] = $group;
                    }
                }
            }
            foreach ($data['groups'] as $k => $group) {
                if (empty($group['members'])) {
                    $group['members'] = array();
                }
                if (empty($group['owners'])) {
                    $group['owners'] = array();
                }
                $data['groups'][$k] = $group;
            }


            //dopisz odpowiednim grupom danego użytkownika jako ownera i/lub usera
            foreach ($data['users'] as $user) {
                foreach ($user['member-of'] as $group) {
                    if (!in_array($user['e-mail'], $data['groups'][$group['e-mail']]['members'])) {
                        $data['groups'][$group['e-mail']]['members'] [] = $user['e-mail'];
                    }
                }
                foreach ($user['owner-of'] as $group) {
                    if (!in_array($user['e-mail'], $data['groups'][$group['e-mail']]['owners'])) {
                        $data['groups'][$group['e-mail']]['owners'] [] = $user['e-mail'];
                        if (!in_array($user['e-mail'], $data['groups'][$group['e-mail']]['members'])) {
                            $data['groups'][$group['e-mail']]['members'] [] = $user['e-mail'];
                        }
                    }
                }
            }

			$this->addDebug('analyzing google groups');
            //przeiteruj po googlowych grupach
            foreach ($googleGroups as $googleGroup) {
                $googleGroup = $gapps->getGroup($googleGroup['e-mail']);
                ClassGroup_Progress::step($progressID);
                //nie ma w spreadsheecie -> usuwamy
                if (!isset($data['groups'][$googleGroup['e-mail']])) {
                    $systemGroup = $googleGroup;
                    $systemGroup['action'] = 'remove';
                }
                //jest w spreadsheecie -> nic nie robimy
                else {
                    $systemGroup = $data['groups'][$googleGroup['e-mail']];
                    $percent1 = $this->getSimilarityPercentage($systemGroup['members'], $googleGroup['members']);
                    $percent2 = $this->getSimilarityPercentage($systemGroup['owners'], $googleGroup['owners']);
                    $percent = min($percent1, $percent2);
                    if ($percent == 100) {
                        $systemGroup['action'] = 'do-nothing';
                    } else {
                        $systemGroup['action'] = 'update';
                    }
                }
                $systemGroup['e-mail'] = $googleGroup['e-mail'];
                $systemGroup['name'] = $googleGroup['name'];
                $systemGroup['real-members'] = $googleGroup['members'];
                $systemGroup['real-owners'] = $googleGroup['owners'];
                $data['groups'][$systemGroup['e-mail']] = $systemGroup;
            }

			$this->addDebug('analyzing google users');
            //przeiteruj po userach z google
            foreach ($googleUsers as $googleUser) {
                //nie ma w spreadsheecie -> usuwamy
                if (!isset($data['users'][$googleUser['e-mail']])) {
					$googleUser2 = $gapps->getUser($googleUser['e-mail']);
					if (!empty($googleUser2)) {
						$googleUser = array_merge($googleUser, $googleUser2);
					} else {
						$googleUser['member-of'] = array();
						$googleUser['owner-of'] = array();
					}
                    ClassGroup_Progress::step($progressID);
                    $systemUser = $googleUser;
                    $systemUser['action'] = 'remove';
                }
                //jest w spreadsheecie -> nic nie robimy
                else {
                    $systemUser = $data['users'][$googleUser['e-mail']];
                    $systemUser['first-name'] = $googleUser['first-name'];
                    $systemUser['last-name'] = $googleUser['last-name'];
                    $systemUser['action'] = 'do-nothing';
                }
                $data['users'][$googleUser['e-mail']] = $systemUser;
            }

			$this->addDebug('skip wildcard groups and users');
            //nic nie rob z grupami ktore maja gwiazdke w nazwie
            foreach ($data['groups'] as $group) {
				if ($this->ignoreGroup($group['name'])) {
                    $group['action'] = 'do-nothing';
                    $data['groups'][$group['e-mail']] = $group;
                }
            }
			foreach ($data['users'] as &$user) {
				$m = array();
				foreach ($user['member-of'] as $group) {
					if (!$this->ignoreGroup($group['name'])) {
						$m []= $group;
					}
				}
				$user['member-of'] = $m;
				$m = array();
				foreach ($user['owner-of'] as $group) {
					if (!$this->ignoreGroup($group['name'])) {
						$m []= $group;
					}
				}
				$user['owner-of'] = $m;
			}
			unset($user);

			$this->addDebug('merging group and user information II');
            //właściciele i członkowie grup
            $data['members-to-remove'] = array();
            $data['owners-to-remove'] = array();
            $data['owners-to-add'] = array();
            $data['members-to-add'] = array();
            foreach ($data['groups'] as $group) {
                if ($group['action'] == 'update') {
                    foreach ($group['real-owners'] as $member) {
                        if (!in_array($member, $group['owners'])) {
                            $data['owners-to-remove'] [] = array('user' => $member, 'group' => $group['e-mail']);
                        }
                    }
                    foreach ($group['real-members'] as $member) {
                        if (!in_array($member, $group['members'])) {
                            $data['members-to-remove'] [] = array('user' => $member, 'group' => $group['e-mail']);
                        }
                    }
                    foreach ($group['owners'] as $member) {
                        if (!in_array($member, $group['real-owners'])) {
                            $data['owners-to-add'] [] = array('user' => $member, 'group' => $group['e-mail']);
                        }
                    }
                    foreach ($group['members'] as $member) {
                        if (!in_array($member, $group['real-members'])) {
                            $data['members-to-add'] [] = array('user' => $member, 'group' => $group['e-mail']);
                        }
                    }
                }
                if (($group['action'] == 'remove') or ($group['action'] == 'recreate')) {
                    foreach ($group['real-members'] as $member) {
                        $data['members-to-remove'] [] = array('group' => $group['e-mail'], 'user' => $member);
                    }
                    foreach ($group['real-owners'] as $member) {
                        $data['owners-to-remove'] [] = array('group' => $group['e-mail'], 'user' => $member);
                    }
                }
                if (($group['action'] == 'create') or ($group['action'] == 'recreate')) {
                    foreach ($group['members'] as $member) {
                        $data['members-to-add'] [] = array('group' => $group['e-mail'], 'user' => $member);
                    }
                    foreach ($group['owners'] as $member) {
                        $data['owners-to-add'] [] = array('group' => $group['e-mail'], 'user' => $member);
                    }
                }
            }
            foreach ($data['users'] as $user) {
                if (($user['action'] == 'remove') or ($user['action'] == 'recreate')) {
                    foreach ($user['member-of'] as $group) {
                        $data['members-to-remove'] [] = array('group' => $group['e-mail'], 'user' => $user['e-mail']);
                    }
                    foreach ($user['owner-of'] as $group) {
                        $data['owners-to-remove'] [] = array('group' => $group['e-mail'], 'user' => $user['e-mail']);
                    }
                }
                if (($user['action'] == 'create') or ($user['action'] == 'recreate')) {
                    foreach ($user['member-of'] as $group) {
                        $data['members-to-add'] [] = array('group' => $group['e-mail'], 'user' => $user['e-mail']);
                    }
                    foreach ($user['owner-of'] as $group) {
                        $data['owners-to-add'] [] = array('group' => $group['e-mail'], 'user' => $user['e-mail']);
                    }
                }
            }
            //pousuwaj duplikaty
            foreach (array('members-to-add', 'owners-to-add', 'members-to-remove', 'owners-to-remove') as $key) {
				$data[$key] = $this->removeDuplicateGroupMembers($data[$key]);
				$data[$key] = $this->removeWildcardGroups($data[$key]);
			}

			$this->addDebug('final validation');

            //zwaliduj jeszcze raz...
            if (!$this->validateData($data)) {
                $this->_redirectExit('index', 'dashboard');
            }

			$this->addDebug('add information about protected users');
            //dodaj info o tym kto jest protektowany
            $modelProtected = new Model_Protected();
            foreach ($data['groups'] as $key => $group) {
                $data['groups'][$key]['protected'] = $modelProtected->isProtected($group['e-mail']);
            }
            foreach ($data['users'] as $key => $user) {
                $data['users'][$key]['protected'] = $modelProtected->isProtected($user['e-mail']);
            }
            if (isset($data['users'][$this->user->email])) {
                $data['users'][$this->user->email]['protected'] = true;
                $data['users'][$this->user->email]['disabled'] = true;
            }

			$this->addDebug('analysis done!');
            ClassGroup_Session::restore();
            $_SESSION['action-data'] = $data;
            $this->finishSuccess();
		} catch (Exception $e) {
			$this->addCrashReport($e);
			$this->finishError($e->getMessage());
		}
    }

    public function confirmAction()
    {
        $this->view->data = $_SESSION['action-data'];
        $this->view->processID = ClassGroup_Process::generateProcessId();
		$client = new GN_GClient($this->user);
		$spreadsheet = $client->getSpreadsheetEntry($this->view->data['spreadsheet-id']);
		$this->view->spreadsheetTitle = $spreadsheet->title->text;
		$this->view->spreadsheetLink = $spreadsheet->getLink('alternate')->href;
    }

    private function finishAlert($message = false)
    {
        $progressID = $this->_getParam('progress-id');
        ClassGroup_Progress::finish($progressID, false);
        if (!empty($message)) {
            $this->addAlert($message);
        }
		die;
    }

    private function finishError($message = false)
    {
        $progressID = $this->_getParam('progress-id');
        ClassGroup_Progress::finish($progressID, false);
        if (!empty($message)) {
            $this->addError($message);
        }
		die;
    }

    private function finishSuccess($message = false)
    {
        $progressID = $this->_getParam('progress-id');
        ClassGroup_Progress::finish($progressID, true);
        if (!empty($message)) {
            $this->addSuccess($message);
        }
		die;
    }


    public function validateData($data)
    {
        $progressID = $this->_getParam('progress-id');

        //walidacja grup
        foreach ($data['groups'] as $group) {
            //walidacja nazwy
            if (empty($group['name'])) {
                $this->finishError($this->view->translate('import_no_group_name_specified_error'));
                return false;
            }
			if ($this->ignoreGroup($group['name'])) {
				continue;
			}
            if (!preg_match('/^[a-zA-Z0-9\._-]+$/', $group['id'])) {
                $this->finishError($this->view->translate('import_group_name_invalid_chars_error', $group['name']));
                return false;
            }
        }

        //walidacja użytkowników
        foreach ($data['users'] as $user) {
            //ignoruj wildcard meta-usera
			if ($this->ignoreUser($user['e-mail'])) {
				continue;
			}

            //walidacja e-maila
            list ($userName, $userDomainName) = explode('@', $user['e-mail']);

            if (!preg_match('/^[a-zA-Z0-9\._-]+$/', $userName)) {
                $this->finishError($this->view->translate('import_user_email_invalid_chars_error', $user['first-name'], $user['last-name'], $user['e-mail']));
                return false;
            }

            //walidacja haseł
            if ((($user['action'] == 'create') or ($user['action'] == 'recreate')) and (strlen($user['password']) < 8)) {
                $this->finishError($this->view->translate('import_user_password_too_short_error', $user['first-name'], $user['last-name']));
                return false;
            }

            //walidacja ownershipu
            if (!empty($user['owner-of'])) {
                foreach ($user['owner-of'] as $userGroup) {
                    $ok = false;
                    foreach ($data['groups'] as $group) {
                        if (($group['id'] == $userGroup['id']) and (($group['action'] == 'update') or ($group['action'] == 'recreate') or ($group['action'] == 'do-nothing') or ($group['action'] == 'create'))) {
                            $ok = true;
                        }
                    }
                    if (!$ok) {
                        $this->finishError($this->view->translate('import_user_ownership_conflict_error', $user['first-name'], $user['last-name'], $userGroup['id']));
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function secondPhaseAction()
    {
        $this->_helper->layout->disableLayout();
        if (!$this->_hasParam('process-id') or !ClassGroup_Process::confirmProcessId($this->_getParam('process-id'))) {
            die($this->view->translate('misc_wrong_process'));
        }
		$progressID = $this->_getParam('progress-id');
		//ClassGroup_Process::discardProcessId();
		ClassGroup_Session::stop();
		ClassGroup_Progress::detachBrowser();
		ClassGroup_Progress::start($progressID, 0, 1);

		try {
            ClassGroup_Session::restore();
            if (empty($_SESSION['action-data'])) {
                $this->finishError($this->view->translate('import_no_data_specified_error'));
                return;
            }
            $data = $_SESSION['action-data'];
            ClassGroup_Session::stop();

            if (!$this->validateData($data)) {
                $this->finishError($this->view->translate('import_no_data_specified_error'));
                return;
            }

			//nie usuwaj odznaczonych usuwanych userów
			$reallyRemoveUsers = $this->_getParam('remove-user');
			if (empty($reallyRemoveUsers)) {
				$reallyRemoveUsers = array();
			}
			foreach ($data['users'] as $key => $user) {
				if ($user['action'] == 'remove') {
					$reallyRemove = in_array($user['e-mail'], $reallyRemoveUsers);
					if (!$reallyRemove) {
						unset($data['users'][$key]);
					}
				}
			}

			//nie usuwaj odznaczonych usuwanych grup
			$reallyRemoveGroups = $this->_getParam('remove-group');
			if (empty($reallyRemoveGroups)) {
				$reallyRemoveGroups = array();
			}
			foreach ($data['groups'] as $key => $group) {
				if ($group['action'] == 'remove') {
					$reallyRemove = in_array($group['e-mail'], $reallyRemoveGroups);
					if (!$reallyRemove) {
						unset($data['groups'][$key]);
					}
				}
			}

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            //usuń wszystkie akcje po aktywnej akcji
            $modelAction = new Model_Action();
            $previousAction = $this->user->getDomain()->getActiveAction();
            if (!empty($previousAction)) {
                $modelAction->delete(array('id > ?' => $previousAction->id));
            }

            $activeAction = $modelAction->createRow();
            $activeAction->domain_id = $this->user->domain_id;
            $activeAction->save();

            //dodaj akcje usuwania memberów z grup
            foreach ($data['owners-to-remove'] as $ownerToRemove) {
                $activeAction->addStep(Model_ActionStep::TYPE_GROUP_OWNER_REMOVE, $ownerToRemove);
            }
            foreach ($data['members-to-remove'] as $memberToRemove) {
                $activeAction->addStep(Model_ActionStep::TYPE_GROUP_MEMBER_REMOVE, $memberToRemove);
            }

            //dodaj akcje usuwania grup
            foreach ($data['groups'] as $group) {
                if (($group['action'] == 'remove') or ($group['action'] == 'recreate')) {
                    $activeAction->addStep(Model_ActionStep::TYPE_GROUP_REMOVE, $group);
                }
            }

            //dodaj akcje usuwania użytkowników
            foreach ($data['users'] as $user) {
                if (($user['action'] == 'remove') or ($user['action'] == 'recreate')) {
                    $activeAction->addStep(Model_ActionStep::TYPE_USER_REMOVE, $user);
                }
            }

            //dodaj akcje tworzenia użytkowników
            foreach ($data['users'] as $user) {
                if (($user['action'] == 'create') or ($user['action'] == 'recreate')) {
                    if (!empty($user['password'])) {
                        $realPassword = $user['password'];
                        $user['password'] = sha1($user['password']);
                    } else {
                        $realPassword = '?';
                    }
                    $step = $activeAction->addStep(Model_ActionStep::TYPE_USER_CREATE, $user);
                    foreach ($user['locations'] as $location) {
                        if (!empty($user['first-name-generated'])) {
                            $activeAction->addStep(Model_ActionStep::TYPE_SPREADSHEET_UPDATE, array_merge($location, array('rely-on' => $step->id, 'col-index' => 1, 'value' => $user['first-name'])));
                        }
                        if (!empty($user['last-name-generated'])) {
                            $activeAction->addStep(Model_ActionStep::TYPE_SPREADSHEET_UPDATE, array_merge($location, array('rely-on' => $step->id, 'col-index' => 2, 'value' => $user['last-name'])));
                        }
                        if (!empty($user['e-mail-generated'])) {
                            $activeAction->addStep(Model_ActionStep::TYPE_SPREADSHEET_UPDATE, array_merge($location, array('rely-on' => $step->id, 'col-index' => 3, 'value' => $user['e-mail'])));
                        }
                        if (!empty($user['password-generated'])) {
                            $activeAction->addStep(Model_ActionStep::TYPE_SPREADSHEET_UPDATE, array_merge($location, array('rely-on' => $step->id, 'col-index' => 4, 'value' => $realPassword)));
                        }
                    }
                }
            }

            //dodaj akcje tworzenia grup
            foreach ($data['groups'] as $group) {
                if (($group['action'] == 'create') or ($group['action'] == 'recreate')) {
                    $activeAction->addStep(Model_ActionStep::TYPE_GROUP_CREATE, $group);
                }
            }

            //dodaj akcje dodawania memberów do grup
            foreach ($data['members-to-add'] as $memberToAdd) {
                $activeAction->addStep(Model_ActionStep::TYPE_GROUP_MEMBER_ADD, $memberToAdd);
            }
            foreach ($data['owners-to-add'] as $ownerToAdd) {
                $activeAction->addStep(Model_ActionStep::TYPE_GROUP_OWNER_ADD, $ownerToAdd);
            }


            $this->user->getDomain()->setActiveActionId($activeAction->id);
            $db->commit();

            $this->migrateAction();
		} catch (Exception $e) {
			$this->addCrashReport($e);
			$this->finishError($e->getMessage());
		}
    }


    public function migrateAction()
    {
        $this->_helper->layout->disableLayout();
        if ($this->_hasParam('process-id')
        and ClassGroup_Process::confirmProcessId($this->_getParam('process-id'))
        ) {
            $progressID = $this->_getParam('progress-id');
            ClassGroup_Process::discardProcessId();
            ClassGroup_Session::stop();
            ClassGroup_Progress::detachBrowser();
            ClassGroup_Progress::start($progressID, 0, 1);

            if (!$this->_hasParam('direction')) {
                $this->finishError($this->view->translate('import_no_direction_specified_error'));
            } elseif ($this->_getParam('direction') == 'backward') {
                $direction = Model_Action::DIRECTION_BACKWARD;
            } elseif ($this->_getParam('direction') == 'forward') {
                $direction = Model_Action::DIRECTION_FORWARD;
            } else {
                $this->finishError($this->view->translate('import_wrong_direction_error', $this->_getParam('direction')));
                return;
            }

            $activeAction = $this->user->getDomain()->getActiveAction();
            if ($activeAction == null) {
                $this->finishError($this->view->translate('import_no_action_error'));
            }

            //twórz klienta gapps
            $gclient = new GN_GClient($this->user);
            $domainName = $gclient->getDomain();
            $gapps = new ClassGroup_Gapps($gclient);

            $wrapper = new ClassGroup_ActionWrapper($gapps, $activeAction, $progressID);
            if ($direction == Model_Action::DIRECTION_BACKWARD) {
                $wrapper->backward();

                //ustaw aktywne id migracji na poprzednie lub puste jeśli nie ma poprzednika
                $modelAction = new Model_Action();
                $select = $modelAction->select(true)->where('id < ?', $activeAction->id)->order('id DESC');
                $rows = $modelAction->fetchAll($select);
                if ($rows->count() == 0) {
                    $newActionId = null;
                } else {
                    $newActionId = $rows->current()->id;
                }
                $this->user->getDomain()->setActiveActionId($newActionId);
            } elseif ($direction == Model_Action::DIRECTION_FORWARD) {
                $wrapper->forward();

                //skasuj wszystkie migracje po następnej
                $modelAction = new Model_Action();
                $select = $modelAction->select(true)->where('id > ?', $activeAction->id);
                $rows = $modelAction->fetchAll($select);
                foreach ($rows as $row) {
                    $row->delete();
                }
            }

            $steps = $activeAction->getSteps();
            $success = true;
            foreach ($steps as $step) {
                if ($step->result == 0) {
                    $success = false;
                }
            }
            if ($success) {
                $message =
                    $this->view->translate('import_finished_success_prefix') .
                        ' <a href="' . $this->view->url(array('controller' => 'import', 'action' => 'report', 'migration-id' => $activeAction->id), null, true) . '">' .
                        $this->view->translate('import_finished_success_link') .
                        '</a> ' .
                        $this->view->translate('import_finished_success_suffix');
                $this->finishSuccess($message);
            } else {
                $message =
                    $this->view->translate('import_finished_errors_prefix') .
                        ' <a href="' . $this->view->url(array('controller' => 'import', 'action' => 'report', 'migration-id' => $activeAction->id), null, true) . '">' .
                        $this->view->translate('import_finished_errors_link') .
                        '</a> ' .
                        $this->view->translate('import_finished_errors_suffix');
                $this->finishAlert($message);
            }
        }
    }

    public function reportAction()
    {
        if (!$this->_hasParam('migration-id')) {
            $this->addError($this->view->translate('import_no_migration_specified_error'));
            $this->_redirectExit('index', 'dashboard');
        }
        $modelAction = new Model_Action();
        $action = $modelAction->find(intval($this->_getParam('migration-id')))->current();
        if ($action == null) {
            $this->addError($this->view->translate('import_wrong_migration_error', intval($this->_getParam('migration-id'))));
            $this->_redirectExit('index', 'dashboard');
        }

        $this->view->action = $action;
        $this->view->actionSteps = $action->getExecutedSteps();
    }
}

?>
