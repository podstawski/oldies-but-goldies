<?php

class WwwController extends Yala_Controller
{
    const PATTERN_STRING = '/^[a-zA-Z0-9 -_]+$/';
    const PATTERN_DATE   = '/^\d{2}\-\d{2}\-\d{4}$/';

    protected $_session;

    public function init()
    {
        $this->_helper->layout->setLayout('www');

        $this->_inputFilters = array(
            '*'        => 'StringTrim',
            'tc_name'  => 'StripTags',
            'courseID' => 'Digits',
            'pageID'   => 'Digits',
            'term'     => 'StripTags'
        );
        $this->_inputValidators = array(
            'tc_name'     => new Zend_Validate_Regex(self::PATTERN_STRING),
            'course_name' => new Zend_Validate_Regex(self::PATTERN_STRING),
            'start_date'  => new Zend_Validate_Regex(self::PATTERN_DATE),
            'end_date'    => new Zend_Validate_Regex(self::PATTERN_DATE),
            'courseID'    => array(
                new Zend_Validate_Int(),
                new Zend_Validate_GreaterThan(0)
            ),
            'pageID'      => array(
                new Zend_Validate_Int(),
                new Zend_Validate_GreaterThan(0)
            ),
            'field'       => new Zend_Validate_InArray(array('tc_name', 'course_name')),
            'term'        => new Zend_Validate_Regex(self::PATTERN_STRING)
        );

        parent::init();
        
        $this->_session = new Zend_Session_Namespace('www');
//        $this->_session->unsetAll();die();

        $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
        if ($googleapps['enabled']) {
            $domain = $this->_getParam('domain', null);
            if ($domain !== null) {
                if ($domain) {
                    if ($this->_db->select()->from('apps')->where('domain = ?', $domain)->query()->fetch(Zend_Db::FETCH_OBJ)) {
                        $this->_session->domain = $domain;
                    } else {
                        die('Nieprawidłowa domena');
                    }
                } else {
                    $this->_session->domain = null;
                }
            }

            if (isset($this->_session->domain)) {
                $db = $this->getInvokeArg('bootstrap')->getOption('db');
                $adapter = $db['adapter'];
                unset($db['adapter'], $db['prefix']);
                $db['dbname'] = Yala_User::getDbname($this->_session->domain);
                $this->_db = Zend_Db::factory('pdo_' . $adapter, $db);
                $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
                Yala_User::init(null, $this->_session->domain);
            } else {
//                die('Nie podano domeny');
            }
            
            $userData = $this->_getParam('userData');
            if (is_array($userData) && array_key_exists('email', $userData)) {
                $userData = array_intersect_key($userData, array_fill_keys(array('email', 'first_name', 'last_name'), null));
                list ($login, $domain) = explode('@', $userData['email']);
                if ($theSameDomain = (isset($this->_session->domain) && $this->_session->domain == $domain)) {
                    $username = $login;
                } else {
                    $username = Yala_User::cleanString($userData['email']);
                }
                Yala_User::setIdentity('admin');
                $userRow = User::find_by_username($username);
                if ($userRow == null) {
                    $userData['username'] = $username;
                    $userData['plain_password'] = User::generatePassword($userData['email']);
                    $userData['is_google'] = $theSameDomain;
                    $userRow = User::createUser($userData, Role::USER);
                } else {
                    $courses = array();
                    User::connection()->query_and_fetch('SELECT courses.id
                        FROM courses
                        INNER JOIN group_users ON group_users.group_id = courses.group_id
                        WHERE courses.status = 1
                        AND group_users.user_id = ' . $userRow->id,
                    function ($row) use (&$courses, $userRow) {
                        $courses[$row['id']] = $userRow->to_array();
                    });
                    $this->_session->my_courses = $courses;
                }
                Yala_User::setIdentity('own');
                $this->_session->user_data = $userRow->to_array();
            }
        }

        if ($this->_session->my_courses == null) {
            $this->_session->my_courses = array();
        }

        $this->view->my_courses = $this->_session->my_courses;
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    private function _getSelect()
    {
        return $this->_db
                    ->select()
                    ->from('courses', array('id', 'course_name' => 'name', 'code', 'start_date', 'end_date', 'price', 'color', 'description', 'project_id'))
                    ->join('training_centers', 'training_centers.id = courses.training_center_id', array('tc_name' => 'name', 'city', 'street', 'zip_code', 'tc_description' => 'description', 'phone_number'))
                    ->columns(array('empty_seats' => new Zend_Db_Expr('get_empty_seats(courses.id)')))
                    ->where('courses.status = 1')
                    ->where('courses.show_on_www = 1');
    }

    public function myCoursesAction()
    {
        $this->view->my_courses = $this->_getSelect()
            ->join('group_users', 'group_users.group_id = courses.group_id', array('status'))
            ->where('group_users.user_id = ?', $this->_session->user_data['id'])
            ->query()
            ->fetchAll(Zend_Db::FETCH_OBJ);
    }

    public function listAction()
    {
        $input = $this->_filterInput('tc_name', 'course_name', 'start_date', 'end_date', 'pageID');

        $select = $this->_getSelect()
                       ->order('courses.start_date ASC');

        if ($input->isValid('tc_name')) {
            $select->where('training_centers.name ILIKE \'%' . $input->tc_name . '%\'');
        }

        if ($input->isValid('course_name')) {
            $select->where('courses.name ILIKE \'%' . $input->course_name . '%\'');
        }

        $startDate = new DateTime();
        if ($input->isValid('start_date')) {
            $startDate = new DateTime($input->start_date);
        }
        $select->where('courses.start_date IS NOT NULL')
               ->where('CAST(courses.start_date AS DATE) >= CAST(\'' . $startDate->format('Y-m-d') . '\' AS DATE)');

        if ($input->isValid('end_date')) {
            $endDate = new DateTime($input->end_date);
            if ($startDate->diff($endDate)->invert) {
                $this->_flash('end date is earlier than start date', 'search');
            } else {
                $select->where('courses.end_date IS NOT NULL')
                       ->where('CAST(courses.end_date AS DATE) <= CAST(\'' . $endDate->format('Y.m.d') . '\' AS DATE)');
            }
        }

//        $startDate = new DateTime($input->start_date ?: 'now');
//
//        $endDate   = new DateTime($input->end_date   ?: 'now');
//
//        $select->where('courses.start_date IS NOT NULL')
//               ->where('courses.end_date IS NOT NULL')
//               ->where('(courses.start_date, courses.end_date) OVERLAPS (DATE \'' . $startDate->format('Y-m-d') . '\', DATE \'' . $endDate->format('Y-m-d') . '\')');

//        die('<pre>' . print_r($select, 1) . PHP_EOL);

        $pager = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $pager->setItemCountPerPage(10);
        $pageCount = $pager->getPages()->pageCount;
        if ($input->isValid('pageID')) {
            $pageID = max(1, min($input->pageID, $pageCount));
        } else {
            $pageID = 1;
        }
        $pager->setCurrentPageNumber($pageID);

        $this->view->pager = $pager;
        $this->view->input = $input;
    }

    public function viewAction()
    {
        $input = $this->_filterInput('tc_name', 'course_name', 'start_date', 'end_date', 'pageID', 'courseID');
        if ($input->isValid('courseID')) {
            $info = $this->_getSelect()
                         ->where('courses.id = ?', $input->courseID, Zend_Db::PARAM_INT)
                         ->query()
                         ->fetch();
            
            if ($info) {
                $info->hour_count = 0;

                if (!($info->empty_seats > 0)) {
                    $this->_flash('course group is full');
                }
                
                $schedule = $this->_db
                    ->select()
                    ->from('course_units')
                    ->join('users', 'users.id = user_id', array('first_name', 'last_name'))
                    ->where('course_id = ?', $input->courseID, Zend_Db::PARAM_INT)
                    ->order('course_units.id ASC')
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_OBJ);
                
                if ($schedule) {
                    $this->view->schedule = array();
                    foreach ($schedule as $row) {
                        $info->hour_count += $row->hour_amount;
                        $row->lessons = $this->_db
                            ->select()
                            ->from('lessons')
                            ->join('rooms', 'rooms.id = room_id', array('room_name' => 'name'))
                            ->joinLeft('users', 'users.id = user_id', array('user_id' => 'id', 'first_name', 'last_name'))
                            ->where('course_unit_id = ?', $row->id, Zend_Db::PARAM_INT)
                            ->order('start_date ASC')
                            ->query()
                            ->fetchAll();

                        $this->view->schedule[$row->name] = $row;
                    }
                }

                $subscribers = $this->_db
                    ->select()
                    ->from('courses', null)
                    ->join('group_users', 'group_users.group_id = courses.group_id', array('status'))
                    ->join('users', 'users.id = group_users.user_id', array('first_name', 'last_name'))
                    ->where('courses.id = ?', $input->courseID, Zend_Db::PARAM_INT)
                    ->order('last_name ASC')
                    ->query()
                    ->fetchAll();

                if ($subscribers) {
                    $this->view->subscribers = $subscribers;
                }

                $this->view->info = $info;
            } else {
                $this->_flash('course was not found');
            }
        }
        $this->view->input = $input;
    }

