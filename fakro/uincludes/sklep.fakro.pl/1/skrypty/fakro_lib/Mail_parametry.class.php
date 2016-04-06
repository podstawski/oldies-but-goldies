<?
/*
if($maildb) {
	$parametry = new MailParametr('"'.$fakro[lang].'"',0);
	
	$tresc = $parametry->get();
	$list_tresc = processor_tpl(
								array(
										"%login"=>"xcxdc",
										"%pass"=>"dsdasdr",
										"%nazwisko"=>"safdsresdgds"
									),
								$tresc);
	
	$mail = new sendMail();
	$mail->params = $params;
	
	$mail->mail_header($fakro[meta_charset],'robot@fakro.com.pl','robot@fakro.com.pl','robot@fakro.com.pl',$list_tresc[subject],$list_tresc[contents]);
	$mail->send();
	}
*/

include("Mail.php");
include("Mail/mime.php");

class MailParametr {
	var $lang;
	var $id;
	var $folder;
	var $sys_mail;
	var $sys_mail_count;
	var $subject;
	var $file;
	
	function MailParametr($lang,$id) {
		global $CFG;
		$this->lang = $lang;
		$this->id = $id;
		
		$this->folder			= $CFG['mail_tpl_dir'].$this->lang.'/';
		$this->sys_mail			= file($this->folder.'sys_mail_lista.txt');
		$this->sys_mail_count	= count($this->sys_mail);
		
		$this->subject = array();
		$this->file = array();
		
		for($i=0; $i<$this->sys_mail_count; $i++) {
			$linia = explode('|', trim($this->sys_mail[$i]));
			$this->subject[]	= $linia[0];
			$this->file[]	= $linia[1];
			}
		}
	
	function get() {
		$res = '';
		
		if($this->id < $this->sys_mail_count) {
			$res[subject] = $this->subject[$this->id];
			$res[contents] = nl2br(file_get_contents($this->folder.$this->file[$this->id]));
			}
		return $res;
		}
	}

function processor_tpl($params,$template) {
	reset($params);
	$t = $template;
	
	while(list($k,$v)=each($params)) {
		if(substr($k,0,1)!="%") $k="%".$k;
		if(substr($k,0,1)=="%") {
			$t=str_replace($k,$v,$t);
			}
		}
	
	reset($params);
	
	while(list($k,$v)=each($params)) {
		if(substr($k,0,1)!="%") $k="%".$k;
			
			if(substr($k,0,1)=="%") {
				$st = strpos($t,"[?");
				$li = 0;
				
				while($st!==false && $li<5) {
					$li++;
					$kn = strpos($t,"?]");
					$war = substr($t,$st+2,$kn-$st-2);
					list($wyr,$yes,$no)=split(":",$war);
					
					if(strpos($wyr,"!=")!==false) $rz="!="; else
					if(strpos($wyr,"=")!==false) $rz="=";
					
					list($zm,$value)=split($rz,$wyr);
					
					if($rz=="!=") {
						if($zm==$value) $pyt=$no; else $pyt=$yes;
						}
					if($rz=="=") {
						if($zm!=$value) $pyt=$no; else $pyt=$yes;
						}
					
					$nt = substr($t,0,$st).$pyt.substr($t,$kn+2,strlen($t)-$kn+2);
					$t = $nt;
					$st = strpos($t,"[?");
					}
				}
			}
		return $t;
	}

class sendMail {
	var $params;
	
	var $ISO;
	
	var $From;
	var $TO;
	var $Reply;
	
	var $mail_subject;
	var $mail_tresc_listu;
	
	function mail_header($iso,$from,$to,$reply,$mail_subject,$mail_tresc_listu) {
		$this->ISO				= $iso;
		$this->From				= $from;
		$this->TO				= $to;
		$this->Reply			= $reply;
		$this->mail_subject		= $mail_subject;
		$this->mail_tresc_listu	= $mail_tresc_listu;
		}
	
	function send() {
		$headers['From']			= $this->From;
		$headers['To']				= $this->TO;
		$headers['Reply-to']		= $this->Reply;
		$headers['Subject']			= $this->mail_subject;
		
		$htmlMessage				= $this->mail_tresc_listu;
		
		$mime = new Mail_Mime();
		$mime->setHtmlBody($htmlMessage);
		
		$body = $mime->get(array(
								"text_charset" => $this->ISO,
								"html_charset" => $this->ISO,
								"head_charset" => $this->ISO,
								"html_encoding" => $this->ISO,
								"text_encoding" => "8bit")
							);
		$hdrs = $mime->headers($headers);
		
		$m=&Mail::Factory("smtp",$this->params);
		
		$error = @$m->send($this->TO,$hdrs,$body);
		# $this->error($error); // dodac logowanie
		}
	
	function error($re) {
		if(PEAR::isError($re)) {
			echo $re->toString();
			}else{
			echo "mail wyslany";
			}
		}
	
	}
?>