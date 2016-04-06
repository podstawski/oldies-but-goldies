<?php

class CSS
{
	//public
	var $cssFile;
	var $cssArray = array();
	var $oneFileCssArray;
	var $removeDots;

	//private
	var $cssFileContent;

	//Constructor
	//public
	function CSS( $cssFile, $removeDots = true )
	{
		$this->makeFileArray( $cssFile );
		$this->removeDots = $removeDots;
		
		if ( !empty($this->cssFile) )
		{
			foreach ( $this->cssFile as $cssFile )
			{
				//echo $cssFile;
				$this->readCss( $cssFile );
			}
		}
		else
		{
			return;
			// nie ma pliku
		}
	}

	//private
	function makeFileArray( $fileOrArray )
	{
		if ( is_array($fileOrArray) ) $this->cssFile = $fileOrArray;
		else ( $this->cssFile = array( $fileOrArray ) );
	}
	
	//private
	function readCss( $cssFile )
	{
		$css = @implode ('', @file ( $cssFile ) );
		
		if ( empty($css) ) return;
		
		if ($css)
		{
			$this->cssFileContent = $css;
			$this->cleanComments();
		}
		else
		{
			return;
			// nie ma pliku, albo siê nie chce dac przeczytaæ.
		}
		
	}

	//private
	function cleanComments()
	{
		$machComments = '/\/\*.*?\*\//s';
		$this->cssFileContent = preg_replace( $machComments, '', $this->cssFileContent );
		$this->buildCssArray();
	}
	
	////private
	function buildCssArray( )
	{
		$this->removeLineBreaks();
		$this->separateNamesAndStyles();
	}

	//private
	function removeLineBreaks()
	{
		$this->cssFileContent = str_replace( array("\r","\n","\t","\v"), '', $this->cssFileContent );
	} 
	
	//private
	function separateNamesAndStyles()
	{
		// index 1 = style name
		// index 2 = style properties
		$match = '/(.*?)\{(.*?)\}/m';
		preg_match_all($match, $this->cssFileContent, $Ahits);

		$AstyleNames 	= $Ahits[1];
		$AstyleValues	= $Ahits[2];

		// Clean and rebuild style properties and names
		$AstyleNames	= $this->buildStyleNames($AstyleNames);
		$AstyleValues	= $this->buildStyleValues($AstyleValues);
		
		
		//Merge keys and values
		$this->oneFileCssArray = $this->my_array_combine( $AstyleNames, $AstyleValues );

		$this->splitMultiStyles($this->oneFileCssArray);
		
		$this->cssArray = array_merge( $this->cssArray, $this->oneFileCssArray );

		reset($this->cssArray);
		ksort($this->cssArray);
	}

	//private
	function splitMultiStyles(&$AstyleArray)
	{
		if ( is_array($AstyleArray) && !empty($AstyleArray) )
		{
			foreach ( $AstyleArray as $key => $val )
			{
				if ( strpos($key, ',') )
				{
					$styleNames = explode( ',', $key );
					$styleProperties = 	$AstyleArray[$key];
					
					foreach ( $styleNames as $styleName )
					{
						$AstyleArray[$styleName] = $styleProperties; 					
					}
					
					unset( $AstyleArray[$key] );
				}
			}
			return true;
		}
		else
		{
			//Do nothing, and set no error
			return false;
		}	
	}

	//private
	function buildStyleNames($AstyleNames)
	{
		if ( is_array($AstyleNames) && !empty($AstyleNames) )
		{
			foreach ( $AstyleNames as $key => $val )
			{
				$AstyleNames[$key] = trim($val);
			}
			
			return $AstyleNames;
			
		}
		else
		{
			//Do nothing, and set no error
			return;
		}
			
	}

	//private
	function buildStyleValues($AstyleValues)
	{
		$AnewStyleProperties	= array();
		
		if ( is_array($AstyleValues) )
		{
			// Separate stype properties, and
			// build new array
			foreach ( $AstyleValues as $key => $val )
			{
				$AstyleProperties = explode(';', $val);
				
				if ( is_array($AstyleProperties) && !empty($AstyleProperties) )
				{
					$AnewStyleProperties = array();

					foreach ( $AstyleProperties as $propKey => $propVal )
					{
						$propVal = trim( $propVal );
						
						if ( !empty($propVal) )
						{
							$AnewStyleProperties[$propKey] = $propVal . ';';
						}
					}
				
				$AstyleValues[$key] = $AnewStyleProperties;
					
				}
				else
				{
					// not array, but no error	
				}
			}

			return $AstyleValues;

		}
		else
		{
			//Do nothing, and set no error
			return;
		}
	}

	//public
	function getCssArray()
	{
		if ( $this->removeDots ) 
		{
			$this->removeDots();
		}
		return $this->cssArray;
	}
	
	//public
	function explodeStyleItem( $item )
	{
		if (empty($item)) return;

		$Aitem = explode (':', str_replace(';', '', $item));
		return $Aitem;
	}

	//public 
	function getCSS()
	{
		header("Content-type: text/css");
		foreach ($this->cssArray as $styleName => $styleArray )	
		{
			echo $styleName . "\n" . '{' . "\n";
			foreach ( $styleArray as $styleValues )
			{
				echo ($styleValues . "\n");
			}
			echo '}' . "\n";
		}
	}

	//private
	function removeDots()
	{
		foreach ( $this->cssArray as $key => $val )
		{
			if ( strpos($key, '.') == 0 && strpos($key, '.') !== false )
			{
				$newKey = substr($key, 1);
				$this->cssArray[$newKey] = $val;
				unset($this->cssArray[$key]); 
			}
		}	
	}
	
	//private
	function my_array_combine($a1, $a2)
	{
		if(count($a1) != count($a2))
			return false;

		if(count($a1) <= 0)
			return false;

		$a1 = array_values($a1);
		$a2 = array_values($a2);

		$output = array();
		$ile = count($a1);

		//print_r($a1);		print_r($a2);
		
		for($i = 0; $i < $ile; $i++)
		{
			$output[$a1[$i]] = $a2[$i];
		}

		return $output;
	}

}
