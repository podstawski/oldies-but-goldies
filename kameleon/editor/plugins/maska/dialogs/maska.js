

CKEDITOR.dialog.add( 'maska', function( editor )
{
	// Function called in onShow to load selected element.
	var loadElements = function( editor, selection, element )
	{
		this.editMode = true;
		this.editObj = element;

		var attributeValue = this.editObj.getAttribute( 'name' );
		if ( attributeValue )
			this.setValueOf( 'info','txtName', attributeValue );
		else
			this.setValueOf( 'info','txtName', "" );
	};
	  
  var schowekjs = new Array;
  var schowek = editor.config.schowek['mask'];
  if (typeof(schowek)=='undefined')
  {
    schowek = new Array();
    schowek[0]=new Array(2);
    schowek[0]['t'] = "-- brak --";
    schowek[0]['k'] = "";
  }
  for (i=0;i<schowek.length;i++)
  {
    schowekjs[i]=new Array(2);
    schowekjs[i][0] = schowek[i]['t'];
    schowekjs[i][1] = schowek[i]['k'];
  }

	
	return {
		title : editor.lang.maska.title,
		minWidth : 300,
		minHeight : 60,
		onOk : function()
		{
			// Always create a new anchor, because of IE BUG.
			var name = this.getValueOf( 'info', 'txtName' ),
				element = CKEDITOR.env.ie ?
				editor.document.createElement( '<maska>' ) :
				editor.document.createElement( 'maska' );

			// Move contents and attributes of old anchor to new anchor.
			if ( this.editMode )
			{
				this.editObj.copyAttributes( element, { name : 1 } );
				this.editObj.moveChildren( element );
			}

			// Set name.
			element.removeAttribute( '_cke_saved_name' );
			element.setAttribute( 'name', name );

			// Insert a new anchor.
			var fakeElement = editor.createFakeElement( element, 'cke_maska', 'maska' );
			if ( !this.editMode )
				editor.insertElement( fakeElement );
			else
			{
				fakeElement.replace( this.fakeObj );
				editor.getSelection().selectElement( fakeElement );
			}

			return true;
		},
		onShow : function()
		{
			this.editObj = false;
			this.fakeObj = false;
			this.editMode = false;

			var selection = editor.getSelection();
			var element = selection.getSelectedElement();
			if ( element && element.getAttribute( '_cke_real_element_type' ) && element.getAttribute( '_cke_real_element_type' ) == 'maska' )
			{
				this.fakeObj = element;
				element = editor.restoreRealElement( this.fakeObj );
				loadElements.apply( this, [ editor, selection, element ] );
				selection.selectElement( this.fakeObj );
			}
			this.getContentElement( 'info', 'txtName' ).focus();
		},
		contents : [
			{
				id : 'info',
				label : editor.lang.maska.title,
				accessKey : 'I',
				elements :
				[
					{
						type : 'text',
						id : 'txtName',
						label : editor.lang.maska.name,
						validate : function()
						{
							if ( !this.getValue() )
							{
								alert( editor.lang.maska.errorName );
								return false;
							}
							return true;
						}
					},
    			{
            type : 'select',
            id : 'selectschowka',
            label : editor.lang.paste,
            items : schowekjs,
            style : 'width : 100%;',
						onChange : function()
						{
						  var sel = this.getDialog().getContentElement( 'info', 'selectschowka' ).getValue();
						  var tel = this.getDialog().getContentElement( 'info', 'txtName' );
						  tel.setValue(sel);
						}
											
          }
				]
			}
		]
	};
} );