<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Grid_Balance extends Game_Grid
{
    /**
     * @return Zend_Db_Select
     */
    protected function getDataSelect()
    {
        return Zend_Db_Table::getDefaultAdapter()
            ->select()
            ->from('balance', array('day', 'text', 'amount', 'current_balance'))
            ->where('company_id = ?', Model_Player::getCompany()->id, Zend_Db::PARAM_INT)
            ->where('amount <> 0')
            ->order('day DESC')
            ->order('id DESC');
    }

    public function init()
    {
        $this->setSource(new Bvb_Grid_Source_Zend_Select($this->getDataSelect()));

        $view = $this->getView();

        $this->updateColumn('day', array(
            'title' => $view->translate('balance day'),
            'callback' => array(
                'function' => function ($day) use ($view) {
                    return $view->formatDay($day)->asString();
                },
                'params' => array('{{day}}')
            )
        ));

        $this->updateColumn('amount', array(
            'class' => 'text-right',
            'title' => $view->translate('balance amount'),
            'callback' => array(
                'function' => array($view, 'currency'),
                'params' => array('{{amount}}')
            )
        ));

        $this->updateColumn('current_balance', array(
            'class' => 'text-right',
            'title' => $view->translate('balance current balance'),
            'callback' => array(
                'function' => array($view, 'currency'),
                'params' => array('{{current_balance}}')
            )
        ));

        $this->updateColumn('text', array(
            'escape' => false,
            'title' => $view->translate('balance text'),
        ));

        $this->setNoFilters(true);
    }
}