<?
$sql = "SELECT su_kod_pocztowy FROM system_user WHERE su_id = ".$AUTH[id];
parse_str(ado_query2url($sql));

$su_kod_pocztowy_temp = substr($su_kod_pocztowy, 0, 2);

if($su_kod_pocztowy_temp == 20) {
  $doplata_korsyka = 15;
}else{
  $doplata_korsyka = 0;
}

?>