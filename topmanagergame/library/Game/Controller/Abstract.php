<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class Game_Controller_Abstract extends GN_Controller
{
    /**
     * @var Model_GameServerRow
     */
    protected $_gameServer;

    public function init()
    {
        parent::init();

        try {
            $this->_gameServer = Game_Server::init();
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Game_Exception_NoGameServer('game server does not exist');
        }
    }

    /**
     * @return GN_Plugin_Acl
     */
    public function getAcl()
    {
        return $this->getFrontController()->getPlugin('GN_Plugin_Acl');
    }

    protected function _redirectToDefaultGameServer()
    {
        $this->_redirectUrlExit(Game_Server::getDefaultGameServerUrl());
    }

    protected function _checkRegistrationClosed()
    {
        if (Model_Param::get('general.registration_closed')) {
            $this->_flash('Registration is closed');
            $this->_redirectToDefaultGameServer();
        }
    }

    protected function _checkLoginClosed()
    {
        if (Model_Param::get('general.login_closed')) {
            $this->_flash('Login is disabled');
            $this->_redirectToDefaultGameServer();
        }
    }

    /**
     * @return bool
     */
    protected function _isWizardEnabled()
    {
        $options = Zend_Registry::get('application_options');
        $options = $options['topmanager'];
        return $options['wizard'] == true;
    }

    /**
     * @param array|int|Model_UserRow $data
     * @return bool
     */
    protected function _doLogin($data)
    {
        $auth = Zend_Auth::getInstance();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('users')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');

        if (is_int($data)) {
            $modelUser = new Model_User();
            $data = $modelUser->find($data)->current();
        }

        $remote = $data instanceof Model_UserRow;
        if ($remote) {
            $data = $data->toArray();
        } else if (is_array($data)) {
            $authAdapter->setCredentialTreatment('MD5(?) AND (is_hidden IS NULL OR is_hidden <> 1)');
        } else {
            throw new Exception('data must be an array, integer or instance of Model_UserRow');
        }

        $authAdapter->setIdentity($data['username'])
                    ->setCredential($data['password']);

        $result = $auth->authenticate($authAdapter);

        if ($result->isValid()) {
            $data = $authAdapter->getResultRowObject();
            if (!empty($data->activation_code)) {
                $auth->clearIdentity();
                $this->_flash('please active your account ');
                $this->_redirectExit('login');
            }

            Model_GameData::checkCanLogin($data->email);
            Zend_Session::rememberMe();
            $auth->getStorage()->write($data);

            if (Game_Server::isDefaultGameServer() == true)
                $this->_redirectToDefaultGameServer();

            if ($remote) {
                $this->_flash(array('You have been logged in as %s', $data->username));
            } else {
                $this->_flash('You have been logged in');
            }

            $this->_redirectExit('index', 'office');
        } else {
            $this->_flash('Invalid login data, try again');
            $this->_redirectExit('login');
        }
    }
}