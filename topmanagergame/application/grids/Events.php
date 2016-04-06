<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Grid_Events extends Grid_Balance
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
            ->where('type NOT IN (?)', array(
                Playgine_TaskFactory::getTaskTypeByName('AssignEmployee'),
                Playgine_TaskFactory::getTaskTypeByName('RevokeEmployee'),
                Playgine_TaskFactory::getTaskTypeByName('ProductionOutput'),
                Playgine_TaskFactory::getTaskTypeByName('PayCommitments'),
            ), Zend_Db::PARAM_INT)
            ->order('day DESC')
            ->order('id DESC');
    }
}