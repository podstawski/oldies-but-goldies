INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES ('master','Gospodarz','SELECT su_id AS value,su_nazwisko AS option FROM system_user WHERE su_parent IS NULL ORDER BY su_nazwisko','');

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES ('auth','Autoryzacja','email,login','login');

