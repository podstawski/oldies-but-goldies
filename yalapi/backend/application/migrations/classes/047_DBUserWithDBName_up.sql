

CREATE OR REPLACE FUNCTION acl_has_right(Name, Integer, Varchar, Name) RETURNS Boolean
AS $$
DECLARE
	table_name ALIAS FOR $1;
	object_id ALIAS FOR $2;
	"right" ALIAS FOR $3;
	username ALIAS FOR $4;
	result Boolean;
BEGIN
	SELECT INTO result usesuper FROM pg_user WHERE usename IN ($4);

	IF result THEN
		RETURN true;
	END IF;
        

	FOR result IN EXECUTE('SELECT  _' || "right" || ' FROM ' || table_name || '_acl WHERE object_id IN (0,' || object_id || ') AND username IN (''*'', ''' || CURRENT_DATABASE() || '_'|| username || ''');') LOOP
	END LOOP;


	RETURN COALESCE(result,false);
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION acl_has_right(Name, Integer, Varchar) RETURNS Boolean
AS $$
SELECT acl_has_right($1, $2, $3, replace(CURRENT_USER,CURRENT_DATABASE()||'_',''));
$$
LANGUAGE sql;



CREATE OR REPLACE FUNCTION create_acl_table(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE('
		DROP TABLE IF EXISTS ' || $1 || '_acl CASCADE; 
		CREATE TABLE ' || $1 || '_acl (object_id Integer DEFAULT 0, username Name DEFAULT CURRENT_USER, _select boolean DEFAULT true, _update boolean DEFAULT true, _insert boolean DEFAULT true, _delete boolean DEFAULT true);
		CREATE UNIQUE INDEX ' || $1 || '_acl_key ON ' || $1 || '_acl (object_id, username);
		GRANT ALL ON ' || $1 || '_acl TO public;
                GRANT ALL ON ' || $1 || ' TO public;
                GRANT ALL ON ' || $1 || '_id_seq TO public;

		DROP RULE IF EXISTS ' || $1 || '_delete ON ' || $1 || ';
		CREATE RULE ' || $1 || '_delete AS ON DELETE TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , id, ''delete'') DO INSTEAD NOTHING;		

		DROP RULE IF EXISTS ' || $1 || '_update ON ' || $1 || ';
		CREATE RULE ' || $1 || '_update AS ON UPDATE TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , NEW.id, ''update'') DO INSTEAD NOTHING;

		DROP RULE IF EXISTS ' || $1 || '_insert ON ' || $1 || ';
		CREATE RULE ' || $1 || '_insert AS ON INSERT TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , 0, ''insert'') DO INSTEAD NOTHING;

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




CREATE OR REPLACE FUNCTION update_password(Varchar, Varchar, Boolean)  RETURNS Integer
AS $$
DECLARE
	show_plain_password ALIAS FOR $3;
	result Integer;
	pass Varchar;
BEGIN
	EXECUTE ('
		ALTER USER '||CURRENT_DATABASE()||'_'||$1||' ENCRYPTED PASSWORD '''||$2||''';
	');

	pass=null;
	IF show_plain_password THEN
		pass=$2;
	END IF;

	UPDATE users SET plain_password = pass WHERE username = $1;
	SELECT INTO result id FROM users WHERE username = $1;

	RETURN result;

END;
$$
LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION create_user(Varchar, Varchar, Varchar, Varchar, Boolean, Integer)  RETURNS Integer
AS $$
DECLARE
	result Integer;
BEGIN

	EXECUTE ('
		CREATE USER '||CURRENT_DATABASE()||'_'||$1||' ENCRYPTED PASSWORD '''||$2||''';
		INSERT INTO users (username, first_name, last_name) VALUES ('''||$1||''', '''||$3||''', '''||$4||''');
	');

	SELECT INTO result update_password($1,$2,$5);
        UPDATE users SET role_id = $6 WHERE id = result;

	RETURN result;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION delete_user(Varchar)  RETURNS Integer
AS $$
DECLARE
	result Integer;
BEGIN
	SELECT INTO result id FROM users WHERE username = $1;

	EXECUTE ('
		DROP USER '||CURRENT_DATABASE()||'_'||$1||' ;
		DELETE FROM users WHERE username='''||$1||''';
	');

	RETURN result;
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

SELECT create_acl_table('course_schedule');

GRANT ALL ON course_schedule_id_seq TO public;



    


CREATE OR REPLACE FUNCTION yala_user(Varchar) RETURNS Varchar
AS $$
BEGIN
    EXECUTE ('
        ALTER USER ' || $1 || ' RENAME TO '||CURRENT_DATABASE()||'_'||$1||' ;
    ');
    RETURN $1;    
END;
$$
LANGUAGE plpgsql;


UPDATE users SET username=yala_user(username) WHERE username<>CURRENT_USER;

DROP FUNCTION yala_user(Varchar);
