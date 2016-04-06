
DROP TRIGGER IF EXISTS acl_table_delete ON acl;

CREATE TRIGGER acl_table_delete
BEFORE DELETE ON acl
FOR EACH ROW
EXECUTE PROCEDURE acl_table_delete();



