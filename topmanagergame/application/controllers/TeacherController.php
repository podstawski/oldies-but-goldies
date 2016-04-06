<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class TeacherController extends Game_Controller
{
    private $_classID;

    public function init()
    {
        parent::init();

        if (!Model_Player::getTeacherClassId()) {
            $this->_flash('you dont have access to this page');
            $this->_redirectBack();
        }
    }

    public function indexAction()
    {
        $classID = Model_Player::getTeacherClassId();

        $modelSchoolClass = new Model_SchoolClass();
        $schoolClass = $modelSchoolClass->find($classID)->current();
        $school = $schoolClass->findParentRow('Model_School');

        $datagrid = new Grid_ClassMembers(array('class_id' => $classID));
        $datagrid->deploy();

        $this->view->grid = $datagrid;
        $this->view->school = $school;
        $this->view->schoolClass = $schoolClass;
    }

    public function setSchoolClassMemberStatusAction()
    {
        $this->_db->update('school_class_member', array(
            'status' => $this->_getParam('status')
        ), array(
            'user_id = ?' => $this->_getParam('user_id')
        ));
        die();
    }
}