var form_count = 0;

function add_product()
{
	div = el('produkt');
	form = el('produkt_form');

	var new_form = document.createElement('div');
	new_form.id = 'produkty[' + form_count + ']';
	new_form.innerHTML = form.innerHTML;

	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[0].getElementsByTagName('td')[1].getElementsByTagName('input')[0].id = 'produkt_nazwa_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[0].getElementsByTagName('td')[0].id = 'produkt_nazwa_' + form_count + '_label';
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[1].getElementsByTagName('td')[1].getElementsByTagName('input')[0].id = 'nazwa_nr_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[1].getElementsByTagName('td')[1].getElementsByTagName('input')[1].id = 'nazwa_nr2_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[1].getElementsByTagName('td')[0].id = 'nazwa_nr_' + form_count + '_label';
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[2].getElementsByTagName('td')[1].getElementsByTagName('input')[0].id = 'data_nabycia_towaru_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[2].getElementsByTagName('td')[0].id = 'data_nabycia_towaru_' + form_count + '_label';
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[3].getElementsByTagName('td')[1].getElementsByTagName('input')[0].id = 'data_montazu_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[3].getElementsByTagName('td')[0].id = 'data_montazu_' + form_count + '_label';
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[3].getElementsByTagName('td')[0].getElementsByTagName('span')[0].id = 'data_montazu_gwiazdka_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[4].getElementsByTagName('td')[1].getElementsByTagName('select')[0].id = 'montaz_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[8].getElementsByTagName('td')[1].getElementsByTagName('input')[0].id = 'ilosc_' + form_count;
	new_form.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr')[8].getElementsByTagName('td')[0].id = 'ilosc_' + form_count + '_label';

	div.appendChild(new_form);

	form_count++;

	return false;
}

function remove_product(link)
{
	form = el(link.parentNode.id);
	form.parentNode.removeChild(form);
	return false;
}

function validate(address, daneJakWyzej)
{
    if(!daneJakWyzej)
	{
		var fields = new Array('nazwa', 'ulica', 'nr_domu', 'kod_pocztowy', 'miasto',
						'okno_ulica', 'okno_nr_domu', 'okno_kod_pocztowy', 'okno_miasto');
	}
	else
	{
		var fields = new Array('nazwa', 'ulica', 'nr_domu', 'kod_pocztowy', 'miasto');
	}

	for (var i in fields)
	{
		if (el(fields[i]).value == '')
			return inform(fields[i]);
	}

	if (address==1) return true;

	if (el('email').value == '')
	    return inform('email');

	if (el('powod_zgloszenia_reklamacji').value == '')
	    return inform('powod_zgloszenia_reklamacji');

    if (el('t1_numer').value == '')
    {
        alert('Musisz podać numer telefonu!');
        el('t1_numer').focus();
	    return false;
	}

	var phones = new Array('t1', 'telefon_kontaktowy');

	for (var i in phones)
	{
		if (!is_number(el(phones[i] + '_kraj').value) && el(phones[i] + '_kraj').value != '')
			return inform_number(phones[i], '_kraj', false);

		if (!is_number(el(phones[i] + '_miasto').value) && el(phones[i] + '_miasto').value != '')
			return inform_number(phones[i], '_miasto', false);

		if (!is_number(el(phones[i] + '_numer').value) && el(phones[i] + '_numer').value != '')
			return inform_number(phones[i], '_numer', false);

		if (!is_number(el(phones[i] + '_wewnetrzny').value) && el(phones[i] + '_wewnetrzny').value != '')
			return inform_number(phones[i], '_wewnetrzny', false);
	}

	var produkty_exists;

	for (var i = 0; i <= form_count; i++)
	{
		if (el('produkty[' + i + ']'))
		{
			produkty_exists = true;
			if (el('produkt_nazwa_' + i).value == '')
				return inform('produkt_nazwa_' + i);

			if (el('nazwa_nr_' + i).value == '' || el('nazwa_nr2_' + i).value == '')
				return inform('nazwa_nr_' + i);

			if (!is_date(el('data_nabycia_towaru_' + i).value))
				return inform_date('data_nabycia_towaru_' + i, true);

			if(!el('data_montazu_' + i).disabled)
			{
				if (el('data_montazu_' + i).value == '' || !is_date(el('data_montazu_' + i).value))
					return inform_date('data_montazu_' + i);

				if(el('data_montazu_' + i).value < el('data_nabycia_towaru_' + i).value)
				{
					return inform_date_not_less('data_montazu_' + i,
						'Data montażu nie może być wcześniejsza niż data nabycia towaru!');
				}
			}

			if (!is_number(el('ilosc_' + i).value))
				return inform_number('ilosc_' + i, '', true);
		}
	}

	var email = /^[^@]+@([a-z0-9\-]+\.)+[a-z]{2,4}$/ig;

 	var str = new String(el('email').value);
 	if (str != '')
 	{
	  if (!str.match(email))
	  {
	  	alert('Niepoprawny adres e-mail!');
	  	el('email').focus();
	  	return false;
	  }
	}

	if (!produkty_exists)
	{
		alert('Nie dodano produktu reklamowanego!');
		return false;
	}

	if(!el('regulamin').checked)
	{
		alert('Aby kontynuować należy przeczytać i zaakceptować regulamin!');
		return false;
	}

	return true;
}

var num_files = 1;

function add_file()
{
    num_files++;
    form = el('files');
    hiddens = el('hiddens');
    var input = document.createElement('input');
    input.setAttribute('name','userfile' + num_files);
    input.setAttribute('id','file' + num_files);
    input.setAttribute('type','file');
    var br = document.createElement('br');
    form.appendChild(input);
    form.appendChild(br);
}
