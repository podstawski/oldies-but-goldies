<?
	if (!$WEBTD->sid)
	{
		foreach ( array_keys($_REQUEST) AS $k ) eval("\$$k=\$_REQUEST[\"$k\"];");
		foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");
	}
	

	$PLATNOSCI_PL_POS_ID=1727;
	$PLATNOSCI_PL_POS_K1='375b579c63e7b6c79e445a9e7bc47ddb';
	$PLATNOSCI_PL_POS_K2='45262b71a49155d6a6ca2cb01b2a31d7';


	if ($_REQUEST[pos_id]==$PLATNOSCI_PL_POS_ID);
	{

		$sig2=md5($_REQUEST[pos_id].$_REQUEST[session_id].$_REQUEST[ts].$PLATNOSCI_PL_POS_K2);
		$_REQUEST[sig_wg_mnie]=$sig2;

		if ($_REQUEST[sig]==$sig2)  
		{
			echo "OK\n";
			$_REQUEST[action]='PlatnosciPl';
		}
		
	}

	$SKLEP_INCLUDE_PATH=$INCLUDE_PATH.'/sklep';
	include($SKLEP_INCLUDE_PATH.'/pre.php');

?>