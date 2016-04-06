<?php

require_once 'RestController.php';

class UserAccountController extends RestController
{
    public function getAction()
    {
        $userID = $this->_getUserId();
        try {
            $row = User::find($userID);
            $this->setRestResponseAndExit($row->to_array(), self::HTTP_OK);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }
    }
    
    public function putAction()
    {
        $userID = $this->_getUserId();
        try {
            $row = User::find($userID);
            
            $postData = $this->_getRequestData('PUT');
            $rowData  = $this->_validateData($postData);

            $db = $this->getInvokeArg('bootstrap')->getOption('db');
            $adapter = $db['adapter'];
            unset($db['adapter'], $db['prefix']);
            $adapter = Zend_Db::factory('pdo_' . $adapter, $db);
            
            if (array_key_exists('plain_password', $rowData)) {
                if ($db['username'] === $row->username) {
                    $this->setRestResponseAndExit('You can\'t change password for admin user', self::HTTP_CONFLICT);
                }
            }
            
            $row->set_attributes($rowData);
            if ($row->is_valid()) {
                $row->save();

                if (array_key_exists('plain_password', $rowData)) {
                    $adapter->prepare('SELECT update_password(?, ?, true)')
                            ->execute(array($row->username, $row->plain_password));
                }

                if ($row->id === Yala_User::getUid()) {
                    $data = $row->to_array();
                    $data['password'] = $row->plain_password;
                    unset($data['plain_password']);
                    $auth = Yala_User::getInstance();
                    $auth->getStorage()->write($data);
                }
                $this->setRestResponseAndExit($row->to_array(), self::HTTP_OK);
            } else {
                $this->setRestResponseAndExit($row->errors->get_raw_errors(), self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    private function _validateData($postData)
    {
        $rowData = array_intersect_key($postData, array_fill_keys(array('first_name', 'last_name', 'email'), null));
        if (isset($postData['new_password'])) {
            if ($postData['new_password'] != $postData['retype_password']) {
                throw new Exception('New and retyped passwords do not match');
            }
            $rowData['plain_password'] = $postData['new_password'];
        }
        return $rowData;
    }

    public function indexAction()  { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function deleteAction() { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }

    private function _getUserId()
    {
        $userID = intval($this->_getParam('id'));

        if (!$userID) {
            $this->setRestResponseAndExit('Please provide user ID', self::HTTP_NOT_ACCEPTABLE);
        } elseif ($userID != Yala_User::getUid() && Yala_User::getRoleId() != Role::ADMIN) {
            $this->setRestResponseAndExit('You do not have rights to perform this action', self::HTTP_UNAUTHORIZED);
        }

        return $userID;
    }
}

