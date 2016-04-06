<?
if($KAMELEON_MODE!=0) {
	echo '<br/><br/><br/>';
	echo '<div align="center"><font color="#ff0000"><strong>Ten moduГ moПe byц uruchomiony tylko na wersji opublikowanej.</strong></font></div>';
	echo '<br/><br/><br/>';
	exit;
}

global $_REQUEST;
global $page_ok;

$uploaddir = $UFILES."/upload_crm";

if(!file_exists($uploaddir)) @mkdir($uploaddir);
if(!file_exists($uploaddir)) {
	echo sysmsg("Hard to create");
	return;
}

$_path		= $INCLUDE_PATH.'/crm/zgloszenie/';
$tab		= unserialize(stripslashes($costxt));
$_SEND_FORM_MODULE = $tab["_SEND_FORM_MODULE"];

$_action	= $self;

function file_array($path, $filename_md5_type, $exclude = ".|..") {
	$folder_handle = opendir($path);
	$exclude_array = explode("|", $exclude);
	$result = array();
	
	while(false !== ($filename = readdir($folder_handle))) {
		if(!in_array(strtolower($filename), $exclude_array)) {
			$filename_md5 = explode("-", $filename);
			
			if($filename_md5_type == $filename_md5[0]) $result[] = $filename;
		}
	}
	return $result;
}
	
$upload_file_u = md5(session_id()).'x'.base64_encode($uploaddir.'|'.md5(session_id()));
?>

<script type="text/javascript" src="<? echo $_path; ?>js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="<? echo $_path; ?>js/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<? echo $_path; ?>js/jquery.swfupload.js"></script>
<script type="text/javascript">
$(function(){
	$('#swfupload-control').swfupload({
		upload_url: "<? echo $_path; ?>upload-file.php?u=<? echo $upload_file_u; ?>",
		file_post_name: 'uploadfile',
		file_size_limit : "1024",
		file_types : "*.jpg;*.jpeg",
		file_types_description : "Image files",
		file_upload_limit : 5,
		flash_url : "<? echo $_path; ?>js/swfupload/swfupload.swf",
		button_image_url : '<? echo $_path; ?>js/swfupload/XPButtonUploadText_104x22.png',
		button_width : 104,
		button_height : 22,
		button_placeholder : $('#button')[0],
		debug: false
	})
		.bind('fileQueued', function(event, file){
			var listitem='<li id="'+file.id+'" >'+
				'Plik: '+file.name+' ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
				'<div class="progressbar" ><div class="progress" ></div></div>'+
				'<p class="status" >Pending</p>'+
				'<span class="cancel" >&nbsp;</span>'+
				'</li>';
			$('#log').append(listitem);
			$('li#'+file.id+' .cancel').bind('click', function(){
				var swfu = $.swfupload.getInstance('#swfupload-control');
				swfu.cancelUpload(file.id);
				$('li#'+file.id).slideUp('fast');
			});
			// start the upload since it's queued
			$(this).swfupload('startUpload');
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
			alert('Problem z przesГaniem pliku.');
		})
		.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
			$('#queuestatus').text('Wybrane pliki: '+numFilesSelected+' / w kolejce: '+numFilesQueued);
		})
		.bind('uploadStart', function(event, file){
			$('#log li#'+file.id).find('p.status').text('Uploading...');
			$('#log li#'+file.id).find('span.progressvalue').text('0%');
			$('#log li#'+file.id).find('span.cancel').hide();
		})
		.bind('uploadProgress', function(event, file, bytesLoaded){
			//Show Progress
			var percentage=Math.round((bytesLoaded/file.size)*100);
			$('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
			$('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
		})
		.bind('uploadSuccess', function(event, file, serverData){
			var item=$('#log li#'+file.id);
			item.find('div.progress').css('width', '100%');
			item.find('span.progressvalue').text('100%');
			item.addClass('success').find('p.status').html('');
		})
		
		.bind('uploadComplete', function(event, file){
			// upload has completed, try the next one in the queue
			$(this).swfupload('startUpload');
		})
	
});	

