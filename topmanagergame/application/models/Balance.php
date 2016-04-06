<?php
/**
 * @author Radosław Szczepaniak
 */
 
class Model_Balance extends Zend_Db_Table_Abstract
{
    protected $_name = 'balance';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );

    public function fetchAllCostsAndIncome($companyId, $from, $to, array $tasks = null, $exclude = false)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->_name, null)
            ->where('company_id = ?', $companyId, Zend_Db::PARAM_INT)
            ->where('day >= ?', $from, Zend_Db::PARAM_INT)
            ->where('day <= ?', $to, Zend_Db::PARAM_INT)
            ->columns(array('amount'));

        if ($tasks) {
            $select->where('type' . ($exclude ? ' NOT' : '') . ' IN (?)', $tasks, Zend_db::PARAM_INT);
        }

        $data = $this->getAdapter()->fetchAll($select, null, Zend_Db::FETCH_ASSOC);

        $costs = 0;
        $income = 0;
        foreach ($data as $row) {
            if ($row['amount'] < 0) {
                $costs += -1 * $row['amount'];
            } else {
                $income += $row['amount'];
            }
        }
        return array($costs, $income);
    }
}
