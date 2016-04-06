function validateForm(obj,lang)
{
	switch (lang) {
		case 'd':
			txt_rbcb = "Bitte füllen Sie alle felder aus!";
			txt_brak = "Bitte füllen Sie alle felder aus!";
			txt_mail = "Dein e-mail ist nicht korrekt!";
		break;
		
		default: 
			txt_rbcb = "proszę zaznaczyć kwadracik !";
			txt_brak = "proszę uzupełnić dane !";
			txt_mail = "nieprawidłowy adres email !";
	}
	
	oCol=obj.elements;
	oColLen=oCol.length;
	for (i=0;i<oColLen;i++ )
	{
		var oElem=oCol(i);
		var wyr=oElem.validate;
		if (wyr==1)
		{
			switch (oElem.type)
			{
				case "CHECKBOX":
				case "checkbox":
				case "RADIO":
				case "radio":
					if (!oElem.checked)
					{
						alert(txt_rbcb);
						oElem.focus();
						return false;
					}

				default:
					if (oElem.value == "")
					{
						alert(txt_brak);
						oElem.focus();
						return false;
					}
			}
		}
		if (wyr=='email')
		{
			if (checkEmail(oElem))
			{
				alert(txt_mail);
				oElem.focus();
				return false;
			}
		}
	}
	return true;
}

function checkEmail(obj)
{
	if (obj.value == "")
		return true;

	re = new RegExp("[a-z|A-Z|0-9|\.|\-|\_]+@[a-z|A-Z|0-9|\.|\-|\_]+");

	if (!re.test(obj.value))
	{
	 	obj.focus();
		return true;
	}
	return false;
}


function czyKodOK(obj)
{
  var text, znak, kropka, ERR;

  kreska=0;
  text=obj.value;
  if (text.length < 6)
	  return false;
  for (i=0;i<text.length;i++)
  {
    znak=text.substring(i,i+1);
    if (znak<"0" || znak>"9")
    {
      if (znak!="-")  return false;
      if (znak=="-") kreska++;
    }
  }
  if (kreska!=1) return false;
  if (text.substring(2,3)!="-") return false;

return true;
}
