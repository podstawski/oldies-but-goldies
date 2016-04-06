<?php

class Model_WarehouseRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @param int $amount
     *
     * @throws Playgine_Exception
     */
    public function sellToNPC($amount, Model_CompanyRow $company = null)
    {
        if ($this->amount < $amount) {
            throw new Playgine_Exception('cannot sell more products than exist');
        }

        if ($this->status != Model_Warehouse::ON_MARKET) {
            throw new Playgine_Exception('cannot sell products which are not on market');
        }

        if ($company == null) {
            $company = $this->findParentRow('Model_Company');
        }

        $today = $company->getToday();

        $modelSaleReport = new Model_SaleReport();
        $report = $modelSaleReport->createRow();
        $report->warehouse_id = $this->id;
        $report->offered_price = $this->price;
        $report->offered_amount = $this->amount;
        $report->sold_amount = $amount;
        $report->day = $today;
        $report->save();

        $this->amount -= $amount;
        $this->save();

        return $this;
    }

    /**
     * @return Model_WarehouseRow
     */
    public function archive()
    {
        $this->status = Model_Warehouse::ARCHIVED;
        $this->save();

        return $this;
    }
}
