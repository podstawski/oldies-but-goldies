<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class OfficeController extends Game_Controller
{
    public function indexAction()
    {
        $this->view->saleReportHistory     = $this->_company->getSaleReportHistory();
        $this->view->costsAndIncomeHistory = $this->_company->getCostsAndIncomeHistory();
        $this->view->balanceHistory        = $this->_company->getBalanceHistory();

//        $this->_flash('Wyprodukowano:<ul class="l"><li>Komputery stacjonarne: <strong>12 szt.</strong></li><li>Tablety: <strong>3 szt.</strong></li><li>Konsole do gier: <strong>3 szt.</strong></li><li>Laptopy: <strong>3 szt.</strong></li></ul>');
//        $this->_flash('Minął kolejny dzień.<br>Raport sprzedaży wskazuje, że Twoja firma sprzedała:<br><ul class="l"><li>Komputery stacjonarne: <strong>4 szt.</strong></li><li>Tablety: <strong>1 szt.</strong></li><li>Konsole do gier: <strong>1 szt.</strong></li><li>Laptopy: <strong>1 szt.</strong></li></ul>Osiągnąłeś przychód ze sprzedaży w wysokości: <strong>11&nbsp;000,00&nbsp;zł</strong>.<br>Zarobione środki możesz zainwestować w produkcję kolejnej partii towarów.');
    }

    public function companyAction()
    {
        $form = new Form_Company();
        $form->populate($this->_company->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $formData = $form->getValues();
            $this->_company->setFromArray($formData);
            $this->_company->save();
            $this->_flash('company data have been saved');
        }

        $form->setTableLayout();
        $form->getElement('table_header')
             ->setValue($this->view->translate('form company table header edit'));

        $this->view->form = $form;
    }

    public function payCommitmentsAction()
    {
        $task = Playgine_TaskFactory::factory('PayCommitments');
        $task->setOption('commitmentTypes', array_keys((array) $this->_getParam('commitment')));
        $this->_flash($this->runTask($task));
        $this->_redirectBack();
    }


    public function nextRoundAction()
    {
        $this->_checkRemoteLogin();

        if (!($this->_company->rounds_left > 0)) {
            throw new Exception('No more rounds left');
        }

        $engine = new Playgine_Engine();
        $engine->setCompany($this->_company);
        $counter = $engine->run();

        $yersterday = $this->_company->getToday();

        $saleData = $this->_db->fetchAll(
            $this->_db->select()
                 ->from('sale_report', null)
                 ->join('warehouse', 'warehouse.id = warehouse_id', array('type', 'sold_amount' => new Zend_Db_Expr('SUM(sold_amount)')))
                 ->where('company_id = ?', $this->_company->id, Zend_Db::PARAM_INT)
                 ->where('day = ?', $yersterday, Zend_Db::PARAM_INT)
                 ->group('warehouse.type')
                 ->order('warehouse.type ASC')
        );

        $soldAmount = 0;
        foreach ($saleData as $k => $data)
            $soldAmount += $data->sold_amount;

        if ($soldAmount == 0) {
            $this->_flash('next round with no sale data');
        } else {
            $html = '';
            foreach ($saleData as $k => $data)
                $html .= '<li>' . $this->view->translate('ProductType:' . $data->type) . ': <strong>'.  $data->sold_amount . ' szt.</strong></li>';

            /*
            $tmp = $this->_db->fetchPairs(
                $this->_db
                     ->select()
                     ->from('balance', array('type', 'amount' => new Zend_Db_Expr('ABS(SUM(amount))')))
                     ->where('company_id = ?', $this->_company->id, Zend_Db::PARAM_INT)
                     ->where('type IN (?)', array(
                        Playgine_TaskFactory::getTaskTypeByName('Production'),
                        Playgine_TaskFactory::getTaskTypeByName('NpcBuy')
                     ), Zend_Db::PARAM_INT)
                     ->where('day = ?', $yersterday, Zend_Db::PARAM_INT)
                     ->group('type')
            );

            $income = $tmp[Playgine_TaskFactory::getTaskTypeByName('NpcBuy')];
            $profit = $income - $tmp[Playgine_TaskFactory::getTaskTypeByName('Production')];
            */

            $income = $this->_db->fetchOne(
                $this->_db
                     ->select()
                     ->from('balance', array(new Zend_Db_Expr('SUM(amount)')))
                     ->where('company_id = ?', $this->_company->id, Zend_Db::PARAM_INT)
                     ->where('type = ?', Playgine_TaskFactory::getTaskTypeByName('NpcBuy'), Zend_Db::PARAM_INT)
                     ->where('day = ?', $yersterday, Zend_Db::PARAM_INT)
            );

            $profit = $income - $this->_db->fetchOne(
                $this->_db
                     ->select()
                     ->from('sale_report', null)
                     ->join('warehouse', 'warehouse.id = sale_report.warehouse_id', array(new Zend_Db_Expr('SUM(sale_report.sold_amount * warehouse.parts_cost)')))
                     ->where('warehouse.company_id = ?', $this->_company->id, Zend_Db::PARAM_INT)
                     ->where('day = ?', $yersterday, Zend_Db::PARAM_INT)
            );

            $this->_flash(array(
                'next round with sale data',
                $html,
                $this->view->currency($income),
                $this->view->currency($profit),
            ));
        }

        $this->_redirectUrlExit($_SERVER['HTTP_REFERER']);
    }

    public function eventsAction()
    {
        $datagrid = new Grid_Events();
        $datagrid->deploy();
        $this->view->grid = $datagrid;
    }

    public function usAction()
    {
        $modelTax     = new Model_Tax();
        $modelBalance = new Model_Balance();

        $today = $this->_company->getToday();

        $lastMonthParams    = Model_Day::getPreviousMonthParams($today);
        $currentMonthParams = Model_Day::getCurrentMonthParams($today);

        $this->view->lastMonthTax = $modelTax->fetchforCompanyYearMonth($this->_company->id, $lastMonthParams[1], $lastMonthParams[0]);

        list ($costs, $income) = $modelBalance->fetchAllCostsAndIncome($this->_company->id, $currentMonthParams[2], $currentMonthParams[3], array(
            Playgine_TaskFactory::getTaskTypeByName('NpcBuy'),
            Playgine_TaskFactory::getTaskTypeByName('Production'),
            Playgine_TaskFactory::getTaskTypeByName('UpgradeTechnology'),
            Playgine_TaskFactory::getTaskTypeByName('UpgradeQuality'),
        ));
        $this->view->currentMonthIncome = $income - $costs;

        $this->view->us_text = Model_GameData::getData(Model_GameData::US_TEXT);
    }

    public function restartAction()
    {
        if (Model_Param::get('commitment.bankruptcy_delay') > 0
        &&  Model_Param::get('commitment.allow_restart')
        &&  $this->_company->status == Model_Company::STATUS_BANKRUPT
        ) {
            $this->_company->resetCompany();
            $this->_flash('company restarted');
        }
        $this->_redirectExit('index');
    }

    public function profileAction()
    {
        $this->_checkRemoteLogin();
        $this->_checkCompanyOwner();

        $form = new Form_Profile();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $form->save();
            $this->_flash('changes have been saved');
            $this->_redirectExit('profile');
        }
        $this->view->form = $form;
    }

    public function getClassessFromSchoolAction()
    {
        echo json_encode($this->_db->fetchPairs(
            $this->_db
                 ->select()
                 ->from('school_class', array('id', 'name'))
                 ->where('school_id = ?', $this->_getParam('id'))
        ));
        die();
    }
}