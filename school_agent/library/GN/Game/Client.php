<?php
set_time_limit(5);
class GN_Game_Client {
	private $port;
	private $host;
	private $hash;
	private $socket;
	private $connected = false;
	private $sentKey;
	private $respondKey;
	protected $read = false;

	public function __construct($port,$host,$hash)
	{
		$this->port=$port;
		$this->host=$host;
		$this->hash=$hash;
		$this->ready=false;
		
	}

	public function connect()
	{
		if ($this->connected) return true;
		
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		if (!$this->socket) return false;


		$this->connected = socket_connect($this->socket, $this->host,$this->port);
		
		
		
		if (!$this->connected) return false;
		//echo "connected<br />";


		$this->generateKeys() ;
		//echo $this->sentKey;
		//echo "<br />".$this->respondKey;
		if (!$this->sendHeaders())	return false;
		//echo "headers sent<br />";
		if (!$this->handshake())
		return false;
		//else
		//echo "hash poprawny";

		//echo "TODO: zautoryzuj siê w serwerze kluczem '".$this->hash."'<br>";
		$json = '{"Authorize":"'.$this->hash.'"}';
		$this->send($json);
		$this->ready = true;
		
		//socket_close($this->socket);
		return true;
	}
	
	private function send ($msg)
	{
		$msg.="\r\n";
		$left = strlen($msg);
		do {
			$sent = @socket_send($this->socket, $msg, $left, 0);
			if ($sent === false) return false;

			$left -= $sent;
			if ($sent > 0) $msg = substr($msg, $sent);
		}
		while ($left > 0);
		return true;
	}

	private function handshake()
	{

		while (isset($this->socket)) {
			$buffer = '';
			$bytes = @socket_recv($this->socket, $buffer, 4096, 0);

			if ($bytes === false) {
				return false;
			}
			elseif ($bytes > 0) {
				
				$rec = explode(":",$buffer);
				if ($rec[0]!= 'Key')
				{
					echo "Wrong header";
					return false;
				}
				else
				{
					$rec[1] = trim($rec[1]);
					if ($rec[1]!=$this->respondKey)
					{
						echo "Hash mismatch!";
						//echo "*" . $rec[1] . "*!=*" . $this->respondKey . "*";
						return false;
					}
				}
				return true;
			}
		}	
	}

	function generateKeys($length = "")
	{	
		$code = md5(uniqid(rand(), true));
		if ($length != "") $code = substr($code, 0, $length);
		$this->sentKey = $code;	
		$magic = "258EAFA5-E914-47DA-95CA-C5AB0DC85C11";  
		$code.= $magic;
		$code = base64_encode(sha1($code,true));
		$this->respondKey = $code;
	}


	public function startServer($file='')
	{
		//echo "START SERVER";
		$config = Config::parse();

		$php = isset($config['php.cli_path']) ? $config['php.cli_path'].'/php' : 'php';

		$cmd = $php . ' ' . realpath(__DIR__.'/server.php') .' ' . $this->port . ' ' . $this->hash;
		
		if (strlen($file)) $cmd.=' ' . $file;
		
		
		if (isset($config['php.cli_suffix'])) $cmd.=' '.$config['php.cli_suffix'];
		exec($cmd);
	}
	
	private function sendHeaders()
	{
		$headers = "GET " . "/server" . " HTTP/1.1" . "\r\n";
		$headers.= "Host: " . $this->host .":" . $this->port . "\r\n";
		$headers.= "Admin: true\r\n";
		$headers.= "Key: " .$this->sentKey . "\r\n\r\n";
		
		$left = strlen($headers);
		do {
			$sent = @socket_send($this->socket, $headers, $left, 0);
			if ($sent === false) return false;

			$left -= $sent;
			if ($sent > 0) $headers = substr($headers, $sent);
		}
		while ($left > 0);
		return true;
	}


	public function authorize($key,$name,$mail)
	{
		//echo "TODO: powiedz serwerowi, ¿e je¿eli siê kto¶ zautoryzuje kluczem '$key', to to bêdzie facet o nicku '$name'<br>";
		$json = '{"Register":{"Name":"'.$name.'","Key":"'.$key.'","Mail":"'.$mail.'"}}';
		$this->send($json);
		
	}

}
