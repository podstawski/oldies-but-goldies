<?
	if ($_GET[action]==$action || $_POST[action]==$action) return;

	include_once("$INCLUDE_PATH/kameleon.platnosci.pl.class.php");
	$PL=new PLATNOSCI_PL($PLATNOSCI_PL_POS_ID,$PLATNOSCI_PL_POS_K1,$PLATNOSCI_PL_POS_K2,'ISO');

	
	$weryfikacja=$PL->get($_REQUEST[session_id]);
	
	
	$z=explode('-',$_REQUEST[session_id]);
	$_za_id=$z[0]+0;
	$za_id=0;
	$query="SELECT * FROM zamowienia WHERE za_id=$_za_id";
	parse_str(ado_query2url($query));
	if (!$za_id) return;

	if ( $za_status==0 || $za_status==1 ) 
		if  ( $weryfikacja[trans_status]=='99' )
		{
			$FORM[acc_status]=$za_status;
			$FORM[new_status]=2;
			$FORM[accept_id]=$za_id;

			$action='ZamowienieUp';
		}

	echo $action;
?>