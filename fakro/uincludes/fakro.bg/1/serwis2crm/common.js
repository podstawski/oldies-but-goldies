function ustawDateMontazu(select, data_montazuID)
{
	var data_montazu_gwiazdka = '';
	if(data_montazuID == '')
	{
		data_montazuID = 'data_montazu_' + select.id.split('_')[1];
		data_montazu_gwiazdka = 'data_montazu_gwiazdka_' + select.id.split('_')[1];
	}
	else
	{
		data_montazu_gwiazdka = data_montazuID + '_gwiazdka';
	}

	if(select.options[select.selectedIndex].value == 3)
	{
		el(data_montazuID).value = '';
		el(data_montazuID).disabled = true;
		el(data_montazu_gwiazdka).style.visibility = 'hidden';
	}
	else
	{
		el(data_montazuID).disabled = false;
		el(data_montazu_gwiazdka).style.visibility = '';
	}
}

function is_date(data)
{
	if (data != undefined)
	{
		if ((data.substring(4, 5) != "-") ||
		    (data.substring(7, 8) != "-"))
			return false;

		var rok = data.substring(0, 4);
		var mies = data.substring(5, 7);
		var dzien = data.substring(8, 10);
		var t = new Date(rok, mies - 1, dzien);
		if (t.getFullYear() != rok)
			return false;
		if (t.getMonth() != (mies-1))
			return false;
		if (t.getDate() != dzien)
			return false;
		return true;
	}
	return false;
}

function inform(id)
{
	var text = el(id+'_label').innerHTML;
	alert('Pole "' + strip(text) + '" nie moПe byц puste!');
	el(id).focus();
	return false;
}

function is_number(number)
{
	return (parseInt(number) == number);
}

function inform_date(id, not_empty)
{
	var text = el(id+'_label').innerHTML;

	if (not_empty == true)
		alert('Pole "' + strip(text) + '" nie moПe byц puste i musi zawieraц datъ w prawidГowym formacie!');
	else
		alert('Pole "' + strip(text) + '" musi zawieraц datъ w prawidГowym formacie!');

	el(id).focus();
	return false;
}

function inform_number(id, suffix, not_empty)
{
	var text = el(id+'_label').innerHTML;

	if (not_empty == true)
		alert('Pole "' + strip(text) + '" nie moПe byц puste i moПe zawieraц tylko liczby!');
	else
		alert('Pole "' + strip(text) + '" moПe zawieraц tylko liczby!');

	el(id + suffix).focus();
	return false;
}

function inform_date_not_less(id, msg)
{
	var text = el(id + '_label').innerHTML;

	alert(msg);

	el(id).focus();
	return false;
}

function strip(str)
{
	str = str.replace(/:/g, "");
	str = str.replace(/\n/g, "");
	str = str.replace(/^\s*|\s*$/g,"");
	return str.replace(/<[^>]*>/g, "");
}

function el(id)
{
	return document.getElementById(id);
}

function kopiuj_adres_link()
{
    if (!validate(1, true)) return false;
	if (el('kopiuj').checked)
	{
		el('kopiuj').checked = false;
		kopiuj_adres();
	}
	else
	{
		el('kopiuj').checked = true;
		kopiuj_adres();
	}
}

function kopiuj_zmiany(id)
{
	if (el('kopiuj').checked)
	{
		el('okno_'+id).value = el(id).value;
	}
}

function kopiuj_adres()
{
	if (el('kopiuj').checked)
	{
		el('okno_ulica').value = el('ulica').value;
		el('okno_ulica').disabled = true;
		el('okno_nr_domu').value = el('nr_domu').value;
		el('okno_nr_domu').disabled = true;
		el('okno_nr_mieszkania').value = el('nr_mieszkania').value;
		el('okno_nr_mieszkania').disabled = true;
		el('okno_kod_pocztowy').value = el('kod_pocztowy').value;
		el('okno_kod_pocztowy').disabled = true;
		el('okno_miasto').value = el('miasto').value;
		el('okno_miasto').disabled = true;
		el('okno_id_kraje').value = el('id_kraje').value;
		el('okno_id_kraje').disabled = true;

	}
	else
	{
		el('okno_ulica').disabled = false;
		el('okno_nr_domu').disabled = false;
		el('okno_nr_mieszkania').disabled = false;
		el('okno_kod_pocztowy').disabled = false;
		el('okno_miasto').disabled = false;
		el('okno_id_kraje').disabled = false;
	}
}