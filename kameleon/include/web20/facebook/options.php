<?
	$options['type']=array($kameleon->label('Widget type'),'','button|box');
	$options['color']=array($kameleon->label('Color scheme'),'','light|dark');
	$options['width']=array($kameleon->label('Width')."(px)",'width: 100px');
	$options['url']=array($kameleon->label('Link to site/profile'),'width: 400px');
	$options['style']=array($kameleon->label('Layout style')." (".$kameleon->label('button').")",'','standard|button_count|box_count');
	$options['verb']=array($kameleon->label('Verb to display')." (".$kameleon->label('button').")",'','like|recommend');
	$options['faces']=array($kameleon->label('Show faces'),'','true|false');
	$options['stream']=array($kameleon->label('Stream')." (".$kameleon->label('box').")",'','true|false');
	$options['header']=array($kameleon->label('Header')." (".$kameleon->label('box').")",'','true|false');