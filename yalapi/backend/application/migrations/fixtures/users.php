<?php

return function($dbh) {
    $dbh->exec("SELECT create_user('robson', 'robson', 'Robert', 'Posiadała', false, 1)");
    $dbh->exec("SELECT create_user('pudel', 'pudel', 'Piotr', 'Podstawski', false, 1)");
    $dbh->exec("SELECT create_user('trener', 'trener', 'Big', 'Zbig', false, 5)");
    $dbh->exec("SELECT create_user('uczestnik', 'uczestnik', 'Janek', 'Franek', true, 2)");
    $dbh->exec("SELECT create_user('uczestnik2', 'uczestnik2', 'Zosia', 'Frania', true, 2)");
};
