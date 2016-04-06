<?php

class Playgine_Task_AnalystSummary extends Playgine_Task_Abstract
{
    /**
     * @var bool
     */
    protected $_storeMessage = false;

    public function run()
    {
        $today = $this->getDay();

        $modelAnalyst = new Model_Analyst();
        $modelAnalyst->delete('day >= ' . $today);

        $recentDay = $modelAnalyst->getRecentDay();

//        if ($recentDay < $today)
        {

            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from('sale_report', null)
                ->joinInner('warehouse', 'warehouse.id = warehouse_id', null)
                ->columns(array(
                              'warehouse.type',
                              'sold_amount'      => new Zend_Db_Expr('SUM(sold_amount)'),
                              'offered_amount'   => new Zend_Db_Expr('SUM(offered_amount)'),
                              'average_price'    => new Zend_Db_Expr('AVG(offered_price)'),
                              'companies_amount' => new Zend_Db_Expr('COUNT(DISTINCT warehouse.company_id)')
                          )
                )
                ->where('sale_report.day >= ?', $recentDay, Zend_Db::PARAM_INT)
                ->where('sale_report.day < ?', $today, Zend_Db::PARAM_INT)
                ->where('sold_amount > 0')
                ->group('warehouse.type');

            $saleReports = $db->fetchAll($select);

            $soldAmountTotal = 0;

            foreach ($saleReports as $report) {
                $soldAmountTotal += intval($report->sold_amount);
                if ($report->average_price == 0) {
                    unset($report->average_price);
                }
            }

            $typesArray = array_fill_keys(array_merge(Model_Product::$types, array(0)), false);

            foreach ($saleReports as $report) {
                $analystRow = $modelAnalyst->fetchRowForType($report->type, $today);
                $analystRow->setFromArray((array) $report);
                $analystRow->share_amount = $report->sold_amount * 100 / $soldAmountTotal;
                $analystRow->save();

                $typesArray[$report->type] = $analystRow;
            }

            $prediction = 0;
            foreach ($typesArray as $type => $row) {
                if ($row == false) {
                    $row = $modelAnalyst->fetchRowForType($type, $today);
                }

                $typesArray[$type] = $row;

                if ($type > 0) {
                    $prediction += $row->prediction;
                }
            }

            $typesArray[0]->prediction = $prediction;
            $typesArray[0]->save();

            @Zend_Registry::get('cache')->remove(Model_Analyst::DATA_CACHE_NAME);
        }
    }
}