    protected function _subscribe($courseID, $userData, $formName = null)
    {
        Yala_User::setIdentity('admin');

        $db = ActiveRecord\Model::connection();
        $db->transaction();

        try {
            $course = Course::find($courseID);
            $userData = $course->subscribe($userData);
            $db->commit();

            Yala_User::setIdentity('own');

            $this->_session->my_courses[$courseID] = $userData;
            $this->_session->user_data = $userData;

            $this->_flash('you have been subscribed to this course', $formName);
            $this->_redirectExit('thanks', 'www', array('courseID' => $courseID));

        } catch (Exception $e) {
            $db->rollback();
            Yala_User::setIdentity('own');
            $this->_flash($e->getMessage(), $formName);
        }
    }

    public function subscribeAction()
    {
        $input = $this->_filterInput('courseID');
        if ($input->isValid('courseID')) {
            $info = $this->_getSelect()
                         ->join('projects', 'projects.id = courses.project_id', array('project_extra_fields' => 'extra_fields'))
                         ->where('courses.id = ?', $input->courseID, Zend_Db::PARAM_INT)
                         ->query()
                         ->fetch();
            
            if ($info) {
                $this->view->info = $info;
                if ($this->view->is_subscribed = array_key_exists($input->courseID, $this->_session->my_courses)) {
                    $this->_flash('you are already subscribed to this course');
                } elseif (!($info->empty_seats > 0)) {
                    $this->_flash('course group is full');
                } elseif (isset($this->_session->user_data)) {
                    $userData = $this->view->user_data = $this->_session->user_data;
                    if ($this->_getParam('confirm')) {
                        $this->_subscribe($input->courseID, $userData);
                    }
                } else {
                    $this->view->forms = array();
                    $config = new Zend_Config_Ini(APPLICATION_PATH . '/forms/subscribe.ini');

                    $form = new Form_Simple($config->full);

                    if ($extra = Project::getExtraFields($info->project_extra_fields))
                        $form->addDisplayGroup($extra, 'extra_fields', array(
                            'order' => 3,
                            'legend' => 'pola dodatkowe'
                        ));

                    $form->setAction($this->view->makeUrl('subscribe', 'www', array('courseID' => $input->courseID)));
                    $this->view->forms['full'] = $form;

                    $form = new Form_Simple($config->existing);
                    $form->setAction($this->view->makeUrl('subscribe', 'www', array('courseID' => $input->courseID)));
                    $this->view->forms['existing'] = $form;

                    if ($this->_hasParam('form_name')) {
                        $formName = $this->_getParam('form_name');
                        if (array_key_exists($formName, $this->view->forms) && $this->getRequest()->isPost()) {
                            $postData = $this->getRequest()->getPost();
                            $form =& $this->view->forms[$formName];
                            if ($form->isValid($postData)) {
                                $formData = $form->getValues();
                                $this->_subscribe($input->courseID, $formData);
                            } else {
                                $this->_flash('form has errors', $formName);
                            }
                        }
                    }
                }
            } else {
                $this->_flash('course was not found');
            }
        }
    }

