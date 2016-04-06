<?php

/**
 * @author Radosław Szczepaniak
 */

class Model_CompanyRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @var Model_EmployeeRow
     */
    protected $_employeeRow;

    /**
     * @return Zend_Db_Table_Rowset
     */
    protected $_products;

    /**
     * @return string
     */
    public function getLogoImagePath()
    {
        return '/uploads/company/' . $this->id . '.png';
    }

    /**
     * @param string $file
     */
    public function makeLogoImage($srcPath)
    {
        $PATHS = Zend_Controller_Front::getInstance()->getParam("bootstrap")->getOption('paths');
        $destPath = $PATHS['company_logo'] . '/' . $this->id . '.png';

        $w = intval(Model_Param::get('company.logo.width'));
        $h = intval(Model_Param::get('company.logo.height'));

        //GN_Thumbnail::createThumbnail($srcPath, $destPath, $w, $h);
    }

    /**
     * @return void
     */
    public function createDefaultLogoImage()
    {
        $PATHS = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('paths');
        $defaultLogosDir = $PATHS['company_default_logos'];

        $dir = glob($defaultLogosDir . '/*.png');
        $dir = array_map('basename', $dir);
        $dir = array_map('intval', $dir);

        $randomLogoNumber = rand(min($dir), max($dir));
        $logoPath = $defaultLogosDir . DIRECTORY_SEPARATOR . $randomLogoNumber . '.png';

        $this->makeLogoImage($logoPath);
    }

    /**
     * @param int $type
     * @param float $amount
     * @param string $text
     */
    public function addProfitInfo($type, $amount, $text = null)
    {
        $this->balance += $amount;
        $this->save();

        $modelBalance = new Model_Balance();
        $modelBalance->insert(array(
                                  'type'            => $type,
                                  'company_id'      => $this->id,
                                  'day'             => $this->getToday(),
                                  'date'            => date('c'),
                                  'amount'          => $amount,
                                  'current_balance' => $this->balance,
                                  'text'            => $text
                              )
        );
    }

    /**
     * @param int $type
     * @param float $amount
     * @param string $text
     */
    public function addBalanceInfo($type, $amount, $text = null)
    {
        $this->addProfitInfo($type, -1 * $amount, $text);
    }

    /**
     * @return Model_EmployeeRow
     */
    public function getEmployeeRow($type)
    {
        $employee = $this->findDependentRowset('Model_Employee', null, $this->select()->where('type = ?', $type, Zend_Db::PARAM_INT))->current();
        if ($employee == null) {
            $modelEmployee = new Model_Employee();
            $employee = $modelEmployee->createRow();
            $employee->company_id = $this->id;
            $employee->type = $type;
            $employee->save();
        }
        return $employee;
    }

    /**
     * @return Model_EmployeeRow
     */
    public function getWorkers()
    {
        return $this->getEmployeeRow(Model_CompanyEmployee::TYPE_WORKER);
    }

    /**
     * @return bool
     */
    public function checkManagers()
    {
        $workers  = $this->getWorkers();
        $managers = $this->getManagers();

        if ($managers->amount == 0 || ($workers->amount / $managers->amount) > $managers->getEfficiency()) {
            return ceil($workers->amount / $managers->getEfficiency());
        }
        return 0;
    }

    /**
     * @return Model_EmployeeRow
     */
    public function getManagers()
    {
        return $this->getEmployeeRow(Model_CompanyEmployee::TYPE_MANAGER);
    }

    /**
     * @return Zend_Db_Table_Rowset
     */
    public function getProducts()
    {
        if ($this->_products === null) {
            $this->_products = $this->findDependentRowset(
                'Model_Product',
                null,
                $this->select()->order('type ASC')
            );
        }
        return $this->_products;
    }

    /**
     * @param $id
     *
     * @return Model_ProductRow|null
     */
    public function getProduct($id)
    {
        foreach ($this->getProducts() as $product) {
            if ($product->id == $id) {
                return $product;
            }
        }
        return null;
    }

    /**
     * @param int $status
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getWarehouseRowsByStatus($status)
    {
        return $this->findDependentRowset(
            'Model_Warehouse',
            null,
            $this->select()
                ->where('status = ?', $status, Zend_Db::PARAM_INT)
                ->where('amount > 0')
        );
    }

    /**
     * @return int
     */
    public function getToday()
    {
        return Model_Day::getToday() + Model_Param::get('general.game_rounds') - $this->rounds_left;
    }

    /**
     * @var array
     */
    protected $_summarizedCommitments;

    /**
     * @return array
     */
    public function getSummarizedCommitments()
    {
        if ($this->_summarizedCommitments == null) {
            $this->_summarizedCommitments = array();
            $db = Zend_Db_Table::getDefaultAdapter();
            foreach (Model_Commitment::$commitmentTypes as $type) {
                $cost = $db->fetchOne('SELECT SUM(cost)
                    FROM commitment
                    WHERE company_id = ?
                    AND type = ?', array($this->id, $type)
                ) ?: 0;

                $this->_summarizedCommitments[] = (object) array(
                    'type' => $type,
                    'cost' => $cost,
                );
            }
        }
        return $this->_summarizedCommitments;
    }

    /**
     * @return bool
     */
    public function getCanProduce()
    {
//        return true;

        return $this->findDependentRowset(
                   'Model_Balance',
                   null,
                   $this->select()
                       ->where('type = ?', Playgine_TaskFactory::getTaskTypeByName('Production'), Zend_Db::PARAM_INT)
                       ->where('day = ?', $this->getToday(), Zend_Db::PARAM_INT)
               )->count() == 0;
    }

    /**
     * @return bool
     */
    public function getCanTrain()
    {
        return $this->findDependentRowset(
                   'Model_Balance',
                   null,
                   $this->select()
                       ->where('type = ?', Playgine_TaskFactory::getTaskTypeByName('TrainEmployee'), Zend_Db::PARAM_INT)
                       ->where('day = ?', $this->getToday(), Zend_Db::PARAM_INT)
               )->count() == 0;
    }

    /**
     * If $typesArray is TRUE, fetch commitments that should be paid till today.
     *
     * @param array|true|null $typesArray
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getCommitments($typesArray = null)
    {
        $select = $this->select();
        if (is_array($typesArray) && !empty($typesArray)) {
            $select->where('type IN (?)', $typesArray, Zend_DB::PARAM_INT);
        } elseif ($typesArray === true) {
            $select->where('day = ?', $this->getToday(), Zend_DB::PARAM_INT);
        }

        return $this->findDependentRowset('Model_Commitment', null, $select);
    }

    public function hasCommitmentsOlderThan($days)
    {
        $select = $this->select()
                       ->where($this->getToday() . ' - day >= ?', $days, Zend_Db::PARAM_INT)
                       ->order('day ASC')
                       ->limit(1);

        return $this->findDependentRowset('Model_Commitment', null, $select)->current() != null;
    }

    /**
     * @return int|null
     */
    public function getOldestCommitment()
    {
        return $this->findDependentRowset(
            'Model_Commitment',
            null,
            $this->select()
                 ->where('cost > 0')
                 ->order('day ASC')
        )->current();
    }

    /**
     *
     */
    public function checkOldestCommitment()
    {
        //if ($this->status != Model_Company::STATUS_BANKRUPT) {

            $commitment = $this->findDependentRowset(
                'Model_Commitment',
                null,
                $this->select()
                     ->where('cost > 0')
                     ->order('day ASC')
            )->current();

            $this->status = Model_Company::STATUS_OK;
            if ($commitment) {
                $params = Model_Param::get('commitment');
                $diff = $this->getToday() - $commitment->day;
                if ($diff >= $params['bankruptcy_delay']) {
                    $this->status = Model_Company::STATUS_BANKRUPT;

//                    $user = $this->getOwner();
//                    $user->is_hidden = 1;
//                    $user->save();
                } else if ($diff >= $params['warning_delay']) {
                    $this->status = Model_Company::STATUS_WARNING;
                }
            }
            $this->save();

        //}
    }

    /**
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getOwner()
    {
        return $this->findParentRow('Model_User');
    }

    /**
     * @param bool $excludeOwner
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getCoOwners($excludeOwner = false)
    {
        $coOwners = array();
        foreach ($this->findDependentRowset('Model_UserToCompany') as $row)
            if ($row->user_id)
                $coOwners[$row->user_id] = $row->user_id;

        if ($excludeOwner)
            unset($coOwners[$this->user_id]);

        $modelUser = new Model_User;
        return $modelUser->find($coOwners);
    }

    /**
     * @return float
     */
    public function getTotalProductionCost()
    {
        $cost = 0.0;
        foreach ($this->getProducts() as $product) {
            $cost += $product->getProductionCost();
        }
        return $cost;
    }

    /**
     * @param int $bankID
     * @return Model_LoanRow
     */
    public function getLoanForBank($bankID, $unpaid = true)
    {
        $select = $this->select()->where('bank_id = ?', $bankID, Zend_Db::PARAM_INT);
        if ($unpaid)
            $select->where('months_amount > months_paid');

        return $this->findDependentRowset(
            'Model_Loan',
            null,
            $select
        )->current();
    }

    /**
     * @param $loanID
     * @param bool $unpaid
     * @return Model_LoanRow
     */
    public function getLoan($loanID, $unpaid = true)
    {
        $select = $this->select()->where('id = ?', $loanID, Zend_Db::PARAM_INT);
        if ($unpaid)
            $select->where('months_amount > months_paid');

        return $this->findDependentRowset(
            'Model_Loan',
            null,
            $select
        )->current();
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getLoanRows($unpaid = true)
    {
        $select = $this->select();
        if ($unpaid)
            $select->where('months_amount > months_paid');

        return $this->findDependentRowset(
            'Model_Loan',
            null,
            $select
        );
    }

    /**
     * @return array
     */
    public function getSaleReportHistory()
    {
        $today = $this->getToday();
        $gameDate = Model_Day::gameDayIntoGameDate($today);
        $firstDayOfMonth = Model_Day::gameDateIntoGameDay(1, $gameDate['month'], $gameDate['year']);
        $row = array();
        foreach (Model_Product::$types as $type) {
            $row[$type] = (object) array(
                'type' => $type,
                'sold_amount' => 0,
                'offered_amount' => 0,
            );
        }
        if ($today == $firstDayOfMonth) return array($today => $row);
        $saleData = array_fill($firstDayOfMonth, $today - $firstDayOfMonth, $row);
        ksort($saleData);
        $db = $this->getTable()->getAdapter();
        foreach ($t = $db->fetchAll(
            $db->select()
               ->from('sale_report', array(
                    'day',
                    'sold_amount' => new Zend_Db_Expr('SUM(sold_amount)'),
                    'offered_amount' => new Zend_Db_Expr('SUM(offered_amount)')
               ))
               ->join('warehouse', 'warehouse.id = sale_report.warehouse_id', array('type'))
               ->where('warehouse.company_id = ?', $this->id, Zend_Db::PARAM_INT)
               ->where('sale_report.day >= ?', $firstDayOfMonth, Zend_Db::PARAM_INT)
               ->group('warehouse.type')
               ->group('sale_report.day')
               ->order('day DESC')
               ->order('type ASC')
        ) as $row
        ) {
            $saleData[$row->day][$row->type] = $row;
        }
        return $saleData;
    }

    /**
     * @return array
     */
    public function getCostsAndIncomeHistory()
    {
        $today = $this->getToday();
        $gameDate = Model_Day::gameDayIntoGameDate($today);
        $firstDayOfMonth = Model_Day::gameDateIntoGameDay(1, $gameDate['month'], $gameDate['year']);
        $row = array(
            'costs'  => 0,
            'income' => 0,
        );
        if ($today == $firstDayOfMonth) return array($today => $row);
        $costsAndIncomeData = array_fill($firstDayOfMonth, $today - $firstDayOfMonth + 1, $row);
        ksort($costsAndIncomeData);
        $db = $this->getTable()->getAdapter();
        foreach ($db->fetchAll(
            $db->select()
               ->from('balance', array('day', 'amount'))
               ->where('company_id = ?', $this->id, Zend_Db::PARAM_INT)
               ->where('day >= ?', $firstDayOfMonth, Zend_Db::PARAM_INT)
               ->where('amount <> 0')
               ->where('type NOT IN (?)', array(
                    Playgine_TaskFactory::getTaskTypeByName('CreateBankLoan')
               ), Zend_Db::PARAM_INT)
               ->order('day DESC')
        ) as $row
        ) {
            if ($row->amount > 0) {
                $costsAndIncomeData[$row->day]['income'] += $row->amount;
            } else {
                $costsAndIncomeData[$row->day]['costs']  -= $row->amount;
            }
        }
        return $costsAndIncomeData;
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getBalanceHistory()
    {
        $db = $this->getTable()->getAdapter();
        return $this->findDependentRowset(
            'Model_Balance',
            null,
            $this->select()
                 ->where('(' . $db->quoteInto('type NOT IN (?)', array(
                     Playgine_TaskFactory::getTaskTypeByName('AssignEmployee'),
                     Playgine_TaskFactory::getTaskTypeByName('RevokeEmployee'),
                     Playgine_TaskFactory::getTaskTypeByName('ProductionOutput'),
                     Playgine_TaskFactory::getTaskTypeByName('PayCommitments'),
                     Playgine_TaskFactory::getTaskTypeByName('NpcBuy'),
                 ), Zend_Db::PARAM_INT) . ') OR (' . $db->quoteInto('type = ?', Playgine_TaskFactory::getTaskTypeByName('NpcBuy'), Zend_Db::PARAM_INT) . ' AND amount > 0)')
                 ->order('day DESC')
                 ->limit(6)
        );
    }

    /**
     * @param string|int $type
     * @param bool $fromTheBeginningOfMonth
     * @return float
     */
    public function getSummarizedTaskCostByType($type)
    {
        $today = $this->getToday();
        list ($month, $year, $from, $to) = Model_Day::getPreviousMonthParams($today);
        if ($to == 1) return 0;

        if (is_string($type))
            $type = Playgine_TaskFactory::getTaskTypeByName($type);

        $db = $this->getTable()->getAdapter();
        return abs($db->fetchOne(
            $db->select()
               ->from('balance', array('amount' => new Zend_Db_Expr('SUM(amount)')))
               ->where('company_id = ?', $this->id, Zend_Db::PARAM_INT)
               ->where('type = ?', $type, Zend_Db::PARAM_INT)
               ->where('day >= ?', $from, Zend_Db::PARAM_INT)
               ->where('day <= ?', $to, Zend_Db::PARAM_INT)
        )) ?: 0;
    }

    /**
     * @param int $type
     * @return float
     */
    public function getSummarizedCommitmentCostByType($type)
    {
        $today = $this->getToday();
        if ($today == 1) return 0;

        $db = $this->getTable()->getAdapter();
        return $db->fetchOne(
            $db->select()
               ->from('commitment', array('cost' => new Zend_Db_Expr('SUM(cost)')))
               ->where('company_id = ?', $this->id, Zend_Db::PARAM_INT)
               ->where('type = ?', $type, Zend_Db::PARAM_INT)
        ) ?: 0;
    }

    /**
     * @return float
     */
    public function getSummarizedPenaltyCost()
    {
        $today = $this->getToday();
        if ($today == 1) return 0;

        $db = $this->getTable()->getAdapter();
        return $db->fetchOne(
            $db->select()
               ->from('commitment', array('cost' => new Zend_Db_Expr('SUM(cost)')))
               ->where('company_id = ?', $this->id, Zend_Db::PARAM_INT)
               ->where('type NOT IN (?)', Model_Commitment::$commitmentTypes, Zend_Db::PARAM_INT)
        ) ?: 0;
    }

    /**
     * @return Model_CompanyRow
     */
    public function resetCompany()
    {
        $user = $this->getOwner();

        $companyData = $this->toArray();
        unset($companyData['id']);
        $this->delete();

        $modelMessageUser = new Model_MessageUser;
        $modelMessageUser->delete('user_id = ' . $user->id);

        $modelCompany = new Model_Company();
        $company = $modelCompany->createCompany($companyData, $user);

        return $company;
    }
}
