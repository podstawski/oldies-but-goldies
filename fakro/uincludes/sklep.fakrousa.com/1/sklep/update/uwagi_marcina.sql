ALTER TABLE zamowienia ADD za_uwagi_przyjecia Text;
ALTER TABLE zamowienia ADD za_uwagi_realizacji Text;
ALTER TABLE zamowienia ADD za_osoba_przyjecia Integer;
ALTER TABLE zamowienia ADD za_osoba_realizacji Integer;

ALTER TABLE zampoz ADD zp_rabat Double precision;

ALTER TABLE promocja_towaru ADD pt_poczatek Integer;
ALTER TABLE promocja_towaru ADD pt_koniec Integer;
