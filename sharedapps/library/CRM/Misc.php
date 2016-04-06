<?php
class CRM_Misc
{
    /**
     * @var Model_Users
     */
    protected static $_modelUsers;

	public static function isSpecialDomain($domain) {
		return $domain == 'gmail.com' or $domain == 'google.com';
	}

    /**
     * @return Model_Users
     */
    protected static function getUsersModel()
    {
        if (self::$_modelUsers == null)
            self::$_modelUsers = new Model_Users;
        return self::$_modelUsers;
    }

    /**
     * @param $email
     * @return Model_UsersRow
     */
    public static function findUser($email)
    {
        return self::getUsersModel()->getByEmail($email);
    }

    /**
     * @param $email
     * @return Model_UsersRow
     */
    public static function createUser($email)
    {
        $activeUser = Zend_Registry::get('user');

        static $modelDomains;
        if ($modelDomains == null)
            $modelDomains = new Model_Domains();

        list ($userName, $domainName) = explode('@', strtolower($email));
        $select = $modelDomains->select()->where('domain_name = ?', $domainName);
        $domainRow = $modelDomains->fetchRow($select);
        if (empty($domainRow)) {
            $domainRow = $modelDomains->createRow();
            $domainRow->domain_name = $domainName;
            $domainRow->org_name = $domainName;
            $domainRow->create_date = 'NOW()';
            $domainRow->save();
        }
        $userRow = self::getUsersModel()->createRow();
        $userRow->domain_id = $domainRow->id;
        $userRow->email = strtolower($email);
        $userRow->role = Model_Users::ROLE_USER;
        if ($activeUser) {
            $userRow->referer = $activeUser->email;
        }
        $userRow->save();
        return $userRow;
    }

    /**
     * @param string $email
     * @return Model_UsersRow
     */
    public static function findCreateUser($email)
    {
		$userRow = self::findUser($email);
        if ($userRow == null)
            $userRow = self::createUser($email);
        return $userRow;
	}

}

