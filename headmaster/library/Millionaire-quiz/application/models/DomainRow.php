<?php

class Millionaire_Model_DomainRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @param Zend_Oauth_Token_Access $token
     * @return Model_DomainRow
     */
    public function setAccessToken(Zend_Oauth_Token_Access $token)
    {
        $this->oauth_token = base64_encode(serialize($token));
        return $this;
    }

    /**
     * @return Zend_Oauth_Token_Access
     */
    public function getAccessToken()
    {
        return unserialize(base64_decode($this->oauth_token));
    }
}