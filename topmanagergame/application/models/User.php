<?php

/**
 * @author Radosław Szczepaniak
 */
class Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_rowClass = 'Model_UserRow';

    protected $_dependentTables = array('Model_Company', 'Model_GameServerUser');

    /**
     * Returns currently logged user
     * @return Model_UserRow 
     */
    public static function getCurrentUser()
    {
        $id = Model_Player::getUserId();
        if (!$id) {
            throw new Exception('You are not logged in');
        }
        $modelUser = new self();
        return $modelUser->find($id)->current();
    }

    /**
     * @param string $password
     * @return string
     */
    public static function encryptPassword($password)
    {
        return md5($password);
    }

    /**
     * @param string $email
     * @return Model_UserRow
     */
    public function fetchUserByEmail($email)
    {
        return $this->fetchRow(array(
            'email = ?' => $email
        ));
    }
}
