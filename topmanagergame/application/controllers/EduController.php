<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class EduController extends Game_Controller
{
    /**
     * @var Model_EduParams
     */
    protected $_modelEduParams;

    public function init()
    {
        parent::init();

        $this->_modelEduParams = new Model_EduParams();
    }

    public function indexAction()
    {

    }

    public function contentAction()
    {
        $treeEntry = $this->_modelEduParams->find(intval($this->_getParam('id')))->current();

        $this->_response->setBody($treeEntry->content);
        $this->_response->sendResponse();
        exit;
    }

    public function bankCentralnyAction()
    {

    }
}