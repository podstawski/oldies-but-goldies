Create table "promocja"
(
	"pm_id" Serial NOT NULL,
	"pm_symbol" Char(32),
	"pm_poczatek" integer,
	"pm_koniec" integer,
	"pm_rabat_domyslny" Double precision,
 primary key ("pm_id")
);

Create table "promocja_towaru"
(
	"pt_id" Serial NOT NULL,
	"pt_ts_id" integer NOT NULL,
	"pt_pm_id" integer NOT NULL,
	"pt_cena" Double precision,
 primary key ("pt_id")
);


Alter table "promocja_towaru" add  foreign key ("pt_pm_id") references "promocja" ("pm_id") on update cascade on delete cascade;
Alter table "promocja_towaru" add  foreign key ("pt_ts_id") references "towar_sklep" ("ts_id") on update cascade on delete cascade;
