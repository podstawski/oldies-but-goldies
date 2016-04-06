<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_Analyst extends Zend_Db_Table_Abstract
{
    const DATA_CACHE_NAME = 'analyst_data_tip';

    protected $_name = 'analyst';
    protected $_rowClass = 'Model_AnalystRow';

    /**
     * @param int $day
     *
     * @return array
     */
    public function getRecentData()
    {
        static $data;

        if ($data == null) {
            $data = array();
            foreach (array_merge(array(0), Model_Product::$types) as $type) {
                $data[$type] = $this->fetchRowForType($type);
            }
        }

        return $data;
    }

    public function getRandomPrediction()
    {
        $params = Model_Param::get('analyst');

        return rand(
            $params['prediction']['min'],
            $params['prediction']['max']
        );
    }

    /**
     * @return int
     */
    public function getRecentDay()
    {
        $day = $this->getAdapter()->query('SELECT MAX(day) FROM analyst')->fetch(Zend_Db::FETCH_COLUMN);
        return $day ? : 0;
    }

    /**
     * @param int $type
     * @param int $month
     * @param int $year
     *
     * @return Model_AnalystRow
     */
    public function fetchRowForType($type, $day = null)
    {
        $day = $day ?: $this->getRecentDay();

        $row = $this->fetchRow(
            array(
                'type = ?'  => $type,
                'day = ?'   => $day,
            )
        );

        if ($row == null) {
            $row = $this->createRow();
            $row->type = $type;
            $row->day = $day;
            $row->average_price = ($type > 0) ? floatval(Model_Param::get('product.' . $type . '.parts') * 1.3) : 0;
            $row->prediction = $this->getRandomPrediction();
            $row->save();
        }

        return $row;
    }

    /**
     * Returns array of data to be applied to translated tip message.
     *
     * @return array
     */
    public function getRecentDataTip()
    {
        if (!($recentDay = $this->getRecentDay())) {
            return false;
        }

        $cache = Zend_Registry::get('cache');
        $data = $cache->load(self::DATA_CACHE_NAME);

        if ($data === false) {

            $db = $this->getAdapter();

            $data = array();
            $view = new Zend_View();

            $typesData = $this->fetchAll(array(
                'day = ?' => $recentDay,
                'type > 0'
            ), 'sold_amount ASC')
            ->toArray();

            $topType = (object) array_pop($typesData);
            $topTypeCompanies = $topType->companies_amount;

            $data['general'] = array(
                $view->translate('ProductType:' . $topType->type),
                round($topType->share_amount, 2),
                $topType->sold_amount,
                $view->currency($topType->average_price)
            );

            $worstType = (object) array_shift($typesData);

            $data['worst_type'] = array(
                $view->translate('ProductType:' . $worstType->type),
                $worstType->companies_amount
            );

            $data['predictions'] = array();

            if ($predictionsMin = $this->fetchRow(array(
                'day = ?' => $recentDay,
                'prediction < 0',
                'type > 0',
            ), 'prediction ASC')) {
                $data['predictions']['min'] = array(
                    $view->translate('ProductType:' . $predictionsMin->type),
                    $predictionsMin->prediction
                );
            }

            if ($predictionsMax = $this->fetchRow(array(
                'day = ?' => $recentDay,
                'prediction > 0',
                'type > 0',
            ), 'prediction DESC')) {
                $data['predictions']['max'] = array(
                    $view->translate('ProductType:' . $predictionsMax->type),
                    $predictionsMax->prediction
                );
            }

            if ($generalType = $this->fetchRow(array(
                'day = ?' => $recentDay,
                'type = 0',
            ))) {
                $data['general_prediction'] = $generalType->prediction;
            }

            $stats = array();

            foreach ($db->fetchAll(
                $db->select()
                   ->from('sale_report', null)
                   ->join('warehouse', 'warehouse.id = warehouse_id', array('type', 'company_id'))
                   ->columns(array(
                                  'sold_amount'      => new Zend_Db_Expr('SUM(sold_amount)'),
                                  'offered_amount'   => new Zend_Db_Expr('SUM(offered_amount)'),
                                  'average_price'    => new Zend_Db_Expr('AVG(offered_price)'),
                                  'companies_amount' => new Zend_Db_Expr('COUNT(DISTINCT warehouse.company_id)')
                              )
                   )
                   ->where('sale_report.day >= ?', $recentDay - Model_Param::get('general.game_rounds') - 1, Zend_Db::PARAM_INT)
                   ->where('sale_report.day < ?', $recentDay, Zend_Db::PARAM_INT)
                   ->group('warehouse.type')
                   ->group('warehouse.company_id')
                   ->order('sold_amount DESC')
                   ->order('warehouse.type ASC')
            ) as $row) {
                if (!array_key_exists($row->type, $stats)) {
                    $stats[$row->type] = $row;
                }
            }

            if (!empty($stats)) {
                $topType = (object) array_shift($stats);
                $companyName = $db->fetchOne(
                    $db->select()
                       ->from('company', array('name'))
                       ->where('id = ?', $topType->company_id, Zend_Db::PARAM_INT)
                );
                $data['top_type'] = array(
                    $view->translate('ProductType:' . $topType->type),
                    $topTypeCompanies,
                    $companyName,
                    $topType->sold_amount,
                    $view->currency($topType->average_price)
                );

                foreach ($stats as $row) {
                    $companyName = $db->fetchOne(
                        $db->select()
                           ->from('company', array('name'))
                           ->where('id = ?', $row->company_id, Zend_Db::PARAM_INT)
                    );
                    $data['other_types'][$row->type] = array(
                        $view->translate('ProductType:' . $row->type),
                        $companyName,
                        $view->currency($row->average_price)
                    );
                }
            }

            $cache->save($data, self::DATA_CACHE_NAME);
        }

        return $data;
    }

    /**
     * @param int $type
     * @return float
     */
    public function getAveragePriceForType($type)
    {
        $data = $this->getRecentData();
        return $data[$type]->average_price;
    }

    /**
     * @param int $type
     * @return int
     */
    public function getPredictionForType($type)
    {
        $data = $this->getRecentData();
        return $data[$type]->prediction;
    }

    public function getParamHistoryForType($paramName, $type)
    {
        $data = array();
        foreach ($this->fetchAll(array(
            'type = ?' => $type,
            'day > 0'
        ), 'day DESC', 6) as $row) {
            $data[$row->day] = $row->{$paramName};
        }
        return array_reverse($data, true);
    }
}
