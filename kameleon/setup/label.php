<?php

        $rh=explode(".",$REMOTE_HOST);
        $lang="en";
        if (count($rh)>1)
        {
                $l=$rh[count($rh)-1];
                if (!is_int($l)) $lang=$l;
        }
        if ($HTTP_ACCEPT_LANGUAGE=="pl") $lang="pl";


	include("./labels.php");

	function label($txt)
	{
		global $lang,$LABEL;

		$wynik=$LABEL["${lang}_$txt"];
		if (!strlen($wynik)) $wynik=$LABEL["en_$txt"];
		if (strlen($wynik)) return $wynik;
		return $txt;
	}
?>
