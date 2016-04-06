<?php

class ExamGrade extends AclModel
{
    static $table_name = 'exam_grades';
    static $use_view = true;

    static $belongs_to = array(
        array('exam'),
        array('user')
    );

    static $after_save = 'RunAcl';

    public function RunAcl()
    {
        $this->grant(Role::USER, $this->user_id, $this->id);
    }
}
