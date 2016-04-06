ALTER TABLE zamowienia ADD za_ws_update Int;
ALTER TABLE towar ADD to_ws_update Int;

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES ('wsu','WebServices u¿ytkownik','','');

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES ('wsp','WebServices has³o','','');
