
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


SELECT create_acl_view('course_schedule');
SELECT create_acl_view('course_units');
SELECT create_acl_view('courses');
SELECT create_acl_view('exam_grades');
SELECT create_acl_view('exams');
SELECT create_acl_view('files');
SELECT create_acl_view('group_users');
SELECT create_acl_view('groups');
SELECT create_acl_view('lesson_presence');
SELECT create_acl_view('lessons');
SELECT create_acl_view('message_attachments');
SELECT create_acl_view('message_users');
SELECT create_acl_view('messages');
SELECT create_acl_view('projects');
SELECT create_acl_view('poland');
SELECT create_acl_view('quiz_scores');
SELECT create_acl_view('quizzes');
SELECT create_acl_view('quiz_users');
SELECT create_acl_view('reports');
SELECT create_acl_view('resource_types');
SELECT create_acl_view('resources');
SELECT create_acl_view('roles');
SELECT create_acl_view('rooms');
SELECT create_acl_view('survey_detailed_results');
SELECT create_acl_view('survey_possible_answers');
SELECT create_acl_view('survey_questions');
SELECT create_acl_view('survey_results');
SELECT create_acl_view('survey_users');
SELECT create_acl_view('surveys');
SELECT create_acl_view('training_centers');
SELECT create_acl_view('users');
SELECT create_acl_view('user_profile');



