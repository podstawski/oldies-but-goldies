
Create table "adresy"
(
	"ad_id" Serial NOT NULL,
	"ad_su_id" integer NOT NULL,
	"ad_adres" Text,
	"ad_ws" Char(32),
 primary key ("ad_id")
);

Alter table "adresy" add  foreign key ("ad_su_id") references "system_user" ("su_id") on update cascade on delete cascade;

Create index "adrest_fkey" on "adresy" using btree ("ad_su_id");


Alter table system_user Add su_ws Char(32);
Alter table system_user Add su_ws_update int;
Alter table system_user Add su_saldo Double precision;
Create index "system_user_email" on "system_user" using btree ("su_email");
Create index "system_user_ws" on "system_user" using btree ("su_ws");
