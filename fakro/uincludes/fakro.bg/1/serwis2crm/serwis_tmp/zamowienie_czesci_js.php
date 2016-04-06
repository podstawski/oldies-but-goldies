<script type="text/javascript">
<!--
function validate(address, daneJakWyzej)
{
	if (!is_date(el('data_nabycia_towaru').value))
	{
		return inform_date('data_nabycia_towaru', true);
	}

	if(!el('data_montazu').disabled)
	{
		if (!is_date(el('data_montazu').value))
		{
			return inform_date('data_montazu');
		}
		if(el('data_montazu').value < el('data_nabycia_towaru').value)
		{
			return inform_date_not_less('data_nabycia_towaru',
				'Data montaПu nie moПe byц wczeЖniejsza niП data nabycia towaru!');
		}
	}

	if (!is_number(el('ilosc_produktow').value))
	{
		return inform_number('ilosc_produktow', '', true);
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
		if (el(fields[i]).value == '')
		{
			return inform(fields[i]);
		}
	}

	if (address==1) return true;

    if (el('email').value == '')
	    return inform('email');

    if (el('t1_numer').value == '')
    {
        alert('Musisz podaц numer telefonu!');
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

	if(!el('regulamin').checked)
	{
		alert('Aby kontynuowaц naleПy przeczytaц i zaakceptowaц regulamin!');
		return false;
	}

    return true;
}

function validate_zamowienie()
{
<? for($i = 0; $i < count($_czesci); $i++) { ?>
	count = el('ilosc[<?=$_czesci[$i]["id_bp_czesci"];?>]');
	bool = el('zamowienie_<?=$_czesci[$i]["nr_explozyjny"];?>').value;
	if (bool == '1' && !is_number(count.value))
	{
		alert('Musisz podaц poprawnБ iloЖц zamawianej czъsci!');
		count.focus();
		return false;
	}
<? } ?>
	return true;
}

current = false;
explozyjny = false;
var form_count = 0;

function show_image(id)
{
	img = el('image');
	img.style.display = '';
	img.src = '<?=$UIMAGES._FILES_URL;?>bp_explozyjne_pliki_' + id;

	if (current)
	{
		c_map = el('map');
		map.name = 'map_' + current;
		map.id = 'map_' + current;
	}

	map = el('map_' + id);
	map.name = 'map';
	map.id = 'map';
	current = id;

	if (explozyjny) el('infoczesci_' + explozyjny).style.display = 'none';
    el('wstep_info').style.display = '';
}

function display(id)
{
    if (explozyjny) el('infoczesci_' + explozyjny).style.display = 'none';
    el('wstep_info').style.display = 'none';
    el('infoczesci_' + id).style.display = '';
    explozyjny = id;
	window.scrollTo(0,0);
	// el('info').innerHTML += '<a href="#" onClick="add(\'' + id + '\');"> Dodaj czъЖц do zamѓwienia</a>';
}

function add(id)
{
    if (el('czesci_' + id).style.display != '') form_count++;
    el('czesci_' + id).style.display = '';
	
<? for($i = 0; $i < count($_czesci); $i++) { ?>
        if (id=="<?=$_czesci[$i]["nr_explozyjny"];?>") el('czesci_ilosc_' + id).value = '<?=$_czesci[$i]["ilosc"];?>';
<? } ?>
	el('zamowienie_' + id).value = 1;
	if (form_count > 0)
	{
		el('submit').style.display = '';
		el('czesci_header').style.display = '';
		el('czesci_suma').style.display = '';
		el('dostawca').style.display = '';
		el('zamowienie_dane').style.display = '';
		el('zakladka1').style.display = '';
		el('zakladka2').style.display = '';
		el('zakladka3').style.display = '';
		el('produkt_form').style.display = '';
	}
	zmiana_ilosci(id);
	change_uwagi_dostawca();

	return false;
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

function zmiana_ilosci(id)
{
    el('czesci_cena_' + id).value = el('czesci_cenaszt_' + id).value * el('czesci_ilosc_' + id).value;
    el('czesci_cenavalue_' + id).value = el('czesci_cenaszt_' + id).value * el('czesci_ilosc_' + id).value;
    sum_cena();
}

function sum_cena()
{
    suma = '0';
<? for($i = 0; $i < count($_czesci); $i++) { ?>
	   <? if($_czesci[$i]["access_www"] == 1) { ?>
	    if (el('czesci_<?=$_czesci[$i]["nr_explozyjny"];?>').style.display == '')
	    {
	    	suma = 1 * (suma + (el('czesci_cena_<?=$_czesci[$i]["nr_explozyjny"];?>').value * 1));
	    }
	   <? } ?>
<? } ?>
    //suma = suma + (el('uwagi_dostawca').value * 1);

    el('suma').value = suma;
    el('sumavalue').value = suma;
}

function change_uwagi_dostawca()
{
    option = el('select_dostawca');
<? for($i = 0; $i < count($dostawcy); $i++) { ?>
        if (option[option.selectedIndex].value=='<?=$dostawcy[$i]["id_zgloszenie_serwisowe_dostawcy"];?>') {
            el('uwagi_dostawca').innerHTML = '<?=$dostawcy[$i]["uwagi"];?>';
            el('hidden_uwagi_dostawca').value = '<?=$dostawcy[$i]["uwagi"];?>';
        }
<? } ?>
    if (option[option.selectedIndex].value==0) {
        el('uwagi_dostawca').value = 0;
        el('hidden_uwagi_dostawca').value = 0;
    }
    el('nazwa_dostawcy').value = option[option.selectedIndex].text;
    sum_cena();
}
-->
</script>