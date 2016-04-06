CREATE OR REPLACE FUNCTION public.create_user(character varying, character varying, character varying, character varying, boolean, integer)
	RETURNS integer
	LANGUAGE plpgsql
AS $$
DECLARE
	result integer;
BEGIN
	EXECUTE E'CREATE USER "' || CURRENT_DATABASE() || '_' || $1 || '" ENCRYPTED PASSWORD ''' || $2 || '''';
	INSERT INTO users (username, first_name, last_name) VALUES ($1, $3, $4);

	SELECT INTO result update_password($1, $2, $5);
    UPDATE users SET role_id = $6 WHERE id = result;

	RETURN result;
END;
$$;

CREATE OR REPLACE FUNCTION public.create_user(character varying, character varying, boolean, integer)
	RETURNS integer
	LANGUAGE plpgsql
AS $$
DECLARE
	result Integer;
BEGIN
	EXECUTE E'CREATE USER "' || CURRENT_DATABASE() || '_' || $1 || '" ENCRYPTED PASSWORD ''' || $2 || '''';
	INSERT INTO users (username) VALUES ($1);

	SELECT INTO result update_password($1, $2, $3);
    UPDATE users SET role_id = $4 WHERE id = result;

	RETURN result;
END;
$$;

CREATE OR REPLACE FUNCTION public.update_password(character varying, character varying, boolean)
	RETURNS integer
	LANGUAGE plpgsql
AS $$
DECLARE
	show_plain_password ALIAS FOR $3;
	result Integer;
	pass Varchar;
BEGIN
	EXECUTE E'ALTER USER "' || CURRENT_DATABASE() || '_' || $1 || '" ENCRYPTED PASSWORD ''' || $2 || '''';

	pass = NULL;
	IF show_plain_password THEN
		pass = $2;
	END IF;

	UPDATE users SET plain_password = pass WHERE username = $1;
	SELECT INTO result id FROM users WHERE username = $1;

	RETURN result;

END;
$$;

CREATE OR REPLACE FUNCTION public.delete_user(character varying)
	RETURNS integer
	LANGUAGE plpgsql
AS $$
DECLARE
	result Integer;
BEGIN
	SELECT INTO result id FROM users WHERE username = $1;

	EXECUTE E'DROP USER "' || CURRENT_DATABASE() || '_' || $1 || '"';
	DELETE FROM users WHERE username = $1;

	RETURN result;
END;
$$;

CREATE OR REPLACE FUNCTION public.acl_has_right(name, integer, character varying, name)
	RETURNS boolean
	LANGUAGE plpgsql
AS $$
DECLARE
	result Boolean;
BEGIN
	SELECT INTO result usesuper FROM pg_user WHERE usename IN ($4);

	IF result THEN
		RETURN true;
	END IF;

	FOR result IN EXECUTE 'SELECT _' || $3 || ' FROM ' || $1 || '_acl WHERE object_id IN (0, ' || $2 || ') AND username IN (''*'', ''' || CURRENT_DATABASE() || '_' || $4 || ''') ORDER BY _' || $3 || ' DESC LIMIT 1' LOOP END LOOP;


	RETURN COALESCE(result, FALSE);
END;
$$;

CREATE OR REPLACE FUNCTION public.acl_has_right(name, integer, character varying)
	RETURNS boolean
	LANGUAGE sql
AS $$
	SELECT acl_has_right($1, $2, $3, replace(CURRENT_USER, CURRENT_DATABASE() || '_', ''));
$$;