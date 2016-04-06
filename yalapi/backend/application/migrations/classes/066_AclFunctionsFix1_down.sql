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


CREATE TABLE surveys_library_acl
(shit Int);
