<?

class KAMELEON
{
	var $adodb;
	var $lang;
	var $pagelang;
	var $ver;
	var $server;
	var $charset;
	var $page;
	var $user;
	var $acl;


	function KAMELEON(&$adodb)
	{
		$this->adodb=&$adodb;
		$this->acl=null;
	}

	function init($lang,$ver=1,$server=0,$charset="",$page=0)
	{
		$this->server=$server;
		if (!strlen($this->lang) && strlen($lang) ) $this->lang=$lang;
		$this->ver=$ver;
		$this->charset=$charset;
		$this->page=$page;

	}

	function initAcl($acl,$old_pages,$old_menus,$old_proof,$old_templ,$old_class,$old_ftp,$old_admin)
	{
		$this->acl=$acl;
		
		$this->acl_pages=$old_pages;
		$this->acl_menus=$old_menus;
		$this->acl_proof=$old_proof;
		$this->acl_templ=$old_templ;
		$this->acl_class=$old_class;
		$this->acl_ftp=$old_ftp;
		$this->acl_admin=$old_admin;
		
	}

	function setlang($lang)
	{
		if (strlen($lang)) $this->lang=$lang;
	}

	function setpagelang($lang)
	{
		if (strlen($lang)) $this->pagelang=$lang;
	}


	function userFullName($u)
	{
		static $cache;
		
		if (isset($cache[$u])) return $cache[$u]; 
		
		global $auth_acl;
		
		$oryg=$u;
		if (is_object($auth_acl))
		{
			$user=$auth_acl->usernameInfo($u);
			if (is_array($user) && strlen($user['au_name']) )
			{
				$u=$user['au_name'];
			}
		}
		else
		{
			$sql="SELECT fullname AS u FROM passwd WHERE username ='$u' AND fullname<>''";
			parse_str(ado_query2url($sql));
		}
		
		$cache[$oryg]=$u;
		return $u;
	}

	function label($l,$lang='',$set='')
	{
		static $cache;
		global $CHARSET_TAB;

		

		if (!strlen($lang)) $lang=$this->lang;
		

		if (strlen($this->pagelang)==1 && $this->pagelang!=$lang)
		{
			include_once (dirname(__FILE__).'/../'.(strlen($lang)==1?'str_to_url_iso.h':'str_to_url_utf.h'));
		}

		//echo "L<b>$lang</b><br>";


		if (!is_array($cache[$lang]))
		{
			$charset=$CHARSET_TAB[$lang];
			if (!strlen($charset) && strlen($lang)==2) $charset='utf-8';
			$file=dirname(__FILE__).'/../lang/'.$lang.'_'.strtolower($charset).'.php';

			if (file_exists($file)) include($file);
			$cache[$lang]=$words;
		}


		

		$wynik = (strlen($cache[$lang][$l])) ? $cache[$lang][$l] : $l;

		if (strlen($this->pagelang)==1 && $this->pagelang!=$lang) $wynik = str_to_url($wynik);

		return $wynik;

		if (strlen($set))
		{
			$l=addslashes(stripslashes($l));
			$query="SELECT count(*) AS c FROM label WHERE label='$l' AND lang='$lang' LIMIT 1";
			parse_str(ado_query2url($query));



			$set=addslashes(stripslashes($set));
			if ($c)
				$query="UPDATE label SET value='$set' WHERE label='$l' AND lang='$lang'";
			else
				$query="INSERT INTO label (label,lang,value) VALUES ('$l','$lang','$set')";

			//$this->adodb->debug=1;
			$this->adodb->Execute($query);

			return $set;
		}


		$cache_label=$lang.'_'.$l;
		$CHARSET=&$this->charset;




		if ($this->adodb->checkSessionValue("label.$cache_label"))  
			return $this->adodb->getFromSession("label.$cache_label");

		$defaultlang="e";
		$lab=$l;
		$l=addslashes($l);

		$label="";
		$query="SELECT label FROM label 
			WHERE label='$l' AND lang='$defaultlang'
			LIMIT 1";
		parse_str(ado_query2url($query));
		if (!strlen($label) && strlen($l)) 
		{
			$query="INSERT INTO label (label,lang,value) VALUES ('$l','$defaultlang','$l')";
			$this->adodb->Execute($query);
		}	


		$query="SELECT value FROM label WHERE label='$l' AND lang='$lang' LIMIT 1";
		parse_str(ado_query2url($query));
		
		$found=0;
		if (!strlen($value)) $value=$l;
		else $found=1;

		if (function_exists("b_convert_charset") && function_exists("s_convert_charset")) 
			if (b_convert_charset($lang,$CHARSET,"label"))
			{
				$value=s_convert_charset($value,$lang,$CHARSET,"label");
			}

		$this->adodb->addToSession("label.$cache_label",stripslashes($value));
		return stripslashes($value);
	}
	
	
	function checkRight($right,$resource,$id='',$who='')
	{
		if ($resource=='page' && $id<0 ) $id='0';
		
		if (is_object($this->acl))
		{
			return $this->acl->hasRight($right,$resource,$id,$who);
		}
		
		
		if ($resource=='page')
		{
			if ($right=='read') return true;
			if ($right=='write' || $right=='insert' || $right=='delete')
			{
				$page=$id;
				$nr=$id;
				$zakres=$this->acl_pages;
			}
			if ($right=='publish') return $this->acl_ftp;
			if ($right=='proof')
			{
				$page=$id;
				$nr=$id;
				$zakres=$this->acl_proof;
			}
		}

		if ($resource=='box')
		{
			if ($right=='read') return true;
			if ($right=='write' || $right=='insert' || $right=='delete')
			{
				$sql="SELECT page_id FROM webtd WHERE sid=$id";
				parse_str(ado_query2url($sql));
				$page=$page_id;
				$nr=$page_id;
				$zakres=$this->acl_pages;
			}			
		}

		if ($resource=='menu')
		{
			if ($right=='read') return true;
			if ($right=='write' || $right=='insert' || $right=='delete')
			{
				$nr=$id;
				$zakres=$this->acl_menus;
			}			
		}
		
		
	
		if (!strlen($nr)) return true;
		if (!strlen($zakres)) return true;
		if ($nr==-1) return true;
		if ($zakres=='-') return false;
		
	
	
	
		$zakresy=explode(";",$zakres);
		for ($i=0;$i<count($zakresy);$i++)
		{
			$oddo=explode("-",$zakresy[$i]);
			
			if ( strpos($oddo[0],"+"))
			{
	
				$root=$oddo[0]+0;
				if ($nr==$root) return true;
				$page+=0;
				//echo "$root + ($tree) $query";
				$tree=kameleon_tree($page);
				if (strstr($tree,":$root:")) return true;
				else continue;
			}
		
	
			$od=$oddo[0]+0;
			$do=$oddo[1]+0;
			if (!$do) $do=$od;
			if ($nr>=$od && $nr<=$do) return true;
		}
		return false;
		
	}
	
}


