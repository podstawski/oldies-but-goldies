<?
  if (!$kameleon->checkRight('write','box',$_POST['tdsid']) || !$kameleon->checkRight('write','page',$_POST['page_id']))
  {
	  $error=$norights;
	  return;
  }



  if (strlen($_POST['kolejka']) && strlen($_POST['levelek']) && strlen($_POST['tdsid'])) 
  {
    $kolejka=$_POST["kolejka"];
    $levelek=$_POST["levelek"];
    $tdsid=explode(",",$_POST["tdsid"]);
    $sidek=$tdsid[0];
    
  	$STD_WHERE="lang='$lang' AND server=$SERVER_ID";
  	if (isset($ver)) $STD_WHERE.=" AND ver=$ver";
  	if (strlen($pole)) $STD_WHERE.=" AND page_id='".$_POST["page_id"]."' ";
  
  	$VS=-100;
  
    
  
    $kolej = explode(";",substr($kolejka,0,-1));
    
    $sidy=array();
    $primy = array();
    
    for ($i=0;$i<sizeof($kolej);$i++)
    {
      $ktmp = explode(",",$kolej[$i]);
      $sidy[$i] = $ktmp[0];
      $primy[$i] = $ktmp[1];
    }
    
    sort($primy);
    
    for ($i=0;$i<sizeof($kolej);$i++)
    {
      if ($sidy[$i]==$sidek) $supd=", level=".$levelek." "; else $supd="";
      $adodb->Execute("UPDATE webtd SET pri=".$primy[$i].$supd." WHERE sid=".$sidy[$i]." AND $STD_WHERE");
    }
  }
