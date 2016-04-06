/* Polish initialisation for the jQuery UI date picker plugin. */
/* Written by Jacek Wysocki (jacek.wysocki@gmail.com). */
jQuery(function($){
	$.datepicker.regional['pl'] = {
		closeText: 'Zamknij',
		prevText: 'Poprzedni',
		nextText: 'Następny',
		currentText: 'Dzi&#347;',
		monthNames: ['Stycze&#324;','Luty','Marzec','Kwiecie&#324;','Maj','Czerwiec','Lipiec','Sierpie&#324;','Wrzesie&#324;','Pa&#378;dziernik','Listopad','Grudzie&#324;'],
		monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze','Lip','Sie','Wrz','Pa&#378;','Lis','Gru'],
		dayNames: ['Niedziela','Poniedziałek','Wtorek','&#346;roda','Czwartek','Pi&#261;tek','Sobota'],
		dayNamesShort: ['Nie','Pn','Wt','&#346;r','Czw','Pt','So'],
		dayNamesMin: ['N','Pn','Wt','&#346;r','Cz','Pt','So'],
		weekHeader: 'Tydz',
		dateFormat: 'dd-mm-yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
    };
	$.datepicker.setDefaults($.datepicker.regional['pl']);
});