<?php

require_once 'RestController.php';

class CalendarController extends RestController
{
    function putAction() {}
    function postAction() {}
    function getAction() {}
    function deleteAction() {}

    private $courseHash;
    private $_db;

    function indexAction()
    {
        $courseHash = $this->_getParam('course');
        $googleCalendarAction = $this->_getParam('googlecal');
        $domain = $this->_getParam('domain');

        if($courseHash !== null)
        {
            //Should log as a Google
            require_once APPLICATION_PATH . '/services/RestClient.php';
            $db = $googleapps = $this->getInvokeArg('bootstrap')->getOption('db');
            $userData = array('username' => $db['username'], 'password' =>$db['password']);

            $restClient = new RestClient($this->_getBaseUrl(), $userData);
            if ($restClient->login())
            {
                if($googleCalendarAction === null)
                {
                    $url = '/calendar/?course=' .  $courseHash . '&googlecal=true&domain=' . $domain;
                    $response = $restClient->get($url);

                    $this->getResponse()->setHeader('Content-Type', 'text/calendar; charset=UTF-8');
                    echo $response;
                }
                else
                {
                    Yala_User::init(NULL, $domain);
                    Yala_User::setIdentity('admin');
                    
                    $course = Course::find('first', array('conditions' => array('hash = ?', $courseHash)));
                    $trainingCenter = $course->training_center;
                    $courseUnits = $course->units;
                    $courseUnitsIds = array();
  
                    foreach($courseUnits as $courseUnit) {
                        $courseUnitsIds[] = $courseUnit->id;
                    }

                    $lessonModel = new Lesson();
                    $lessonModel->changeUseView(false);
                    $lessons = $lessonModel->getLessonBy(array('courseunits' => $courseUnitsIds));

                    $this->courseHash = $courseHash;
                    echo $this->getICal($course, $trainingCenter, $lessons, $courseUnits);
                }
            }
        }
    }

    private  function getICal($course, $trainingCenter, $lessons, $units)
    {
        require_once APPLICATION_PATH . '/../library/bennu/bennu.inc.php';
        $iCalendar = new iCalendar();
        $iCalendar->add_property('PRODID'           , 'PRODID:-//Yala Pro//PL'  );
        $iCalendar->add_property('CALSCALE'         , 'GREGORIAN'               );
        $iCalendar->add_property('METHOD'           , 'PUBLISH'                 );
        $iCalendar->add_property('X-WR-CALNAME'     , $course->name             );
        $iCalendar->add_property('X-WR-TIMEZONE'    , 'Europe/Warsaw'           );
        $iCalendar->add_property('X-WR-CALDESC'     , $trainingCenter->name     );

        $eventDateFormat    = 'Ymd\THis\Z';
        $eventLocation      = $trainingCenter->street . ', ' . $trainingCenter->zip_code . ', ' . $trainingCenter->city;

        $courseUnits = array();
        foreach($units as $unit)
        {
            $courseUnits[$unit->id] = $unit->name;
        }

        foreach($lessons as $row) {
            $date = new DateTime($row['start_date']);
            $row['start_date'] = $date->format($eventDateFormat);

            $date = new DateTime($row['end_date']);
            $row['end_date'] = $date->format($eventDateFormat);

            $date = new DateTime();
            $now = $date->format($eventDateFormat);

            $event = new iCalendar_event();
            $event->add_property('DTSTAMP'                       , $now                  );
            $event->add_property('DTSTART'                       , $row['start_date']    );
            $event->add_property('DTEND'                         , $row['end_date']      );
            $event->add_property('CREATED'                      , $row['start_date']    );
            $event->add_property('DESCRIPTION'                  , $row['coach_name'] . ', ' . $row['roomName']);
            $event->add_property('LAST-MODIFIED'                , $row['start_date']    );
            $event->add_property('LOCATION'                     , $eventLocation        );
            $event->add_property('SEQUENCE'                     , $row['sequence']      );
            $event->add_property('SUMMARY'                      , $row['text']          );
            $event->add_property('TRANSP'                       , 'OPAQUE'              );

            $iCalendar->add_component($event);
        }

        return $iCalendar->serialize();
    }
}

