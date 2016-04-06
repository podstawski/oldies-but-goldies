<?php

class EmployeeController extends Game_Controller
{
//    public function hireAction()
//    {
//        $task = Playgine_TaskFactory::factory('HireEmployee');
//        $message = $this->runTask($task);
//        $this->_flash($message);
//        $this->_redirectExit('index', 'office');
//    }

    public function fireAction()
    {
        $this->view->type = $this->_getParam('type', 0);

        if ($this->getRequest()->isPost()) {
            $task = Playgine_TaskFactory::factory('FireEmployee');
            $task->setOptions($this->getRequest()->getPost());
            $message = $this->runTask($task);
            $this->_flash($message);
        }

        $this->_redirectBack();
    }

    public function trainAction()
    {
        if ($this->_company->getCanTrain()) {
            $task = Playgine_TaskFactory::factory('TrainEmployee');
            $task->setOption('type', $this->_getParam('type'));
            $message = $this->runTask($task);
        } else {
            $message = 'you can train employees only once per round';
        }
        $this->_flash($message);
        $this->_redirectBack();
    }

    public function indexAction()
    {
        if ($neededManagers = $this->_company->checkManagers()) {
            $this->_flash(array(
                'you have %s managers, you can assign max %s workers',
                $this->_company->getManagers()->getMaxAmount(),
                $this->_company->getWorkers()->getMaxAmount(),
            ));
        }
    }

    public function manageAction()
    {
        $this->view->type = $this->_getParam('type', 0);

        $pageID = $this->_getParam('pageID', 1);

        $select = $this->_db
                       ->select()
                       ->from('company_employee', array('id', 'fired'))
                       ->join('employee_cv', 'employee_cv.id = company_employee.employee_cv_id', array('name', 'sex', 'age', 'experience', 'face'))
                       ->where('type = ?', $this->view->type, Zend_Db::PARAM_INT)
                       ->where('company_id = ?', $this->_company->id, Zend_Db::PARAM_INT);

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($pageID);

        $this->view->paginator = $paginator;
    }

    public function recruitAction()
    {
        $this->view->type = $this->_getParam('type', 0);

        if ($this->getRequest()->isPost()) {
            $task = Playgine_TaskFactory::factory('Recruit');
            $task->setOptions($this->getRequest()->getPost());
            $message = $this->runTask($task);
            $this->_flash($message);
            $this->_redirectBack();
        }

        $modelCompanyEmployee = new Model_CompanyEmployee();
        foreach (Model_CompanyEmployee::$types as $type) {
            $count = $this->_db->query('SELECT COUNT(*)
                FROM company_employee
                WHERE company_id IS NULL
                AND type = ?', array($type)
            )->fetchColumn();
            if ($count < Model_CompanyEmployee::MIN_AVAILABLE) {
                $modelCompanyEmployee->generate($type, Model_CompanyEmployee::MIN_AVAILABLE - $count);
            }
        }

        $recruits = $this->_db->fetchAll('SELECT DISTINCT ON (face) *
            FROM (
                SELECT employee_cv.*
                FROM employee_cv
                INNER JOIN company_employee ON company_employee.employee_cv_id = employee_cv.id
                WHERE company_id IS NULL
                AND type = :type
                ORDER BY RANDOM()
            ) tmp
            LIMIT 10', array(':type' => $this->view->type));
        shuffle($recruits);

        $this->view->recruits = $recruits;
    }

    public function showCvAction()
    {
        $id = $this->_getId();

        $this->view->data = $this->_db->fetchRow(
            $this->_db
                 ->select()
                 ->from('company_employee')
                 ->join('employee_cv', 'employee_cv.id = employee_cv_id', array('name', 'sex', 'age', 'experience', 'education', 'last_employer', 'face'))
                 ->where('company_employee.id = ?', $id, Zend_Db::PARAM_INT)
        );
    }
}