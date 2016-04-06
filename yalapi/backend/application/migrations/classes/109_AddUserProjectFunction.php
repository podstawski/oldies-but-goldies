<?php

class AddUserProjectFunction extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->execute('
	    CREATE OR REPLACE function user_projects(Integer) RETURNS setof Integer
	    AS
	    $$
		    SELECT project_id FROM project_leaders WHERE user_id=$1

		    UNION

		    SELECT courses.project_id FROM courses,course_units WHERE courses.id=course_units.course_id AND course_units.user_id=$1

		    UNION

		    SELECT courses.project_id FROM courses,course_units,lessons WHERE courses.id=course_units.course_id AND lessons.course_unit_id=course_units.id AND lessons.user_id=$1

		    UNION

		    SELECT courses.project_id FROM courses,group_users WHERE courses.group_id=group_users.group_id AND group_users.user_id=$1
	    $$
	    LANGUAGE sql
	');
    }

    public function down()
    {
	Doctrine_Manager::connection()->execute('DROP FUNCTION user_projects(Integer)');
    }
}
