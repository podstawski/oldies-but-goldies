/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.plugins.add( 'pliki',
{
	init : function( editor )
	{
		// Add the link and unlink buttons.
		editor.addCommand( 'pliki', new CKEDITOR.dialogCommand( 'pliki' ) );
		editor.ui.addButton( 'pliki',
			{
				label : editor.lang.link.pliki,
				command : 'pliki',
				icon:this.path+'images/file.gif'
			} );
		
		CKEDITOR.dialog.add( 'pliki', this.path + 'dialogs/pliki.js' );
	},

	
	requires : [ 'fakeobjects' ]
} );
