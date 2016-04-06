$(function()
{
	function updateProjectName()
	{
		var text = $('select#project option:selected').text();
		text = text.replace(/ \([0-9:\- ]+\)$/, '');
		text = text.replace('^\s+', '');
		text = text.replace('\s+$', '');
		$('input#name').val(text);

		var projectId = $('#project').val();
		$('#standard').children('option').remove();
		$('#faux-standard option').each(function()
		{
			var doShow = $(this).attr('data-project-id') == projectId;
			if (doShow)
			{
				var option = $('<option/>');
				option.val($(this).val());
				option.text($(this).text());
				$('#standard').append(option);
			}
		});
		$('#standard option').prop('selected', false);
		$('#standard option:visible:first').prop('selected', true);
	}
	$('select#project').change(updateProjectName);
	updateProjectName();
});
