/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
//klasa edytująca rekordy w pamięci.
//wejście: id dla cssów, tablica definicji, gdzie umieścić.
function EntityEditor(identifier, definitions, where, postFunc)
{
	this.postFunc = postFunc; //funkcja wywoływana przy zatwierdzeniu edycji
	this.entities = []; //docelowa lista obiektów, na której chcemy operować
	this.identifier = identifier; //css id dla okna z popupem
	this.definitions = definitions; //definicje pól w formularzu

	//przygotuj potrzebne elementy
	this.popup = $('<div class="popup" id="' + identifier + '-popup"/>');
	this.table = $('<table id="' + identifier + '-table"><thead/><tbody/></table>');
	this.popup.append($('<form><fieldset></fieldset><div class="op"></div></form>'));

	$(document.body).append(this.popup);
	where.append(this.table);

	//utwórz linię z nagłówkami dla tabelki agregującej obiekty
	var headerRow = $('<tr/>');
	headerRow.append($('<th class="lp"><span>L.p.</span></th>'));
	for (var i in this.definitions)
	{
		var definition = this.definitions[i];
		if (definition['type'] == 'hidden' || definition['show'] == false)
		{
			continue;
		}
		headerRow.append($('<th><span>' + definition['title'] + '</span></th>'));
	}
	headerRow.append($('<th class="op"><span>Dostępne operacje</span></th>'));
	$('thead', this.table).append(headerRow);

	//utwórz pola w formularzu
	for (var i in this.definitions)
	{
		var definition = this.definitions[i];
		if (definition['type'] == 'hidden')
		{
			continue;
		}
		var row = $('<div>');
		var label = $('<label>' + definition['title'] + '</label>');
		var input = $('<' + definition['type'] + '/>');

		input.attr('id', identifier + '-' + definition['key'] + '-edit');
		label.attr('for', input.attr('id'));
		//todo: tutaj by się przydała obsługa dla <option> jeśli type == select
		if (definition['class'])
		{
			var classes = definition['class'].split(' ');
			for (var c in classes)
			{
				input.addClass(classes[c]);
			}
		}
		this.definitions[i]['input'] = input;
		row.append(label);
		row.append(input);
		$('fieldset', this.popup).append(row);
	}

	//utwórz przyciski
	var applyButton = $('<button class="button default action-update" type="submit">Gotowe</button>'); //potwierdzenie edycji
	var cancelButton = $('<button class="button action-cancel" type="reset">Anuluj</button>'); //anulowanie edycji
	var createButton = $('<button class="button default action-create" type="button">Dodaj pozycję</button>'); //tworzenie nowego obiektu
	$('form div.op', this.popup).append(applyButton);
	$('form div.op', this.popup).append(cancelButton);
	createButton.insertAfter(this.table);



	//tworzenie nowego obiektu
	createButton.click($.proxy(function()
	{
		$.removeData(this.popup, 'index');
		utils.togglePopup(this.popup, true);
		return false;
	}, this));

	//anulowanie edycji
	cancelButton.click($.proxy(function()
	{
		utils.togglePopup(this.popup, false);
		return false;
	}, this));

	//potwierdzanie edycji
	$('form', this.popup).submit($.proxy(function()
	{
		//pobieramy zapamiętany index, który obiekt edytujemy
		var index = this.popup.data('index');
		var entity = {};
		if (typeof index != 'undefined' && index >= 0)
		{
			//sklonuj, zamiast przypisywać referencję na obiekt
			entity = $.extend({}, this.entities[index]);
		}
		//tworzymy obiekt na podstawie zawartości pól formularza
		for (var i in this.definitions)
		{
			var definition = this.definitions[i];
			if (definition['type'] == 'hidden')
			{
				continue;
			}
			entity[definition['key']] = {};
			entity[definition['key']]['value'] = definition['input'].val();
			entity[definition['key']]['shown-text'] = definition['input'].text();
			if (!entity[definition['key']]['shown-text'])
			{
				entity[definition['key']]['shown-text'] = entity[definition['key']]['value'];
			}
			//jeśli klasa zawiera integer, to zapisz to jako liczbę
			if (definition['class'] && definition['class'].indexOf('integer') != -1)
			{
				entity[definition['key']]['value'] = parseInt(entity[definition['key']]['value']);
			}
		}
		//prześlij do funkcji użytkownika. jeśli funkcji się to nie spodoba (tj. zwróci false), to anulujemy edycję.
		//użytkownikowi zostawiamy wybór, co należy zrobić w takim wypadku
		if (this.postFunc && !this.postFunc(entity))
		{
			return false;
		}


		//nadpisujemy obiekt
		if (typeof index != 'undefined' && index >= 0)
		{
			this.entities.splice(index, 1, entity);
		}
		//lub, jeśli tworzymy nowy, dołączamy go na koniec
		else
		{
			this.entities.push(entity);
		}
		//updatujemy wszystkie rekordy w tabelce
		this.updateTable();
		//wyłączamy popup z edycją rekordu
		utils.togglePopup(this.popup, false);
		return false;
	}, this));

	this.setEntities = function(newEntities)
	{
		this.entities = newEntities;
		for (var index in this.entities)
		{
			var entity = this.entities[index];
			for (var i in entity)
			{
				if (!(entity[i] instanceof Object))
				{
					entity[i] =
					{
						'shown-text' : entity[i],
						'value' : entity[i]
					}
				}
			}
			this.entities.splice(index, 1, entity);
		}
		this.updateTable();
	}

	this.getEntities = function()
	{
		ret = [];
		for (var index in this.entities)
		{
			var entity = this.entities[index];
			var ret2 = {};
			for (var i in entity)
			{
				ret2[i] = entity[i]['value'];
			}
			ret.push(ret2);
		}
		return ret;
	}

	//wizualne updatowanie rekordów w tabelce
	this.updateTable = function()
	{
		//czyścimy tabelę
		$('#' + identifier + '-table tbody').empty();
		for (var index in this.entities)
		{
			//pobieramy dany obiekt z pamięci
			var entity = this.entities[index];
			var row = $('<tr/>');
			row.data('index', index); //zapamiętujemy jego index; przyda się, kiedy będziemy chcieli go edytować
			row.append($('<td class="lp">' + (parseInt(index) + 1) + '</td>')); //dodajemy l.p.
			//dodajemy wszystkie pola z definicji obiektu
			for (var i in this.definitions)
			{
				var definition = this.definitions[i];
				if (definition['type'] == 'hidden' || definition['show'] == false)
				{
					continue;
				}
				row.append($('<td>' + entity[definition['key']]['shown-text'] + '</td>'));
			}

			//tworzymy linki do edycji i usunięcia rekordu.
			var removeLink;
			var editLink;
			row.append($('<td class="op"><ul class="op"><li class="icon-edit"><a class="action-edit" href="#">Edytuj</a></li><li class="icon-delete"><a class="action-delete" href="#">Usuń</a></li></ul></td>'));
			removeLink = $('li[class=\'icon-delete\'] a', row);
			editLink = $('li[class=\'icon-edit\'] a', row);

			//usuwanie
			removeLink.click($.proxy(function(e)
			{
				var index = $(e.target).parents('tr').data('index');
				this.entities.splice(index, 1);
				this.updateTable();
				return false;
			}, this));

			//edycja
			editLink.click($.proxy(function(e)
			{
				var index = $(e.target).parents('tr').data('index');
				this.popup.data('index', index);
				utils.togglePopup(this.popup, true);
				return false;
			}, this));

			//dołączamy wiersz z rekordem do tabeli.
			$(this.table).append(row);
		}
	}

	//kiedy popup dostanie info, że został wyświetlony
	this.popup.bind('show', $.proxy(function(event)
	{
		//pobierzemy indeks
		var index = this.popup.data('index');
		if (typeof index != 'undefined' && index >= 0)
		{
			var entity = this.entities[index];
			//jeśli nie jest pusty, to wczytamy wartości obiektu, który edytujemy!
			for (var i in this.definitions)
			{
				var definition = this.definitions[i];
				if (definition['type'] == 'hidden')
				{
					continue;
				}
				$(definition['input']).val(entity[definition['key']]['value']);
			}
		}
		else
		{
			//w przeciwnym razie wczytamy wartości domyślne z definicji obiektu -- o ile takie istnieją, that is
			for (var i in this.definitions)
			{
				var definition = this.definitions[i];
				if (definition['type'] == 'hidden')
				{
					continue;
				}
				$(definition['input']).val(definition['defaultValue']);
			}
		}
		
	}, this));
};

