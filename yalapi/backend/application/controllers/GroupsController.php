<?php

require_once 'RestController.php';

class GroupsController extends RestController
{
    public function indexAction()
    {
         if ($groupID = $this->_getParam('group_user_id')) {
            $groupRow = Group::first($groupID);
            $users = $groupRow->users;
             
            array_walk($users, function(&$item) {
                $role = $item->role->to_array();
                $item = $item->to_array();
                $item['full_name'] = $item['first_name'] . ' ' . $item['last_name'];
                if ($item['is_google']) {
                    $item['username'] = $item['email'];
                }
                unset($item['first_name'], $item['last_name']);
                $item['role'] = $role;
            });

            $countFlag = $this->_getParam('count_flag');
            $response = (intval($countFlag) !== 0) ? count($users) : $users;

            $this->setRestResponseAndExit($response, self::HTTP_OK);
         }

        parent::indexAction();
    }

    public function postAction()
    {
        $postData = $this->_getRequestData('POST');
        $import   = $this->_getParam('from_google');

        try
        {
            $group = new Group();
            $group->set_attributes(array_intersect_key($postData, Group::table()->columns));

            if ($group->google_group_id && Group::find_by_google_group_id($group->google_group_id)) {
                $this->setRestResponseAndExit('group with provided google id already exists', self::HTTP_CONFLICT);
            }

            if ($group->is_valid()) {
                if ($group->google_group_id
                && !$import
                && ($gappsClient = Yala_User::getGappsClient())
                ) {
                    if (!$gappsClient->retrieveGroup($group->google_group_id)) {
                        $gappsClient->createGroup($group->google_group_id, $group->name);
                    } else {
                        $this->setRestResponseAndExit('group with provided google id already exists in domain apps', self::HTTP_SERVER_ERROR);
                    }
                }

                $group->save();

                if (!$import) {
                    $users = (array) json_decode($postData['users']);

                    foreach ($users as $userID => $status) {
                        $row = new GroupUser();
                        $row->group_id = $group->id;
                        $row->user_id  = $userID;
                        $row->status   = $status;
                        $row->save();
                    }
                }

                $this->setRestResponseAndExit(null, self::HTTP_OK);
            } else {
                $this->_log($group);
                $this->setRestResponseAndExit('there was an error adding group to YALA', self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error adding group to Google Apps', self::HTTP_SERVER_ERROR);
        } catch (Zend_Gdata_Gapps_ServiceException $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error adding group to Google Apps', self::HTTP_SERVER_ERROR);
        } catch (Exception $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error adding group to YALA', self::HTTP_SERVER_ERROR);
        }
    }

    public function putAction()
    {
        try
        {
            $group = $this->_getById(true);
            $putData = $this->_getRequestData('PUT');
            unset($putData['id']);

            if ($group->google_group_id) {
                unset($putData['google_group_id']);
            }

            $group->set_attributes(array_intersect_key($putData, Group::table()->columns));

            if ($group->is_valid()) {
                $group->save();

                if (isset($putData['users'])) {
                    $users = (array) json_decode($putData['users']);

                    foreach (GroupUser::find_all_by_group_id($group->id) as $groupUser) {
                        $userID = $groupUser->user_id;
                        if (!array_key_exists($userID, $users)) {
                            $groupUser->delete();
                            continue;
                        }
                        if ($users[$userID] != $groupUser->status) {
                            $groupUser->status = $users[$userID];
                            $groupUser->save();
                        }
                        unset($users[$userID]);
                    }

                    foreach ($users as $userID => $status) {
                        $row = new GroupUser();
                        $row->group_id = $group->id;
                        $row->user_id = $userID;
                        $row->status = $status;
                        $row->save();
                    }
                }

                $this->setRestResponseAndExit(null, self::HTTP_OK);
            } else {
                $this->_log($group);
                $this->setRestResponseAndExit('there was an error updating group in YALA', self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('group not found', self::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error updating group in YALA', self::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function deleteAction()
    {
        try {
            $group = $this->_getById(true);
            $group->delete();
            $this->setRestResponseAndExit(null, self::HTTP_NO_CONTENT);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('group not found', self::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error deleting group in YALA', self::HTTP_SERVER_ERROR);
        }
    }

    protected function _getPagerOptionsForModel()
    {
        $options = parent::_getPagerOptionsForModel();
        $tableName = $this->_getTableNameFromModelClass('Group');
        $coursesTableName = $this->_getTableNameFromModelClass('Course');

        if (!array_key_exists('total_records', $options)){
            $options['select'] = "$tableName.id, $tableName.name, $tableName.advance_level AS advance_level_name, $tableName.advance_level, $tableName.google_group_id, COUNT(u.id) AS members, array_to_string(array(SELECT $coursesTableName.name FROM $coursesTableName WHERE $coursesTableName.group_id = $tableName.id), '#') AS courses";
        }

        $options['group']  = "$tableName.id, $tableName.name, $tableName.advance_level, $tableName.google_group_id";
        $options['joins']  = "LEFT JOIN group_users gu ON gu.group_id = $tableName.id
                              LEFT JOIN users u ON gu.user_id = u.id";
                              
        $options['from']   = $tableName;
        return $options;
    }


}

