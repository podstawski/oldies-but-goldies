Create table "system_opcje"
(
	"so_id" Serial NOT NULL,
	"so_nazwa2" Char(20),
	"so_nazwa" Text,
	"so_lista" Text,
	"so_wart" Char(20)
);

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES ('mag','Kontrola magazynu','0,1','1');

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES ('koszyk','Nawiguj do koszyka','0,1','0');

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES ('czas','Limit czasu dla produktѓw w koszyku','0,1','1');