    public function thanksAction()
    {
        $input = $this->_filterInput('courseID');
        if ($input->isValid('courseID') && array_key_exists($input->courseID, $this->_session->my_courses)) {
            $info = $this->_getSelect()
                         ->where('courses.id = ?', $input->courseID, Zend_Db::PARAM_INT)
                         ->query()
                         ->fetch();

            if ($info) {
                $this->view->info = $info;
                $this->view->user_data = $this->_session->my_courses[$input->courseID];
                $this->view->input = $input;
            } else {
                $this->_flash('course was not found');
            }
        } else {
            $this->_flash('direct access is not allowed');
        }
    }

    public function registrationFormAction()
    {
        $input = $this->_filterInput('courseID');
        if ($input->isValid('courseID') && array_key_exists($input->courseID, $this->_session->my_courses)) {
            $userData = $this->_session->my_courses[$input->courseID];
            require_once APPLICATION_PATH . '/services/RestClient.php';
            $restClient = new RestClient($this->_getBaseUrl(), $userData);
            if ($restClient->login()) {
                $url = "/reports/8/?user_id={$userData['user_id']}&course_id={$input->courseID}";
                $raport = $restClient->get($url);
                $restClient->logout();
                $this->getResponse()->setHeader('Content-Disposition', 'attachment;filename="report.pdf"', true)
                                    ->setHeader('Content-Type', 'application/pdf', true)
                                    ->sendHeaders();
                echo $raport;
                die();
            }
        }
        die('you do not have rights to perform this action');
    }

    public function autocompleteAction()
    {
        $input = $this->_filterInput('field', 'term');
        $data = array();
        if ($input->isValid('field') && $input->isValid('term')) {
            $select = null;
            switch ($input->field)
            {
                case 'tc_name':
                    $select = $this->_db
                        ->select()
                        ->distinct()
                        ->from('training_centers', array('name'))
                        ->where('name ILIKE \'%' . $input->term . '%\'')
                        ->order('name ASC');
                    break;

                case 'course_name':
                    $select = $this->_db
                        ->select()
                        ->distinct()
                        ->from('courses', array('name'))
                        ->where('name ILIKE \'%' . $input->term . '%\'')
                        ->order('name ASC');
                    break;
            }

            if ($select) {
                $data = $select->limit(10)
                               ->query()
                               ->fetchAll(Zend_Db::FETCH_COLUMN);
            }
        }
        die(json_encode($data));
    }
}