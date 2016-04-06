<?php
class Model_Users extends Model_Abstract {
	protected $_name = 'users';
	protected $_rowClass = 'Model_UsersRow';

	const ROLE_USER = 'user';
	const ROLE_ADMINISTRATOR = 'admin';
	const ROLE_SUPER_ADMINISTRATOR = 'superadmin';
	const ROLE_CLI = 'cli';

	public static function getAllRoles() {
		return array (
			self::ROLE_USER,
			self::ROLE_ADMINISTRATOR,
			self::ROLE_SUPER_ADMINISTRATOR
		);
	}

	public static function compareRoles($role1, $role2) {
		if ($role1 == $role2) { return 0;
		} elseif ($role1 == self::ROLE_USER) { return -1;
		} elseif ($role2 == self::ROLE_USER) { return 1;
		} elseif ($role1 == self::ROLE_ADMINISTRATOR) { return -1;
		} elseif ($role2 == self::ROLE_ADMINISTRATOR) { return 1;
		} elseif ($role1 == self::ROLE_SUPER_ADMINISTRATOR) { return -1;
		} elseif ($role2 == self::ROLE_SUPER_ADMINISTRATOR) { return 1;
		}
		return 0;
	}

	protected $_referenceMap = array(
		'Domain' => array(
			'columns'       => 'domain_id',
			'refTableClass' => 'Model_Domains',
			'refColumns'    => 'id'
			),
		);

	public function getCount() {
		return $this->getAdapter()->fetchOne('SELECT COUNT(*) FROM ' . $this->_name);
	}

	public function selectGroup($groupId) {
		return $this
			->select(true)
			->setIntegrityCheck(false)
			->join('user_groups', 'users.id = user_groups.user_id', array('group_id', 'owner'))
			->where('user_groups.group_id = ?', $groupId, Zend_DB::PARAM_INT)
			;
	}

	public function selectDomain($domainId) {
		return $this
			->select(true)
			->where('domain_id = ?', $domainId)
			;
	}

	public static function getRoleName($roleID) {
		return Zend_Registry::get('Zend_Translate')->translate($roleID);
	}

	public function getDisabled()
	{
		$select = $this
			->select(true)
			->where('disabled IS NOT NULL')
			->order ('disabled')
			;
			
		return $this->fetchAll($select);
	}
	
	
	public static function getAll($mailSearch=null)
	{
		$model = new self();

		$select = $model->select(true)->
			setIntegrityCheck(false)->
			joinLeft(array('domains'),'domains.id=domain_id',
				 array('marketplace','admin_email','domain_name','disabled'));

		if ($mailSearch) $select=$select->where("email ~* '$mailSearch'");

		
		
		$rowset = $model->fetchAll($select);

		return $rowset;
	}

    /**
     * @param $email
     * @return Model_UsersRow
     */
    public function getByEmail($email) {
		$select = $this
			->select(true)
			->where('LOWER(email) = ?', strtolower($email))
			;
		return $this->fetchRow($select);
	}

	public function getByIdentity($identity) {
		$select = $this
			->select(true)
			->where('identity = ?', $identity)
			;
		return $this->fetchRow($select);
	}

	public function getByDomainID($id) {
		$select = $this
			->select(true)
			->where('domain_id = ?', $id)
			;
		return $this->fetchAll($select);
	}

	public function getByID($id) {
		$select = $this
			->select(true)
			->where('id = ?', $id)
			;
		return $this->fetchRow($select);
	}
}
