<?php
class Model_Users extends Model_Abstract {
	protected $_name = 'users';
	protected $_rowClass = 'Model_UsersRow';

	const ROLE_TEACHER = 'teacher';
	const ROLE_ADMINISTRATOR = 'admin';
	const ROLE_SUPER_ADMINISTRATOR = 'superadmin';
	const ROLE_CLI = 'cli';

	public static function getAllRoles() {
		return array (
			self::ROLE_TEACHER,
			self::ROLE_ADMINISTRATOR,
			self::ROLE_SUPER_ADMINISTRATOR
		);
	}

	public static function compareRoles($role1, $role2) {
		if ($role1 == $role2) { return 0;
		} elseif ($role1 == self::ROLE_TEACHER) { return -1;
		} elseif ($role2 == self::ROLE_TEACHER) { return 1;
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

	public function fetchUser($email) {
		return $this->fetchRow(array('email = ?' => $email));
	}


	public static function getAll($mailSearch=null)
	{
		$model = new self();

		$select = $model->select(true)->
			setIntegrityCheck(false)->
			joinLeft(array('domains'),'domains.id=domain_id',
				 array('domain_expire'=>'expire','domain_trial_count'=>'trial_count', 'domain_token'=>'oauth_token', 'marketplace','admin_email'));

		if ($mailSearch) $select=$select->where("email ~* '$mailSearch'");

		$rowset = $model->fetchAll($select);

		return $rowset;
	}

    public function getByEmail($email) {
		$select = $this
			->select(true)
			->where('LOWER(email) = ?', strtolower($email))
			;
		return $this->fetchRow($select);
	}
}
