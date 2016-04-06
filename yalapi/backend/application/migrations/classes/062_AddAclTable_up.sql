CREATE TABLE acl
(
    id Serial,
    table_name Varchar(64),
    username Name,
    _select Boolean Default true,
    _update Boolean Default true,
    _insert Boolean Default true,
    _delete Boolean Default true,
    updated Timestamp Default CURRENT_TIMESTAMP
);

GRANT ALL ON acl TO public;
CREATE INDEX acl_key ON acl(table_name,username);

CREATE OR REPLACE FUNCTION acl_table_change() RETURNS "trigger"
AS $$
BEGIN
NEW.updated=CURRENT_TIMESTAMP;
RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE TRIGGER acl_table_change
BEFORE UPDATE ON acl
FOR EACH ROW
EXECUTE PROCEDURE acl_table_change();


CREATE OR REPLACE FUNCTION acl_table_delete() RETURNS "trigger"
AS $$
BEGIN
UPDATE acl SET updated=CURRENT_TIMESTAMP WHERE id IN
    (SELECT id FROM acl WHERE id<>OLD.id AND table_name=OLD.table_name LIMIT 1);
RETURN OLD;
END;
$$
LANGUAGE plpgsql;

CREATE TRIGGER acl_table_delete
BEFORE UPDATE ON acl
FOR EACH ROW
EXECUTE PROCEDURE acl_table_delete();