</script>
<style type="text/css" >
#swfupload-control p {
	margin: 10px 0px 0px 0px;
	font-size: 10px;
	color: #999999;
}
#log {
	margin:0;
	padding:0;
	width:100%;
	color: #999999;
}
#log li {
	list-style-position:inside;
	margin:2px;
	border:1px solid #ccc;
	padding:10px;
	font-size: 10px;
	font-family:Arial, Helvetica, sans-serif;
	color:#333;
	background:#fff;
	position:relative;
}
#log li .progressbar {
	border:1px solid #333;
	height:2px;
	background:#fff;
}
#log li .progress {
	background:#999;
	width:0%;
	height:2px;
}
#log li p {
	margin:0;
	line-height:18px;
}
#log li.success {
	border: 1px solid #7dd07e;
	background: #ecfae4;
}
#log li span.cancel {
	position:absolute;
	top:5px;
	right:5px;
	width:20px;
	height:20px;
	background:url('<? echo $_path; ?>js/swfupload/cancel.png') no-repeat;
	cursor:pointer;
}
</style>
<?
require_once("HTML/QuickForm.php");


$form = new HTML_QuickForm('frmTest','post',$_action);
$renderer =& $form->defaultRenderer();

$renderer->setFormTemplate(
<<<EOT
<table width="100%" border="0" cellpadding="3" cellspacing="2">
<form{attributes}>
	{content}
</form>
</table>
EOT
);

$renderer->setHeaderTemplate(
<<<EOT
<tr>
	<td style="border-bottom:1px solid #E9832F;color:#E9832F;font-weight:normal;text-align:left;" align="left" colspan="2"><b>{header}</b></td>
</tr>
EOT
);

$renderer->setElementTemplate(
<<<EOT
<tr>
    <td align="left" valign="top" nowrap="nowrap">{label} <!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required --></td>
    <td valign="top" align="left">
        <!-- BEGIN error --><span style="color: #ff0000">{error}</span><br /><!-- END error -->{element}
        <!-- BEGIN label_2 --><br/><span style="font-size: 80%">{label_2}</span><!-- END label_2 -->
    </td>
</tr>
EOT
);

$renderer->setElementTemplate(
<<<EOT
<tr>
    <td colspan="2" valign="top" align="left">
        <!-- BEGIN error --><span style="color: #ff0000">{error}</span><br /><!-- END error -->{element}
        <!-- BEGIN label_2 --><br/><span style="font-size: 80%">{label_2}</span><!-- END label_2 -->
    </td>
</tr>
EOT
,'zgoda'); 

$renderer->setElementTemplate(
<<<EOT
<tr>
    <td colspan="2" valign="top" align="left">
        <!-- BEGIN error --><span style="color: #ff0000">{error}</span><br /><!-- END error -->{element}
        <!-- BEGIN label_2 --><br/><span style="font-size: 80%">{label_2}</span><!-- END label_2 -->
    </td>
</tr>
EOT
,'zgoda'); 

$form->_jsPrefix = 'BГБd w formularzu:';
$form->_jsPostfix = 'Popraw formularz i sprѓbuj ponownie.';

$form->addElement('hidden', 'form_id', md5(session_id()));

$form->addElement('header', null, 'Dane adresowe');

$form->addElement('text','firma','Nazwa firmy',array('size'=>50,'maxlength'=>50));
$form->addRule('firma','Proszъ wpisaц: Nazwъ firmy','required', null, 'client');

$form->addElement('text','nazwisko','Imiъ i nazwisko',array('size'=>50,'maxlength'=>50));
$form->addRule('nazwisko','Proszъ wpisaц: Imiъ i nazwisko','required', null, 'client');

