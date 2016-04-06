<?php

require_once 'RestController.php';

class DashboardEventsController extends RestController
{
    public function init()
    {
        parent::init();
        ActiveRecord\Serialization::$DATETIME_FORMAT = 'd-m-Y H:i:s';
    }
    
    public function indexAction()
    {
        $day = intval($this->_getParam('day'));
        if ($day < 1 || $day > 31) {
            $day = 1;
        }

        $month = $this->_getParam('month');
        if (!(is_numeric($month) && $month >= 1 && $month <= 12)) {
            $month = date('n');
        }

        $year = intval($this->_getParam('year'));
        if ($year == 0) {
            $year = date('Y');
        }

        $data = Dashboard::getNewEventsData($day, $month, $year);

        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }

    public function getAction()    { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function deleteAction() { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
    public function putAction()    { $this->setRestResponseAndExit('method not used', self::HTTP_BAD_REQUEST); }
}

