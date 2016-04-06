<?php

require 'RestController.php';

class UsersController extends RestController
{
    protected $_autoPager = true;

    private function _getFrontendOptions()
    {
        $config = $this->getInvokeArg('bootstrap')->getOptions();

        $options = array();
        $options['appsEnabled'] = ($config['googleapps']['enabled']) ? 1 : 0;

        return $options;
    }

    public function indexAction()
    {
        $db = $this->getInvokeArg('bootstrap')->getOption('db');
        $username = $db['username'];
        $conditions = array("username <> '$username'");

        $params = array_intersect_key($this->getRequest()->getParams(), User::table()->columns);
        foreach ($params as $key => $value) {
            if (is_numeric($value)) {
                $conditions[] = "$key = $value";
            } else if (!empty($value)) {
                $conditions[] = "$key = '$value'";
            }
        }

        $users = User::find('all', array(
            'conditions' => implode(' AND ', $conditions),
            'select'     => 'id, CASE is_google WHEN 1 THEN email ELSE username END AS username, first_name, last_name, role_id, email',
            'from'       => 'users'
        ));
        array_walk($users, function(&$user) {
            $user = $user->to_array();
        });
        $this->setRestResponseAndExit($users, self::HTTP_OK);
    }

    public function getAction()
    {
        try {
            $item = $this->_getById();
            $row = $item->to_array();
            $row['role'] = $item->role->name;
            $row['username'] = sprintf('%s %s (%s)', $item->first_name, $item->last_name, $item->username);
            $this->setRestResponseAndExit($row, self::HTTP_OK);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->_log($e);
            $this->setRestResponseAndExit($this->view->translate('user with ID %s not found', $this->_getParam('id')), self::HTTP_NOT_FOUND);
        }
    }

