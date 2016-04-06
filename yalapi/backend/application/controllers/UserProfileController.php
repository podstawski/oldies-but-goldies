<?php

require_once 'RestController.php';

class UserProfileController extends RestController
{
    protected $_modelName = 'UserProfile';

    public function getAction()
    {
        $userID = $this->_getUserId();
        try {
            $profileRow = UserProfile::find_by_user_id($userID);
            $userRow    = User::find_by_id($userID);
            if ($profileRow === null) {
                $data = array_fill_keys(array_keys(UserProfile::table()->columns), null);
                $data['user_id'] = $userID;
            } else {
                $data = $profileRow->to_array();
            }
            $data['first_name'] = $userRow->first_name;
            $data['last_name']  = $userRow->last_name;
            $this->setRestResponseAndExit($data, self::HTTP_OK);
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    public function putAction()
    {
        $userID = $this->_getUserId();
        try {
            $putData = $this->_getRequestData('PUT');
            unset($putData['user_id'], $putData['id']);
            $userRow = User::find_by_id($userID);
            $userRow->set_attributes(array_intersect_key($putData, User::table()->columns));

            $profileRow = UserProfile::find_by_user_id($userID);
            if ($profileRow === null) {
                $profileRow = new UserProfile();
                $profileRow->user_id = $userID;
            }
            $profileRow->set_attributes(array_intersect_key($putData, UserProfile::table()->columns));

            if ($userRow->is_google
            && ($profileRow->field_has_changed('first_name') || $profileRow->field_has_changed('last_name'))
            && ($gappsClient = Yala_User::getGappsClient())
            ) {
                list ($login, ) = explode('@', $userRow->email);
                $googleUser = $gappsClient->retrieveUser($login);
                $googleUser->name->givenName  = $putData['first_name'];
                $googleUser->name->familyName = $putData['last_name'];
                $googleUser->save();
            }

            $userRow->save();
            $profileRow->save();

            $data = $profileRow->to_array();
            $data['first_name'] = $userRow->first_name;
            $data['last_name']  = $userRow->last_name;

            $this->setRestResponseAndExit($data, self::HTTP_OK);

        } catch (Zend_Gdata_App_Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_SERVER_ERROR);
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function indexAction()  { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function postAction()   { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
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

