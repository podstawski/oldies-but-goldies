function checkform ( form )
//r email_form = "/^([a-zA-Z0-9])+([.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/";

{
    	if (form.name.value == "") {
        alert( "Pole imie nie moze byc puste!" );
        form.name.focus();
        return false ;
    	}
	else if (form.surname.value == "") {
        alert( "Pole nazwisko nie moze byc puste!" );
        form.surname.focus();
        return false ;
    	}
	else if ((form.mail.value == "") && (form.telefon.value == "")) {
        alert( "Muszisz podac adres e-mail lub numer telefonu!" );
        form.mail.focus();
        return false ;
    	}
	//else if ((form.mail.value != "") && (form.mail.value != "(\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})")) {
        //alert( "sprawdz adres e-mail!" );
        //form.mail.focus();
        //return false ;
    	//}
		
return true ;
}

function checkData(form) {
	if(form.rezerwacja_date_od.value == "") {
		alert("Proszę wprowadzić date przyjazdu.");
		return false ;
		}
	if(form.rezerwacja_date_do.value == "") {
		alert("Proszę wprowadzić date wyjazdu.");
		return false ;
		}
	return true ;
	}
