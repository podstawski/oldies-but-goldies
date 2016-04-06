<?
  //  $projdb->debug=1;

  $user_action = '';

  if(isset($_POST['user_action'])) $user_action = $_POST['user_action'];
  elseif(isset($_GET['user_action'])) $user_action = $_GET['user_action'];

  $tab = unserialize(stripslashes($costxt));

  //  echo '<pre>';
  //  var_dump($user_action);
  //  var_dump($tab);
  //  echo '</pre>';

  // pobieranie danych uzytkownika
  $sql = "SELECT * FROM system_user, voucher WHERE system_user.su_id=voucher.su_id AND voucher.voucher_status=2 AND voucher.voucher_id=".$user_action['voucher_id'];
  parse_str(ado_query2url($sql));

  // przerwanie dzialania
  if(!strlen($su_email)) {
    return;
  }

  //$su_email = 'oginski.michal@gmail.com';

  $voucher_waznosc = date("Y-m-d", ($voucher_date_modyfikacji+(86400*365)));

  $MAILF = $tab["BONY_SEND_INFORMACJA"];

  $plain = '';
  $html = '';

  ob_start();
  $aa = array(
  'voucher_wartosc'=>$voucher_wartosc,
  'voucher_name'=>$voucher_name,
  'voucher_date'=>$voucher_waznosc
  );
  foreach($aa as $k => $v) {
    $MAILF['tresc'] = str_replace('$'.$k,$v,$MAILF['tresc']);
  }
  $body = $MAILF['tresc'];
  ob_end_clean();

  // ustawienia do poczty
  $params["host"] = "mail.fakro.com.pl";  // adres serwera SMTP
  $params["port"] = "25";                 // port serwera SMTP (zazwyczaj: 25)
  $params["auth"] = true;                 // czy serwer wymaga autoryzacji (zazwyczaj: true)
  $params["username"] = "robotfakro";     // login konta (ewentualnie adres e-mail konta)
  $params["password"] = "2wsxcde3";       // haslo konta
  // wysylka maila
  include("Mail.php");
  // tworzenie obiektu przy uzyciu metody Mail::factory
  $m=&Mail::Factory("smtp",$params);
  // definiowanie naglowka
  $header['From'] = $MAILF['mailfrom'];
  $header["Reply-To"] = $MAILF['mailfrom']; 
  $header['To'] = $su_email;
  $header['Subject'] = $MAILF['subject'];
  $header['Content-Type'] = "text/plain;\n\tharset=utf-8;";
  $error = @$m->send($su_email,$header,$body);

  if(PEAR::isError($error)) {
    echo '<br><br><br><div align="center"><strong>'.$MAILF['send_error'].'</strong></div>';
  }else{
    echo '<br><br><br><div align="center"><strong>'.$MAILF['send_ok'].'</strong></div>';
  }
?>