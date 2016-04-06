<?php

class CreateFunctionModules45 extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->exec('
CREATE OR REPLACE FUNCTION public.LessonModules45(integer)
    RETURNS integer
    LANGUAGE sql
AS $$
	SELECT CAST(FLOOR((EXTRACT(EPOCH FROM end_date) - EXTRACT(EPOCH FROM start_date))/2700) AS Integer) FROM lessons WHERE id = $1
$$;

CREATE OR REPLACE FUNCTION public.LessonModules45(timestamp, timestamp)
    RETURNS integer
    LANGUAGE sql
AS $$
	SELECT CAST(FLOOR((EXTRACT(EPOCH FROM $2) - EXTRACT(EPOCH FROM $1))/2700) AS Integer)
$$;
        ');
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec('
            DROP FUNCTION IF EXISTS public.LessonModules45(integer);
            DROP FUNCTION IF EXISTS public.LessonModules45(timestamp, timestamp);
        ');
    }
}