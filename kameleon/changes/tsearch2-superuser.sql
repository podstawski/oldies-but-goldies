--\i /usr/local/pgsql/share/contrib/tsearch2.sql
\i /usr/share/postgresql-8.4/contrib/tsearch2.sql

UPDATE pg_ts_cfg SET locale = 'UTF8' WHERE ts_name = 'default';

GRANT ALL ON pg_ts_dict TO kameleon;
GRANT ALL ON pg_ts_parser TO kameleon;
GRANT ALL ON pg_ts_cfg TO kameleon;
GRANT ALL ON pg_ts_cfgmap TO kameleon;

CREATE LANGUAGE  plpgsql;

