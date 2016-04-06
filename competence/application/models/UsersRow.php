<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_UsersRow extends Zend_Db_Table_Row implements GN_Model_IDomainUser
{
    /**
     * @var Model_DomainsRow
     */
    protected $_domain;

	public function getAssociatedGroups()
	{
		$model = new Model_Groups();
		$select = $model
			->select(true)
			->setIntegrityCheck(false)
			->distinct('groups.id')
			->join('user_groups', 'user_groups.group_id = groups.id')
			->where('groups.domain_id = ?', $this->domain_id)
			->where('user_groups.user_id = ?', $this->id)
			;
		return $model->fetchAll($select);
	}

    /**
     * @return Model_DomainsRow
     */
    public function getDomain()
    {
        if ($this->_domain == null) {
            $this->_domain = $this->findParentRow('Model_Domains');
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

    /**
     * @return Zend_Gdata_Gapps
     */
    public function getGappsClient()
    {
        return new Zend_Gdata_Gapps(
            $this->getHttpClient(),
            $this->getDomain()->domain_name
        );
    }

    /**
     * @return Zend_Oauth_Client
     */
    public function getHttpClient($getRealToken = false /* even if we change token in active record, get real */)
    {
		$token = $this->getAccessToken($getRealToken);
		if (($token === null) or ($token === false))
		{
			return null;
		}
		return $token->getHttpClient(Zend_Registry::get('oauth_options'));
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return Model_Users::getRole($this->role);
    }

    /**
     * @return string
     */
    public function getRoleName()
    {
        return Model_Users::getRoleName($this->role);
    }

    /**
     * @param Zend_Oauth_Token_Access $token
     * @return Model_DomainsRow
     */
    public function setAccessToken(Zend_Oauth_Token_Access $token)
    {
        $this->token = base64_encode(serialize($token));
        return $this;
    }

    /**
     * @return Zend_Oauth_Token_Access
     */
    public function getAccessToken($getRealToken = false)
    {
		if ($getRealToken)
		{
			$token = $this->getTable()->find($this->id)->current()->token;
		}
		else
		{
			$token = $this->token;
		}
		return unserialize(base64_decode($token));
    }
}
