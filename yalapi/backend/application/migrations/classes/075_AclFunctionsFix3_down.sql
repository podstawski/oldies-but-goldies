

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

