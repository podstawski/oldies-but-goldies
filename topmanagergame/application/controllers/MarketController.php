<?php

class MarketController extends Game_Controller
{
    public function init()
    {
        parent::init();

        $this->_inputFilters = array(
            'item_id' => 'Digits',
            'new_price' => 'Digits'
        );

        $this->_inputValidators = array(
            'item_id' => array(
                new Zend_Validate_Int(),
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            ),
            'new_price' => array(
                new Zend_Validate_Int(),
                new Zend_Validate_GreaterThan(0),
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
    }

    public function indexAction()
    {
        $pageID = $this->_getParam('pageID', 1);

        $select = $this->_db
            ->select()
            ->from('warehouse')
            ->where('company_id = ?', $this->_company->id, Zend_Db::PARAM_INT)
            ->where('status <> ?', Model_Warehouse::ARCHIVED, Zend_Db::PARAM_INT)
            ->order('id ASC');

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($pageID);

        $this->view->paginator = $paginator;

        if ($this->getRequest()->isPost()) {
            $task = Playgine_TaskFactory::factory('ChangeMarketPrice');
            $task->setOptions($this->getRequest()->getPost());
            $message = $this->runTask($task);
            $this->_flash($message);
            $this->_redirectExit('index');
        }
    }

    public function reportAction()
    {
        $id = $this->_getId();
        $pageID = $this->_getParam('pageID', 1);

        $modelWarehouse = new Model_Warehouse();
        $warehouseRow = $modelWarehouse->fetchRow(array(
            'id = ?' => $id,
            'company_id = ?' => $this->_company->id
        ));
        if ($warehouseRow) {
            $select = $this->_db
                ->select()
                ->from('sale_report', array('warehouse_id', 'offered_price', 'offered_amount', 'sold_amount', 'day'))
                ->where('warehouse_id = ?', $id, Zend_Db::PARAM_INT)
                ->order('day DESC');

            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
            $paginator->setCurrentPageNumber($pageID);

            $this->view->warehouseRow = $warehouseRow;
            $this->view->paginator = $paginator;
        } else {
            $this->_flash('could not find sale reports');
            $this->_redirectExit('index', 'market');
        }
    }

    public function archiveAction()
    {
        $id = $this->_getId();

        $modelWarehouse = new Model_Warehouse();
        $warehouseRow = $modelWarehouse->fetchRow(array(
            'id = ?' => $id,
            'company_id = ?' => $this->_company->id
        ));
        if ($warehouseRow) {
            $warehouseRow->archive();
            $this->_flash('warehouse row archived');
        } else {
            $this->_flash('could not find warehouse row');
        }
        $this->_redirectExit('index', 'market');
    }
}