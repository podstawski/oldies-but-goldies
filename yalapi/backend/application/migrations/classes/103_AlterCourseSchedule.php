<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterCourseSchedule extends Doctrine_Migration_Base
{
    public function up()
    {
        $db = Doctrine_Manager::connection();

        $db->exec('SELECT drop_acl_view(\'course_schedule\')');

        $db->exec('ALTER TABLE course_schedule ADD COLUMN lesson_id INT');

        foreach ($db->fetchAll('SELECT id, CAST (start_date AS DATE) AS lesson_date, course_unit_id FROM lessons ORDER BY start_date ASC') as $lesson) {
            $id = $db->fetchOne('SELECT id FROM course_schedule
                WHERE lesson_id IS NULL
                AND lesson_date = ?
                AND course_unit_id = ?
                ORDER BY lesson_date ASC
                LIMIT 1', array($lesson['lesson_date'], $lesson['course_unit_id'])
            );

            if ($id) {
                $db->exec('UPDATE course_schedule SET lesson_id = ? WHERE id = ?', array($lesson['id'], $id));
            }
        }

        $db->exec('DELETE FROM course_schedule WHERE lesson_id IS NULL');
        $db->exec('ALTER TABLE course_schedule ALTER COLUMN lesson_id SET NOT NULL');
        $db->exec('ALTER TABLE course_schedule ADD CONSTRAINT fk_schedule_lesson_id FOREIGN KEY (lesson_id) REFERENCES lessons (id)');
        $db->exec('CREATE UNIQUE INDEX idxu_schedule_lesson_id ON course_schedule (lesson_id)');

        $db->exec('ALTER TABLE course_schedule DROP CONSTRAINT fk_course_schedule_group');
        $db->exec('ALTER TABLE course_schedule DROP COLUMN course_unit_id');
        $db->exec('ALTER TABLE course_schedule DROP COLUMN lesson_date');

        $db->exec('SELECT create_acl_view(\'course_schedule\')');
    }

    public function down()
    {
        $db = Doctrine_Manager::connection();

        $db->exec('SELECT drop_acl_view(\'course_schedule\')');

        $db->exec('ALTER TABLE course_schedule ADD COLUMN lesson_date DATE');
        $db->exec('ALTER TABLE course_schedule ADD COLUMN course_unit_id INT');

        $db->exec('UPDATE course_schedule
            SET lesson_date = CAST (lessons.start_date AS DATE), course_unit_id = lessons.course_unit_id
            FROM lessons
            WHERE lessons.id = lesson_id'
        );

        $db->exec('DELETE FROM course_schedule WHERE lesson_date IS NULL');

        $db->exec('DROP INDEX IF EXISTS idxu_schedule_lesson_id');
        $db->exec('ALTER TABLE course_schedule DROP CONSTRAINT fk_schedule_lesson_id');
        $db->exec('ALTER TABLE course_schedule DROP COLUMN lesson_id');

        $db->exec('ALTER TABLE course_schedule ALTER COLUMN lesson_date SET NOT NULL');
        $db->exec('ALTER TABLE course_schedule ALTER COLUMN course_unit_id SET NOT NULL');
        $db->exec('ALTER TABLE course_schedule ADD CONSTRAINT fk_course_schedule_group FOREIGN KEY (course_unit_id) REFERENCES course_units(id)');

        $db->exec('SELECT create_acl_view(\'course_schedule\')');
    }
}