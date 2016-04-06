ALTER TABLE towar_sklep ADD ts_aktywny Smallint ;
ALTER TABLE towar_sklep ALTER ts_aktywny SET DEFAULT 1;
UPDATE towar_sklep SET ts_aktywny=1;
