<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

require_once 'GN/Controller.php';

class GoogleAppsController extends GN_Controller
{
    const HTTP_OK                   = 200;
    const HTTP_CREATED              = 201;
    const HTTP_NO_CONTENT           = 204;
    const HTTP_BAD_REQUEST          = 400;
    const HTTP_UNAUTHORIZED         = 401;
    const HTTP_NOT_FOUND            = 404;
    const HTTP_METHOD_NOT_ALLOWED   = 405;
    const HTTP_NOT_ACCEPTABLE       = 406;
    const HTTP_CONFLICT             = 409;
    const HTTP_SERVER_ERROR         = 500;

    const SYNC_MODE_IMPORT = 'import';
    const SYNC_MODE_EXPORT = 'export';
    const SYNC_MODE_MERGE  = 'merge';

    /**
     * @var Zend_Gdata_Gapps
     */
    protected $_client;

    public static $syncModes = array(self::SYNC_MODE_IMPORT, self::SYNC_MODE_EXPORT, self::SYNC_MODE_MERGE);

    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function init()
    {
        parent::init();

        if (!($this->_client = Yala_User::getGappsClient())) {
            $this->setRestResponseAndExit(null, self::HTTP_SERVER_ERROR);
        }

        $actionName = $this->getFrontController()->getDispatcher()->formatActionName($this->getRequest()->getActionName());
        $actionName = substr($actionName, 0, -6);

        if (method_exists($this, $actionName)) {
            try {
                $this->$actionName();
            } catch (Zend_Gdata_Gapps_ServiceException $e) {
                $this->_handleServiceException($e);
            } catch (Zend_Gdata_App_HttpException $e) {
                $this->setRestResponseAndExit($e->getResponse()->getMessage(), self::HTTP_BAD_REQUEST);
            } catch (Exception $e) {
                $this->setRestResponseAndExit($e->getMessage(), self::HTTP_BAD_REQUEST);
            }
        }
    }

