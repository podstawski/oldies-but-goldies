<?
	function kameleon_href($href,$variables,$page)
	{
		global $SERVER_ID;
		$query="SELECT file_ext FROM servers WHERE id=$SERVER_ID";
		parse_str(ado_query2url($query));
		if (!strlen($file_ext)) $file_ext="php";

		$wynik="$page.$file_ext";
		if (strlen($variables)) $wynik.="?$variables";
		return $wynik;
	}

?>