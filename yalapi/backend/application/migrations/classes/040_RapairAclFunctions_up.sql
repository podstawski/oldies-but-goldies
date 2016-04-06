

CREATE OR REPLACE FUNCTION create_acl_table(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE('
		DROP TABLE IF EXISTS ' || $1 || '_acl CASCADE; 
		CREATE TABLE ' || $1 || '_acl (object_id Integer, username Name DEFAULT CURRENT_USER, _select boolean DEFAULT true, _update boolean DEFAULT true, _insert boolean DEFAULT true, _delete boolean DEFAULT true);
		CREATE UNIQUE INDEX ' || $1 || '_acl_key ON ' || $1 || '_acl (object_id, username);
		GRANT ALL ON ' || $1 || '_acl TO public;
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







GRANT ALL ON course_units_id_seq TO public;
GRANT ALL ON courses_id_seq TO public;
GRANT ALL ON exam_grades_id_seq TO public;
GRANT ALL ON exams_id_seq TO public;
GRANT ALL ON group_users_id_seq TO public;
GRANT ALL ON groups_id_seq TO public;
GRANT ALL ON lesson_presence_id_seq TO public;
GRANT ALL ON lessons_id_seq TO public;
GRANT ALL ON projects_id_seq TO public;
GRANT ALL ON quiz_scores_id_seq TO public;
GRANT ALL ON quizzes_id_seq TO public;
GRANT ALL ON reports_id_seq TO public;
GRANT ALL ON resource_types_id_seq TO public;
GRANT ALL ON resources_id_seq TO public;
GRANT ALL ON roles_id_seq TO public;
GRANT ALL ON rooms_id_seq TO public;
GRANT ALL ON survey_detailed_results_id_seq TO public;
GRANT ALL ON survey_possible_answers_id_seq TO public;
GRANT ALL ON survey_questions_id_seq TO public;
GRANT ALL ON survey_results_id_seq TO public;
GRANT ALL ON survey_users_id_seq TO public;
GRANT ALL ON surveys_id_seq TO public;
GRANT ALL ON surveys_library_id_seq TO public;
GRANT ALL ON training_centers_id_seq TO public;
GRANT ALL ON users_id_seq TO public;
