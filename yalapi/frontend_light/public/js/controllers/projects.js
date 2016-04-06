/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
$(function()
{
	//top navigation
	$('#content .op select').change(function(e)
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

	$('#projects-edit form, #projects-create form').submit(function()
	{
		var leaders = [];
		$('#edit-leaders input[type=checkbox]').each(function()
		{
			var id = $(this).attr('name').match(/(\d+)/)[0];
			if (!$(this).prop('checked'))
			{
				return;
			}
			leaders.push(id);
		});
		$('input[name=leaders]').val($.JSON.encode(leaders));
		return true;
	});
});

