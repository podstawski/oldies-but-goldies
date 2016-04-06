<?php

class CreateFunctionGetEmptySeats extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->exec('
CREATE OR REPLACE FUNCTION public.get_empty_seats(integer)
    RETURNS integer
    LANGUAGE plpgsql
AS $$
BEGIN
	RETURN (
        SELECT COALESCE(MIN(rooms.available_space), 0)
        FROM course_units
        INNER JOIN lessons ON lessons.course_unit_id = course_units.id
        INNER JOIN rooms ON rooms.id = lessons.room_id
        WHERE course_units.course_id = $1
    ) - (
        SELECT COUNT(*)
        FROM group_users
        INNER JOIN courses ON courses.group_id = group_users.group_id
        WHERE courses.id = $1
    );
END;
$$;
        ');
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec('DROP FUNCTION IF EXISTS public.get_empty_seats(integer);');
    }
}
