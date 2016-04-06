<?

if (strlen($action) && $ver<=$LOCK_VERSION )
{
	$error="Wersje poni¿ej $LOCK_VERSION w³¹cznie s¹ zablokowane !";
	$action="";
}

if (isset($_POST)) 
   while ( list( $key, $val ) = each( $_POST ) )
   {
	$str2eval="\$$key=toText(\$$key);";
	eval($str2eval);
   }


while ( strlen($action) )
{
	include ("include/action/$action.h");
}

if ($error) echo "<script>alert('$error'); history.back()</script>";


?>