ALTER TABLE towar RENAME to_ka_id TO to_ka_c;
ALTER TABLE towar ALTER to_ka_c SET DEFAULT 0;

ALTER TABLE kategorie ADD ka_to_c Integer;
ALTER TABLE kategorie ALTER ka_to_c SET DEFAULT 0;

CREATE function ileTowWKat (Integer) returns Bigint
AS 'SELECT count(tk_id) FROM towar_kategoria WHERE tk_ka_id = $1'
LANGUAGE 'sql';

CREATE function wIluKatTow (Integer) returns Bigint
AS 'SELECT count(tk_id) FROM towar_kategoria WHERE tk_to_id = $1'
LANGUAGE 'sql';

UPDATE towar SET to_ka_c = wIluKatTow (to_id);
UPDATE kategorie SET ka_to_c = ileTowWKat (ka_id);
