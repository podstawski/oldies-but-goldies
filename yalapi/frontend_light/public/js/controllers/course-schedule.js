/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
$(function()
{
	$('#content .op select').change(function()
	{
		$(this).parents('form').submit();
	});
	$('#content .op form').submit(function()
	{
		var url = $('#base-url').text() + '/' + $('#controller-name').text() + '/' + $('#action-name').text();
		var data = $(this).serializeArray();
		for (var i in data)
		{
			var key = data[i]['name'];
			var value = data[i]['value'];
			url = url + '/' + key + '/' + value;
		}
		url = url + '/';
		window.location.href = url;
		return false;
	});
});