$form->addElement('text','ulica','Ulica',array('size'=>50,'maxlength'=>50));
$form->addRule('ulica','Proszъ wpisaц: Ulicъ','required', null, 'client');

$form->addElement('text','nr_budynku','Nr budynku',array('size'=>10,'maxlength'=>10));
$form->addRule('nr_budynku','Proszъ wpisaц: Nr budynku','required', null, 'client');

$form->addElement('text','nr_mieszkania','Nr mieszkania',array('size'=>10,'maxlength'=>10));

$form->addElement('text','kod','Kod pocztowy',array('size'=>10,'maxlength'=>10));
$form->addRule('kod','Proszъ wpisaц: Kod pocztowy','required', null, 'client');

$form->addElement('text','miejscowosc','MiejscowoЖц',array('size'=>50,'maxlength'=>50));
$form->addRule('miejscowosc','Proszъ wpisaц: MiejscowoЖц','required', null, 'client');

$form->addElement('text','kraj','Kraj',array('size'=>50,'maxlength'=>50));
$form->addRule('kraj','Proszъ wpisaц: Kraj','required', null, 'client');

$form->addElement('text','telefon','Numer telefonu',array('size'=>50,'maxlength'=>50));
$form->addRule('telefon','Proszъ wpisaц: Numer telefonu','required', null, 'client');

$form->addElement('text','email','E-mail',array('size'=>50,'maxlength'=>50));
$form->addRule('email','Proszъ wpisaa: E-mail','required', null, 'client');
$form->addRule('email','NieprawidГowy: E-mail.','email', null, 'client');

$form->addElement('text','osoba_kontaktowa','Osoba kontaktowa',array('size'=>50,'maxlength'=>50));

$form->addElement('text','telefon_kontaktowy','Telefon kontaktowy',array('size'=>50,'maxlength'=>50));

$form->addElement('header', null, 'Dane produktu');

$form->addElement('text','nazwa','Nazwa produktu',array('size'=>50,'maxlength'=>50));

$form->addElement('text','nr_tabl_znamionowej','Numer tabl. znamionowej',array('size'=>50,'maxlength'=>50));
$form->addRule('nr_tabl_znamionowej','Proszъ wpisaц: Numer tabl. znamionowej','required', null, 'client');

$form->addElement('text','data_nabycia','Data nabycia produktu',array('size'=>10,'maxlength'=>50));
$form->addRule('data_nabycia','Proszъ wpisaц: Datъ nabycia produktu','required', null, 'client');

$form->addElement('text','rozmiar','Rozmiar',array('size'=>10,'maxlength'=>10));

$form->addElement('text','wersja','Wersja',array('size'=>10,'maxlength'=>10));

$form->addElement('text','ilosc','IloЖц',array('size'=>10,'maxlength'=>10));
$form->addRule('ilosc','Proszъ wpisaц: IloЖц','required', null, 'client');

$form->addElement('textarea','przedmiot_uslugi','Przedmiot usГugi (opis)',array('rows'=>4,'cols'=>52));

$form->addElement('checkbox','zgoda','','WyraПam zgodъ na przetwarzanie moich danych osobowych zawartych w tym formularzu przez FAKRO Sp. z o.o. z siedzibъ w Nowym SБczu przy ul. Wъgierskiej 144A, zgodnie z Ustawъ z dn. 29.08.1997 (Dz.U. 1997r., nr 133, poz.883 wraz z pѓПniejszymi zmianami) o ochronie danych osobowych.');
$form->addRule('zgoda','Proszъ zaznaczyц zgodъ na przetwarzanie danych osobowych.','required', null, 'client');

// info o wymaganych polach
$form->setRequiredNote('<font color="red">*</font> - wymagane pola oznaczone sЙ gwiazdkЙ');

$form->setRequiredNote('<div id="swfupload-control"><p>moПna dodaц 5 plikѓw graficznych, kaПdy o maksymalnej wielkoЖci 1 MB</p><input type="button" id="button" /><p id="queuestatus" ></p><ol id="log"></ol></div>');

