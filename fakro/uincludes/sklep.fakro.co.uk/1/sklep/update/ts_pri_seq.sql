CREATE OR REPLACE FUNCTION ts_pri_seq() returns integer
AS '
	SELECT max(ts_pri)+1 FROM towar_sklep WHERE 1 BETWEEN 0 AND (SELECT count(*) FROM towar_sklep WHERE ts_pri IS NOT NULL)
	UNION
	SELECT 1 FROM towar_sklep WHERE 0 BETWEEN (SELECT count(*) FROM towar_sklep WHERE ts_pri IS NOT NULL) AND 1
'
LANGUAGE 'sql';

CREATE OR REPLACE FUNCTION ts_pri2_seq() returns integer
AS '
	SELECT max(ts_pri2)+1 FROM towar_sklep WHERE 1 BETWEEN 0 AND (SELECT count(*) FROM towar_sklep WHERE ts_pri2 IS NOT NULL)
	UNION
	SELECT 1 FROM towar_sklep WHERE 0 BETWEEN (SELECT count(*) FROM towar_sklep WHERE ts_pri2 IS NOT NULL) AND 1
'
LANGUAGE 'sql';


ALTER TABLE towar_sklep ALTER ts_pri DROP DEFAULT ;
ALTER TABLE towar_sklep ALTER ts_pri SET DEFAULT ts_pri_seq();

ALTER TABLE towar_sklep ALTER ts_pri2 DROP DEFAULT ;
ALTER TABLE towar_sklep ALTER ts_pri2 SET DEFAULT ts_pri2_seq();

UPDATE towar_sklep SET ts_pri=ts_id WHERE ts_pri IS NULL;
UPDATE towar_sklep SET ts_pri2=ts_id WHERE ts_pri2 IS NULL;
