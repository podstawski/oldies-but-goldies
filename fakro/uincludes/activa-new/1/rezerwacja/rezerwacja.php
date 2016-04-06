<?
global $_REQUEST;
global $rezerwacja;

$tab = unserialize(stripslashes($costxt));
$rezerwacja = $tab['rezerwacja'];

$_path = $INCLUDE_PATH.'/rezerwacja/';
$_action = $self;

require_once($_path.'includes/conf.php');
require_once($_path.'includes/idb_mysql.php');
require_once($_path.'includes/pl.inc.php');

$idb =  new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);

require_once("HTML/QuickForm.php");
?>

<script type="text/javascript">
	var languageCode = 'pl';
	var pathToImages = '<?=$_path;?>images/';
</script>
<link media="screen" href="<?=$_path;?>css/calendar-1.css?random=20051112" rel="stylesheet" />
<script type="text/javascript" src="<?=$_path;?>js/calendar.js?random=20060118"></script>

<?
$form = new HTML_QuickForm('frmRezerwacja','post',$_action);
$renderer =& $form->defaultRenderer();

$renderer->setFormTemplate(
<<<EOT
<table width="100%" border="0" cellpadding="3" cellspacing="2">
<col width="30%">
<col width="70%">
<form{attributes}>
	{content}
</form>
</table>
EOT
);

$renderer->setHeaderTemplate(
<<<EOT
<tr>
	<td style="white-space:nowrap;background:#F9F9F9;color:#3F4852;" align="left" colspan="2"><b>{header}</b></td>
</tr>
EOT
);

$renderer->setElementTemplate(
<<<EOT
<tr>
    <td align="right" valign="top" nowrap="nowrap">{label} <!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required --></td>
    <td valign="top" align="left">
        <!-- BEGIN error --><span style="color: #ff0000">{error}</span><br /><!-- END error -->{element}
        <!-- BEGIN label_2 --><br/><span style="font-size: 80%">{label_2}</span><!-- END label_2 -->
    </td>
</tr>
EOT
);

$form->_jsPrefix = pl_win2iso('Błšd w formularzu:');
$form->_jsPostfix = pl_win2iso('Popraw formularz i spróbuj ponownie.');

#$form->addElement('header', null, '');

$form->addElement('header', '1', '&nbsp;');
$form->addElement('text','data_od',pl_win2iso('Data przyjazdu'),array('id'=>'data_od','readonly'=>'readonly','size'=>30,'maxlength'=>20,'onmousedown'=>"this.className='date_over'","onclick"=>"displayCalendar(document.forms[0].data_od,'yyyy-mm-dd',this);"));
$form->addRule('data_od',pl_win2iso('Proszę wpisać: Data przyjazdu'),'required', null, 'client');
$form->addElement('text','data_do',pl_win2iso('Data wyjazdu'),array('id'=>'data_do','readonly'=>'readonly','size'=>30,'maxlength'=>20,'onmousedown'=>"this.className='date_over'","onclick"=>"displayCalendar(document.forms[0].data_do,'yyyy-mm-dd',this);"));
$form->addRule('data_do',pl_win2iso('Proszę wpisać: Data wyjazdu'),'required', null, 'client');

$form->addElement('header', '1', '&nbsp;');
$form->addElement('text','wymagana_liczba_miejsc',pl_win2iso('Iloć osób'),array('size'=>2,'maxlength'=>2));
$form->addRule('wymagana_liczba_miejsc',pl_win2iso('Proszę wpisać: Iloć osób'),'required', null, 'client');

$form->addElement('select','pierwszy_pos',pl_win2iso('Pierwszy posiłek w dniu przyjazdu'),array('' => '--wybierz--', 1 => pl_win2iso('NIADANIE'), 2 => pl_win2iso('OBIAD'), 3 => pl_win2iso('KOLACJA')));
$form->addRule('pierwszy_pos',pl_win2iso('Proszę wpisać: Pierwszy posiłek w dniu przyjazdu'),'required', null, 'client');
$form->addElement('select','ostatni_pos',pl_win2iso('Ostani posiłek w dniu wyjazdu'),array('' => '--wybierz--', 1 => pl_win2iso('NIADANIE'), 2 => pl_win2iso('OBIAD'), 3 => pl_win2iso('KOLACJA')));
$form->addRule('ostatni_pos',pl_win2iso('Proszę wpisać: Ostani posiłek w dniu wyjazdu'),'required', null, 'client');

