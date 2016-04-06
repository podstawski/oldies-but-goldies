<?php
class ClassGroup_ActionWrapper
{
    private $actionRow;
    /**
     * @var ClassGroup_Gapps
     */
    private $gapps;

    private $progressID;

    public function __construct($gapps, $actionRow, $progressID)
    {
        $this->progressID = $progressID;
        $this->gapps = $gapps;
        $this->actionRow = $actionRow;
    }

    private function getActionSteps($direction)
    {
        $modelActionStep = new Model_ActionStep();
        $select = $modelActionStep
            ->select(true)
            ->where('action_id = ?', $this->actionRow->id);
        if ($direction == Model_Action::DIRECTION_FORWARD) {
            $select->order('id ASC');
        }
        elseif ($direction == Model_Action::DIRECTION_BACKWARD) {
            $select->order('id DESC');
        }
        $steps = array();
        foreach ($modelActionStep->fetchAll($select) as $actionStepRow) {
            $steps [] = ClassGroup_ActionStepWrapper::factory($this->gapps, $actionStepRow);
        }
        return $steps;
    }

    private function work($direction)
    {
        $progressID = $this->progressID;

        //zacznij mielić akcję
        $this->actionRow->date_start = 'NOW()';
        $this->actionRow->date_end = null;
        $this->actionRow->current_action_step = null;
        $this->actionRow->last_direction = $direction;
        $this->actionRow->save();

        foreach ($this->actionRow->getSteps() as $step) {
            $step->date_executed = null;
            $step->result = null;
            $step->save();
        }

        $steps = $this->getActionSteps($direction);
        ClassGroup_Progress::start($progressID, 0, count($steps));

        foreach ($steps as $step) {
            $stepRow = $step->getActionStepRow();
            $stepRow->date_executed = 'NOW()';
            $stepRow->save();

            //przemiel jeden krok
            $this->actionRow->current_action_step = $stepRow->id;
            $this->actionRow->save();

            ClassGroup_Progress::step($progressID);

            //otaczamy try { } catch { } ponieważ po stronie googli mogą się stać różne rzeczy...
            try {
                if ($direction == Model_Action::DIRECTION_FORWARD) {
                    $step->forward();
                }
                elseif ($direction == Model_Action::DIRECTION_BACKWARD) {
                    $step->backward();
                }
            }
            catch (Exception $e) {
                //tutaj jakaś fancy obsługa błędów
                trigger_error(sprintf('Error while processing %s: %s at %s:%s', get_class($step), $e->getMessage(), $e->getFile(), $e->getLine()), E_USER_NOTICE);
            }
        }

        $this->actionRow->date_end = 'NOW()';
        $this->actionRow->current_action_step = null;
        $this->actionRow->save();
    }

    public function backward()
    {
        $this->work(Model_Action::DIRECTION_BACKWARD);
    }

    public function forward()
    {
        $this->work(Model_Action::DIRECTION_FORWARD);
    }
}
