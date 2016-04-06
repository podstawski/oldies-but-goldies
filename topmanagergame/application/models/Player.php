<?php
/**
 * @author Radosław Szczepaniak
 *
 * @method static int getUserId
 * @method static string getUsername
 * @method static int getRoleId
 * @method static string getPassword
 * @method static string getEmail
 * @method static string getIdentity
 * @method static string getFirstName
 * @method static string getLastName
 */
 
class Model_Player
{
    const ROLE_GUEST  = 0;
    const ROLE_USER   = 1;
    const ROLE_ADMIN  = 2;
    const ROLE_TESTER = 3;

    public static $roles = array(
        self::ROLE_GUEST  => 'guest',
        self::ROLE_USER   => 'user',
        self::ROLE_TESTER => 'tester',
        self::ROLE_ADMIN  => 'admin'
    );

    /**
     * @var int
     */
    private static $_userID;

    /**
     * @var Model_UserRow
     */
    private static $_user;

    /**
     * @var array
     */
    private static $_methodToFieldMap = array(
        'UserId'    => 'id',
        'Username'  => 'username',
        'RoleId'    => 'role',
        'Password'  => 'password',
        'Email'     => 'email',
        'Identity'  => 'identity',
        'FirstName' => 'first_name',
        'LastName'  => 'last_name',
    );

    /**
     * @var Model_CompanyRow
     */
    private static $_company;

    /**
     * @static
     * @param $userID
     */
    public static function init($userID)
    {
        self::$_userID = $userID;
        self::refresh();
    }

    /**
     * @static
     * @param $name
     * @param $params
     * @return null
     * @throws InvalidArgumentException
     */
    public static function __callStatic($name, $params)
    {
        if (substr($name, 0, 3) == 'get') {
            $name = substr($name, 3);
            if (array_key_exists($name, self::$_methodToFieldMap) == false) {
                throw new InvalidArgumentException("You can't retrieve value at key $name");
            }
            $key = self::$_methodToFieldMap[$name];
            return isset(self::$_user->$key) ? self::$_user->$key : null;
        }
    }

    /**
     * @static
     */
	public static function refresh()
	{
	    $modelUser = new Model_User;
	    self::$_user = $modelUser->find(self::$_userID)->current();
        self::$_company = null;
	}

    /**
     * @static
     * @return Model_CompanyRow
     */
    public static function getCompany()
    {
        if (self::$_company == null) {
            $modelUserToCompany = new Model_UserToCompany;
            $row = $modelUserToCompany->fetchRow(array(
                'email = ?' => self::getEmail()
            ));
            if ($row) {
                if ($row->user_id == null) {
                    $row->user_id = self::getUserId();
                    $row->save();
                }
                self::$_company = $row->findParentRow('Model_Company');
            }
        }
        return self::$_company;
    }

    /**
     * @static
     * @return int
     */
    public static function getToday()
    {
        return Model_Day::getToday() + Model_Param::get('general.game_rounds') - self::getCompany()->rounds_left;
    }

    /**
     * @return Model_UserRow
     */
    public static function getUser()
    {
        return self::$_user;
    }

    /**
     * @static
     * @return bool
     */
    public static function isAdmin()
    {
        return self::getRoleId() == self::ROLE_ADMIN;
    }

    /**
     * @return null
     */
    public static function getTeacherClassId()
    {
        return null;
    }

    /**
     * @static
     * @param int $userID
     * @return bool
     */
    public static function isTeacherOfStudent($userID)
    {
        $classID = self::getTeacherClassId();
        return  $classID && Zend_Db_Table::getDefaultAdapter()->fetchOne('SELECT 1 FROM school_class_member WHERE class_id = ? AND user_id = ? AND is_teacher = 0 AND status = 1 LIMIT 1', array($classID, $userID));
    }

    /**
     * @return bool
     */
    public static function isCompanyOwner()
    {
        return self::getUserId() == self::getCompany()->user_id;
    }
}
