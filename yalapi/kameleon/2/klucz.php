<?
	$klucz=strtolower(trim($_REQUEST['q']));

	if (!strlen($klucz)) die('brak adresu strony');

	if (preg_match('/[^a-z0-9]+/',$klucz)) die('adres zawiera nieprawidłowe znaki');


	include(dirname(__FILE__).'/db.php');

	


	$sql="SELECT count(*) AS c FROM yala WHERE klucz='$klucz'";
	parse_str(query2url($sql));

	if ($c) die('adres '.$klucz.'.yala.pl już istnieje');