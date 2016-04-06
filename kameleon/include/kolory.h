<html>

<head>
    <title>KAMELEON: <?echo label("Colors");?></title>

    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type"
        content="text/html; charset=<?echo $CHARSET?>">
</head>
<body bgcolor="#c0c0c0" topmargin="10">

<?php
	include_js("kolory");
?>

<?
 include ("include/usercolors.h");
 if (strlen($u_color) && $u_color[0]!="#")
   $u_color="#$u_color";
?>

<form method=post action=kolory.<?echo $KAMELEON_EXT?> name=kolory ENCTYPE="multipart/form-data" >
<input type=hidden name=page_id value="<?echo $page_id?>">
<input type=hidden name=page value="<?echo $page?>">
<input type=hidden name=pri value="<?echo $pri?>">
<input type=hidden name=action value="">

<table border=1 align=center bgcolor=white cellpadding=2 cellspacing=2> 
<tr class=k_form>
 <td valign=top align=center>
 <span class=k_text><?echo label("User colors")?></span>
 
  <table border=1 cellpadding=0 cellspacing=0>
    <?
     $len=count($USERCOLORS);
     $mod=1;
     for ($i=0;$i<$len;$i++)
     {
      $color=$USERCOLORS[$i];
      $td.="
        <td bgcolor='$color' onClick='preview(this)'>
        <img src=img/spacer.gif width=15 height=15 border=0></td>
        ";
      if (!($mod++%5))
      {
       $tr.="<tr>$td</tr>\n";
       $td="";
      }
     }
     if ($td!="")
      $tr.="<tr>$td</tr>\n";
     echo $tr;
    ?>
  </table>
 </td>
 <td valign=top>
  <span class=k_text><?echo label("Default colors")?></span>
    <table border=1 cellpadding=0 cellspacing=0>
      <tr>  
       <td bgcolor=#000000 onClick="preview(this)" colspan=5><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFFFF onClick="preview(this)" colspan=5><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>  
      <tr>  
       <td bgcolor=#000000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#202020 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#404040 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#606060 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#808080 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#A0A0A0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#C0C0C0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#E0E0E0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F0F0F0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#FF0000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF2020 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF4040 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF6060 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF8080 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFA0A0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFC0C0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFE0E0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFF0F0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFF9F9 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#00FF00 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#20FF20 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#40FF40 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#60FF60 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#80FF80 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#A0FFA0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#C0FFC0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#E0FFE0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F0FFF0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F9FFF9 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#0000FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#2020FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#4040FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#6060FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#8080FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#A0A0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#C0C0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#E0E0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F0F0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F9F9FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#001010 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#002020 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#004040 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#006060 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#008080 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#00A0A0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#00C0C0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#00E0E0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#00F0F0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#00FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#10FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#20FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#40FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#60FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#80FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#A0FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#C0FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#E0FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F0FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F9FFFF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#101000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#202000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#404000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#606000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#808000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#A0A000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#C0C000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#E0E000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F0F000 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFF00 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#FFFF10 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFF20 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFF40 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFF60 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFF80 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFFA0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFFC0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFFE0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFFF0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFFFF9 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#100010 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#200020 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#400040 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#600060 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#800080 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#A000A0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#C000C0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#E000E0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#F000F0 onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF00FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
      <tr>
       <td bgcolor=#FF10FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF20FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF40FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF60FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FF80FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFA0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFC0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFE0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFF0FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
       <td bgcolor=#FFF9FF onClick="preview(this)"><img src=img/spacer.gif width=15 height=15 border=0></td>
      </tr>
    </table>  
 </td>
</tr>
<tr class=k_form>
 <td align=center>
 
 <table border=0 cellpadding=0 cellspacing=0><tr><td id="view" bgcolor=<?echo $u_color?>> 
     <img src=img/spacer.gif width=80 height=30 border=1 bordercolor=black><br>
 </td></tr></table>
 <br>
 
 <span class=k_text><?echo label("Full color")?>: </span>    
 <input class=k_input type=text size=8 name=hexcolor value='<?echo strtoupper($u_color);?>' onBlur="userpreview(this);HEXtoRGB(hexcolor)"><br> 
 </td>
 <td valign=top align=left>
 
  <table border=0 cellpadding=0 cellspacing=0 width=100%>
  <tr>
   <td align=right>
   <span class=k_text><?echo label("Red")?>:</span> <input class=k_input type=text size=3 name=rcolor value='255' onBlur="RGBtoHEX(rcolor.value,gcolor.value,bcolor.value);userpreview(hexcolor)"><br>
   <span class=k_text><?echo label("Green")?>:</span> <input class=k_input type=text size=3 name=gcolor value='255' onBlur="RGBtoHEX(rcolor.value,gcolor.value,bcolor.value);userpreview(hexcolor)"><br>
   <span class=k_text><?echo label("Blue")?>:</span> <input class=k_input  type=text size=3 name=bcolor value='255' onBlur="RGBtoHEX(rcolor.value,gcolor.value,bcolor.value);userpreview(hexcolor)"><br>
   </td>
   <td align=right valign=bottom>
   <input class=k_button type=button value="<?echo label("Save")?>" onClick="zapiszKolor(this)">
   </td>
  </tr>
  </table>
  
  
  
 </td>
</tr>
</table>
</form>
</body>
</html>
