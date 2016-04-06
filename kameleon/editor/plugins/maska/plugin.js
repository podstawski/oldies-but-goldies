CKEDITOR.plugins.add('maska',{
  requires : [ 'fakeobjects' ],
  init:function(editor){
      editor.addCommand( 'maska', new CKEDITOR.dialogCommand( 'maska' ) );
      editor.ui.addButton('maska',{ label:editor.lang.maska.toolbar, command:'maska', icon:this.path+'images/maska.gif' })
      CKEDITOR.dialog.add( 'maska', this.path + 'dialogs/maska.js' );
      
      editor.addCss(
			'img.cke_maska' +
			'{' +
				'background-image: url(' + CKEDITOR.getUrl( this.path + 'images/icon.gif' ) + ');' +
				'background-position: center center;' +
				'background-repeat: no-repeat;' +
				'width: 22px;' +
				'height: 22px;' +
			'}\n'
			);
		    
		  
		  
		  if ( editor.addMenuItems )
  		{
  			editor.addMenuItems(
				{
					maska :
					{
						label : editor.lang.maska.properties,
						command : 'maska',
						group : 'div',
						order : 1
					}
				});
  		}
		  
		  if ( editor.contextMenu )
			{
				editor.contextMenu.addListener( function( element, selection )
				{
					if ( element && element.is( 'img' ) && element.getAttribute( '_cke_real_element_type' ) == 'maska' )
						return { maska : CKEDITOR.TRISTATE_OFF };
				});
			}
  },
  afterInit : function( editor )
	{
		var dataProcessor = editor.dataProcessor,
			dataFilter = dataProcessor && dataProcessor.dataFilter;

		if ( dataFilter )
		{
			dataFilter.addRules(
				{
					elements :
					{
						maska : function( element )
						{
							var attributes = element.attributes;
							if ( attributes.name)
								return editor.createFakeParserElement( element, 'cke_maska', 'maska' );
						},
						img : function( element )
						{
              var attributes = element.attributes;
							if ( attributes.include==1)
								return editor.createFakeParserElement( element, 'cke_maska', 'maska' );
            }
					}
				});
		}
	}
});
