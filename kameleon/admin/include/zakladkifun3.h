<?
function zakladka2($text,$href,$bgcolor,$tcolor, $img)
{
 $text="<a href=$href class=k_zakl01 style='color: $tcolor'>$text</a>";
 $res="
 <table cellspacing=0 cellpadding=0 border=1 width=100%>
  <tr>
   <td bgcolor=$bgcolor width=1 rowspan=2 align=right valign=top><img src=$img/z_left.gif border=0></td>
   <td bgcolor=#000000  align=center valign=top><img src=$img/spacer.gif border=0 width=0 height=1></td>
   <td bgcolor=$bgcolor width=1 rowspan=2 align=left valign=top><img src=$img/z_right.gif border=0></td>
  </tr>
  <tr>
   <td bgcolor=$bgcolor align=center valign=middle class=k_zakl01>$text</td>
  </tr>
 </table>";
 return $res;
}


function zakladki2($kattab,$katmax, $kategoria, $pre,$post,$apre,$apost)
{

 $katlen=count($kattab);
 while ($katmax && ($katlen%$katmax)>0 && ($katlen%$katmax)<($katmax/2) )
        $katmax--;
 $table="";
 $zakladki_rows="";
 $id=0;
 for ($i=0;$i<$katlen;$i++)
 {
  $data=$kattab[$i];
  $nazwa=$data[0];
  $id=$data[1];
  $href=$data[2];
  $param="$data[3]=$id&$data[4]";
  $param=koduj_url($param);
  if (strlen($data[3]) || strlen($data[4])) $href.="?$param";

  if ($id==$kategoria)
  {
	$przed=$apre;
	$za=$apost;
    $row_active=1;
  }
  else
  {
   	$przed=$pre;
	$za=$post;
  }

  $tcol=$textcolor;

  $nazwa="<a href=\"$href\" class=\"km_item_href\">&nbsp;$nazwa&nbsp;</a>";
  $zak="$przed $nazwa $za";

  $mod=0+(($i+1) % $katmax);
  if ($mod!=0)
  {
   $row.= $zak;
  }
  else
  {
   $row.= $zak;
   $zakladki_row =$row;
   $row="";
   if ($row_active>0)
   {
    $zakladki_active=$zakladki_row;
    $row_active=0;
   }
   else
    $zakladki_rows.=$zakladki_row;
  }
 }
 if ($row!="")
 {
   $zakladki_rows.=$row;
 }
 $zakladki_rows.=$zakladki_active;
 $table=$zakladki_rows;
 return $table;
}

?>