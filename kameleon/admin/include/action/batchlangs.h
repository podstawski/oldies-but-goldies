<?
	$action="";



	$labels=explode("\n",trim($_REQUEST['labellabel']));
	$values=explode("\n",trim($_REQUEST['labelvalue']));
	$langs=explode(',',trim($_REQUEST['batchlangs']));


	if (count($labels)!=count($values))
	{
		$error=label('Number of lines is not the same');
		return;
	}

	for($lab=0;$lab<count($labels);$lab++)
		for ($l=0;$l<count($langs); $l++ )
		{
			label(trim($labels[$lab]),trim($langs[$l]),trim($values[$lab]));
		}

?>