<?
$ret = "<form action=\"".$next."\" method=\"post\">";
$ret.= "<table class=\"tb\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>";
$ret.= "<td><img src=\"$SKLEP_IMAGES/s1.gif\" width=16 height=32></td>";
$ret.= "<td background=\"$SKLEP_IMAGES/s2.gif\">";
$ret.= "<input class=\"in\" type=\"text\" name=\"sword\" value=\"szukaj\">";
$ret.= "</td>";
$ret.= "<td background=\"$SKLEP_IMAGES/s2.gif\">";
$ret.= "<input type=\"image\" src=\"$SKLEP_IMAGES/sb.gif\">";
$ret.= "</td>";
$ret.= "<td><img src=\"$SKLEP_IMAGES/s3.gif\" width=16 height=32></td>";
$ret.= "</tr><table>";
$ret.= "</form>";
echo $ret;
?>
