<?
define ('TESTOWANIE',30);
define ('FROM','Yala <yala@yala.pl>');
define ('TEMAT','Aktywacja yali');



class YALA
{
	public $db;


	public function YALA($db)
	{
		$this->db=$db;
	}

	public function dodaj($klucz,$email,$openid,$pass)
	{

		if (strlen($openid))
		{
			$sql="SELECT id FROM yala WHERE openid='$openid'";
			parse_str(query2url($sql));

			if ($id) return $id;
		}

		$txt=file_get_contents(dirname(__FILE__).'/aktywacja_mail.txt');



		$sqlc = "SELECT count(id) as ile FROM yala WHERE klucz='$klucz'";
		parse_str(query2url($sqlc));
		
		if ($ile>0)
		{
			echo "Takie konto już istnieje!";
			return false;
		}
		else
		{
			$sql="INSERT INTO yala (klucz,email,pass,openid) VALUES ('$klucz','$email','$pass','$openid'); SELECT id FROM yala WHERE klucz='$klucz' AND email='$email'";
			parse_str(query2url($sql));
	
	
			if (!$id) return;
	
	
			while (true)
			{
				$hash=md5($id.time().rand(100000,3494959));
				$_REQUEST['hash']=$hash;
				$sql="UPDATE yala SET hash='$hash' WHERE klucz='$klucz'";
				if (pg_exec($this->db,$sql)) break;
			}
	
			$_REQUEST['sign']=strstr($_SERVER['REQUEST_URI'],'?')?'&':'?';
	
	
	
	
			foreach ($_REQUEST AS $k=>$v)
			{
				$txt=str_replace('{'.$k.'}',$v,$txt);
			}
	
			foreach ($_SERVER AS $k=>$v)
			{
				if (is_string($v)) $txt=str_replace('{'.$k.'}',$v,$txt);
			}
	
	
			if (!strlen($openid))
			{
				$headers='Content-Type: text/plain; charset="utf-8"'."\nFrom: ".FROM;
				mail($email,TEMAT,$txt,$headers);
			}
			else
			{
				$this->aktywuj($_REQUEST['hash']);
			}
	
			return $id;
		}
		
		
	}



	public function aktywuj($hash)
	{
		$sql="UPDATE yala SET aktywny=CURRENT_TIMESTAMP, wygasa=CURRENT_DATE+".TESTOWANIE." WHERE hash='$hash' AND aktywny IS NULL";
		pg_exec($this->db,$sql);

		$sql="SELECT id,klucz FROM yala WHERE hash='$hash'";
		parse_str(query2url($sql));

		echo "<script>wykonaj_jq_migracja('$klucz');</script>";

		

		return $id;
	}



}
