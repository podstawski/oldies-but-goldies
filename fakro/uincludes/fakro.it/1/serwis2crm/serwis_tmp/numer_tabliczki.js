function walidacjaTabliczki()
{
	return (numerTabliczki1_check() && numerTabliczki2_check());
}

function numerTabliczki1_check()
{
	if(document.getElementById('nazwa_nr') != null)
	{
		var e = document.getElementById('nazwa_nr');
		var v = e.value;

		if(v.length == 0)
		{
			e.focus();
			alert('Nale¿y podaæ pierwsz± czê¶æ numeru tabliczki znamionowej!');
			return false;
		}
	}
	return true;
}

function numerTabliczki2_check()
{
	if(document.getElementById('nazwa_nr2') != null)
	{
		var e = document.getElementById('nazwa_nr2');
		var v = e.value;

		if(v.length != 4)
		{
			e.focus();
			alert('Nale¿y podaæ drug± czê¶æ numeru tabliczki znamionowej!');
			return false;
		}

		// test czy sa 4 znaki numeryczne
		var re = /\d{4}/g;
		// var re = /\d\d[1-9]\d/g;
		if(!v.match(re))
		{
			e.focus();
			alert('Druga czê¶æ numeru tabliczki znamionowej mo¿e zawieraæ tylko cyfry!');
			return false;
		}

		var tydzien = v.substring(2, 4);
		tydzien = tydzien.substring(1, 2) + "" + tydzien.substring(0, 1);
		if((parseInt(tydzien) >= 1) && (parseInt(tydzien) <= 52))
		{
			//OK
		}
		else
		{
			e.focus();
			alert('B³êdny warto¶æ w drugiej czê¶ci numeru tabliczki znamionowej!');
			return false;
		}
	}
	return true;
}