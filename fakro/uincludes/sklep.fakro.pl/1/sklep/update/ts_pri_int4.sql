
Alter table "towar_sklep" add  ts_pri4 Integer;
Alter table "towar_sklep" add  ts_pri24 Integer;

UPDATE "towar_sklep" SET ts_pri4=ts_pri;
UPDATE "towar_sklep" SET ts_pri24=ts_pri2;

Alter table "towar_sklep" Rename ts_pri TO ts_pri_old;
Alter table "towar_sklep" Rename ts_pri4 TO ts_pri;

Alter table "towar_sklep" Rename ts_pri2 TO ts_pri2_old;
Alter table "towar_sklep" Rename ts_pri24 TO ts_pri2;
