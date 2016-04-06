<?php

require_once 'RestController.php';

class DashboardController extends RestController
{
    const NEW_USERS_GROUP_ID = 'new_users';

    public function indexAction()
    {
        $data = array_merge(
            array(
                'messages' => Dashboard::getNewMessagesCount(),
                'wizards' => array()
            ),
            Dashboard::getNewSurveysCount()
        );

        $wizards  = new Zend_Session_Namespace('wizards');
        $complete = $this->getRequest()->getParam('complete');

        $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');

        // SIM admin 'first steps' wizard
        if (Yala_User::getRoleId() == Role::ADMIN) {
            if ($wizards->FirstSteps == null) {
                $wizards->FirstSteps = Project::first() == null;
            }
            if ($complete == 'FirstSteps') {
                $wizards->FirstSteps = false;
                die();
            }
            if ($wizards->FirstSteps) {
                $data['wizards'][] = 'FirstSteps';
            }
        }

        // SIM user 'new student' wizard
        if ($googleapps['enabled']) {
            $uid = Yala_User::getUid();
            if ($wizards->NewStudent == null) {
                $userProfileRow = UserProfile::find_by_user_id($uid);
                $wizards->NewStudent = !($userProfileRow && $userProfileRow->printed);
            }
            if ($complete == 'NewStudent' && $userProfileRow = UserProfile::find_by_user_id($uid)) {
                $userProfileRow->printed = 1;
                $userProfileRow->save();
                
                $httpClient = Yala_User::getAccessToken()->getHttpClient(array(
                    'consumerKey'     => $googleapps['consumerKey'],
                    'consumerSecret'  => $googleapps['consumerSecret'],
                    'signatureMethod' => 'HMAC-SHA1',
                ));
                $gApps = new Zend_Gdata_Gapps($httpClient, Yala_User::getDomain());
                try {
                    $gApps->removeMemberFromGroup(Yala_User::getEmail(), self::NEW_USERS_GROUP_ID);
                } catch (Exception $e) {

                }
                $wizards->NewStudent = false;
                die();
            }
            if ($wizards->NewStudent) {
                $data['wizards'][] = 'NewStudent';
            }
        }

        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }

    public function getAction()    { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function deleteAction() { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function putAction()    { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
}