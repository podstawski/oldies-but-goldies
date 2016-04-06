
ALTER TABLE zamowienia ADD za_poczta_nt double precision;
ALTER TABLE zamowienia ADD za_poczta_br double precision;
ALTER TABLE zamowienia ADD za_poczta Varchar(80);
ALTER TABLE zapytania ADD za_cena double precision;

Create table "poczta"
(
        "po_id" Serial NOT NULL,
        "po_nazwa" Varchar(80),
        "po_cena_nt" double precision,
        "po_cena_br" double precision,
	"po_darmo_powyzej" double precision,
 primary key ("po_id")
);

