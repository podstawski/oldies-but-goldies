<?php

class GapiController extends Zend_Controller_Action
{
    const HTTP_OK = 200;
    const HTTP_NO_CONTENT = 204;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 404;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_CONFLICT = 409;
    const HTTP_SERVER_ERROR = 500;

    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function init()
    {
        $googleapps = (object)$this->getInvokeArg('bootstrap')->getOption('googleapps');

        if (Yala_User::getEmail() == null) {
            $domain = $this->_getParam('domain');
            $email = $this->_getParam('email');

            if (!$domain) {
                $this->setResponseAndExit(self::HTTP_OK, 'no domain');
            }

            if (!$email) {
                $this->setResponseAndExit(self::HTTP_OK, 'no email');
            }

            if ($this->_getParam('sig') != GN_User::getSig($email, $googleapps->json_hash)) {
                $this->setResponseAndExit(self::HTTP_OK, 'invalid signature');
            }

            list ($ulogin, $udomain) = explode('@', $email);

            if ($domain == $udomain) {
                $username = $ulogin;
            } else {
                $username = Yala_User::cleanString($email);
            }
            $password = User::generatePassword($email);

            Yala_User::init(array(
                'username' => $username,
                'plain_password' => $password
            ), $domain);

            $userRow = User::find_by_email($email);
            if (!$userRow) {
                Yala_User::getInstance()->clearIdentity();
                $this->setResponseAndExit(self::HTTP_OK, 'user not found');
            }
            $userData = $userRow->to_array();
            $userData['password'] = $password;
            unset($userData['plain_password']);
            $userData['domain'] = $domain;
            Yala_User::updateIdentity($userData);
        }
    }

    public function setResponseAndExit($code, $data = null)
    {
        if ($data) {
            if (is_string($data)) {
                $data = array('message' => $data);
            } else if (is_object($data)) {
                $data = (array)$data;
            }
            $this->_response->setBody(json_encode($data));
        }
        $this->_response->setHttpResponseCode($code);
        $this->_response->sendHeaders();
        $this->_response->sendResponse();
        exit;
    }

    public function externalDashboardAction()
    {
        $data = array(
            'events' => Dashboard::getNewEventsData(date('j'), date('n'), date('Y')),
            'messages' => Dashboard::getNewMessagesCount(),
            'surveys' => Dashboard::getNewSurveysCount()
        );
        $this->setResponseAndExit(self::HTTP_OK, $data);
    }

    public function indexAction()
    {
        $this->setResponseAndExit(self::HTTP_NO_CONTENT);
    }
}