$form->addElement('header', '1', '&nbsp;');
$form->addElement('text','imie',pl_win2iso('Imię'),array('size'=>50,'maxlength'=>20));
$form->addRule('imie',pl_win2iso('Proszę wpisać: Imię'),'required', null, 'client');

$form->addElement('text','nazwosko',pl_win2iso('Nazwisko'),array('size'=>50,'maxlength'=>20));
$form->addRule('nazwosko',pl_win2iso('Proszę wpisać: Nazwisko'),'required', null, 'client');

/*
$form->addElement('text','data_urodzenia',pl_win2iso('Data urodzenia'),array('id'=>'data_urodzenia','readonly'=>'readonly','size'=>30,'maxlength'=>20,'onmousedown'=>"this.className='date_over'","onclick"=>"displayCalendar(document.forms[0].data_urodzenia,'yyyy-mm-dd',this);"));
$form->addRule('data_urodzenia',pl_win2iso('Proszę wpisać: Data urodzenia'),'required', null, 'client');
*/

/*
$form->addElement('text','kraj',pl_win2iso('Państwo'),array('size'=>30,'maxlength'=>20));
$form->addRule('kraj',pl_win2iso('Proszę wpisać: Państwo'),'required', null, 'client');
*/

$form->addElement('text','wojewodztwo',pl_win2iso('Województwo'),array('size'=>50,'maxlength'=>20));
$form->addRule('wojewodztwo',pl_win2iso('Proszę wpisać: Województwo'),'required', null, 'client');

$form->addElement('text','ulica',pl_win2iso('Ulica'),array('size'=>50,'maxlength'=>20));
$form->addRule('ulica',pl_win2iso('Proszę wpisać: Ulica'),'required', null, 'client');

$form->addElement('text','miasto',pl_win2iso('Miasto'),array('size'=>50,'maxlength'=>20));
$form->addRule('miasto',pl_win2iso('Proszę wpisać: Miasto'),'required', null, 'client');

$form->addElement('text','kod',pl_win2iso('Kod pocztowy'),array('size'=>50,'maxlength'=>20));
$form->addRule('kod',pl_win2iso('Proszę wpisać: Kod pocztowy'),'required', null, 'client');

$form->addElement('text','email',pl_win2iso('Adres e-mail'),array('size'=>50,'maxlength'=>20));
$form->addRule('email',pl_win2iso('Proszę wpisać: Adres e-mail'),'required', null, 'client');
$form->addRule('email',pl_win2iso('Nieprawidłowy: Adres e-mail.'),'email', null, 'client');

$form->addElement('text','telefon',pl_win2iso('Telefon'),array('size'=>50,'maxlength'=>20));
$form->addRule('telefon',pl_win2iso('Proszę wpisać: Telefon'),'required', null, 'client');

$form->addElement('textarea','uwagi',pl_win2iso('Uwagi'),array('rows'=>5,'cols'=>90));

// info o wymaganych polach
$form->setRequiredNote(pl_win2iso('<font color="red">*</font> - wymagane pola oznaczone sš gwiazdkš'));

$grp_grpSubmitReset[] = &HTML_QuickForm::createElement('submit','btnSubmit',pl_win2iso('wylij'));
$grp_grpSubmitReset[] = &HTML_QuickForm::createElement('reset','btnReset',pl_win2iso('anuluj'));
$form->addGroup($grp_grpSubmitReset,'grpSubmitReset','','&nbsp;&nbsp;');


