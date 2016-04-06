<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Grid_Rank_Schools extends Game_Grid
{
    public function init()
    {
         $select = Zend_Db_Table::getDefaultAdapter()
            ->select()
            ->from('rank_school', array('rank_lp' => 'id', 'school_score' => 'score', 'employee_amount'))
            ->join('school', 'school.id = rank_school.school_id', array('school_name' => 'name'))
            ->order('rank_lp ASC');

        $this->setSource(new Bvb_Grid_Source_Zend_Select($select));

        $this->hideColumns('employee_amount');

        $this->updateColumn('rank_lp', array(
            'class' => 'text-right',
            'position' => 0
        ));

        $this->updateColumn('school_name', array(
            'position' => 1
        ));

        $this->updateColumn('school_score', array(
            'class' => 'text-right',
            'position' => 2,
            'format' => array('number')
        ));

        $this->updateColumn('employee_amount', array(
            'class' => 'text-right',
            'position' => 3,
            'format' => array('number')
        ));
    }
}