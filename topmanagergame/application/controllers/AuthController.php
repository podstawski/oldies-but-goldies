<?php

require_once 'UserController.php';
require_once 'GN/LightOpenID.php';

class AuthController extends Game_Controller_Abstract
{
    const PASSWORD_SALT = '79a5ff68d846e0eea97ba3f9b9c2118e';

    public function indexAction()
    {
        $this->_forward('open-id');
    }

    public function openIdAction()
    {
        $this->_checkLoginClosed();

        $openid = new LightOpenID($this->_getBaseUrl() . '/auth/open-id');
        $openid->identity = 'https://www.google.com/accounts/o8/id';
        $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');

        $modelUser = new Model_User();

        if (isset($_REQUEST['openid_ns'])) {
            if ($openid->mode === 'cancel') {
                throw new Exception('OpenID canceled');
            } else if (!$openid->validate()) {
                if ($this->_hasParam('email')) {
                    header('Location:  ' . $openid->authUrl());
                    exit;
                }
                throw new Exception('OpenID validation failed');
            } else {
                $attribues = $openid->getAttributes();
                Model_GameData::checkCanLogin($attribues['contact/email']);
                list (, $identity) = explode('=', $openid->data['openid_identity']);
                $userData = array(
                    'username'      => $attribues['contact/email'],
                    'email'         => $attribues['contact/email'],
                    'passwordClean' => md5($attribues['contact/email'] . self::PASSWORD_SALT),
                    'first_name'    => $attribues['namePerson/first'],
                    'last_name'     => $attribues['namePerson/last'],
                    'identity'      => $identity
                );

                $userRow = $modelUser->fetchUserByEmail($userData['email']);
                if ($userRow == null) {
                    $userRow = Game_Server::createGameUser($userData);
                } else if (empty($userRow->identity) || $userRow->identity != $userData['identity']) {
                    $userRow->identity = $identity;
                    $userRow->save();
                }

                $data = (object) $userRow->toArray();
                Zend_Auth::getInstance()->getStorage()->write($data);

                $this->rlogin($data->email);

                if (Game_Server::isDefaultGameServer() == true)
                    $this->_redirectToDefaultGameServer();

                $this->_flash('You have been logged in');
                $this->_redirectExit('index', 'office');
            }
        } else {
            if ($immediate = ($this->_hasParam('email')
                              && filter_var($this->_getParam('email'), FILTER_VALIDATE_EMAIL) !== false
                              && ($userRow = $modelUser->fetchRow(array('email = ?' => $this->_getParam('email')))) != null
                              && !empty($userRow->identity)
            )
            ) {
                $openid->identity .= '?id=' . $userRow->identity;
            }
            header('Location:  ' . $openid->authUrl($immediate));
            exit;
        }
    }
}