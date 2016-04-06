
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


SELECT create_acl_table('course_schedule');
SELECT create_acl_table('course_units');
SELECT create_acl_table('courses');
SELECT create_acl_table('exam_grades');
SELECT create_acl_table('exams');
SELECT create_acl_table('files');
SELECT create_acl_table('group_users');
SELECT create_acl_table('groups');
SELECT create_acl_table('lesson_presence');
SELECT create_acl_table('lessons');
SELECT create_acl_table('message_attachments');
SELECT create_acl_table('message_users');
SELECT create_acl_table('messages');
SELECT create_acl_table('projects');
SELECT create_acl_table('poland');
SELECT create_acl_table('quiz_scores');
SELECT create_acl_table('quizzes');
SELECT create_acl_table('quiz_users');
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
SELECT create_acl_table('training_centers');
SELECT create_acl_table('users');
SELECT create_acl_table('user_profile');



