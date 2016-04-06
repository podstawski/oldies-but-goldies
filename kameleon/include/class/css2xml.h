<?php

// TODO: Obs³uga b³êdów

require_once('css.h');

class CSS2XML extends CSS
{
	////public
	var $xmlFileContent;
	var $addBackground;

	////private
	// XML predefined entities
	var $bodyBgColor;
	var $Aentities = array( '&'		=> '&amp',
							'\''	=> '&apos;',
							'<'		=> '&lt;',
							'>'		=> '&gt;',
							'"' 	=> '&quot;'); 
	
	var $AhtmlTags = array( 'A','ABBREV','ACRONYM','ADDRESS','APPLET','AREA','AU','AUTHOR','B','BANNER','BASE',
							'BASEFONT','BGSOUND','BIG','BLINK','BLOCKQUOTE','BQ','BODY','BR','CAPTION','CENTER',
							'CITE','CODE','COL','COLGROUP','CREDIT','DEL','DFN','DIR','DIV','DL','DT','DD','EM',
							'EMBED','FIG','FN','FONT','FORM','FRAME','FRAMESET','H1','H2','H3','H4','H5','H6',
							'HEAD','HR','HTML','I','IFRAME','IMG','INPUT','INS','ISINDEX','KBD','LANG','LH','LI',
							'LINK','LISTING','MAP','MARQUEE','MATH','MENU','META','MULTICOL','NOBR','NOFRAMES',
							'NOTE','OL','OVERLAY','P','PARAM','PERSON','PLAINTEXT','PRE','Q','RANGE','SAMP','SCRIPT',
							'SELECT','SMALL','SPACER','SPOT','STRIKE','STRONG','SUB','SUP','TAB','TABLE','TBODY','TD',
							'TEXTAREA','TEXTFLOW','TFOOT','TH','THEAD','TITLE','TR','TT','U','UL','VAR','WBR','XMP');	
	//Constructor
	//public
	function CSS2XML( $cssFile, $removeDots = true )
	{
		$this->CSS( $cssFile, $removeDots );
	}	


	//public
	function getXML( $addBackground = false )
	{
		$this->addBackground = $addBackground;

		header("Content-type: text/xml");
		$this->buildXML();
		echo $this->xmlFileContent; 
	}

	//private
	function buildXML()
	{
		$this->prepareXML();
		$this->createXMLContent();	
	}

	//private
	function createXMLContent()
	{
		$xmlContent = '<?xml version="1.0" encoding="utf-8" ?>' . "\r\n";
		
		if ( is_array($this->cssArray) )
		{
			$xmlContent .= "\t" . '<Styles>' . "\r\n";		
			foreach ( $this->cssArray as $key => $val )
			{
				//$xmlContent .= "\t\t" . '<Style name="' . $key . '" element="span">' . "\r\n";
				$xmlContent .= "\t\t" . '<Style name="' . $key . '" element="span">' . "\r\n";	
				$xmlContent .= "\t\t\t" . '<Attribute name="class" value="' . $key . '" />' . "\r\n"; 

				if ( $this->addBackground )
				{
					$xmlContent .= "\t\t\t" . '<Attribute name="style" value="background-color: ' . $this->bodyBgColor . ';" />' . "\r\n"; 
				}

				$xmlContent .= "\t\t" . '</Style>' . "\r\n";

				$xmlContent .= "\t\t" . '<Style name="' . $key . ' _" element="img">' . "\r\n";	
				$xmlContent .= "\t\t\t" . '<Attribute name="class" value="' . $key . '" />' . "\r\n"; 

				if ( $this->addBackground )
				{
					$xmlContent .= "\t\t\t" . '<Attribute name="style" value="background-color: ' . $this->bodyBgColor . ';" />' . "\r\n"; 
				}

				$xmlContent .= "\t\t" . '</Style>' . "\r\n";				
			}
			$xmlContent .= "\t" . '</Styles>' . "\r\n";
		}
		
		$this->xmlFileContent = $xmlContent;
		
	}


	//private
	function prepareXML()
	{
		if ( is_array($this->cssArray) )
		{
			if ( $this->addBackground == true )
			{
				$this->setBodyBgColor();
			}
//			$this->removeStyleProperties();
			$this->removeTagStyles();
			$this->removeDots();
			$this->removeColonStyles();
		}
		else
		{
			//pusty styl ???	
		}
	}
	
	//private
	function removeTagStyles()
	{
		foreach ( $this->cssArray as $key => $val )
		{
			if ( in_array( strtoupper($key), $this->AhtmlTags ) )
			{
				unset($this->cssArray[$key]);
			}
		}
	}
	
	//private
	function removeStyleProperties()
	{
		foreach ( $this->cssArray as $key => $val )
		{
			if ( $this->addBackground == true )
			{
				if ( strpos('background-color', $val) !== false)
				{
					$this->cssArray[$key] = null;
				}
				else
				{
					$this->cssArray[$key] = $this->bodyBgColor;
				}
			}
			else
			{
				$this->cssArray[$key] = null;
			}
		}
//		print_r($this->cssArray);
	}
	
	//private
	function removeColonStyles()
	{
		foreach ( $this->cssArray as $key => $val )
		{
			if ( strpos($key, ':') !== false )
			{
				unset( $this->cssArray[$key] );	
			}
		}
	}

	//public
	function getBGColor()
	{
		if ( empty($this->bodyBgColor) )
		{
			$this->setBodyBgColor();
		}
		return $this->bodyBgColor;
	}
	
	//private
	function setBodyBgColor($element = 'body')
	{
		if ( empty($this->bodyBgColor) )
		{
			if ( is_array($this->cssArray) && !empty($this->cssArray) ) 
			{
				foreach ( $this->cssArray as $key => $val )
				{
					if ( $key == $element )
					{
						foreach( $val as $value )
						{
							if ( strpos($value, 'background-color') !== false )
							{
								$pattern = '/background-color\:\s*?(.*?)\s*?;/mi';
								preg_match_all($pattern, $value, $matches);
								$this->bodyBgColor = $matches[1][0];
							}
						}
					}
				}
		    
			}
		}
	}

}
