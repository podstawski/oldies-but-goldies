<?
	$options['src']=array($kameleon->label('Identificator'),'width:300px');
	$options['width']=array($kameleon->label('Width'),'width:100px');
	$options['height']=array($kameleon->label('Height'),'width:100px');
	
	$options['showTitle']=array($kameleon->label('Show calendar title'),'','true|false');
	$options['showDate']=array($kameleon->label('Show date'),'','true|false');
	$options['showPrint']=array($kameleon->label('Show print button'),'','true|false');
	
	$options['showTabs']=array($kameleon->label('Show tabs'),'','true|false');
	$options['showCalendars']=array($kameleon->label('Show calendars'),'','true|false');
	$options['showTz']=array($kameleon->label('Show timezone'),'','true|false');
	$options['mode']=array($kameleon->label('Mode'),'','MONTH|WEEK|AGENDA','WEEK');
	$options['bgcolor']=array($kameleon->label('Background color'),'width: 80px','','#FFFFFF','img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'in__web20_bgcolor\')');
	$options['color']=array($kameleon->label('Calendar color'),'width: 80px','','','img/i_colors_n.gif|img/i_colors_a.gif|otworzPalete(\'in__web20_color\')');
	$options['tz']=array($kameleon->label('Timezone'),'','','Europe/Warsaw');

	$options['hl']=array($kameleon->label('Language'),'','pl|en|es|de|fr|ru');
	
	$options['wkst']=array($kameleon->label('First day of the week'),'','1|2|3|4|5|6|7','2');
	