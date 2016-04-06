var current = false;
var explozyjny = false;
var form_count = 0;

function validate(address, daneJakWyzej)
{
	if (el('data_nabycia_towaru') && !is_date(el('data_nabycia_towaru').value))
	{
		return inform_date('data_nabycia_towaru', true);
	}

	if(el('data_montazu') && !el('data_montazu').disabled)
	{
		if (!is_date(el('data_montazu').value))
		{
			return inform_date('data_montazu');
		}
		if(el('data_montazu').value < el('data_nabycia_towaru').value)
		{
			return inform_date_not_less('data_nabycia_towaru',
				'Data montażu nie może być wcześniejsza niż data nabycia towaru!');
		}
	}

	if (el('ilosc_produktow') && !is_number(el('ilosc_produktow').value))
	{
		return inform_number('ilosc_produktow', '', true);
	}

	if (address == 1) return true;

	if(el('email'))
	{
	    if (el('email').value == '')
		    return inform('email');
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
	}


    if (el('t1_kraj') && el('t1_kraj').value == '')
    {
        alert('Musisz podać numer telefonu!');
        el('t1_kraj').focus();
	    return false;
	}
    if (el('t1_miasto') && el('t1_miasto').value == '')
    {
        alert('Musisz podać numer telefonu!');
        el('t1_miasto').focus();
	    return false;
	}
    if (el('t1_numer') && el('t1_numer').value == '')
    {
        alert('Musisz podać numer telefonu!');
        el('t1_numer').focus();
	    return false;
	}

	var phones = new Array('t1', 'telefon_kontaktowy');
	for (var i in phones)
	{
		if (el(phones[i] + '_kraj') && !is_number(el(phones[i] + '_kraj').value) && el(phones[i] + '_kraj').value != '')
			return inform_number(phones[i], '_kraj', false);

		if (el(phones[i] + '_miasto') && !is_number(el(phones[i] + '_miasto').value) && el(phones[i] + '_miasto').value != '')
			return inform_number(phones[i], '_miasto', false);

		if (el(phones[i] + '_numer') && !is_number(el(phones[i] + '_numer').value) && el(phones[i] + '_numer').value != '')
			return inform_number(phones[i], '_numer', false);

		if (el(phones[i] + '_wewnetrzny') && !is_number(el(phones[i] + '_wewnetrzny').value) && el(phones[i] + '_wewnetrzny').value != '')
			return inform_number(phones[i], '_wewnetrzny', false);
	}
	
	if(el('sposob_platnosci') && el('sposob_platnosci').value == '')
	{
		alert('Nie wybrano sposobu płatności');
		el('sposob_platnosci').focus();
		return false;
	}		
	
	if(el('regulamin') && !el('regulamin').checked)
	{
		alert('Aby kontynuować należy przeczytać i zaakceptować regulamin!');
		return false;
	}

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
		if (el(fields[i]) && el(fields[i]).value == '')
		{
			return inform(fields[i]);
		}
	}
	
	return true;
}

function zmiana_ilosci(id)
{
    el('czesci_cena_' + id).value = el('czesci_cenaszt_' + id).value * el('czesci_ilosc_' + id).value;
    el('czesci_cenavalue_' + id).value = el('czesci_cenaszt_' + id).value * el('czesci_ilosc_' + id).value;
    sum_cena();
}

function remove(id)
{
    el('czesci_' + id).style.display = 'none';
    el('czesci_ilosc_' + id).value = '0';
	el('zamowienie_' + id).value = 0;
	form_count--;
	if (form_count == 0)
	{
		el('submit').style.display = 'none';
		el('czesci_header').style.display = 'none';
		el('czesci_suma').style.display = 'none';
		el('dostawca').style.display = 'none';
		el('zamowienie_dane').style.display = 'none';
		el('zakladka1').style.display = 'none';
		el('zakladka2').style.display = 'none';
		el('zakladka3').style.display = 'none';
		el('produkt_form').style.display = 'none';
	}
	sum_cena();
	return false;
}

function display(id)
{
    if (explozyjny) el('infoczesci_' + explozyjny).style.display = 'none';
    el('wstep_info').style.display = 'none';
    el('infoczesci_' + id).style.display = '';
    explozyjny = id;
}