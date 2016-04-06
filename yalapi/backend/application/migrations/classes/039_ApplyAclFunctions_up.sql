
GRANT ALL ON SCHEMA public TO public;

DROP FUNCTION IF EXISTS create_acl_table();

CREATE OR REPLACE FUNCTION drop_acl_view(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE ('
		DROP VIEW IF EXISTS ' || $1 || '_view;
	');

	RETURN $1;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION create_acl_view(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE ('
                DROP VIEW IF EXISTS ' || $1 || '_view;
		CREATE VIEW ' || $1 || '_view AS 
		SELECT ' || $1 || '.* FROM ' || $1 || ' 
		INNER JOIN ' || $1 || '_acl 
			ON ' || $1 || '_acl.object_id IN (0,' || $1 || '.id) AND ' || $1 || '_acl.username IN (CURRENT_USER,''*'') AND _select ;
		GRANT ALL ON ' || $1 || '_view TO public;

	');

	RETURN $1;
END;
$$
LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION acl_insert_cascade() RETURNS "trigger"
AS $$
BEGIN
	EXECUTE ('
		INSERT INTO ' || TG_ARGV[0] || '_acl (object_id)
                SELECT ' || NEW.id || '
                WHERE NOT acl_has_right(''' || TG_ARGV[0] || ''',' || NEW.id || ',''update'');
	');
RETURN OLD;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION create_acl_table(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE('
		DROP TABLE IF EXISTS ' || $1 || '_acl CASCADE; 
		CREATE TABLE ' || $1 || '_acl (object_id Integer, username Name DEFAULT CURRENT_USER, _select boolean DEFAULT true, _update boolean DEFAULT true, _insert boolean DEFAULT true, _delete boolean DEFAULT true);
		CREATE UNIQUE INDEX ' || $1 || '_acl_key ON ' || $1 || '_acl (object_id, username);
		GRANT SELECT,DELETE,INSERT ON ' || $1 || '_acl TO public;
                GRANT ALL ON ' || $1 || ' TO public;

		DROP RULE IF EXISTS ' || $1 || '_delete ON ' || $1 || ';
		CREATE RULE ' || $1 || '_delete AS ON DELETE TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , id, ''delete'') DO INSTEAD NOTHING;		

		DROP RULE IF EXISTS ' || $1 || '_update ON ' || $1 || ';
		CREATE RULE ' || $1 || '_update AS ON UPDATE TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , NEW.id, ''update'') DO INSTEAD NOTHING;

		DROP RULE IF EXISTS ' || $1 || '_insert ON ' || $1 || ';
		CREATE RULE ' || $1 || '_insert AS ON INSERT TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , NEW.id, ''insert'') DO INSTEAD NOTHING;

		DROP TRIGGER IF EXISTS ' || $1 || '_acl_delete ON ' || $1 || ';
		CREATE TRIGGER ' || $1 || '_acl_delete
		AFTER DELETE ON ' || $1 || ' FOR EACH ROW
		EXECUTE PROCEDURE acl_delete_cascade(''' || $1 || ''');		

		DROP TRIGGER IF EXISTS ' || $1 || '_acl_insert ON ' || $1 || ';
		CREATE TRIGGER ' || $1 || '_acl_insert
		AFTER INSERT ON ' || $1 || ' FOR EACH ROW
		EXECUTE PROCEDURE acl_insert_cascade(''' || $1 || ''');
                
                
                

	');
        
        PERFORM create_acl_view( $1 );

        EXECUTE ('INSERT INTO ' || $1 || '_acl ("object_id","username") VALUES (0,''*'');');
	RETURN $1 || '_acl';
END;
$$
LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION drop_acl_table(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE('
		DROP TABLE IF EXISTS ' || $1 || '_acl CASCADE; 
		DROP RULE IF EXISTS ' || $1 || '_delete ON ' || $1 || ';	
		DROP RULE IF EXISTS ' || $1 || '_update ON ' || $1 || ';
		DROP RULE IF EXISTS ' || $1 || '_insert ON ' || $1 || ';
		DROP TRIGGER IF EXISTS ' || $1 || '_acl_delete ON ' || $1 || ';
		DROP TRIGGER IF EXISTS ' || $1 || '_acl_insert ON ' || $1 || ';

	');
        PERFORM drop_acl_view( $1 );
	RETURN $1 || '_acl';
END;
$$
LANGUAGE plpgsql;


SELECT create_acl_table('course_units');
SELECT create_acl_table('courses');
SELECT create_acl_table('exam_grades');
SELECT create_acl_table('exams');
SELECT create_acl_table('group_users');
SELECT create_acl_table('groups');
SELECT create_acl_table('lesson_presence');
SELECT create_acl_table('lessons');
SELECT create_acl_table('projects');
SELECT create_acl_table('quiz_scores');
SELECT create_acl_table('quizzes');
SELECT create_acl_table('reports');
SELECT create_acl_table('resource_types');
SELECT create_acl_table('resources');
SELECT create_acl_table('roles');
SELECT create_acl_table('rooms');
SELECT create_acl_table('survey_detailed_results');
SELECT create_acl_table('survey_possible_answers');
SELECT create_acl_table('survey_questions');
SELECT create_acl_table('survey_results');
SELECT create_acl_table('survey_users');
SELECT create_acl_table('surveys');
SELECT create_acl_table('surveys_library');
SELECT create_acl_table('training_centers');
SELECT create_acl_table('users');







