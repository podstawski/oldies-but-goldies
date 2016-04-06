<?
	Header("Content-type: text/plain");

	$klucz=$_GET['klucz'];
	
	$url='http://yala.yala.pl/.tools/migracja/?t='.time().'&new_yala='.$klucz;


	$cmd="curl -k \"$url\"";


	system($cmd);
