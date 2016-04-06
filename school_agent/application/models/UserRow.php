<?php
class Model_UserRow extends Zend_Db_Table_Row implements GN_Model_IDomainUser
{
    /**
     * @var Model_DomainRow
     */
	protected $_domain;

    /**
     * @return Model_DomainRow
     */
	public function getDomain()
	{
		if ($this->_domain == null) {
			$this->_domain = $this->findParentRow('Model_Domain');
		}
		return $this->_domain;
	}

    /**
     * @return string
     */
	public function getEmail()
    {
        return $this->email;
    }
}
?>
