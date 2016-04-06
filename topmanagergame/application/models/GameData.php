<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_GameData extends Zend_Db_Table_Abstract
{
    const ENGINE_RUN_EVERY = 'ENGINE_RUN_EVERY';
    const ENGINE_RUN_AT    = 'ENGINE_RUN_AT';

    const LAST_RANK_UPDATE = 'LAST_RANK_UPDATE';
    const LAST_ENGINE_RUN  = 'LAST_ENGINE_RUN';
    const NEXT_ENGINE_RUN  = 'NEXT_ENGINE_RUN';

    const US_TEXT = 'US_TEXT';
    const LOGIN_EMAILS = 'LOGIN_EMAILS';
    const LOGIN_EMAILS_ERROR = 'LOGIN_EMAILS_ERROR';

    protected $_name = 'game_data';
    protected $_primary = 'key';

    /**
     * @var Model_GameData
     */
    protected static $_instance;

    /**
     * @static
     * @return Model_GameData
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return void
     */
    public static function reset()
    {
        self::$_instance = null;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function getData($key, $defaultValue = null)
    {
        $row = self::getInstance()->fetchRow(array(
            'key = ?' => (string) $key
        ));
        if ($row) {
            return $row->value;
        } else if ($defaultValue !== null) {
            return self::setData($key, $defaultValue);
        }
        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function setData($key, $value)
    {
        $row = self::getInstance()->fetchRow(array(
            'key = ?' => (string) $key
        ));
        if ($row == null) {
            $row = self::getInstance()->createRow();
            $row->key = $key;
        }
        $row->value = $value;
        $row->save();
        return $value;
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function checkCanLogin($email, $throwException = true)
    {
        $dataArray = self::getData(self::LOGIN_EMAILS);
        if (!empty($dataArray)) {
            foreach (explode(PHP_EOL, $dataArray) as $tmp)
                if (strcasecmp(trim($tmp), $email) == 0)
                    return true;

            $modelUser = new Model_User;
            if ($modelUser->fetchRow(array('email = ?' => $email, 'role = ?' => Model_Player::ROLE_ADMIN)))
                return true;

//            if ($modelUser->fetchRow() == null)
//                return true;

            if ($throwException)
                throw new Exception(Model_GameData::getData(Model_GameData::LOGIN_EMAILS_ERROR, 'Sorry, you are not allowed to login'));

            return false;
        }
        return true;
    }
}