/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	config.skin = 'office2003';
	config.extraPlugins = 'maska,pliki';
	config.scayt_autoStartup = false;
	config.autoParagraph = false;
	config.ignoreEmptyParagraph = false;
  config.toolbar =   [
      ['Save','Source','-','Cut','Copy','Paste','PasteText','PasteFromWord'],
      ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
      ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
      ['Link', 'pliki', 'Unlink', 'Anchor','maska'],
      '/',
      ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
      ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
      ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],   
      ['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak'],
      '/',
      ['Styles','Format','Font','FontSize'],
      ['TextColor','BGColor'],
      ['Maximize', 'ShowBlocks','-']
  ];
  
	// config.uiColor = '#AADC6E';
};