$form->addElement('checkbox','zgoda','',pl_win2iso('Wyrażam zgodę na wykorzystywanie i przetwarzanie przez Hotel Aktiv sp. z o.o. z siedzibš w Muszynie przy ul. Złockie 78, moich danych osobowych zawartych w tym formularzu w celach marketingowych, zgodnie z ustawš z dn. 29.08.1997r. o ochronie danych osobowych (Dz. U. Nr 133, poz.883).'));
$form->addRule('zgoda',pl_win2iso('Proszę zaznaczyć zgodę na przetwarzanie danych osobowych.'),'required', null, 'client');
$form->addElement('checkbox','zgodaMarketing','',pl_win2iso('Wyrażam zgodę na otrzymywanie informacji promocyjnych, informacyjnych, reklamowych i marketingowych o produktach Hotel Aktiv sp. z o.o. 33-370 Muszyna, ul. Złockie 78, na mój adres e-mail i telefon zgodnie z ustawš z dnia 18.07.2002r. o wiadczeniu usług drogš elektronicznš (Dz. U. Nr 144, poz. 1204).'));
$form->addRule('zgodaMarketing',pl_win2iso('Proszę zaznaczyć zgodę na otrzymywanie informacji promocyjnych.'),'required', null, 'client');

if($form->validate()) {
	$form->freeze();
	
	foreach($form->exportValues() as $k => $v) {
		$form_xml[$k] = $v;
		}
	
	$_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$_xml .= "<rezerwacja>\n";
	$_xml .= "<wymagana_liczba_miejsc>".$form_xml['wymagana_liczba_miejsc']."</wymagana_liczba_miejsc>\n";
	$_xml .= "<data_od>".$form_xml['data_od']."</data_od>\n";
	$_xml .= "<data_do>".$form_xml['data_do']."</data_do>\n";
	$_xml .= "<uwagi>".$form_xml['uwagi']."</uwagi>\n";
	$_xml .= "<email>".$form_xml['email']."</email>\n";
	$_xml .= "<osoba>\n";
	$_xml .= "<imie>".$form_xml['imie']."</imie>\n";
	$_xml .= "<nazwisko>".$form_xml['nazwosko']."</nazwisko>\n";
	/*
	$_xml .= "<data_urodzenia>".$form_xml['data_urodzenia']."</data_urodzenia>\n";
	*/
	$_xml .= "<kraj>POLSKA</kraj>\n";
	$_xml .= "<wojewodztwo>".$form_xml['wojewodztwo']."</wojewodztwo>\n";
	$_xml .= "<ulica>".$form_xml['ulica']."</ulica>\n";
	$_xml .= "<miasto>".$form_xml['miasto']."</miasto>\n";
	$_xml .= "<kod>".$form_xml['kod']."</kod>\n";
	$_xml .= "<telefon>".$form_xml['telefon']."</telefon>\n";
	$_xml .= "<pierwszy_pos>".$form_xml['pierwszy_pos']."</pierwszy_pos>\n";
	$_xml .= "<ostatni_pos>".$form_xml['ostatni_pos']."</ostatni_pos>\n";
	$_xml .= "<typ_pos>1</typ_pos>\n";
	$_xml .= "</osoba>\n";
	$_xml .= "</rezerwacja>\n";
	
	$body = $_xml;
	
	// wysylka maila
	include("Mail.php");
	// tworzenie obiektu przy uzyciu metody Mail::factory
	$m=&Mail::Factory("smtp",$params);
	// definiowanie naglowka
	$header['From'] = "robot@fakro.com.pl";
	$header['To'] = $rezerwacja['mailto'];
	$header['Subject'] = $rezerwacja['subject'];
	$header['Content-Type'] = "text/plain;\n\tharset=iso-8859-2;";
	$error = @$m->send($rezerwacja['mailto'],$header,$body);
	
	if(PEAR::isError($re)) {
		echo $re->toString().'<br>';
		echo "S:".$re->getMessage();
		echo '<br><br><br><div align="center"><strong>'.$rezerwacja['send_error'].'</strong></div>';
		}else{
		echo '<br><br><br><div align="center"><strong>'.$rezerwacja['send_ok'].'</strong></div>';
		}
	
	echo '<br><br><br>';
	}else{
	$form->display();
	}
?>

<?
/*
echo $_path;
echo '<pre>';
print_r($_POST);
print_r($_GET);
print_r($_SESSION);
echo '</pre>';

echo session_id();
*/
?>
