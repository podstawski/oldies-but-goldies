<?php

class Playgine_Task_FireFiredEmployees extends Playgine_Task_Abstract
{
    /**
     * @var bool
     */
    protected $_storeMessage = false;
    
    public function run()
    {
        $where = array(':company_id' => $this->getCompany()->id);
        $db = Zend_Db_Table::getDefaultAdapter();

        $db->query('DELETE FROM company_employee WHERE company_id = :company_id AND fired = 1', $where)->execute();
        $db->query('UPDATE product SET employees = 0, output = 0 WHERE company_id = :company_id', $where)->execute();
        $db->query('UPDATE employee SET busy = 0, fired = 0, amount = (SELECT COUNT(*) FROM company_employee WHERE company_id = :company_id AND type = :type) WHERE company_id = :company_id AND type = :type', $where + array(':type' => Model_CompanyEmployee::TYPE_MANAGER))->execute();
        $db->query('UPDATE employee SET busy = 0, fired = 0, amount = (SELECT COUNT(*) FROM company_employee WHERE company_id = :company_id AND type = :type) WHERE company_id = :company_id AND type = :type', $where + array(':type' => Model_CompanyEmployee::TYPE_WORKER))->execute();
    }
}