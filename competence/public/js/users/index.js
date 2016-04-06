$(function()
{
	$('input[name=name]').focus();

	//zastosuj zmiany filtrów natychmiastowo
	$('#content .nav-bar select').change(function()
	{
		$(this).parents('form').submit();
	});
	$('#content .nav-bar input').keypress(function(e)
	{
		if (e.which != 13)
		{
			return true;
		}
		$(this).parents('form').submit();
		return false;
	});

	//zastosuj zmiany ról natychmiastwowo
	$('#content table select').change(function()
	{
		$(this).parents('form').submit();
	});
	//poprzez ajax
	$('#content table form').submit(function()
	{
		var data = $(this).serialize();
		var url = $(this).attr('action');
		utils.showThrobber();
		$.post(url, data, function(response)
		{
			utils.hideThrobber();
		});
		return false;
	});

	$(document).ajaxError(function(e, jqxhr, settings, exception)
	{
		utils.hideThrobber();
		utils.alert(exception);
		return false;
	});
});
