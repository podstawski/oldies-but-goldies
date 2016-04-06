<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class FixCourseScheduleFK extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE course_schedule DROP CONSTRAINT fk_schedule_lesson_id');
        Doctrine_Manager::connection()->exec('ALTER TABLE course_schedule ADD CONSTRAINT fk_schedule_lesson_id FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE CASCADE');
    }

    public function down()
    {

    }
}