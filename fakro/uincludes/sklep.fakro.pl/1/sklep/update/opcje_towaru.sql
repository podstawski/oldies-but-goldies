ALTER TABLE zampoz ADD zp_opcje Text;

Create table "opcje_towaru"
(
	"ot_id" Serial NOT NULL,
	"ot_to_id" integer NOT NULL,
	"ot_opcje" Text,
	"ot_ilosc" Smallint,
 primary key ("ot_id")
);

Alter table "opcje_towaru" add  foreign key ("ot_to_id") references "towar" ("to_id") on update cascade on delete cascade;
