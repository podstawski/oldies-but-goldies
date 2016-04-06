var baseUrl = $('body').attr('data-base-url');
$.get(baseUrl + '/index/translations', function(translations) {
	$('#change-date-trigger').datepicker({
		dateFormat: translations['common_date_format2'],
		monthNames: translations['common_date_month_names'],
		monthNamesShort: translations['common_date_month_names_short'],
		dayNames: translations['common_date_day_names'],
		dayNamesShort: translations['common_date_day_names_short'],
		dayNamesMin: translations['common_date_day_names_min'],
	}).change(function(e) {
		e.preventDefault();
		var url = baseUrl + '/labels/ajax-change-date/user-label-id/' + $(this).attr('data-user-label-id');
		var data = {'date': $(this).val()};
		$.post(url, data, function(response) {
			if (response['message']) {
				alert(response['message']);
			}
		});
	});
});