    public function putAction()
    {
        $putData = $this->_getRequestData('PUT');
        
        $userId = intval($this->_getParam('id'));
        $userRow = User::find($userId);

        $adminDBUsername = $this->getInvokeArg('bootstrap')->getOption('db');
        $adminDBUsername = $adminDBUsername['username'];
        
        if ($userRow->username === $adminDBUsername) {
            $this->setRestResponseAndExit('you cant edit super user account', self::HTTP_CONFLICT);
        }

        $db = $this->_getDbAdapter();
        try {
            if ($db->fetchOne("SELECT acl_has_right('users', ?, 'update')", array($userRow->id))) {
                $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
                if ($userRow->is_google
                && $googleapps['profile_editable']
                && $gappsClient = Yala_User::getGappsClient()
                ) {
                    list ($login, ) = explode('@', $userRow->email);
                    $googleUser = $gappsClient->retrieveUser($login);
                    $googleUser->name->givenName = $putData['first_name'];
                    $googleUser->name->familyName = $putData['last_name'];
                    if ($googleapps['password_editable'] && isset($putData['plain_password'])) {
                        $googleUser->login->password = $putData['plain_password'];
                    }
                    $googleUser->save();
                }

                if ($userRow->is_google == false
                || ($userRow->is_google == true && $googleapps['profile_editable'])) {
                    $userRow->first_name = $putData['first_name'];
                    $userRow->last_name  = $putData['last_name'];
                    if (isset($putData['email']) && !$userRow->is_google) {
                        $userRow->email = $putData['email'];
                    }
                }

                if (isset($putData['role_id']) && Yala_User::getRoleId() == Role::ADMIN) {
                    $userRow->role_id = $putData['role_id'];
                }

                $userRow->save();

                if (!$userRow->is_google && isset($putData['plain_password'])) {
                    $db->fetchOne("SELECT update_password(?, ?, true)", array($userRow->username, $putData['plain_password']));
                }

                if ($userRow->id == Yala_User::getUid()) {
                    $userData = $userRow->to_array();
                    if ($userRow->is_google) {
                        $userData['plain_password'] = User::generatePassword($userData['email']);
                    }
                    Yala_User::init($userData);
                    Yala_User::setIdentity('own');
                }
            } else {
                $this->setRestResponseAndExit('You dont have rights to update users', self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error uptading user in google apps', self::HTTP_SERVER_ERROR);
        } catch (Zend_Gdata_Gapps_ServiceException $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error uptading user in google apps', self::HTTP_SERVER_ERROR);
        } catch (Exception $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error uptading user in YALA', self::HTTP_SERVER_ERROR);
        }
        $this->setRestResponseAndExit(null, self::HTTP_OK);
    }
    
    public function postAction()
    {
        if (!$this->_request->isPost()) {
            $this->setRestResponseAndExit(null, self::HTTP_METHOD_NOT_ALLOWED);
        }

        list ($username, $first_name, $last_name) = $this->_getParams(array('username', 'first_name', 'last_name'));

        $role = (int) $this->_getParam('role_id', Role::USER);

        if ($is_google = !!Yala_User::getDomain()) {
            $email = $username . '@' . Yala_User::getDomain();
        } else {
            $email = $this->_getParam('email', null);
        }

        $password = $this->_getParam('plain_password');
        $import = $this->_hasParam('import_from_apps');

        if (!$import && !$password) {
            $this->setRestResponseAndExit('please provide a password', self::HTTP_NOT_ACCEPTABLE);
        }

        try {
            if ($gappsClient = Yala_User::getGappsClient()) {
                if (!$import) {
                    if ($gappsClient->retrieveUser($username)) {
                        $this->setRestResponseAndExit($this->view->translate('google user with username %s already exists in domain %s', $username, Yala_User::getDomain()), self::HTTP_SERVER_ERROR);
                    }
                    if ($password) {
                        $gappsClient->createUser($username, $first_name, $last_name, $password);
                    }
                }
                $password = User::generatePassword($email);
            }

            Yala_User::setIdentity('admin');
            if (User::find_by_username($username)) {
                $this->setRestResponseAndExit($this->view->translate('user with username %s already exists', $username), self::HTTP_SERVER_ERROR);
            } else if (User::find_by_email($email)) {
                $this->setRestResponseAndExit($this->view->translate('user with email %s already exists', $email), self::HTTP_SERVER_ERROR);
            }

            $userRow = User::createUser(array(
                'username'       => $username,
                'plain_password' => $password,
                'first_name'     => $first_name,
                'last_name'      => $last_name,
                'email'          => $email,
                'is_google'      => $is_google
            ), $role);

            Yala_User::setIdentity('own');
            $this->setRestResponseAndExit($userRow->to_array(), self::HTTP_CREATED);
        } catch (Zend_Gdata_App_Exception $e) {
            Yala_User::setIdentity('own');
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error adding user to google apps', self::HTTP_SERVER_ERROR);
        } catch (Zend_Gdata_Gapps_ServiceException $e) {
            Yala_User::setIdentity('own');
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error adding user to google apps', self::HTTP_SERVER_ERROR);
        } catch (Exception $e) {
            Yala_User::setIdentity('own');
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error adding user to YALA', self::HTTP_SERVER_ERROR);
        }
    }
    
    public function deleteAction()
    {
        $user = $this->_getUserRow($this->_getParam('id'));

        $adminDBUsername = $this->getInvokeArg('bootstrap')->getOption('db');
        $adminDBUsername = $adminDBUsername['username'];

        if ($user->username === $adminDBUsername) {
            $this->setRestResponseAndExit('You can\'t delete main admin account', self::HTTP_CONFLICT);
        }

        $db = $this->_getDbAdapter();
        try {
            if ($db->fetchOne("SELECT acl_has_right('users', $user->id, 'delete')")) {
                if ($user->role_id == Role::COACH && CourseUnit::find_by_user_id($user->id)) {
                    throw new Exception('coach is assigned to some course units');
                }
                $db->fetchOne("SELECT delete_user('$user->username')");
            } else {
                $this->setRestResponseAndExit('You dont have rights to delete users', self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (Exception $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was en error deleting user in YALA', self::HTTP_NOT_ACCEPTABLE);
        }

        $this->setRestResponseAndExit(null, self::HTTP_NO_CONTENT);
    }

    public function remindPasswordAction()
    {
        //RB we need admin connection, because we're not logged in
        User::$connection = 'admin';
        Role::$connection = 'admin';

        if ($this->_request->isPost()) {
            $user = User::find_by_email($this->_getParam('email'));
            if ($user) {
                $key = sha1(time());
                $url = $this->_getBaseUrl() . '/remind_password/?request=' . $key;

                $mail = $this->_getMailer();
                $mail->addTo($user->email);
                $mail->setBodyText($this->_generateMailContent('remind_password_link.phtml', array(
                    'email' => $user->email,
                    'url' => $url,
                )));

                $mail->send();
                $user->key = $key;
                $user->save();
            }
            $this->setRestResponseAndExit(null, self::HTTP_OK);
        } else if ($this->_hasParam('request')) {
            $user = User::find_by_key($this->_getParam('request'));
            if (!$user) {
                die('Nie znaleziono klucza przypominania hasła.');
            }

            //RB if user has pass, just send current password
            if ($user->plain_password) {
                $pass = $user->plain_password;
            } else {
                $pass = substr(uniqid(), 0, 10);
                try {
                    $adapter = $this->_getDbAdapter();
                    $adapter->prepare("SELECT update_password(?, ?, true)")
                            ->execute(array($user->username, $pass));
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }
            $key = sha1(time());
            $url = $this->_getBaseUrl() . '/remind_password/?confirm=' . $key;

            $mailer = $this->_getMailer();
            $mailer->addTo($user->email);
            $mailer->setBodyText($this->_generateMailContent('remind_password_confirmation.phtml', array(
                'pass' => $pass,
                'user' => $user->username,
                'url' => $url,
            )));
            $mailer->send();
            $user->key = $key;
            $user->save();

            die('Wiadomość e-mail z danymi do konta została wysłana.');
        } else if ($this->_hasParam('confirm')) {
            $user = User::find_by_key($this->_getParam('confirm'));
            if (!$user) {
                die('Nie znaleziono klucza przypominania hasła.');
            }

            $user->key = null;
            $user->save();

            $_SESSION['REMIND_PASSWORD'] = array(
                'username' => $user->username,
                'password' => $user->plain_password
            );

            $this->_redirect($this->_getBaseUrl());
        } else {
            $this->setRestResponseAndExit(null, self::HTTP_BAD_REQUEST);
        }
    }

    protected function _getParams(array $paramNames) {
        $values = array();
        foreach ($paramNames as $paramName) {
            $value = $this->_getParam($paramName, null);
            if (!$value) {
                $this->setRestResponseAndExit($this->view->translate("Parameter '%s' not set", $paramName), self::HTTP_NOT_ACCEPTABLE);
            } else {
                $values[] = $value;
            }
        }
        return $values;
    }

    /**
     * @param array|null $additionalParams
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getDbAdapter(array $additionalParams = null)
    {
        $config = $this->getInvokeArg('bootstrap')->getOption('db');
        $adapter = $config['adapter'];
        unset($config['adapter'], $config['prefix']);

        if ($additionalParams) {
            $config = array_merge($config, $additionalParams);
        } else {
            $config['dbname'] = Yala_User::getDbname(Yala_User::getDomain());
        }

        return Zend_Db::factory('pdo_' . $adapter, $config);
    }

    protected function _getUserRow($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->setRestResponseAndExit($this->view->translate('user with ID %s not found', $userId), self::HTTP_NOT_FOUND);
        }
        return $user;
    }
    
    protected function _getPagerOptionsForModel()
    {
        $dbusername = $this->getInvokeArg('bootstrap')->getOption('db');
        $dbusername = $dbusername['username'];
        
        $options   = parent::_getPagerOptionsForModel();
        $tableName = $this->_getTableNameFromModelClass($this->_modelName);

        if (!array_key_exists('total_records', $options)) {
            $options['joins']  = "LEFT JOIN roles ON roles.id = $tableName.role_id LEFT JOIN user_profile ON user_profile.user_id = $tableName.id";
            $options['select'] = "$tableName.id, $tableName.first_name, $tableName.last_name, $tableName.is_google, $tableName.role_id, $tableName.plain_password, $tableName.email,
            $tableName.first_name || ' ' || $tableName.last_name AS full_name,
            CASE $tableName.is_google WHEN 1 THEN $tableName.email ELSE $tableName.username END AS username,
            roles.name AS role, role_id AS role_name,
            national_identity, address_street || ', ' || address_zip_code || ' ' || address_city AS full_address, update_date";
        }

        $condition = "($tableName.username <> '$dbusername')";
        if (array_key_exists('conditions', $options)) {
            $options['conditions'] .= ' AND ' . $condition;
        } else {
            $options['conditions'] = $condition;
        }
        
        return $options;
    }

    protected function _getMailer()
    {
        $config = $this->getInvokeArg('bootstrap')->getOption('mailer');
        Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Sendmail());
        $mail = new Zend_Mail('utf-8');
        $mail->setFrom($config['from'], $config['from_email']);
        $mail->setSubject('Aplikacja YALA - przypomnienie hasła');
        return $mail;
    }

    protected function _generateMailContent($template, array $values)
    {
        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . "/views/scripts/_emails");
        foreach ($values as $key => $value) {
            $view->assign($key, $value);
        }
        return $view->render($template);
    }
}
