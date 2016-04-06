Create table "temp"
(
	"te_id" Serial NOT NULL,
	"te_indeks" Char(32),
	"te_fk1" integer,
	"te_fk2" integer,
	"te_fk3" integer,
	"te_deadline" integer,
	"te_wart" Char(100),
 primary key ("te_id")
);

Create unique index "temp_indeks_key" on "temp" using btree ("te_indeks");
Create index "temp_fk_key" on "temp" using btree ("te_fk1","te_fk2","te_fk3");
