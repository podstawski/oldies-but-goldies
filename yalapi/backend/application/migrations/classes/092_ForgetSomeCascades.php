<?php

class ForgetSomeCascades extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->exec('
		ALTER TABLE courses DROP CONSTRAINT fk_courses_groups;
		ALTER TABLE courses ADD CONSTRAINT fk_courses_groups FOREIGN KEY (group_id) REFERENCES groups(id) ON UPDATE CASCADE ON DELETE SET NULL;

		ALTER TABLE course_units DROP CONSTRAINT fk_course_units_users;
		ALTER TABLE course_units ADD CONSTRAINT fk_course_units_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL;


        ');
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec('
		ALTER TABLE courses DROP CONSTRAINT fk_courses_groups;
		ALTER TABLE courses ADD CONSTRAINT fk_courses_groups FOREIGN KEY (group_id) REFERENCES groups(id) ON UPDATE CASCADE ON DELETE CASCADE;
	
		ALTER TABLE course_units DROP CONSTRAINT fk_course_units_users;
		ALTER TABLE course_units ADD CONSTRAINT fk_course_units_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;
	');
    }
}
