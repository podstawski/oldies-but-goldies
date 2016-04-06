<?
//echo "ac: $action";

while (strlen($api_action))
{
 include ("include/action/$api_action.h");
}
if ($error) echo "<script>alert('$error'); history.back()</script>";
?>