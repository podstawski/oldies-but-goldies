/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.dialog.add( 'pliki', function( editor )
{
	// Handles the event when the "Target" selection box is changed.
	var nodek = "";
	var targetChanged = function()
	{
		var dialog = this.getDialog(),
			popupFeatures = dialog.getContentElement( 'target', 'popupFeatures' ),
			targetName = dialog.getContentElement( 'target', 'linkTargetName' ),
			value = this.getValue();

		if ( !popupFeatures || !targetName )
			return;

		popupFeatures = popupFeatures.getElement();

		if ( value == 'popup' )
		{
			popupFeatures.show();
			targetName.setLabel( editor.lang.link.targetPopupName );
		}
		else
		{
			popupFeatures.hide();
			targetName.setLabel( editor.lang.link.targetFrameName );
			this.getDialog().setValueOf( 'target', 'linkTargetName', value.charAt( 0 ) == '_' ? value : '' );
		}
	};

	// Handles the event when the "Type" selection box is changed.
	var linkTypeChanged = function()
	{
		var dialog = this.getDialog(),
			partIds = [ 'urlOptions', 'anchorOptions', 'insideOptions',  'plikiOptions', 'obrazkiOptions', 'emailOptions' ],
			typeValue = this.getValue(),
			uploadTab = dialog.definition.getContents( 'upload' ),
			uploadInitiallyHidden = uploadTab && uploadTab.hidden;

		if ( typeValue == 'url' )
		{
			if ( editor.config.linkShowTargetTab )
				dialog.showPage( 'target' );
			if ( !uploadInitiallyHidden )
				dialog.showPage( 'upload' );
		}
		else
		{
			dialog.hidePage( 'target' );
			if ( !uploadInitiallyHidden )
				dialog.hidePage( 'upload' );
		}

		for ( var i = 0 ; i < partIds.length ; i++ )
		{
			var element = dialog.getContentElement( 'info', partIds[i] );
			if ( !element )
				continue;

			element = element.getElement().getParent().getParent();
			if ( partIds[i] == typeValue + 'Options' )
				element.show();
			else
				element.hide();
		}
	};

	// Loads the parameters in a selected link to the link dialog fields.
	var emailRegex = /^mailto:([^?]+)(?:\?(.+))?$/,
		emailSubjectRegex = /subject=([^;?:@&=$,\/]*)/,
		emailBodyRegex = /body=([^;?:@&=$,\/]*)/,
		anchorRegex = /^#(.*)$/,
		// wstawka Kameleona
		insideRegex = /^kameleon:inside_link\((.*)\)$/,
    ufilesRegex = /^ufiles\/(.*)$/,
    uimagesRegex = /^uimages\/(.*)$/,
    // koniec wstawka Kameleona 
		urlRegex = /^(?!javascript)((?:http|https|ftp|news):\/\/)?(.*)$/,
		selectableTargets = /^(_(?:self|top|parent|blank))$/,
		encodedEmailLinkRegex = /^javascript:void\(location\.href='mailto:'\+String\.fromCharCode\(([^)]+)\)(?:\+'(.*)')?\)$/,
		functionCallProtectedEmailLinkRegex = /^javascript:([^(]+)\(([^)]+)\)$/;

	var popupRegex =
		/\s*window.open\(\s*this\.href\s*,\s*(?:'([^']*)'|null)\s*,\s*'([^']*)'\s*\)\s*;\s*return\s*false;*\s*/;
	var popupFeaturesRegex = /(?:^|,)([^=]+)=(\d+|yes|no)/gi;

	var parseLink = function( editor, element )
	{
		var href = element ? ( element.getAttribute( '_cke_saved_href' ) || element.getAttribute( 'href' ) ) : '',
			emailMatch,
			anchorMatch,
			insideMatch, // wstawka Kameleona
			plikiMatch, // wstawka Kameleona
			obrazkiMatch, // wstawka Kameleona
			urlMatch,
			retval = {};

		if ( ( anchorMatch = href.match( anchorRegex ) ) )
		{
			retval.type = 'anchor';
			retval.anchor = {};
			retval.anchor.name = retval.anchor.id = anchorMatch[1];
		}
		// wstawka Kameleona
		else if ( ( insideMatch = href.match ( insideRegex ) ))
		{
      retval.type = 'inside';
      retval.inside = {};
      ind = insideMatch[1].split(",");
      retval.inside.node = ind[0];
      if (ind[1]!=undefined) { retval.inside.variables = ind[1]; }
      document.getElementById('km_treebrowser_frame').src=CKEDITOR.getUrl( editor.plugins.link.path+'../../../tree.php?multi=1&node='+retval.inside.node);
    }
    else if ( ( plikiMatch = href.match( ufilesRegex ) ) )
    {
      retval.type = 'pliki';
      retval.pliki = {};
      retval.pliki.url=plikiMatch[0];
      document.getElementById('km_ufilesbrowser_frame').src=CKEDITOR.getUrl( editor.plugins.link.path+'../../../ufiles.php?galeria=9&ckpath='+retval.pliki.url);
    }
    else if ( ( obrazkiMatch = href.match( uimagesRegex ) ) )
    {
      retval.type = 'obrazki';
      retval.obrazki = {};
      retval.obrazki.url=obrazkiMatch[0];
      document.getElementById('km_uimagesbrowser_frame').src=CKEDITOR.getUrl( editor.plugins.link.path+'../../../ufiles.php?galeria=11&ckpath='+retval.obrazki.url);
    }
		// koniec wstawka Kameleona
		
		// urlRegex matches empty strings, so need to check for href as well.
		else if ( href && ( urlMatch = href.match( urlRegex ) ) )
		{
			retval.type = 'url';
			retval.url = {};
			retval.url.protocol = urlMatch[1];
			retval.url.url = urlMatch[2];
		}
		// Protected email link as encoded string.
		else if ( !emailProtection || emailProtection == 'encode' )
		{
			if( emailProtection == 'encode' )
			{
				href = href.replace( encodedEmailLinkRegex,
						function ( match, protectedAddress, rest )
						{
							return 'mailto:' +
							       String.fromCharCode.apply( String, protectedAddress.split( ',' ) ) +
							       ( rest && unescapeSingleQuote( rest ) );
						} );
			}

			emailMatch = href.match( emailRegex );

			if( emailMatch )
			{
				var subjectMatch = href.match( emailSubjectRegex ),
					bodyMatch = href.match( emailBodyRegex );

				retval.type = 'email';
				var email = ( retval.email = {} );
				email.address = emailMatch[ 1 ];
				subjectMatch && ( email.subject = decodeURIComponent( subjectMatch[ 1 ] ) );
				bodyMatch && ( email.body = decodeURIComponent( bodyMatch[ 1 ] ) );
			}
		}
		// Protected email link as function call.
		else if( emailProtection )
		{
			href.replace( functionCallProtectedEmailLinkRegex, function( match, funcName, funcArgs )
			{
				if( funcName == compiledProtectionFunction.name )
				{
					retval.type = 'email';
					var email = retval.email = {};

					var paramRegex = /[^,\s]+/g,
						paramQuoteRegex = /(^')|('$)/g,
						paramsMatch = funcArgs.match( paramRegex ),
						paramsMatchLength = paramsMatch.length,
						paramName,
						paramVal;

					for ( var i = 0; i < paramsMatchLength; i++ )
					{
						paramVal = decodeURIComponent( unescapeSingleQuote( paramsMatch[ i ].replace( paramQuoteRegex, '' ) ) );
						paramName = compiledProtectionFunction.params[ i ].toLowerCase();
						email[ paramName ] = paramVal;
					}
					email.address = [ email.name, email.domain ].join( '@' );
				}
			} );
		}
		else
			retval.type = 'url';

		// Load target and popup settings.
		if ( element )
		{
			var target = element.getAttribute( 'target' );
			retval.target = {};
			retval.adv = {};

			// IE BUG: target attribute is an empty string instead of null in IE if it's not set.
			if ( !target )
			{
				var onclick = element.getAttribute( '_cke_pa_onclick' ) || element.getAttribute( 'onclick' ),
					onclickMatch = onclick && onclick.match( popupRegex );
				if ( onclickMatch )
				{
					retval.target.type = 'popup';
					retval.target.name = onclickMatch[1];

					var featureMatch;
					while ( ( featureMatch = popupFeaturesRegex.exec( onclickMatch[2] ) ) )
					{
						if ( featureMatch[2] == 'yes' || featureMatch[2] == '1' )
							retval.target[ featureMatch[1] ] = true;
						else if ( isFinite( featureMatch[2] ) )
							retval.target[ featureMatch[1] ] = featureMatch[2];
					}
				}
			}
			else
			{
				var targetMatch = target.match( selectableTargets );
				if ( targetMatch )
					retval.target.type = retval.target.name = target;
				else
				{
					retval.target.type = 'frame';
					retval.target.name = target;
				}
			}

			var me = this;
			var advAttr = function( inputName, attrName )
			{
				var value = element.getAttribute( attrName );
				if ( value !== null )
					retval.adv[ inputName ] = value || '';
			};
			advAttr( 'advId', 'id' );
			advAttr( 'advLangDir', 'dir' );
			advAttr( 'advAccessKey', 'accessKey' );
			advAttr( 'advName', 'name' );
			advAttr( 'advLangCode', 'lang' );
			advAttr( 'advTabIndex', 'tabindex' );
			advAttr( 'advTitle', 'title' );
			advAttr( 'advContentType', 'type' );
			advAttr( 'advCSSClasses', 'class' );
			advAttr( 'advCharset', 'charset' );
			advAttr( 'advStyles', 'style' );
		}
		// wstawka Kameleona
		else
		{
      document.getElementById('km_treebrowser_frame').src=CKEDITOR.getUrl( editor.plugins.link.path+'../../../tree.php?multi=1&node=');
    }
    // koniec wstawka Kameleona

		// Find out whether we have any anchors in the editor.
		// Get all IMG elements in CK document.
		var elements = editor.document.getElementsByTag( 'img' ),
			realAnchors = new CKEDITOR.dom.nodeList( editor.document.$.anchors ),
			anchors = retval.anchors = [];

		for( var i = 0; i < elements.count() ; i++ )
		{
			var item = elements.getItem( i );
			if ( item.getAttribute( '_cke_realelement' ) && item.getAttribute( '_cke_real_element_type' ) == 'anchor' )
			{
				anchors.push( editor.restoreRealElement( item ) );
			}
		}

		for ( i = 0 ; i < realAnchors.count() ; i++ )
			anchors.push( realAnchors.getItem( i ) );

		for ( i = 0 ; i < anchors.length ; i++ )
		{
			item = anchors[ i ];
			anchors[ i ] = { name : item.getAttribute( 'name' ), id : item.getAttribute( 'id' ) };
		}

		// Record down the selected element in the dialog.
		this._.selectedElement = element;

		return retval;
	};

	var setupParams = function( page, data )
	{
		if ( data[page] )
			this.setValue( data[page][this.id] || '' );
	};

	var setupPopupParams = function( data )
	{
		return setupParams.call( this, 'target', data );
	};

	var setupAdvParams = function( data )
	{
		return setupParams.call( this, 'adv', data );
	};

	var commitParams = function( page, data )
	{
		if ( !data[page] )
			data[page] = {};

		data[page][this.id] = this.getValue() || '';
	};

	var commitPopupParams = function( data )
	{
		return commitParams.call( this, 'target', data );
	};

	var commitAdvParams = function( data )
	{
		return commitParams.call( this, 'adv', data );
	};

	function unescapeSingleQuote( str )
	{
		return str.replace( /\\'/g, '\'' );
	}

	function escapeSingleQuote( str )
	{
		return str.replace( /'/g, '\\$&' );
	}

	var emailProtection = editor.config.emailProtection || '';

	// Compile the protection function pattern.
	if( emailProtection && emailProtection != 'encode' )
	{
		var compiledProtectionFunction = {};

		emailProtection.replace( /^([^(]+)\(([^)]+)\)$/, function( match, funcName, params )
		{
			compiledProtectionFunction.name = funcName;
			compiledProtectionFunction.params = [];
			params.replace( /[^,\s]+/g, function( param )
			{
				compiledProtectionFunction.params.push( param );
			} );
		} );
	}

	function protectEmailLinkAsFunction( email )
	{
		var retval,
			name = compiledProtectionFunction.name,
			params = compiledProtectionFunction.params,
			paramName,
			paramValue;

		retval = [ name, '(' ];
		for ( var i = 0; i < params.length; i++ )
		{
			paramName = params[ i ].toLowerCase();
			paramValue = email[ paramName ];

			i > 0 && retval.push( ',' );
			retval.push( '\'',
						 paramValue ?
						 escapeSingleQuote( encodeURIComponent( email[ paramName ] ) )
						 : '',
						 '\'');
		}
		retval.push( ')' );
		return retval.join( '' );
	}

	function protectEmailAddressAsEncodedString( address )
	{
		var charCode,
			length = address.length,
			encodedChars = [];
		for ( var i = 0; i < length; i++ )
		{
			charCode = address.charCodeAt( i );
			encodedChars.push( charCode );
		}
		return 'String.fromCharCode(' + encodedChars.join( ',' ) + ')';
	}

	return {
		title : editor.lang.link.title,
		minWidth : 550,
		minHeight : 430,
		contents : [
			{
				id : 'info',
				label : editor.lang.link.info,
				title : editor.lang.link.info,
				elements :
				[
					{
						id : 'linkType',
						type : 'select',
						label : editor.lang.link.type,
						'default' : 'pliki', // wstawka Kameleona
						items :
						[
							[ editor.lang.common.url, 'url' ],
							[ editor.lang.link.toAnchor, 'anchor' ],
							[ editor.lang.link.toInside, 'inside' ],
							[ editor.lang.link.toPliki, 'pliki' ],
							[ editor.lang.link.toObrazki, 'obrazki' ],
							[ editor.lang.link.toEmail, 'email' ]
						],
						onChange : linkTypeChanged,
						setup : function( data )
						{
							if ( data.type )
								this.setValue( data.type );
						},
						commit : function( data )
						{
							data.type = this.getValue();
						}
					},
					{
						type : 'vbox',
						id : 'urlOptions',
						children :
						[
							{
								type : 'hbox',
								widths : [ '25%', '75%' ],
								children :
								[
									{
										id : 'protocol',
										type : 'select',
										label : editor.lang.common.protocol,
										'default' : 'http://',
										style : 'width : 100%;',
										items :
										[
											[ 'http://' ],
											[ 'https://' ],
											[ 'ftp://' ],
											[ 'news://' ],
											[ '<other>', '' ]
										],
										setup : function( data )
										{
											if ( data.url )
												this.setValue( data.url.protocol || '' );
										},
										commit : function( data )
										{
											if ( !data.url )
												data.url = {};

											data.url.protocol = this.getValue();
										}
									},
									{
										type : 'text',
										id : 'url',
										label : editor.lang.common.url,
										onLoad : function ()
										{
											this.allowOnChange = true;
										},
										onKeyUp : function()
										{
											this.allowOnChange = false;
											var	protocolCmb = this.getDialog().getContentElement( 'info', 'protocol' ),
												url = this.getValue(),
												urlOnChangeProtocol = /^(http|https|ftp|news):\/\/(?=.)/gi,
												urlOnChangeTestOther = /^((javascript:)|[#\/\.])/gi;

											var protocol = urlOnChangeProtocol.exec( url );
											if ( protocol )
											{
												this.setValue( url.substr( protocol[ 0 ].length ) );
												protocolCmb.setValue( protocol[ 0 ].toLowerCase() );
											}
											else if ( urlOnChangeTestOther.test( url ) )
												protocolCmb.setValue( '' );

											this.allowOnChange = true;
										},
										onChange : function()
										{
											if ( this.allowOnChange )		// Dont't call on dialog load.
												this.onKeyUp();
										},
										validate : function()
										{
											var dialog = this.getDialog();

											if ( dialog.getContentElement( 'info', 'linkType' ) &&
													dialog.getValueOf( 'info', 'linkType' ) != 'url' )
												return true;

											if ( this.getDialog().fakeObj )	// Edit Anchor.
												return true;

											var func = CKEDITOR.dialog.validate.notEmpty( editor.lang.link.noUrl );
											return func.apply( this );
										},
										setup : function( data )
										{
											this.allowOnChange = false;
											if ( data.url )
												this.setValue( data.url.url );
											this.allowOnChange = true;

											var linkType = this.getDialog().getContentElement( 'info', 'linkType' );
											if ( linkType && linkType.getValue() == 'url' )
												this.select();

										},
										commit : function( data )
										{
											if ( !data.url )
												data.url = {};

											data.url.url = this.getValue();
											this.allowOnChange = false;
										}
									}
								],
								setup : function( data )
								{
									if ( !this.getDialog().getContentElement( 'info', 'linkType' ) )
										this.getElement().show();
								}
							},
							{
								type : 'button',
								id : 'browse',
								hidden : 'true',
								filebrowser : 'info:url',
								label : editor.lang.common.browseServer
							}
						]
					},
					{
						type : 'vbox',
						id : 'anchorOptions',
						width : 260,
						align : 'center',
						padding : 0,
						children :
						[
							{
								type : 'html',
								id : 'selectAnchorText',
								html : CKEDITOR.tools.htmlEncode( editor.lang.link.selectAnchor ),
								setup : function( data )
								{
									if ( data.anchors.length > 0 )
										this.getElement().show();
									else
										this.getElement().hide();
								}
							},
							{
								type : 'html',
								id : 'noAnchors',
								style : 'text-align: center;',
								html : '<div>' + CKEDITOR.tools.htmlEncode( editor.lang.link.noAnchors ) + '</div>',
								setup : function( data )
								{
									if ( data.anchors.length < 1 )
										this.getElement().show();
									else
										this.getElement().hide();
								}
							},
							{
								type : 'hbox',
								id : 'selectAnchor',
								children :
								[
									{
										type : 'select',
										id : 'anchorName',
										'default' : '',
										label : editor.lang.link.anchorName,
										style : 'width: 100%;',
										items :
										[
											[ '' ]
										],
										setup : function( data )
										{
											this.clear();
											this.add( '' );
											for ( var i = 0 ; i < data.anchors.length ; i++ )
											{
												if ( data.anchors[i].name )
													this.add( data.anchors[i].name );
											}

											if ( data.anchor )
												this.setValue( data.anchor.name );

											var linkType = this.getDialog().getContentElement( 'info', 'linkType' );
											if ( linkType && linkType.getValue() == 'email' )
												this.focus();
										},
										commit : function( data )
										{
											if ( !data.anchor )
												data.anchor = {};

											data.anchor.name = this.getValue();
										}
									},
									{
										type : 'select',
										id : 'anchorId',
										'default' : '',
										label : editor.lang.link.anchorId,
										style : 'width: 100%;',
										items :
										[
											[ '' ]
										],
										setup : function( data )
										{
											this.clear();
											this.add( '' );
											for ( var i = 0 ; i < data.anchors.length ; i++ )
											{
												if ( data.anchors[i].id )
													this.add( data.anchors[i].id );
											}

											if ( data.anchor )
												this.setValue( data.anchor.id );
										},
										commit : function( data )
										{
											if ( !data.anchor )
												data.anchor = {};

											data.anchor.id = this.getValue();
										}
									}
								],
								setup : function( data )
								{
									if ( data.anchors.length > 0 )
										this.getElement().show();
									else
										this.getElement().hide();
								}
							}
						],
						setup : function( data )
						{
							if ( !this.getDialog().getContentElement( 'info', 'linkType' ) )
								this.getElement().hide();
						}
					},
					// wstawka Kameleona
					{
            type : 'vbox',
            id : 'insideOptions',
            padding : 1,
            children :
            [
              {
                type : 'html',
                id : 'path_src_val',
                html : '<div style="height: 360px;"><input type="hidden" id="km_node" value="" /><input type="hidden" id="km_node_title" value="" /><iframe frameborder="0" style="padding: 0; border: 1px solid #a0a0a0; width: 100%; height: 320px;" class="cke_dialog_ui_iframe" allowtransparency="true" id="km_treebrowser_frame" src="'+CKEDITOR.getUrl( editor.plugins.link.path+'../../../tree.php?multi=1&node=')+'"></iframe></div>'
              },
              {
                type : 'text',
								id : 'insideVariables',
								label : editor.lang.link.variables,
								setup : function( data )
								{
									if ( data.inside ) this.setValue( data.inside.variables );
								},
								commit : function( data )
								{
									if ( !data.inside ) data.inside = {};
									data.inside.variables = this.getValue();
									data.inside.node = document.getElementById('km_node').value;
									data.inside.title = document.getElementById('km_node_title').value;
								}
							}
            ]
          },
          {
  					type : 'vbox',
  					padding : 0,
  					id : 'plikiOptions',
  					children :
  					[
							{
                type : 'html',
                id : 'path_src_val',
                html : '<div style="height: 360px;"><input type="hidden" value="" /><input type="hidden" value="" /><iframe frameborder="0" style="padding: 0; border: 0px solid #a0a0a0; width: 100%; height: 380px;" class="cke_dialog_ui_iframe" allowtransparency="true" id="km_ufilesbrowser_frame" src="'+CKEDITOR.getUrl( editor.plugins.link.path+'../../../ufiles.php?galeria=9')+'"></iframe></div>'
              },
              {
								id : 'plikiUrl',
								type : 'text',
								label : editor.lang.image.url ,
								style : 'padding-top: 11px',
                setup : function( data )
								{
									if ( data.pliki ) this.setValue( data.pliki.url );
								},
								commit : function( data )
								{
									if ( !data.pliki ) data.pliki = {};
									data.pliki.url = this.getValue();
									v = data.pliki.url.split("/");
									data.pliki.title="";
                  if (v.length>0) data.pliki.title = v[v.length-1];
								}
							}
  					]
  				},
  				{
  					type : 'vbox',
  					padding : 0,
  					id : 'obrazkiOptions',
  					children :
  					[
							{
                type : 'html',
                id : 'path_src_val',
                html : '<div style="height: 360px;"><input type="hidden" value="" /><input type="hidden" value="" /><iframe frameborder="0" style="padding: 0; border: 0px solid #a0a0a0; width: 100%; height: 380px;" class="cke_dialog_ui_iframe" allowtransparency="true" id="km_uimagesbrowser_frame" src="'+CKEDITOR.getUrl( editor.plugins.link.path+'../../../ufiles.php?galeria=11')+'"></iframe></div>'
              },
              {
								id : 'obrazkiUrl',
								type : 'text',
								label : editor.lang.image.url ,
								style : 'padding-top: 11px',
                setup : function( data )
								{
									if ( data.obrazki ) this.setValue( data.obrazki.url );
								},
								commit : function( data )
								{
									if ( !data.obrazki ) data.obrazki = {};
									data.obrazki.url = this.getValue();
									v = data.obrazki.url.split("/");
									data.obrazki.title="";
                  if (v.length>0) data.obrazki.title = v[v.length-1];
								}
							}
  					]
  				},
          // koniec wstawka Kameleona
					{
						type :  'vbox',
						id : 'emailOptions',
						padding : 1,
						children :
						[
							{
								type : 'text',
								id : 'emailAddress',
								label : editor.lang.link.emailAddress,
								validate : function()
								{
									var dialog = this.getDialog();

									if ( !dialog.getContentElement( 'info', 'linkType' ) ||
											dialog.getValueOf( 'info', 'linkType' ) != 'email' )
										return true;

									var func = CKEDITOR.dialog.validate.notEmpty( editor.lang.link.noEmail );
									return func.apply( this );
								},
								setup : function( data )
								{
									if ( data.email )
										this.setValue( data.email.address );

									var linkType = this.getDialog().getContentElement( 'info', 'linkType' );
									if ( linkType && linkType.getValue() == 'email' )
										this.select();
								},
								commit : function( data )
								{
									if ( !data.email )
										data.email = {};

									data.email.address = this.getValue();
								}
							},
							{
								type : 'text',
								id : 'emailSubject',
								label : editor.lang.link.emailSubject,
								setup : function( data )
								{
									if ( data.email )
										this.setValue( data.email.subject );
								},
								commit : function( data )
								{
									if ( !data.email )
										data.email = {};

									data.email.subject = this.getValue();
								}
							},
							{
								type : 'textarea',
								id : 'emailBody',
								label : editor.lang.link.emailBody,
								rows : 3,
								'default' : '',
								setup : function( data )
								{
									if ( data.email )
										this.setValue( data.email.body );
								},
								commit : function( data )
								{
									if ( !data.email )
										data.email = {};

									data.email.body = this.getValue();
								}
							}
						],
						setup : function( data )
						{
							if ( !this.getDialog().getContentElement( 'info', 'linkType' ) )
								this.getElement().hide();
						}
					}
				]
			},
			{
				id : 'target',
				label : editor.lang.link.target,
				title : editor.lang.link.target,
				elements :
				[
					{
						type : 'hbox',
						widths : [ '50%', '50%' ],
						children :
						[
							{
								type : 'select',
								id : 'linkTargetType',
								label : editor.lang.link.target,
								'default' : 'notSet',
								style : 'width : 100%;',
								'items' :
								[
									[ editor.lang.link.targetNotSet, 'notSet' ],
									[ editor.lang.link.targetFrame, 'frame' ],
									[ editor.lang.link.targetPopup, 'popup' ],
									[ editor.lang.link.targetNew, '_blank' ],
									[ editor.lang.link.targetTop, '_top' ],
									[ editor.lang.link.targetSelf, '_self' ],
									[ editor.lang.link.targetParent, '_parent' ]
								],
								onChange : targetChanged,
								setup : function( data )
								{
									if ( data.target )
										this.setValue( data.target.type );
								},
								commit : function( data )
								{
									if ( !data.target )
										data.target = {};

									data.target.type = this.getValue();
								}
							},
							{
								type : 'text',
								id : 'linkTargetName',
								label : editor.lang.link.targetFrameName,
								'default' : '',
								setup : function( data )
								{
									if ( data.target )
										this.setValue( data.target.name );
								},
								commit : function( data )
								{
									if ( !data.target )
										data.target = {};

									data.target.name = this.getValue();
								}
							}
						]
					},
					{
						type : 'vbox',
						width : 260,
						align : 'center',
						padding : 2,
						id : 'popupFeatures',
						children :
						[
							{
								type : 'html',
								html : CKEDITOR.tools.htmlEncode( editor.lang.link.popupFeatures )
							},
							{
								type : 'hbox',
								children :
								[
									{
										type : 'checkbox',
										id : 'resizable',
										label : editor.lang.link.popupResizable,
										setup : setupPopupParams,
										commit : commitPopupParams
									},
									{
										type : 'checkbox',
										id : 'status',
										label : editor.lang.link.popupStatusBar,
										setup : setupPopupParams,
										commit : commitPopupParams

									}
								]
							},
							{
								type : 'hbox',
								children :
								[
									{
										type : 'checkbox',
										id : 'location',
										label : editor.lang.link.popupLocationBar,
										setup : setupPopupParams,
										commit : commitPopupParams

									},
									{
										type : 'checkbox',
										id : 'toolbar',
										label : editor.lang.link.popupToolbar,
										setup : setupPopupParams,
										commit : commitPopupParams

									}
								]
							},
							{
								type : 'hbox',
								children :
								[
									{
										type : 'checkbox',
										id : 'menubar',
										label : editor.lang.link.popupMenuBar,
										setup : setupPopupParams,
										commit : commitPopupParams

									},
									{
										type : 'checkbox',
										id : 'fullscreen',
										label : editor.lang.link.popupFullScreen,
										setup : setupPopupParams,
										commit : commitPopupParams

									}
								]
							},
							{
								type : 'hbox',
								children :
								[
									{
										type : 'checkbox',
										id : 'scrollbars',
										label : editor.lang.link.popupScrollBars,
										setup : setupPopupParams,
										commit : commitPopupParams

									},
									{
										type : 'checkbox',
										id : 'dependent',
										label : editor.lang.link.popupDependent,
										setup : setupPopupParams,
										commit : commitPopupParams

									}
								]
							},
							{
								type : 'hbox',
								children :
								[
									{
										type :  'text',
										widths : [ '30%', '70%' ],
										labelLayout : 'horizontal',
										label : editor.lang.link.popupWidth,
										id : 'width',
										setup : setupPopupParams,
										commit : commitPopupParams

									},
									{
										type :  'text',
										labelLayout : 'horizontal',
										widths : [ '55%', '45%' ],
										label : editor.lang.link.popupLeft,
										id : 'left',
										setup : setupPopupParams,
										commit : commitPopupParams

									}
								]
							},
							{
								type : 'hbox',
								children :
								[
									{
										type :  'text',
										labelLayout : 'horizontal',
										widths : [ '30%', '70%' ],
										label : editor.lang.link.popupHeight,
										id : 'height',
										setup : setupPopupParams,
										commit : commitPopupParams

									},
									{
										type :  'text',
										labelLayout : 'horizontal',
										label : editor.lang.link.popupTop,
										widths : [ '55%', '45%' ],
										id : 'top',
										setup : setupPopupParams,
										commit : commitPopupParams

									}
								]
							}
						]
					}
				]
			},
			{
				id : 'upload',
				label : editor.lang.link.upload,
				title : editor.lang.link.upload,
				hidden : true,
				filebrowser : 'uploadButton',
				elements :
				[
					{
						type : 'file',
						id : 'upload',
						label : editor.lang.common.upload,
						style: 'height:40px',
						size : 29
					},
					{
						type : 'fileButton',
						id : 'uploadButton',
						label : editor.lang.common.uploadSubmit,
						filebrowser : 'info:url',
						'for' : [ 'upload', 'upload' ]
					}
				]
			},
			{
				id : 'advanced',
				label : editor.lang.link.advanced,
				title : editor.lang.link.advanced,
				elements :
				[
					{
						type : 'vbox',
						padding : 1,
						children :
						[
							{
								type : 'hbox',
								widths : [ '45%', '35%', '20%' ],
								children :
								[
									{
										type : 'text',
										id : 'advId',
										label : editor.lang.link.id,
										setup : setupAdvParams,
										commit : commitAdvParams
									},
									{
										type : 'select',
										id : 'advLangDir',
										label : editor.lang.link.langDir,
										'default' : '',
										style : 'width:110px',
										items :
										[
											[ editor.lang.link.langDirNotSet, '' ],
											[ editor.lang.link.langDirLTR, 'ltr' ],
											[ editor.lang.link.langDirRTL, 'rtl' ]
										],
										setup : setupAdvParams,
										commit : commitAdvParams
									},
									{
										type : 'text',
										id : 'advAccessKey',
										width : '80px',
										label : editor.lang.link.acccessKey,
										maxLength : 1,
										setup : setupAdvParams,
										commit : commitAdvParams

									}
								]
							},
							{
								type : 'hbox',
								widths : [ '45%', '35%', '20%' ],
								children :
								[
									{
										type : 'text',
										label : editor.lang.link.name,
										id : 'advName',
										setup : setupAdvParams,
										commit : commitAdvParams

									},
									{
										type : 'text',
										label : editor.lang.link.langCode,
										id : 'advLangCode',
										width : '110px',
										'default' : '',
										setup : setupAdvParams,
										commit : commitAdvParams

									},
									{
										type : 'text',
										label : editor.lang.link.tabIndex,
										id : 'advTabIndex',
										width : '80px',
										maxLength : 5,
										setup : setupAdvParams,
										commit : commitAdvParams

									}
								]
							}
						]
					},
					{
						type : 'vbox',
						padding : 1,
						children :
						[
							{
								type : 'hbox',
								widths : [ '45%', '55%' ],
								children :
								[
									{
										type : 'text',
										label : editor.lang.link.advisoryTitle,
										'default' : '',
										id : 'advTitle',
										setup : setupAdvParams,
										commit : commitAdvParams

									},
									{
										type : 'text',
										label : editor.lang.link.advisoryContentType,
										'default' : '',
										id : 'advContentType',
										setup : setupAdvParams,
										commit : commitAdvParams

									}
								]
							},
							{
								type : 'hbox',
								widths : [ '45%', '55%' ],
								children :
								[
									{
										type : 'text',
										label : editor.lang.link.cssClasses,
										'default' : '',
										id : 'advCSSClasses',
										setup : setupAdvParams,
										commit : commitAdvParams

									},
									{
										type : 'text',
										label : editor.lang.link.charset,
										'default' : '',
										id : 'advCharset',
										setup : setupAdvParams,
										commit : commitAdvParams

									}
								]
							},
							{
								type : 'hbox',
								children :
								[
									{
										type : 'text',
										label : editor.lang.link.styles,
										'default' : '',
										id : 'advStyles',
										setup : setupAdvParams,
										commit : commitAdvParams

									}
								]
							}
						]
					}
				]
			}
		],
		onShow : function()
		{
			this.fakeObj = false;

			var editor = this.getParentEditor(),
				selection = editor.getSelection(),
				ranges = selection.getRanges(),
				element = null,
				me = this;
			// Fill in all the relevant fields if there's already one link selected.
			if ( ranges.length == 1 )
			{

				var rangeRoot = ranges[0].getCommonAncestor( true );
				element = rangeRoot.getAscendant( 'a', true );
				if ( element && element.getAttribute( 'href' ) )
				{
					selection.selectElement( element );
				}
				else if ( ( element = rangeRoot.getAscendant( 'img', true ) ) &&
						 element.getAttribute( '_cke_real_element_type' ) &&
						 element.getAttribute( '_cke_real_element_type' ) == 'anchor' )
				{
					this.fakeObj = element;
					element = editor.restoreRealElement( this.fakeObj );
					selection.selectElement( this.fakeObj );
				}
				else
					element = null;
			}

			this.setupContent( parseLink.apply( this, [ editor, element ] ) );
			
		},
		onOk : function()
		{
			var attributes = { href : 'javascript:void(0)/*' + CKEDITOR.tools.getNextNumber() + '*/' },
				removeAttributes = [],
				data = { href : attributes.href },
				me = this,
				editor = this.getParentEditor();

			this.commitContent( data );

			// Compose the URL.
			switch ( data.type || 'url' )
			{
				case 'url':
					var protocol = ( data.url && data.url.protocol != undefined ) ? data.url.protocol : 'http://',
						url = ( data.url && data.url.url ) || '';
					attributes._cke_saved_href = ( url.indexOf( '/' ) === 0 ) ? url : protocol + url;
					break;
				case 'anchor':
					var name = ( data.anchor && data.anchor.name ),
						id = ( data.anchor && data.anchor.id );
					attributes._cke_saved_href = '#' + ( name || id || '' );
					break;
					
				// wstawka Kameleona
				case 'inside':
				  links = data.inside.node; 
          if (data.inside.variables.length > 0) links+=','+data.inside.variables;
				  attributes._cke_saved_href = 'kameleon:inside_link(' + links + ')';
				  break;
				case 'pliki':
				  links = data.pliki.url; 
				  attributes._cke_saved_href = links;
				  break;
				// koniec wstawka Kameleona  
				
				case 'email':

					var linkHref,
						email = data.email,
						address = email.address;

					switch( emailProtection )
					{
						case '' :
						case 'encode' :
						{
							var subject = encodeURIComponent( email.subject || '' ),
								body = encodeURIComponent( email.body || '' );

							// Build the e-mail parameters first.
							var argList = [];
							subject && argList.push( 'subject=' + subject );
							body && argList.push( 'body=' + body );
							argList = argList.length ? '?' + argList.join( '&' ) : '';

							if ( emailProtection == 'encode' )
							{
								linkHref = [ 'javascript:void(location.href=\'mailto:\'+',
											 protectEmailAddressAsEncodedString( address ) ];
								// parameters are optional.
								argList && linkHref.push( '+\'', escapeSingleQuote( argList ), '\'' );

								linkHref.push( ')' );
							}
							else
								linkHref = [ 'mailto:', address, argList ];

							break;
						}
						default :
						{
							// Separating name and domain.
							var nameAndDomain = address.split( '@', 2 );
							email.name = nameAndDomain[ 0 ];
							email.domain = nameAndDomain[ 1 ];

							linkHref = [ 'javascript:', protectEmailLinkAsFunction( email ) ];
						}
					}

					attributes._cke_saved_href = linkHref.join( '' );
					break;
			}

			// Popups and target.
			if ( data.target )
			{
				if ( data.target.type == 'popup' )
				{
					var onclickList = [ 'window.open(this.href, \'',
							data.target.name || '', '\', \'' ];
					var featureList = [ 'resizable', 'status', 'location', 'toolbar', 'menubar', 'fullscreen',
							'scrollbars', 'dependent' ];
					var featureLength = featureList.length;
					var addFeature = function( featureName )
					{
						if ( data.target[ featureName ] )
							featureList.push( featureName + '=' + data.target[ featureName ] );
					};

					for ( var i = 0 ; i < featureLength ; i++ )
						featureList[i] = featureList[i] + ( data.target[ featureList[i] ] ? '=yes' : '=no' ) ;
					addFeature( 'width' );
					addFeature( 'left' );
					addFeature( 'height' );
					addFeature( 'top' );

					onclickList.push( featureList.join( ',' ), '\'); return false;' );
					attributes[ '_cke_pa_onclick' ] = onclickList.join( '' );
				}
				else
				{
					if ( data.target.type != 'notSet' && data.target.name )
						attributes.target = data.target.name;
					else
						removeAttributes.push( 'target' );

					removeAttributes.push( '_cke_pa_onclick', 'onclick' );
				}
			}

			// Advanced attributes.
			if ( data.adv )
			{
				var advAttr = function( inputName, attrName )
				{
					var value = data.adv[ inputName ];
					if ( value )
						attributes[attrName] = value;
					else
						removeAttributes.push( attrName );
				};

				if ( this._.selectedElement )
					advAttr( 'advId', 'id' );
				advAttr( 'advLangDir', 'dir' );
				advAttr( 'advAccessKey', 'accessKey' );
				advAttr( 'advName', 'name' );
				advAttr( 'advLangCode', 'lang' );
				advAttr( 'advTabIndex', 'tabindex' );
				advAttr( 'advTitle', 'title' );
				advAttr( 'advContentType', 'type' );
				advAttr( 'advCSSClasses', 'class' );
				advAttr( 'advCharset', 'charset' );
				advAttr( 'advStyles', 'style' );
			}

			if ( !this._.selectedElement )
			{
       
				// Create element if current selection is collapsed.
				var selection = editor.getSelection(),
					ranges = selection.getRanges();
				if ( ranges.length == 1 && ranges[0].collapsed )
				{
				
				  // wstawka Kameleona
				  var linktekst=attributes._cke_saved_href;
          if (data.inside.title) linktekst=data.inside.title;
          if (data.pliki.title) linktekst=data.pliki.title;
					var text = new CKEDITOR.dom.text( linktekst, editor.document );
					// koniec wstawka Kameleona
					
          ranges[0].insertNode( text );
					ranges[0].selectNodeContents( text );
					selection.selectRanges( ranges );
				}

				// Apply style.
				var style = new CKEDITOR.style( { element : 'a', attributes : attributes } );
				style.type = CKEDITOR.STYLE_INLINE;		// need to override... dunno why.
				style.apply( editor.document );

				// Id. Apply only to the first link.
				if ( data.adv && data.adv.advId )
				{
					var links = this.getParentEditor().document.$.getElementsByTagName( 'a' );
					for ( i = 0 ; i < links.length ; i++ )
					{
						if ( links[i].href == attributes.href )
						{
							links[i].id = data.adv.advId;
							break;
						}
					}
				}
			}
			else
			{
			 
				// We're only editing an existing link, so just overwrite the attributes.
				var element = this._.selectedElement;
				// IE BUG: Setting the name attribute to an existing link doesn't work.
				// Must re-create the link from weired syntax to workaround.
				if ( CKEDITOR.env.ie && attributes.name != element.getAttribute( 'name' ) )
				{
					var newElement = new CKEDITOR.dom.element( '<a name="' + CKEDITOR.tools.htmlEncode( attributes.name ) + '">',
							editor.document );

					selection = editor.getSelection();

					element.moveChildren( newElement );
					element.copyAttributes( newElement, { name : 1 } );
					newElement.replace( element );
					element = newElement;

					selection.selectElement( element );
				}

				element.setAttributes( attributes );
				element.removeAttributes( removeAttributes );

				// Make the element display as an anchor if a name has been set.
				if ( element.getAttribute( 'name' ) )
					element.addClass( 'cke_anchor' );
				else
					element.removeClass( 'cke_anchor' );

				if ( this.fakeObj )
					editor.createFakeElement( element, 'cke_anchor', 'anchor' ).replace( this.fakeObj );

				delete this._.selectedElement;
			}
		},
		onLoad : function()
		{
			if ( !editor.config.linkShowAdvancedTab )
				this.hidePage( 'advanced' );		//Hide Advanded tab.

			if ( !editor.config.linkShowTargetTab )
				this.hidePage( 'target' );		//Hide Target tab.
		}
	};
});

/**
 * The e-mail address anti-spam protection option.
 * @name CKEDITOR.config.emailProtection
 * @type {String}
 * Two forms of protection could be choosed from :
 * 1. The whole address parts ( name, domain with any other query string ) are assembled into a
 *   function call pattern which invoke you own provided function, with the specified arguments.
 * 2. Only the e-mail address is obfuscated into unicode code point sequences, replacement are
 *   done by a String.fromCharCode() call.
 * Note: Both approaches require JavaScript to be enabled.
 * @default ''
 * @example
 *  config.emailProtection = '';
 *  // href="mailto:tester@ckeditor.com?subject=subject&body=body"
 *  config.emailProtection = 'encode';
 *  // href="<a href=\"javascript:void(location.href=\'mailto:\'+String.fromCharCode(116,101,115,116,101,114,64,99,107,101,100,105,116,111,114,46,99,111,109)+\'?subject=subject&body=body\')\">e-mail</a>"
 *  config.emailProtection = 'mt(NAME,DOMAIN,SUBJECT,BODY)';
 *  // href="javascript:mt('tester','ckeditor.com','subject','body')"
 */