<?
	$action="";
	$file="$IMAGES/textstyle.css";

	if (!file_exists($file)) $error=label("Template")." ".label("does not exist.");
	if (strlen($error)) return;
	
	include('include/class/css.h');
	
	$oCSS = new CSS( $file , false); //false oznacza, ¿e style bed¹ z kropkami
	
	$styleArray = $oCSS->getCssArray();

	$query="DELETE FROM class WHERE server=$SERVER_ID AND ver=$ver";
	$res = $adodb->Execute($query);
	
	if ( !empty ($styleArray) && is_array($styleArray) ) 
	{
		foreach ( $styleArray as $styleName => $styleItemArray )
		{
			foreach ( $styleItemArray as $val )
			{
				$style = $oCSS->explodeStyleItem( $val );
				$pole = $style[0];
				$wart = $style[1];
				$hash = md5($styleName.$pole.$wart);

   				$query = "INSERT INTO class (server,nazwa,pole,wart,ver)
					VALUES (" . $SERVER_ID . ", '".$styleName."', '".$pole."', '".$wart."', $ver); ";
 				$adodb->Execute($query);
			}
			
		}
	}

	// Client style
	$file="$UIMAGES/$DEFAULT_TEXTFILE_CSS";
	if (!file_exists($file)) return;

	$oCSS = new CSS( $file , false); //false oznacza, ¿e style bed¹ z kropkami
	
	$styleArray = $oCSS->getCssArray();

	if ( !empty ($styleArray) && is_array($styleArray) ) 
	{
		foreach ( $styleArray as $styleName => $styleItemArray )
		{
			foreach ( $styleItemArray as $val )
			{
				$style = $oCSS->explodeStyleItem( $val );
				$pole = $style[0];
				$wart = $style[1];
				//$hash = md5($styleName.$pole.$wart);

				$query="DELETE FROM class WHERE server=$SERVER_ID AND ver=$ver and nazwa like'".$styleName."' and pole like '".$pole."'";
				$res = $adodb->Execute($query);

//   				$query = "INSERT INTO class (server,nazwa,pole,wart,ver, hash)
//					VALUES (" . $SERVER_ID . ", '".$styleName."', '".$pole."', '".$wart."', $ver, 'kameleonOverwrite'); ";
// 				$adodb->Execute($query);
			}
			
		}
	}
	
	//Usuniêcie indywidualnego pliku z CSSem
	unlink ($file);
	
?>
