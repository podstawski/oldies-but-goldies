$(function()
{
	var roomDefinitions = 
	[
		{'key': 'id', 'type': 'hidden', 'title': 'ID'},
		{'key': 'name', 'type': 'input', 'class': 'required name', 'title': 'Nazwa'},
		{'key': 'symbol', 'type': 'input', 'class': 'required code', 'title': 'Symbol'},
		{'key': 'description', 'show': false, 'type': 'textarea', 'title': 'Opis'},
		{'key': 'available_space', 'type': 'input', 'class': 'required integer non-negative-integer', 'title': 'Ilość miejsc'}
	];

	var resourceDefinitions = 
	[
		{'key': 'id', 'type': 'hidden', 'title': 'ID'},
		{'key': 'name', 'type': 'input', 'class': 'required name', 'title': 'Nazwa'},
		{'key': 'quantity', 'type': 'input', 'class': 'required integer non-negative-integer', 'title': 'Ilość'},
	];

	var roomEditor = new EntityEditor('rooms', roomDefinitions, $('#room-editor'));
	var resourceEditor = new EntityEditor('resources', resourceDefinitions, $('#resource-editor'));

	$('body').data('room-editor', roomEditor);
	$('body').data('resource-editor', resourceEditor);

	$('#training-center-form').submit(function(e)
	{
		$('input[name=\'room-data\']').val($.JSON.encode(roomEditor.getEntities()));
		$('input[name=\'resource-data\']').val($.JSON.encode(resourceEditor.getEntities()));
		return true;
	});

	var resources = $.parseJSON($('input[name=\'resources-name-source\']').val());
	$('#resources-name-edit').autocomplete({source: resources});
});
