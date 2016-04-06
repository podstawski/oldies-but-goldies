CREATE OR REPLACE FUNCTION mypri() RETURNS Smallint AS
'
SELECT COALESCE(
    (SELECT pri FROM roles,users WHERE users.role_id=roles.id AND users.username=SUBSTR(CURRENT_USER,2+LENGTH(CURRENT_DATABASE()))),
    CAST (0 AS Smallint)
)
'
LANGUAGE 'sql';


CREATE OR REPLACE FUNCTION rolepri(Integer) RETURNS Smallint AS
'
SELECT COALESCE(
    (SELECT pri FROM roles WHERE id=$1),
    CAST (0 AS Smallint)
)   
'
LANGUAGE 'sql';

CREATE RULE users_change_pri
AS ON UPDATE TO users
WHERE rolepri(COALESCE(NEW.role_id,0))<mypri() OR rolepri(COALESCE(OLD.role_id,0))<mypri()
DO INSTEAD NOTHING;