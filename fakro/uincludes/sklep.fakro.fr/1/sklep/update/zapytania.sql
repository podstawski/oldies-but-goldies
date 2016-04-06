
Create table "zapytania"
(
	"za_id" Serial NOT NULL,
	"za_ts_id" integer NOT NULL,
	"za_pyt_su_id" integer,
	"za_odp_su_id" integer NOT NULL,
	"za_email" Char(96),
	"za_telefon" Char(48),
	"za_pyt" Text,
	"za_odp" Text,
	"za_pyt_data" integer,
	"za_odp_data" integer,
 primary key ("za_id")
);


Alter table "zapytania" add  foreign key ("za_ts_id") references "towar_sklep" ("ts_id") on update cascade on delete cascade;
Alter table "zapytania" add  foreign key ("za_pyt_su_id") references "system_user" ("su_id") on update cascade on delete cascade;
Alter table "zapytania" add  foreign key ("za_odp_su_id") references "system_user" ("su_id") on update cascade on delete cascade;
