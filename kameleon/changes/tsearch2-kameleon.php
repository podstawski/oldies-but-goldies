<?

	$sql=file_get_contents(preg_replace('/\.php$/','.sql',__FILE__));


	$kameleon_adodb->debug=1;
	$kameleon_adodb->execute($sql);
	$kameleon_adodb->debug=0;
