<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_Tax extends Zend_Db_Table_Abstract
{
	protected $_name = 'tax';
    protected $_rowClass = 'Model_TaxRow';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );

    /**
     * @param $companyId
     * @param $numbers
     * @param $date
     * @return Model_TaxRow
     */
    public function setForCompanyYearMonth($companyId, $costs, $income, $year, $month)
	{
	    $taxRow = $this->fetchRow(array(
	        'company_id = ?' => $companyId,
	        'month = ?' => $month,
	        'year = ?' => $year
	    ));
	    if ($taxRow == null) {
	        $taxRow = $this->createRow();
            $taxRow->company_id = $companyId;
            $taxRow->month = $month;
            $taxRow->year = $year;
	    }
        $taxRow->costs = $costs;
        $taxRow->income = $income;
        $taxRow->save();
        return $taxRow;
	}

    /**
     * @param $companyId
     * @param $year
     * @param $month
     * @return Model_TaxRow
     */
    public function fetchforCompanyYearMonth($companyId, $year, $month)
    {
        return $this->fetchRow(array(
            'company_id = ?' => $companyId,
            'year = ?' => $year,
            'month = ?' => $month,
        ));
    }

    /**
     * @param $companyId
     * @param $year
     * @return Model_TaxRow
     */
    public function fetchForCompanyYearSum($companyId, $year)
    {
        $select = $this->select();
        $select->from($this->_name, array(
            'income' => new Zend_Db_Expr('SUM(income)'),
            'costs' => new Zend_Db_Expr('SUM(costs)'),
            'vat_to_pay' => new Zend_Db_Expr('SUM(vat_to_pay)'),
            'income_tax' => new Zend_Db_Expr('SUM(income_tax)'),
        ));
        $select->where('company_id = ?', $companyId, Zend_Db::PARAM_INT);
        $select->where('year = ?', $year, Zend_Db::PARAM_INT);
        return $this->fetchRow($select);
    }
}