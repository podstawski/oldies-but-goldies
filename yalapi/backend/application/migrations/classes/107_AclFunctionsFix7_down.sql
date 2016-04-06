DROP TRIGGER IF EXISTS acl_table_delete ON acl;
?>
CREATE TRIGGER acl_table_delete
BEFORE UPDATE ON acl
FOR EACH ROW
EXECUTE PROCEDURE acl_table_delete();

