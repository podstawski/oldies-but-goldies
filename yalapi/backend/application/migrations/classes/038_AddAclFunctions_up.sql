

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





CREATE OR REPLACE FUNCTION acl_delete_cascade() RETURNS "trigger"
AS $$
BEGIN
	EXECUTE ('
		DELETE FROM ' || TG_ARGV[0] || '_acl WHERE object_id=' || OLD.id || ';
	');
RETURN OLD;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION acl_insert_cascade() RETURNS "trigger"
AS $$
BEGIN
	EXECUTE ('
		INSERT INTO ' || TG_ARGV[0] || '_acl (object_id) VALUES (' || NEW.id || ');
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
		GRANT SELECT,DELETE ON ' || $1 || '_acl TO public;
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

	EXECUTE ('INSERT INTO ' || $1 || '_acl ("object_id") VALUES (0);');
        EXECUTE ('INSERT INTO ' || $1 || '_acl ("object_id","username") VALUES (0,''*'');');
	RETURN $1 || '_acl';
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION create_acl_table() RETURNS Integer
AS $$
DECLARE
	t Name;
	i Integer;
BEGIN
	i:=0;
	FOR t IN EXECUTE ('SELECT tablename FROM pg_tables WHERE tablename NOT LIKE ''pg_%'' AND tablename NOT LIKE ''sql_%'' AND SUBSTR(tablename,LENGTH(tablename)-3)<>''_acl'' AND tablename || ''_acl'' NOT IN (SELECT tablename FROM pg_tables AS pgt) ORDER BY tablename;') LOOP
		

		BEGIN
			SELECT create_acl_table(t);
			i:=i+1;		   

		END;
		


	END LOOP;
	RETURN i;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION create_acl_view(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE ('
		CREATE TEMP VIEW ' || $1 || ' AS 
		SELECT ' || $1 || '.* FROM ' || $1 || ' 
		INNER JOIN ' || $1 || '_acl 
			ON ' || $1 || '_acl.object_id IN (0,' || $1 || '.id) AND username=CURRENT_USER AND _select ;

	');

	RETURN $1;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION drop_acl_view(Name) RETURNS Name
AS $$
BEGIN
	EXECUTE ('
		DROP VIEW ' || $1 || ';
	');

	RETURN $1;
END;
$$
LANGUAGE plpgsql;





