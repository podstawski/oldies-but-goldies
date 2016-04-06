<?php

class AnalystController extends Game_Controller
{
    /**
     * @var Model_Analyst
     */
    private $modelAnalyst;

    public function init()
    {
        parent::init();

        $this->modelAnalyst = new Model_Analyst();
    }
    public function indexAction()
    {
        $this->view->analystData = $this->modelAnalyst->getRecentData();
        $this->view->analystDataTip = $this->modelAnalyst->getRecentDataTip();
    }

    public function paramHistoryChartAction()
    {
        $this->view->type = $type = $this->_getParam('type');
        $this->view->param = $param = $this->_getParam('param');

        $formatOptions = array();
        $vAxisOptions = array();
        $chartType = 'ColumnChart';
        switch ($param) {
            case 'share_amount':
            case 'prediction':
                $formatOptions = array(
                    'suffix' => '%',
                    'fractionDigits' => 0
                );
                break;
            case 'companies_amount':
                $formatOptions = array(
                    'fractionDigits' => 0
                );
                break;
            case 'average_price':
                $symbol = Zend_Registry::get('Zend_Currency')->getSymbol();
                $chartType = 'LineChart';
                $formatOptions = array(
                    'groupingSymbol' => ' ',
                    'decimalSymbol' => ',',
                    'fractionDigits' => 2,
                    'suffix' => $symbol
                );
                $vAxisOptions = array(
                    'format' => '#' . $symbol
                );
                break;
        }
//        $vAxisOptions['minValue'] = 0;
        $this->_drawChart(array(
            'chart_type' => $chartType,
            'chart_data' => $this->modelAnalyst->getParamHistoryForType($param, $type),
            'chart_title' => $this->view->translate('History of param ' . $param, $this->view->translate('ProductType:' . $type)),
            'data_column_title' => $this->view->translate('param ' . $param),
            'data_column_format_options' => $formatOptions,
            'vaxis_options' => $vAxisOptions
        ));
    }

    private function _drawChart(array $chartOptions)
    {
        $this->view->layout()->disableLayout();
        $this->view->chartOptions = $chartOptions;
        $this->render('chart');
    }
}