<?php

require_once APPLICATION_PATH . '/views/helpers/FormatDay.php';

class Playgine_Task_CalculateInterests extends Playgine_Task_Abstract
{
    /**
     * @var bool
     */
    protected $_storeMessage = false;

    /**
     * @var Model_Message
     */
    private $_modelMessage;

    /**
     * @var Model_MessageUser
     */
    private $_modelMessageUser;

    /**
     * @var Model_Commitment
     */
    private $_modelCommitment;

    /**
     * @var Model_UserRow
     */
    private $_admin;

    public function init()
    {
        $this->_modelCommitment = new Model_Commitment();
        $this->_modelMessage = new Model_Message();
        $this->_modelMessageUser = new Model_MessageUser();

        $modelUser = new Model_User();
        $this->_admin = $modelUser->fetchRow(array(
            'role = ?' => Model_Player::ROLE_ADMIN
        ), 'id ASC');
    }

    public function run()
    {
        $today = $this->getCompany()->getToday();
        $params = Model_Param::get('commitment.penalty.interest_percentage');

        list ($month, $year, $from, $to) = Model_Day::getCurrentMonthParams($today);

        foreach ($this->_modelCommitment->fetchAll(array(
            'company_id = ?' => $this->getCompany()->id,
            'day <= ?' => $today
        )) as $commitment) {
            /**
             * @var Model_CommitmentRow $commitment
             */
            if ($commitment->isPenalty()) {

//                $delay = $today - $commitment->day;
//                $penalty = $commitment->cost / $delay;
//                $commitment->cost = $penalty * ($delay + 1);

                $commitment->cost += $commitment->object_id * 0.1;
                $commitment->save();
                continue;
            }

            if ($commitment->day != $today)
                continue;

            $interest = $commitment->calculateInterest();
            if ($interest > 0) {
                $commitmentName = Playgine_TaskFactory::getTaskNameByType($commitment->type);
                $this->_modelCommitment->insert(array(
                    'company_id' => $this->getCompany()->id,
                    'type'       => Playgine_TaskFactory::getTaskTypeByName($commitmentName . 'Penalty'),
                    'day'        => $today,
                    'cost'       => $interest,
                    'object_id'  => ceil($interest),
                ));
                $this->sendNotification($commitment, $interest);
            }
        }

        $this->getCompany()->checkOldestCommitment();
    }

    /**
     * @param Model_CommitmentRow $commitment
     * @param int $interest
     */
    private function sendNotification(Model_CommitmentRow $commitment, $interest)
    {
        $recipient = $this->getCompany()->findParentRow('Model_User');

        $view = new Zend_View();

        $message = $this->_modelMessage->createRow();
        $message->subject = $this->translate('commitment penalty subject');
        $message->body = $this->translate('commitment penalty body', array(
            $this->translate('CommitmentType:' . $commitment->type),
            $view->formatDay($commitment->day),
            $this->currency($interest),
        ));
        $message->send_date = date('c');
        $message->sender_id = $this->_admin->id;
        $message->recipient_list = $recipient->username;
        $message->save();

        $messageUser = $this->_modelMessageUser->createRow();
        $messageUser->message_id = $message->id;
        $messageUser->user_id = $recipient->id;
        $messageUser->save();
    }
}