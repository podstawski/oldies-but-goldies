<?php
	$base=$_REQUEST['base'];
	$sid=$_REQUEST['sid']+0;
	if (!file_exists($base)) $base=$_SERVER['DOCUMENT_ROOT'].$base;

	if (!file_exists($base)) return;

	$try=3;
	while($try>0)
	{
		$html=implode('',file($base));
		$pos1=strpos($html,"<sid$sid>")+strlen("<sid$sid>");
		$pos2=strpos($html,"</sid$sid>");

		if ($pos1>0 && $pos2>0) break;
		usleep(100);
		$try--;
	}

	$len=$pos2-$pos1;

	$xml='<nothing sorry="very"/>';
	if ($pos1>0 && $pos2>0) $xml=substr($html,$pos1,$len);

	Header("Content-type: application/xml");
	echo  '<?xml version="1.0" encoding="utf-8"?>'."\n$xml";

?>