    public function indexAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_NO_CONTENT);
    }

    public function retrieveAllUsers()
    {
        $this->_aclHasRight('users', 'select');

        $data = array();
        $counter = 1;
        foreach ($this->_client->retrieveAllUsers() as $googleUser) {
            $data[] = array_merge(
                $this->_googleUserToArray($googleUser),
                array('id' => $counter++)
            );
        }
        $this->setRestResponseAndExit($data);
    }

    public function retrieveAllGroups()
    {
        $this->_aclHasRight('groups', 'select');

        $data = array();
        $counter = 1;
        foreach ($this->_client->retrieveAllGroups() as $googleGroup) {
            $data[] = array_merge(
                $this->_googleGroupToArray($googleGroup),
                array('id' => $counter++)
            );
        }
        $this->setRestResponseAndExit($data);
    }

    public function createUser()
    {
        $this->_aclHasRight('users', 'insert');

        $googleUser = call_user_func_array(array($this->_client, 'createUser'), $this->_getParams('username', 'first_name', 'last_name', 'password'));
        $this->setRestResponseAndExit($this->_googleUserToArray($googleUser));
    }

    public function deleteUserAction()
    {
        $this->_aclHasRight('users', 'delete');

        $this->_client->deleteUser($this->_getParam('username'));
        $this->setRestResponseAndExit();
    }

    public function retrieveUser()
    {
        $this->_aclHasRight('users', 'select');

        $googleUser = $this->_client->retrieveUser($this->_getParam('username'));
        $this->setRestResponseAndExit($this->_googleUserToArray($googleUser));
    }

    public function updateUser()
    {
        $this->_aclHasRight('users', 'update');

        $googleUser = $this->_client->retrieveUser($this->_getParam('username'));
        $googleUser->name->givenName  = $this->_getParam('first_name');
        $googleUser->name->familyName = $this->_getParam('last_name');
        try {
            $googleUser->login->password = $this->_getParam('password');
        } catch (InvalidArgumentException $e) {

        }
        $googleUser->save();
        $this->setRestResponseAndExit($this->_googleUserToArray($googleUser));
    }

    public function syncGroup()
    {
        $this->_aclHasRight('groups', 'insert');
        $this->_aclHasRight('group_users', 'insert');

        $groupID  = $this->_getParam('group_id');
        $syncMode = $this->_getParam('sync_mode');

        if (!in_array($syncMode, self::$syncModes)) {
            throw new Exception('invalid sync mode');
        }

        $group = Group::find_by_id($groupID);

        if (!$group) {
            throw new Exception('group not found');
        } else if (empty($group->google_group_id)) {
            throw new Exception('please set google group id');
        }

        $googleGroup = $this->_client->retrieveGroup($group->google_group_id);
        if ($googleGroup == null) {
            $googleGroup = $this->_client->createGroup($group->google_group_id, $group->name);
            sleep(1);
        }

        $googleGroupMembers = array();
        foreach ($this->_client->retrieveAllMembers($group->google_group_id)->getEntry() as $memberEntry) {
            $memberID = $memberEntry->property[1]->value;
            $googleGroupMembers[$memberID] = $memberEntry;
        }

        $groupMembers = array();
        foreach ($group->users as $memberEntry) {
            if (!empty($memberEntry->email)) {
                $groupMembers[$memberEntry->email] = $memberEntry;
            }
        }

        switch ($syncMode)
        {
            case self::SYNC_MODE_IMPORT:
                $group->name = $googleGroup->property[1]->value;
                $group->save();
                $this->_syncGroupImport($group, $groupMembers, $googleGroupMembers);
                break;

            case self::SYNC_MODE_EXPORT:
                $googleGroup = $this->_client->updateGroup($group->google_group_id, $group->name);
                $this->_syncGroupExport($group, $groupMembers, $googleGroupMembers);
                break;

            case self::SYNC_MODE_MERGE:
                $this->_syncGroupImport($group, $groupMembers, $googleGroupMembers, false);
                $this->_syncGroupExport($group, $groupMembers, $googleGroupMembers, false);
                break;
        }

        $this->setRestResponseAndExit();
    }

    /**
     * @param Group $group
     * @param array $groupMembers
     * @param array $googleGroupMembers
     * @param bool $del
     */
    private function _syncGroupImport(Group $group, array $groupMembers, array $googleGroupMembers, $del = true)
    {
        foreach ($googleGroupMembers as $memberID => $memberEntry) {
            if (!array_key_exists($memberID, $groupMembers)) {
                if ($user = User::find_by_email($memberID)) {
                    $groupUser = new GroupUser();
                    $groupUser->group_id = $group->id;
                    $groupUser->user_id  = $user->id;
                    $groupUser->save();
                }
            }
        }

        if ($del) {
            foreach ($groupMembers as $memberID => $memberEntry) {
                if (!array_key_exists($memberID, $googleGroupMembers)) {
                    if ($groupUser = GroupUser::find_by_group_id_and_user_id($group->id, $memberEntry->id)) {
                        $groupUser->delete();
                    }
                }
            }
        }
    }

    /**
     * @param Group $group
     * @param array $groupMembers
     * @param array $googleGroupMembers
     * @param bool $del
     */
    private function _syncGroupExport(Group $group, array $groupMembers, array $googleGroupMembers, $del = true)
    {
        foreach ($groupMembers as $memberID => $memberEntry) {
            if (!array_key_exists($memberID, $googleGroupMembers)) {
                $this->_client->addMemberToGroup($memberID, $group->google_group_id);
            }
        }

        if ($del) {
            foreach ($googleGroupMembers as $memberID => $memberEntry) {
                if (!array_key_exists($memberID, $groupMembers)) {
                    $this->_client->removeMemberFromGroup($memberID, $group->google_group_id);
                }
            }
        }
    }

    private function _aclHasRight($tableName, $operationType, $id = null)
    {
        $params = array($tableName, $id ?: 0, $operationType);
        if (!ActiveRecord\Model::connection()->query_and_fetch_one('SELECT acl_has_right(?, ?, ?)', $params)) {
            $this->setRestResponseAndExit('you are not allowed to perform this action', self::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @param Zend_Gdata_Gapps_ServiceException $e
     */
    private function _handleServiceException(Zend_Gdata_Gapps_ServiceException $e)
    {
        $error = current($e->getErrors())->getReason();
        $this->setRestResponseAndExit($error, self::HTTP_BAD_REQUEST);
    }

    /**
     * @param Zend_Gdata_Gapps_UserEntry $googleUser
     * @return array
     */
    private function _googleUserToArray(Zend_Gdata_Gapps_UserEntry $googleUser)
    {
        return array(
            'username'   => $googleUser->login->username,
            'first_name' => $googleUser->name->givenName,
            'last_name'  => $googleUser->name->familyName,
            'admin'      => $googleUser->login->admin ? 1 : 0
        );
    }

    private function _googleGroupToArray(Zend_Gdata_Gapps_GroupEntry $googleGroup)
    {
        return array(
            'google_group_id' => $googleGroup->property[0]->value,
            'name'            => $googleGroup->property[1]->value,
        );
    }

    /**
     * @param $paramName
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function _getParam($paramName)
    {
        $param = parent::_getParam($paramName);
        if (empty($param)) {
            throw new InvalidArgumentException('missing expected parameter "' . $paramName . '"');
        }
        return $param;
    }

    /**
     * @param $paramName
     * @return array
     */
    protected function _getParams($paramName)
    {
        $params = array();
        foreach (func_get_args() as $paramName) {
            $params[$paramName] = $this->_getParam($paramName);
        }
        return $params;
    }

    /**
     * @param mixed $data
     * @param int $code
     */
    public function setRestResponseAndExit($data = null, $code = self::HTTP_OK)
    {
        $oauth = new Zend_Session_Namespace('oauth');
        if ($oauth->tokenInvalid === true) {
            $data = 'invalid token';
        }

        if (!is_null($data)) {
            if (is_object($data)) {
                $data = (array) $data;
            }

            if (is_string($data)) {
                $data = array('message' => $this->view->translate($data));
            }

            $this->_response->setBody(json_encode($data));
        }

        $this->_response->setHttpResponseCode($code);
        $this->_response->sendHeaders();
        $this->_response->sendResponse();
        exit();
    }
}