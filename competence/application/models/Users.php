<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_Users extends Model_Abstract
{
	const ROLE_STUDENT = 1;
	const ROLE_TEACHER = 2;
	const ROLE_ADMINISTRATOR = 3;
	const ROLE_SUPER_ADMINISTRATOR = 4;

	const ROLE_NAME_STUDENT = 'student';
	const ROLE_NAME_TEACHER = 'teacher';
	const ROLE_NAME_ADMINISTRATOR = 'administrator';
	const ROLE_NAME_SUPER_ADMINISTRATOR = 'superadministrator';

	public static $roles = array(
	    self::ROLE_STUDENT => self::ROLE_NAME_STUDENT,
	    self::ROLE_TEACHER => self::ROLE_NAME_TEACHER,
	    self::ROLE_ADMINISTRATOR => self::ROLE_NAME_ADMINISTRATOR,
		self::ROLE_SUPER_ADMINISTRATOR => self::ROLE_NAME_SUPER_ADMINISTRATOR
	);

    protected $_name = 'users';
    protected $_rowClass = 'Model_UsersRow';

    protected $_referenceMap = array(
        'Domain' => array(
            'columns'       => 'domain_id',
            'refTableClass' => 'Model_Domains',
            'refColumns'    => 'id'
        ),
    );

    /**
     * @return string
     */
	public function getCount() 
	{
	    return $this->getAdapter()->fetchOne('SELECT COUNT(*) FROM ' . $this->_name);
	}

    /**
     * @param int $groupId
     * @return Zend_Db_Select
     */
	public function selectGroup($groupId)
	{
		return $this->select(true)
            ->setIntegrityCheck(false)
			->join('user_groups', 'users.id = user_groups.user_id', array('group_id', 'owner'))
			->where('user_groups.group_id = ?', $groupId, Zend_DB::PARAM_INT);
	}

	public function selectDomain($domainId)
	{
		return $this
			->select(true)
			->where('domain_id = ?', $domainId)
			;
	}

    /**
     * @static
     * @param int $roleID
     * @return string
     */
	public static function getRole($roleID)
    {
        return self::$roles[$roleID];
    }

    /**
     * @static
     * @param int $roleID
     * @return string
     */
    public static function getRoleName($roleID)
    {
        return Zend_Registry::get('Zend_Translate')->translate(self::getRole($roleID));
    }

    /**
     * @static
     * @return array
     */
    /*public static function getRoleSelectOptions()
    {
        $options = array();
        foreach (array_keys(self::$roles) as $roleID) {
            $options[$roleID] = self::getRoleName($roleID);
        }
        return $options;
    }*/

    /**
     * @param $email
     * @return Model_UsersRow
     */
    public function fetchUser($email)
    {
        return $this->fetchRow(array(
            'email = ?' => $email
        ));
    }
}
