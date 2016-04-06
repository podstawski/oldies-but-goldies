
DROP LANGUAGE IF EXISTS plpgsql;
CREATE LANGUAGE plpgsql;

CREATE TABLE fts 
(
	fts_page_sid Integer,
	fts_td_sid Integer,
	fts_server Integer,
	fts_lang char(2),
	fts_ver Double precision,
	fts_text tsvector
);

CREATE OR REPLACE FUNCTION webtd_nohtml(Integer) RETURNS Text
AS $$
	SELECT nohtml FROM webtd WHERE sid=$1
$$
LANGUAGE sql;





CREATE OR REPLACE FUNCTION webpage_update_fts() RETURNS "trigger"
AS $$
BEGIN
IF NEW.nd_ftp <> OLD.nd_ftp THEN
	UPDATE fts SET fts_text=to_tsvector(trim(NEW.title)||' '||trim(NEW.description)||' '||trim(NEW.keywords) ) WHERE fts_page_sid=NEW.sid AND fts_td_sid IS NULL;
	UPDATE fts SET fts_text=to_tsvector(webtd_nohtml(fts_td_sid)) WHERE fts_page_sid=NEW.sid AND fts_td_sid IS NOT NULL;
END IF;
RETURN NEW;
END;

$$
LANGUAGE plpgsql;


CREATE TRIGGER webpage_update_fts
AFTER UPDATE ON webpage
FOR EACH ROW
EXECUTE PROCEDURE webpage_update_fts();

 
CREATE OR REPLACE FUNCTION webpage_insert_fts() RETURNS "trigger"
AS $$
BEGIN 
INSERT INTO fts VALUES (NEW.sid,NULL,NEW.server,NEW.lang,NEW.ver,NULL);
RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE TRIGGER webpage_insert_fts
AFTER INSERT ON webpage
FOR EACH ROW
EXECUTE PROCEDURE webpage_insert_fts();


CREATE OR REPLACE FUNCTION webpage_delete_fts() RETURNS "trigger"
AS $$
BEGIN
DELETE FROM fts WHERE fts_page_sid=OLD.sid;
RETURN OLD;
END;
$$
LANGUAGE plpgsql;

CREATE TRIGGER webpage_delete_fts
BEFORE DELETE ON webpage
FOR EACH ROW
EXECUTE PROCEDURE webpage_delete_fts();



CREATE OR REPLACE FUNCTION webtd_insert_fts() RETURNS "trigger"
AS $$
BEGIN 
INSERT INTO fts SELECT sid,NEW.sid,NEW.server,NEW.lang,NEW.ver,NULL FROM webpage WHERE server=NEW.server AND lang=NEW.lang AND ver=NEW.ver AND id=NEW.page_id;
RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE TRIGGER webtd_insert_fts
AFTER INSERT ON webtd
FOR EACH ROW
EXECUTE PROCEDURE webtd_insert_fts();


CREATE OR REPLACE FUNCTION webtd_delete_fts() RETURNS "trigger"
AS $$
BEGIN
DELETE FROM fts WHERE fts_td_sid=OLD.sid;
RETURN OLD;
END;
$$
LANGUAGE plpgsql;

CREATE TRIGGER webtd_delete_fts
BEFORE DELETE ON webtd
FOR EACH ROW
EXECUTE PROCEDURE webtd_delete_fts();





INSERT INTO fts SELECT sid,NULL,server,lang,ver,to_tsvector(trim(title)||' '||trim(description)||' '||trim(keywords) ) FROM webpage;

INSERT INTO fts SELECT webpage.sid,webtd.sid,webtd.server,webtd.lang,webtd.ver,to_tsvector(trim(webtd.title)||' '||nohtml) 
FROM webtd LEFT JOIN webpage ON webtd.server=webpage.server AND webtd.lang=webpage.lang AND webtd.ver=webpage.ver AND webtd.page_id=webpage.id;




CREATE INDEX fts_gin_idx ON fts USING gin(fts_text);

    

