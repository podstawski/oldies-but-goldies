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

    protected $_nonblocking = false;
    protected $_old_adapter = null;

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


    public function request($method = null)
    {
        if (!$this->_nonblocking) {
            if ($this->_old_adapter) $this->config['adapter']=$this->_old_adapter;
            return parent::request($method);
        }

        try {
            $this->_old_adapter = $this->config['adapter'];
            $this->config['adapter']='GN_GClient_NBSocket';
            return parent::request($method);
        }

        catch (Zend_Http_Client_Exception $e) {
            throw new GN_GClient_NotReadyException('Try again in a while');
        }

    }

	public function getNonBlocking() {
		return $this->_nonblocking;
	}

	public function setNonBlocking($nb) {
		$this->_nonblocking = $nb;
	}

}
