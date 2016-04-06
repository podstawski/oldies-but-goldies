<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gammanet.pl>
 */

class Playgine_Engine
{
    /**
     * @var Model_CompanyRow|null
     */
    private $_company;

    /**
     * @var int
     */
    private $_day;

    /**
     * @var array
     */
    private $_taskStorage = array();

    /**
     * @var Playgine_TaskManager
     */
    private $_taskManager;

    /**
     * @var bool
     */
    private $_ignoreGlobalTasks = false;

    public function __construct()
    {
        $this->_taskManager = new Playgine_TaskManager();
    }

    /**
     * @return int processed tasks
     */
    public function run()
    {
        set_time_limit(3600);
        ini_set('memory_limit', '512M');

        $gameDay = Model_Day::getToday();
        $gameRounds = Model_Param::get('general.game_rounds');

        $modelCompany = new Model_Company();
        $modelQueue = new Model_Queue();

        $min = 1;
        $max = $gameRounds + 1;

        /**
         * @var $companies Zend_Db_Table_Rowset
         */
        if ($this->_company) {
            $companies = $modelCompany->fetchAll(array('id = ?' => $this->_company->id));
            // SIM ustaw min = max, żeby poszła tylko jedna tura
            $min = $max = $gameRounds - $this->_company->rounds_left + 1;
        } else {
            $companies = $modelCompany->fetchAll();
        }

        // SIM taski globalne, odpalane dla każdej firmy
        $globalTasks = array(
            'NpcBuy'               => true,
            'CalculateCommitments' => 1,
            'FireFiredEmployees'   => 1,
            'CalculateInterests'   => true,
        );

        // SIM taski systemowe, odpalane dla wszystkich firm
        $systemTasks = array(

        );

        // SIM taski specjalne, odpalane na końcu dla wszystkich firm
        $specialTasks = array(
            'AnalystSummary',
            'UpdateRank',
            'MidnightCleanup',
        );

        $counter = 0;

        for ($i = $min; $i <= $max; $i++) {
            // SIM wyznacz 'dzień gry'
            $currentDay = $gameDay + $i;
            $currentDate = (object) Model_Day::gameDayIntoGameDate($currentDay);

            foreach ($companies as $company) {
                // SIM wyznacz 'dzień firmy'
                $companyDay = $company->getToday() + 1;
                // SIM odpal taski tylko kiedy 'dzień gry' = 'dzień firmy'
                if ($currentDay == $companyDay) {
                    // SIM zakolejkowane taski
                    foreach (
                        $modelQueue->fetchAll(
                            array(
                                'company_id = ?' => $company->id,
                                'day = ?'        => $currentDay
                            )
                        ) as $queuedTask
                    ) {
                        $task = $this->_getTask((int)$queuedTask->type);
                        $task->setIgnoreCheck(true);
                        $task->setCompany($company);
                        $task->setOptions((array)json_decode($queuedTask->data));
                        $this->_taskManager->_runTask($task);

                        $queuedTask->delete();
                        $counter++;
                    }

                    // SIM taski wspólne
                    foreach ($globalTasks as $taskName => $when) {
                        if ($when === true || $currentDate->day == $when) {
                            $task = $this->_getTask($taskName);
                            $task->setIgnoreCheck(true);
                            $task->setCompany($company);
                            $task->setDay($currentDay);
                            $this->_taskManager->_runTask($task);
                            $counter++;
                        }
                    }
                    $company->rounds_left--;
                }
            }

            // SIM nie odpalaj tasków systemowych, jeśli odpalana jest 'następna runda' przez gracza
            if ($this->_company == null) {
                $company = $companies->rewind()->current();
                foreach ($systemTasks as $taskName => $when) {
                    if ($when === true || $currentDate->day == $when) {
                        $task = $this->_getTask($taskName);
                        $task->setIgnoreCheck(true);
                        $task->setCompany($company);
                        $task->setDay($currentDay);
                        $this->_taskManager->_runTask($task);
                        $counter++;
                    }
                }
            }
        }

        $company = $companies->rewind()->current();

        if ($this->_company) {
            $company->save();
        } else {
            foreach ($specialTasks as $taskName) {
                $task = $this->_getTask($taskName);
                $task->setIgnoreCheck(true);
                $task->setCompany($company);
                $task->setDay($gameDay + $gameRounds + 1);
                $this->_taskManager->_runTask($task);
                $counter++;
            }

            Model_Day::addDays($gameRounds + 1);
            $modelCompany->update(
                array(
                    'rounds_left' => $gameRounds
                ), '1 = 1'
            );
        }

        return $counter;
    }

    public function setCompany(Model_CompanyRow $company)
    {
        $this->_company = $company;
    }

    /**
     * @param string|int $id
     *
     * @return Playgine_Task_Abstract
     */
    private function _getTask($id)
    {
        if (is_int($id)) {
            $taskName = Playgine_TaskFactory::getTaskNameByType($id);
        } else {
            $taskName = $id;
        }

        if (!array_key_exists($taskName, $this->_taskStorage)) {
            $this->_taskStorage[$taskName] = Playgine_TaskFactory::factory($taskName);
        } else {
            $this->_taskStorage[$taskName]->reset();
        }
        return $this->_taskStorage[$taskName];
    }

    /**
     * @param int $day
     *
     * @return Playgine_Engine
     */
    public function setDay($day)
    {
        $this->_day = $day;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDay()
    {
        return $this->_day;
    }

    /**
     * @param bool $ignoreGlobalTasks
     */
    public function setIgnoreGlobalTasks($ignoreGlobalTasks)
    {
        $this->_ignoreGlobalTasks = $ignoreGlobalTasks;
    }

    /**
     * @return bool
     */
    public function getIgnoreGlobalTasks()
    {
        return $this->_ignoreGlobalTasks;
    }
}