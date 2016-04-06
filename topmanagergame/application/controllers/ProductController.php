<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class ProductController extends Game_Controller
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->view->products = $this->_company->getProducts();

        $warehouseRowset = $this->_company->getWarehouseRowsByStatus(Model_Warehouse::JUST_PRODUCED);
        if ($warehouseRowset->count()) {
            if ($this->getRequest()->isPost()) {
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                try {
                    $db->beginTransaction();
                    foreach ($warehouseRowset as $warehouseRow) {
                        $task = Playgine_TaskFactory::factory('PutOnMarket');
                        $task->setIgnoreCheck(true);
                        $task->setOption('id', $warehouseRow->id);
                        $task->setOption('warehouseRow', $warehouseRow);
                        $task->setOption('price', $this->_getParam('price_' . $warehouseRow->id));
                        $task->setCompany($this->_company);
                        $this->_getTaskManager()->_runTask($task);
                    }
                    $db->commit();
                    $this->_flash('Products were put on market');
                    $this->_redirectExit('index');
                } catch (Playgine_Exception $e) {
                    $db->rollBack();
                    $this->view->error = $e->getMessage();
                }
            }
            $this->view->warehouseRowset = $warehouseRowset;

            $modelAnalyst = new Model_Analyst();
            $this->view->analystData = $modelAnalyst->getRecentData();
        }

        if ($neededManagers = $this->_company->checkManagers()) {
            $this->_flash(array(
                'you have %s managers, you can assign max %s workers',
                $this->_company->getManagers()->getMaxAmount(),
                $this->_company->getWorkers()->getMaxAmount(),
            ));
        }
    }

    public function upgradeTechnologyAction()
    {
        $id = $this->_getId();

        $task = Playgine_TaskFactory::factory('UpgradeTechnology');
        $task->setOption('id', $id);
        $message = $this->runTask($task);
        $this->_flash($message);
        $this->_redirectExit('index');
    }

    public function upgradeQualityAction()
    {
        $id = $this->_getId();

        $task = Playgine_TaskFactory::factory('UpgradeQuality');
        $task->setOption('id', $id);
        $message = $this->runTask($task);
        $this->_flash($message);
        $this->_redirectExit('index');
    }

    public function assignEmployeesAction()
    {
        $id = $this->_getId();

        $task = Playgine_TaskFactory::factory('AssignEmployee');
        $task->setOption('id', $id);
        $task->setOption('amount', intval($this->_getParam('amount')));
        try {
            $message = $this->runTask($task);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        $data = $this->getProductData($id);
        $data['message'] = $message;
        $this->sendAjaxResponse($data);
    }

    public function setProductionOutputAction()
    {
        $id = $this->_getId();

        $task = Playgine_TaskFactory::factory('ProductionOutput');
        $task->setOption('id', $id);
        $task->setOption('amount', intval($this->_getParam('amount')));
        try {
            $message = $this->runTask($task);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        $data = $this->getProductData($id);
        $data['message'] = $message;
        $this->sendAjaxResponse($data);
    }

    public function doProduceAction()
    {
        if ($this->_company->getCanProduce()) {
            $task = Playgine_TaskFactory::factory('Production');
            $message = $this->runTask($task);
            $this->_flash($message);
        } else {
            $this->_flash('Produkcja jest możliwa tylko raz na dzień');
        }
        $this->_redirectExit('index');
    }

    /**
     * @param int $id
     * @return array
     */
    private function getProductData($id)
    {
        $product = $this->_company->getProduct($id);
        $data = $product->toArray();
        $data['not_busy'] = $product->getWorkers()->getNotBusy();
        $data['max_output'] = $product->getMaxOutput();
        $data['production_cost'] = $this->view->currency($product->getProductionCost());
        $cost = $this->_company->getTotalProductionCost();
        $data['total_production_cost'] = $this->view->currency($cost);
        $data['not_enough_money'] = $cost > $this->_company->balance;
        return $data;
    }

    private function sendAjaxResponse($data, $success = true)
    {
        if (is_string($data)) {
            $data = array('message' => $data);
        } elseif ($data instanceof Exception) {
            $data = array('message' => $data->getMessage());
            $success = false;
        }
        $this->_response->setHttpResponseCode(200);
        $this->_response->setBody(json_encode($data));
        $this->_response->sendResponse();
        die();
    }
}