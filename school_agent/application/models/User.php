<?php
class Model_User extends Model_Abstract
{
	protected $_name = 'users';
	protected $_rowClass = 'Model_UserRow';

	protected $_referenceMap = array
	(
		'Domain' => array
		(
			'columns'       => 'domain_id',
			'refTableClass' => 'Model_Domain',
			'refColumns'    => 'id'
		),
	);

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