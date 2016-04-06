<?php

class LessonPresence extends AclModel
{
    static $table_name = 'lesson_presence';
    static $use_view = true;

    static $after_save = 'RunAcl';

    public function RunAcl()
    {
        $this->grant(Role::USER,$this->user_id, $this->id);
    }
}
