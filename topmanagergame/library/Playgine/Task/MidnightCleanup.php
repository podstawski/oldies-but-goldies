<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Playgine_Task_MidnightCleanup extends Playgine_Task_Abstract
{
    /**
     * @var bool
     */
    protected $_storeMessage = false;

    public function run()
    {
        $cache = Zend_Registry::get('cache');
        @$cache->remove(Model_Loan::CACHE_DATA_NAME);
    }
}