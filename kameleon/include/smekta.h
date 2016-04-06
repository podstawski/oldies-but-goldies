<?

	function smekta($txt,$vars)
	{

		foreach(array('smekta','argv','argc','adb','acl','sql','url','res','ciastka','adodb','kameleon','db','adb2','auth_acl') AS $k ) unset($vars[$k]);

		foreach(array_keys($vars) AS $k) 
		{
			if (strlen($k)==1) unset($vars[$k]);
			if (strtoupper($k)==$k) unset($vars[$k]);
			if ($k[0]=='_') unset($vars[$k]);


			if (is_array($vars[$k]) && is_array($vars[$k][0]) )
			{
				$vars[$k][0]['first']=1;
				$vars[$k][count($vars[$k])-1]['last']=1;
			}
		}



		if (strstr($txt,'{get_defined_vars}')) $vars['get_defined_vars']=print_r($vars,true);


		$kameleon_ob_replace_tokens_vars=$vars;
		$kameleon_ob_replace_tokens_result='';

		ob_start();
		echo $txt;
		include(dirname(__FILE__).'/../remote/ob_end.h');

		return $kameleon_ob_replace_tokens_result;

	}