$grp_grpSubmitReset[] = &HTML_QuickForm::createElement('submit','btnSubmit','wyЖlij');
$grp_grpSubmitReset[] = &HTML_QuickForm::createElement('reset','btnReset','anuluj');
$form->addGroup($grp_grpSubmitReset,'grpSubmitReset','','&nbsp;&nbsp;');

if($form->validate()) {
#######################################################################################################
#ustawienia do poczty 
	$params["host"] = "mail.fakro.com.pl";		// adres serwera SMTP
	$params["port"] = "25";						// port serwera SMTP (zazwyczaj: 25)
	$params["auth"] = true;						// czy serwer wymaga autoryzacji (zazwyczaj: true)
	$params["username"] = "robotfakro";			// login konta (ewentualnie adres e-mail konta)
	$params["password"] = "2wsxcde3";			// haslo konta
	
	include("Mail.php");
	include("Mail/mime.php");
	
	$headers['From']			= $_SEND_FORM_MODULE['mail_from'];
	$headers['To']				= $_SEND_FORM_MODULE['mail_to'];
	$headers['Subject']			= $_SEND_FORM_MODULE['mail_temat'].' - '.date("Y-m-d H:i:s", time());
	
	// tworzenie obiektu przy uzyciu metody Mail::factory
	$m=&Mail::Factory("smtp",$params);
	
	/**tresc maila******************************************************************/
	$plain	= "";
	$html	= "<table style=\"border-top:3px double gray; border-bottom:3px double gray;\">";
	
	$form_array_wykluczenia = array('grpSubmitReset','form_id');
		
	foreach($form->exportValues() as $k => $v) {
		if(!in_array($k, $form_array_wykluczenia)) {
			$plain	.= " $k: $v\n";
			$html	.= "<tr><td style=\"border-bottom:1px solid silver;\">$k</td><td style=\"border-bottom:1px solid silver;\">$v</td></tr>";
		}
	}
	$html.="\n</table>";
	
	$text = $plain;
	$html = '<html><body>'.$html.'</body></html>';
	/*******************************************************************************/
	
	$mime = new Mail_mime();
	$mime->setTXTBody(strip_tags($text));
	$mime->setHTMLBody(nl2br($html));
	
	/**dodanie pliku do maila*******************************************************/
	$file_array = (file_array($uploaddir,$form->exportValue('form_id')));
	if(count($file_array) > 0) {
		foreach ($file_array as $v) {
			$mime->addAttachment($uploaddir.'/'.$v,'application/octet-stream');
		}
	}
	/*******************************************************************************/
	$iso = 'ISO-8859-2';
	
	$body = $mime->get(array(
								"text_charset" => $iso,
								"html_charset" => $iso,
								"head_charset" => $iso,
								"html_encoding" => $iso,
								"text_encoding" => "8bit")
							);
	$headers = $mime->headers($headers);
	
	// wyslanie maila w net
	$error = @$m->send($_SEND_FORM_MODULE['mail_to'],$headers,$body);
	
	/**usuwanie zalaczonych plikow**************************************************/
	$file_array = (file_array($uploaddir,$form->exportValue('form_id')));
	if(count($file_array) > 0) {
		foreach ($file_array as $v) {
			@unlink($uploaddir.'/'.$v);
		}
	}
	/*******************************************************************************/
	
	echo '<script type="text/javascript">';
	if(PEAR::isError($error)) {
		echo 'window.location = "'.$page_error.'";';
		}else{
		echo 'window.location = "'.$page_ok.'";';
		}
	echo '</script>';
		
	/*
	function error($re) {
		if(PEAR::isError($re)) {
			echo $re->toString().'<br>';
			echo "S:".$re->getMessage();
			return false;
			}else{
			return true;
			}
		}
	error($error);
	*/
	
}else{
	$form->display();
}
?>