<?
if ($FORUMFUN_INCLUDED==1) return;
$FORUMFUN_INCLUDED=1;


function SetForumTxt($adodb,$serwisid,$ojciec,$userid,$temat,$msg, $osoba)
{
  global $KEY;
  $osoba=addslashes(stripslashes($osoba));
  $temat=addslashes(stripslashes($temat));
  $msg=addslashes(stripslashes($msg));

  $query="INSERT INTO forum (serwisid,ojciec,userid,data,temat,tresc,osoba,servername)
	  VALUES ('$serwisid',$ojciec,$userid,
	  ".time().",
	  '$temat','$msg','$osoba','$KEY')";
//  echo $query;
  $adodb->Execute($query);
}

function ZliczOdpowiedzi($adodb,$forumid)
{
  global $KEY;
  $forumid+=0;
  $query="SELECT forumid AS fid FROM forum WHERE ojciec=$forumid AND servername='$KEY'";
  $result=$adodb->Execute($query);

  for($i=0;$i<$result->RecordCount();$i++)
  {
	parse_str(ado_ExplodeName($result,$i));
	$suma+=ZliczOdpowiedzi($adodb,$fid);
  }
  return ($suma+$result->RecordCount());
}

function GetForum($adodb,$serwisid,$ojciec)
{
  global $KEY;
  if (strstr($ojciec,":"))
  {
	$daty=explode(":",$ojciec);
	$and="data >= '$daty[0]' AND data <= '$daty[1]' AND ojciec=0";
  }
  else  $and="ojciec=$ojciec";

  $ojciec+=0;
  if (strlen($serwisid)) $id_warunek="serwisid='$serwisid'";
  else $id_warunek="1=1";
  $query="SELECT forumid,osoba,data,czas,temat,tresc
	  FROM forum 
	  WHERE $id_warunek AND $and AND servername='$KEY'
	  ORDER BY forumid";
  //echo $query;

  $result=$adodb->Execute($query);
  
  for ($i=0;$i<$result->RecordCount();$i++)
  {
	parse_str(ado_ExplodeName($result,$i));

	$czas=FormatujDate($data).", $czas";
	$czas=date('d-m-Y H:i',$data);

	$odpow=ZliczOdpowiedzi($adodb,$forumid);
	$temat=stripslashes($temat);
	$tresc=stripslashes($tresc);
	$wynik[]=array($forumid,$osoba,$czas,$temat,$odpow,$tresc);	
  }

  return ($wynik);
}

function ForumSearch($adodb,$serwisid, $q)
{
  global $KEY;

  $q_or=explode(" ",$q);
  for ($or=0;$or<count($q_or);$or++)
  {
	if (!$or) $query.="(";
	if (!strlen(trim($q_or[$or])) ) continue;
	if ($q_or[$or]=="+") 
	{
		if ($or) $query.=" OR ";
		$query.="1=0";
		continue;
	}
	if ($or) $query.=" OR ";
	$q_and=explode("+",$q_or[$or]);
	for ($and=0;$and<count($q_and);$and++)
	{
		if (!$and) $query.="(";
		if (!strlen(trim($q_and[$and])) ) 
		{
			if ($and) $query.=" AND ";
			$query.="1=1";
			continue;
		}
		if ($and) $query.=" AND ";
		$query.="(temat~*'$q_and[$and]' OR tresc~*'$q_and[$and]')";
	}
	$query.=")";
  }
  $query.=")";

  //echo $query;

  if (strlen($serwisid)) $id_warunek="serwisid='$serwisid'";
  else $id_warunek="1=1";

  $query="SELECT forumid,userid,data,czas,temat,ojciec,tresc
	  FROM forum 
	  WHERE $id_warunek AND $query AND servername='$KEY'
	  ORDER BY forumid";

  $result=$adodb->Execute($query);
  
  for ($i=0;$i<$result->RecordCount();$i++)
  {
	parse_str(ado_ExplodeName($result,$i));

	$czas=FormatujDate($data).", $czas";
	$czas=date('d-m-Y H:i',$data);

	$odpow=0;

	$wynik[]=array($forumid,$userid,$czas,$temat,$odpow,$tresc,$ojciecid);	
  }

  return ($wynik);
}

function GetForumTxt($adodb,$forumid)
{
  global $KEY;
  $forumid+=0;
  $query="SELECT tresc FROM forum WHERE forumid=$forumid AND servername='$KEY'";
  
  $result=$adodb->Execute($query);

  if ( $result->RecordCount() ) 
  {
	parse_str(ado_ExplodeName($result,0));
	return  stripslashes($tresc);
  }

  return "";  
}


function GetForumSubject($adodb,$forumid)
{
  global $KEY;  
  $forumid+=0;
  $query="SELECT temat FROM forum WHERE forumid=$forumid AND servername='$KEY'";
  
  $result=$adodb->Execute($query);

  if ( $result->RecordCount() ) 
  {
	parse_str(ado_ExplodeName($result,0));
	return stripslashes($temat);

  }

  return "";  
}

function GetForumUser($adodb,$forumid)
{
  global $KEY;  
  $forumid+=0;
  $query="SELECT userid FROM forum WHERE forumid=$forumid AND servername='$KEY'";
  
  $result=$adodb->Execute($query);

  if ( $result->RecordCount() ) 
  {
	parse_str(ado_ExplodeName($result,0));
	return $userid;
  }

  return 0;  
}

function DeleteForumItem($adodb,$forumid)
{
  global $KEY;
  $forumid+=0;
  $query="SELECT forumid AS id FROM forum WHERE ojciec=$forumid";
  $SQL="DELETE FROM forum WHERE forumid=$forumid AND servername='$KEY'";
  $result=$adodb->Execute($query);
  $rows=$result->RecordCount();
  if ($rows>0)
  {
   for ($i=0;$i<$rows;$i++)
   {
    parse_str(ado_ExplodeName($result,$i));
    DeleteForumItem($adodb,$id);
   }
  }
  $adodb->Execute($SQL);
}

?>
