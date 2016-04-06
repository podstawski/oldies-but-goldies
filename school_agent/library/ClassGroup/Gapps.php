<?php

class ClassGroup_Gapps
{
    /**
     * @var GN_GClient
     */
    private $_gclient;

    /**
     * @param GN_GClient $gclient
     */
    public function __construct(GN_GClient $gclient)
    {
        $this->_gclient = $gclient;
    }

    /**
     * @return GN_GClient
     */
    public function getGClient()
    {
        return $this->_gclient;
    }

    /**
     * @return GN_GClient
     */
    public function getGClientThreeLegged()
    {
        $gclient = $this->getGClient();
        if ($gclient->isTwoLegged() == true)
            $gclient = new GN_GClient($gclient->getUser(), GN_GClient::MODE_DOMAIN_THREE_LEGGED);

        return $gclient;
    }

    /**
     * @return array
     */
    public function getSpreadsheetList($location = null)
    {
        $spreadsheetFeed = $this->_gclient->getSpreadsheetsList($location);
        $spreadsheetList = array();
        /**
         * @var Zend_Gdata_Spreadsheets_SpreadsheetEntry $entry
         * @var Zend_Gdata_App_Extension_Author $author
         */
        foreach ($spreadsheetFeed->getEntry() as $entry) {
            $spreadsheet = array(
                'id'      => $this->_gclient->getDocumentID($entry),
                'title'   => (string) $entry->getTitle(),
                'link'    => $entry->getLink('alternate')->href,
                'authors' => array(),
            );

            foreach ($entry->getAuthor() as $author) {
                $spreadsheet['authors'][] = array(
                    'name'  => (string) $author->getName(),
                    'email' => strtolower((string) $author->getEmail())
                );
            }

            $spreadsheetList[] = $spreadsheet;
        }

        return $spreadsheetList;
    }

    /**
     * @param string $name
     * @return array
     */
    public function resolveName($email)
    {
        $name = $email;
        if (strpos($email, '@') !== false) {
            list ($name) = explode('@', $email);
        } else {
            $email .= '@' . $this->_gclient->getUser()->getDomain()->domain_name;
        }
        return array(
            strtolower($name),
            strtolower($email)
        );
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        $groups = array();

		$i = 0;
		while(true) {
			try {
				$result = $this->_gclient->retrieveAllGroups()->getEntry();
			} catch (Exception $e) {
				++ $i;
				if ($i > 3) {
					throw $e;
				}
				continue;
			}
			break;
		}

        foreach ($result as $googleGroup) {
            list ($groupId, $email) = $this->resolveName($googleGroup->property[0]->value);
			$group = array (
                'id' => $groupId,
                'e-mail' => $email,
                'name' => $googleGroup->property[1]->value,
				'description' => null,
            );
			if (!empty($googleGroup->property[4])) {
				$group['description'] = $googleGroup->property[4]->value;
			}
			$groups[] = $group;
        }
        return $groups;
    }

    /**
     * @param string $email
     * @return array
     */
    public function getGroup($groupEmail)
    {
        list ($groupId, $groupEmail) = $this->resolveName($groupEmail);
        $googleGroup = $this->_gclient->retrieveGroup($groupEmail);
        if ($googleGroup == null) {
			$googleGroup = $this->_gclient->retrieveGroup($groupEmail);
			if ($googleGroup == null) {
				return null;
			}
        }
        $group = array(
            'id' => $groupId,
            'e-mail' => $groupEmail,
            'name' => $googleGroup->property[1]->value,
            'members' => array(),
            'owners' => array(),
        );
		if (!empty($googleGroup->property[4]->value)) {
			$group['description'] = $googleGroup->property[4]->value;
		}

		try {
			/**
			 * @var Zend_Gdata_Gapps_MemberEntry $entry
			 */
			foreach ($this->_gclient->retrieveAllMembers($groupEmail)->getEntry() as $entry) {
				$type = strtolower($entry->property[0]->value);
				if ($type == 'group') {
					continue;
				}
				list ($username, $userEmail) = $this->resolveName($entry->property[1]->value);
				$group['members'][] = $userEmail;
			}

			foreach ($this->_gclient->retrieveGroupOwners($groupEmail)->getEntry() as $entry) {
				list ($username, $userEmail) = $this->resolveName($entry->property[0]->value);
				if (!in_array($userEmail, $group['members'])) {
					$group['members'][] = $userEmail;
				}
				$group['owners'][] = $userEmail;
			}
		} catch (Exception $e) {
			AbstractController::addCrashReport($e);
		}

        return $group;
    }

    /**
     * @param string $name
     * @param array $data
     * @return Zend_Gdata_Gapps_GroupEntry
     */
    public function createGroup($name, array $data = array())
    {
        if (!isset($data['name'])) {
            $data['name'] = $name;
        }
        list ($groupId, $email) = $this->resolveName($name);
        $group = $this->_gclient->newGroupEntry();
        $properties[0] = $this->_gclient->newProperty();
        $properties[0]->name = 'groupId';
        $properties[0]->value = $email;
        $properties[1] = $this->_gclient->newProperty();
        $properties[1]->name = 'groupName';
        $properties[1]->value = $data['name'];
        $properties[2] = $this->_gclient->newProperty();
        $properties[2]->name = 'emailPermission';
        $properties[2]->value = 'Domain';
        $properties[3] = $this->_gclient->newProperty();
        $properties[3]->name = 'permissionPreset';
        $properties[3]->value = 'TeamDomain';
        if (!empty($data['description'])) {
            $properties[4] = $this->_gclient->newProperty();
            $properties[4]->name = 'description';
            $properties[4]->value = $data['description'];
        }
        $group->property = $properties;
        return $this->getGClientThreeLegged()->insertGroup($group);
    }

