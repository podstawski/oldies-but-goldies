<?php

class Millionaire_Model_UserRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @param Zend_Oauth_Token_Access $token
     * @return Model_DomainRow
     */
    public function setAccessToken(Zend_Oauth_Token_Access $token)
    {
        $this->access_token = base64_encode(serialize($token));
        return $this;
    }

    /**
     * @return Zend_Oauth_Token_Access
     */
    public function getAccessToken()
    {
        return unserialize(base64_decode($this->access_token));
    }

    /**
     * @return Zend_Oauth_Token_Access
     */
    public function getAccessTokenForDomain()
    {
        return $this->findParentRow('Model_Domain')->getAccessToken();
    }
}