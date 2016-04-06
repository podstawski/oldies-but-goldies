CREATE OR REPLACE FUNCTION update_password(Varchar, Varchar, Boolean)  RETURNS Integer
AS $$
DECLARE
	show_plain_password ALIAS FOR $3;
	result Integer;
	pass Varchar;
BEGIN
	EXECUTE ('
		ALTER USER '||$1||' ENCRYPTED PASSWORD '''||$2||''';
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



CREATE OR REPLACE FUNCTION create_user(Varchar, Varchar, Boolean, Integer)  RETURNS Integer
AS $$
DECLARE
	result Integer;
BEGIN
	EXECUTE ('
		CREATE USER '||$1||' ENCRYPTED PASSWORD '''||$2||''';
		INSERT INTO users (username) VALUES ('''||$1||''');
	');

	SELECT INTO result update_password($1,$2,$3);
        UPDATE users SET role_id = $4 WHERE id = result;

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
		DROP USER '||$1||' ;
		DELETE FROM users WHERE username='''||$1||''';
	');

	RETURN result;
END;
$$
LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION acl_has_right(Name, Integer, Varchar, Name) RETURNS Boolean
AS $$
DECLARE
	table_name ALIAS FOR $1;
	object_id ALIAS FOR $2;
	"right" ALIAS FOR $3;
	username ALIAS FOR $4;
	result Boolean;
BEGIN
	SELECT INTO result usesuper FROM pg_user WHERE usename = '$4';

	IF result THEN
		RETURN true;
	END IF;

	FOR result IN EXECUTE('SELECT  _' || "right" || ' FROM ' || table_name || '_acl WHERE object_id IN (0,' || object_id || ') AND username IN (''*'', ''' || username || ''');') LOOP
	END LOOP;


	RETURN COALESCE(result,false);
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION acl_has_right(Name, Integer, Varchar) RETURNS Boolean
AS $$
SELECT acl_has_right($1, $2, $3, CURRENT_USER);
$$
LANGUAGE sql;




CREATE OR REPLACE FUNCTION yala_user(Varchar) RETURNS Varchar
AS $$
BEGIN
    EXECUTE ('
        ALTER USER '||CURRENT_DATABASE()||'_'||$1||' RENAME TO '|| $1 || ' ;
    ');
    RETURN $1;    
END;
$$
LANGUAGE plpgsql;


UPDATE users SET username=yala_user(username) WHERE username<>CURRENT_USER;

DROP FUNCTION yala_user(Varchar);
