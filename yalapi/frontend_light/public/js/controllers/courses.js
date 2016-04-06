$(function()
{
	var unitDefinitions = 
	[
		{'key': 'id', 'type': 'hidden', 'title': 'ID'},
		{'key': 'name', 'type': 'input', 'class': 'required name', 'title': 'Nazwa'},
		{'key': 'hour-amount', 'type': 'input', 'class': 'required code', 'title': 'Ile modułów'},
		{'key': 'trainer', 'type': 'select', 'title': 'Trener'},
	];

	var unitEditor = new EntityEditor('units', unitDefinitions, $('#unit-editor'));

	var trainers = $.parseJSON($('input[name=\'trainers-source\']').val());
	for (var i in trainers)
	{
		var option = $('<option value="' + i + '">' + trainers[i] + '</option>');
		$('#units-trainer-edit').append(option);
	}

	$('body').data('unit-editor', unitEditor);
	$('#courses-form').submit(function(e)
	{
		$('input[name=\'unit-data\']').val($.JSON.encode(unitEditor.getEntities()));
		//alert($('input[name=\'unit-data\']').val());
		//return false;
		return true;
	});
});

