<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

/**
 * @method Zend_Oauth_Token_Access getToken
 * @method GN_GClient_HttpClient setToken
 */
class GN_GClient_HttpClient extends Zend_Oauth_Client
{
    const REQUESTOR_PARAM_NAME = 'xoauth_requestor_id';

    /**
     * @var string
     */
    protected $_requestor;

    /**
     * @var bool
     */
    protected $_twoLegged;

    /**
     * @param GN_Model_IDomainUser|string $requestor
     * @return GN_GClient_HttpClient
     */
    public function setRequestor($requestor)
    {
        if ($requestor instanceof GN_Model_IDomainUser)
            $requestor = $requestor->getEmail();

        $this->_requestor = $requestor;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestor()
    {
        return $this->_requestor;
    }

    /**
     * @param bool $flag
     * @return GN_GClient_HttpClient
     */
    public function setTwoLegged($flag)
    {
        $this->_twoLegged = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTwoLegged()
    {
        return $this->_twoLegged;
    }

    public function prepareOauth()
    {
        if ($this->_twoLegged && $this->_requestor)
            $this->setParameterGet(self::REQUESTOR_PARAM_NAME, $this->_requestor);

        parent::prepareOauth();
    }
}
