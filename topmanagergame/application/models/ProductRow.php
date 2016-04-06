<?php

/**
 * @author Radosław Szczepaniak
 */

class Model_ProductRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @var Model_EmployeeRow
     */
    protected $_employeeRow;

    /**
     * @var Model_EmployeeRow
     */
    public function getWorkers()
    {
        if ($this->_employeeRow == null) {
            $this->_employeeRow = $this->findParentRow('Model_Company')->getEmployeeRow(Model_CompanyEmployee::TYPE_WORKER);
        }
        return $this->_employeeRow;
    }

    /**
     * @return int
     */
    public function getMaxOutput()
    {
        return intval($this->employees * $this->getWorkers()->getEfficiency());
    }

    /**
     * @return int
     */
    public function getMaxEmployees()
    {
        return intval($this->employees + $this->getWorkers()->getNotBusy());
    }

    /**
     * @return float
     */
    public function getProductionCost()
    {
        return floatval($this->output *  $this->getPartsCost());
    }

    /**
     * @return float
     */
    public function getPartsCost()
    {
        $bonus = 1 - ($this->getTechnologyBonus() / 100);
        return floatval(Model_Param::get('product.' . $this->type . '.parts') * $bonus);
    }

    /**
     * @return int
     */
    public function getTechnologyBonus()
    {
        return intval(Model_Param::get('product.' . $this->type . '.technology.bonus.' . $this->technology));
    }

    /**
     * @return int
     */
    public function getQualityBonus()
    {
        return intval(Model_Param::get('product.' . $this->type . '.quality.bonus.' . $this->quality));
    }

    /**
     * @return float
     */
    public function getTechnologyUpgradeBonus()
    {
        return floatval(Model_Param::get('product.' . $this->type . '.technology.bonus.' . ($this->technology + 1)));
    }

    /**
     * @return float
     */
    public function getQualityUpgradeBonus()
    {
        return floatval(Model_Param::get('product.' . $this->type . '.quality.bonus.' . ($this->quality + 1)));
    }

    /**
     * @return float
     */
    public function getTechnologyUpgradeCost()
    {
        return floatval(Model_Param::get('product.' . $this->type . '.technology.cost.' . ($this->technology + 1)));
    }

    /**
     * @return float
     */
    public function getQualityUpgradeCost()
    {
        return floatval(Model_Param::get('product.' . $this->type . '.quality.cost.' . ($this->quality + 1)));
    }

    /**
     * @return bool
     */
    public function canUpgradeTechnology()
    {
        return $this->technology < Model_Product::MAX_TECHNOLOGY;
    }

    /**
     * @return bool
     */
    public function canUpgradeQuality()
    {
        return $this->quality < Model_Product::MAX_QUALITY;
    }
}
