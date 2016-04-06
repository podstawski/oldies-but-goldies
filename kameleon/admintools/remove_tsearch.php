#!/usr/local/bin/php
<?
$in=implode('',file('/dev/stdin'));	

$out=$in;

$su_pos=strpos($out,'CREATE TABLE search_ustawienia');
$searchust=substr($out,$su_pos);
$su_pos=strpos($searchust,';');
$searchust=substr($searchust,0,$su_pos+1);


$out=ereg_replace("(ALTER|CREATE)[^;]+(tsearch2|gts| ts| token|_ts_|statinfo|longqueries|kraje|polska|praco)[^;]+;","",$out);

$out=ereg_replace("SET search_path[^;]+;",$searchust,$out);

$out=ereg_replace("--\n","\n",$out);
$out=ereg_replace("\n\n+","\n\n",$out);


echo $out;


?>