    /**
     * @param $name
     * @return ClassGroup_Gapps
     */
    public function removeGroup($email)
    {
        list ($groupId, $email) = $this->resolveName($email);
        $this->getGClientThreeLegged()->deleteGroup($email);
        return $this;
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        $googleUsers = array();

		$i = 0;
		while(true) {
			try {
				$result = $this->_gclient->retrieveAllUsers();
			} catch (Exception $e) {
				++ $i;
				if ($i > 3) {
					throw $e;
				}
				continue;
			}
			break;
		}

        foreach ($result as $googleUser) {
            list ($username, $email) = $this->resolveName($googleUser->login->username);
            $googleUsers[] = array(
                'id'         => $username,
                'e-mail'     => $email,
                'first-name' => $googleUser->name->givenName,
                'last-name'  => $googleUser->name->familyName,
            );
        }
        return $googleUsers;
    }


    public function getUser($email)
    {
        list ($username, $email) = $this->resolveName($email);
        $googleUser = $this->_gclient->retrieveUser($email);
        if (!$googleUser) {
            return null;
        }
        $googleUser = array(
            'id'         => $username,
            'e-mail'     => $email,
            'first-name' => $googleUser->name->givenName,
            'last-name'  => $googleUser->name->familyName,
            'owner-of'   => array(),
            'member-of'  => array(),
        );
		try {
			foreach ($this->_gclient->retrieveGroups($email)->getEntry() as $entry) {
				list ($groupId, $email) = $this->resolveName($entry->property[0]->value);
				$user['member-of'][] = array(
					'e-mail' => $email,
					'name' => $entry->property[1]->value,
				);
			}
		} catch (Exception $e) {
			AbstractController::addCrashReport($e);
		}
        //todo: ustawiać owner-of

        return $googleUser;
    }

    /**
     * @param string $name
     * @param array $data
     * @return Zend_Gdata_Gapps_UserEntry
     */
    public function createUser($email, array $data = array())
    {
        list ($username, $email) = $this->resolveName($email);
        $user = $this->_gclient->newUserEntry();
        $user->login = $this->_gclient->newLogin();

		list (, $domainName) = explode('@', $email);
		if ($domainName != $this->_gclient->getUser()->getDomain()->domain_name) {
			$user->login->username = $email;
		} else {
			$user->login->username = $username;
		}

		try {
			$user->login->passwordHashFunc = 'SHA-1';
		} catch (Exception $e) {
			$user->login->hashFunctionName = 'SHA-1';
		}
        if (isset($data['password'])) {
            $user->login->password = $data['password'];
        } else {
            $user->login->password = sha1('default-password');
            $user->login->changePasswordAtNextLogin = true;
        }
        $user->name = $this->_gclient->newName();
        $user->name->givenName  = $data['first-name'];
        $user->name->familyName = $data['last-name'];
        return $this->getGClientThreeLegged()->insertUser($user);
    }

    /**
     * @param $name
     * @return ClassGroup_Gapps
     */
    public function removeUser($email)
    {
        list ($username, $email) = $this->resolveName($email);
		list (, $domainName) = explode('@', $email);
		if ($domainName != $this->_gclient->getUser()->getDomain()->domain_name) {
			$this->getGClientThreeLegged()->deleteUser($email);
		} else {
			$this->getGClientThreeLegged()->deleteUser($username);
		}
        return $this;
    }

    /**
     * @param $member
     * @param $group
     * @return ClassGroup_Gapps
     */
    public function removeMemberFromGroup($member, $group)
    {
        list ($username) = $this->resolveName($member);
        list ($groupId)  = $this->resolveName($group);
        $this->getGClientThreeLegged()->removeMemberFromGroup($member, $group);
        return $this;
    }

    /**
     * @param $member
     * @param $group
     * @return ClassGroup_Gapps
     */
    public function removeOwnerFromGroup($member, $group)
    {
        list ($username) = $this->resolveName($member);
        list ($groupId)  = $this->resolveName($group);
        $this->getGClientThreeLegged()->removeOwnerFromGroup($member, $group);
        return $this;
    }

    /**
     * @param $member
     * @param $group
     * @return ClassGroup_Gapps
     */
    public function addMemberToGroup($member, $group)
    {
        list ($username) = $this->resolveName($member);
        list ($groupId)  = $this->resolveName($group);
        $this->getGClientThreeLegged()->addMemberToGroup($member, $group);
        return $this;
    }

    /**
     * @param $member
     * @param $group
     * @return ClassGroup_Gapps
     */
    public function addOwnerToGroup($member, $group)
    {
        list ($username) = $this->resolveName($member);
        list ($groupId)  = $this->resolveName($group);
        $this->getGClientThreeLegged()->addOwnerToGroup($member, $group);
        return $this;
    }
}
