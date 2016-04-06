function czyRegonOK(obj)
{
	return obj.value.search(/^\d{9}$/) != -1;
}

function czyNipOK(obj)
{
	if (obj.value.length != 13 && obj.value.length != 10)
		return false;
	return obj.value.search(/^(\d{3}-\d{3}-\d{2}-\d{2})|(\d{3}-\d{2}-\d{2}-\d{3})|(\d{10})$/) != -1;
}

function czyTelOK(obj)
{
	if (obj.value.length != 12)
		return false;
	return obj.value.search(/^(\(\d{3}\)\d{7})$/) != -1;
}

function czyZnakOK(obj)
{
var text, znak, ERR;

  ERR=false;
  kreska=0;
  text=obj.value;
  for (i=0;i<text.length;i++)
  {
    znak=text.substring(i,i+1);
    if (znak=="\`") ERR=true;
    if (znak=="\~") ERR=true;
    if (znak=="\"") ERR=true;
    if (znak=="\'") ERR=true;
    if (znak=="\;") ERR=true;
  }
  if (ERR) {
    alert("Niedozwolone znaki w polu: "+obj.name);
    obj.focus();
    return ERR;
  }
return false;
}
/*
function czyNipOK(obj)
{
var text, znak, kreska, ERR;

  ERR=false;
  kreska=0;
  text=obj.value;
  if (text.length==0) return false;
  for (i=0;i<text.length;i++)
  {
    znak=text.substring(i,i+1);
    if (znak<"0" || znak>"9")
    {
      if (znak!="-")  ERR=true;
      if (znak=="-") kreska++;
    }
  }
  if (kreska!=3) ERR=true;
  if (text.substring(3,4)!="-") ERR=true;
  if (ERR) {
    obj.focus();
    return ERR;
  }
return false;
}
*/

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

function czyHasloOK(obj1,obj2)
{
  var haslo1, haslo2;

//  if (czyDlugoscOK(obj1,1,8)) return true;
//  if (czyDlugoscOK(obj2,1,8)) return true;

  if (obj1.value!=obj2.value) {
//    alert("HasГo nie zgodne z potwierdzeniem. Prosze poprawiц.");
    obj1.focus();
    return true;
  }
  return false;
}

function czyHaslo2OK(obj1,obj2)
{
  var haslo1, haslo2;

  if (obj1.value!=obj2.value) {
//    alert("HasГo nie zgodne z potwierdzeniem. Prosze poprawiц.");
    obj1.focus();
    return true;
  }
  return false;
}

function czyStareHasloOK(obj1,obj2)
{
  var haslo1, haslo2;

  //if (czyDlugoscOK(obj1,1,8)) return true;
  //if (czyDlugoscOK(obj2,1,8)) return true;

  if (obj1.value!=obj2.value) {
    alert("Proszъ wpisaц poprawnie dotychczasowe hasГo.");
    obj1.focus();
    return true;
  }
  return false;
}


function checkLen(obj, min, max)
//gdy min,max=-1 brak ograniczenia
{

if (min!=-1) {
  if (obj.value.length < min) {
    obj.focus();
    //alert("Za maГo znakѓw, proszъ wypeГniц pole. " +obj.name);
    return  true;
  }
  if (max!=-1) 
    if (obj.value.length > max) {
      obj.focus();
      //alert("Za duПo znakѓw w polu: "+obj.name);
      return  true;
    }
}
else
if (max!=-1)
  if (obj.value.length > max) {
    obj.focus();
    //alert("Za duПo znakѓw w polu: "+obj.name);
    return  true;
  }
return false;
}

function checkValue(obj, min, max)
//gdy min,max=-1 brak ograniczenia
{
var text, znak, ERR;
  ERR=false;
  kreska=0;
  text=obj.value;
  for (i=0;i<text.length;i++)
  {
    znak=text.substring(i,i+1);
    if (znak=="\'") ERR=true;
  }
  if (ERR) {
    alert("Wpisano niedozwolone znaki");
    obj.focus();
    return ERR;
  }

if (min!=-1) {
  if (obj.value.length < min) {
    alert("Za maГo znakѓw, proszъ poprawnie wypeГniц pole. ");
    obj.focus();
    return  true;
  }
  if (max!=-1) 
    if (obj.value.length > max) {
      alert("Za duПo znakѓw, proszъ poprawnie wypeГniц pole ");
      obj.focus();
      return  true;
    }
}
else
if (max!=-1)
  if (obj.value.length > max) {
    alert("Za duПo znakѓw, proszъ poprawnie wypeГniц pole");
    obj.focus();
    return  true;
  }
return false;
}

//**************************** checkChar
function checkChar(obj)
{
 text=obj.value;
 wzor='!@#$%^&*()+/"\\\'|;';
 ERR=0;
 z='';
 for (i=0;i<text.length;i++)
 {
  znak=text.substring(i,i+1);
  for (j=0;j<wzor.length;j++)
  { 
   z=wzor.substring(j,j+1);
   if (znak==z) 
   {
    ERR=1;
    obj.focus();
   }
  }
 };
 return ERR;
}

//************************* checkInt
function checkInt(obj, min, max)
//gdy min,max=-1 brak ograniczenia
{
var text, znak, ERR;

  text=obj.value;
  ERR=0;
  liczba=parseInt(text,10);
  liczbatxt=''+liczba;
  if (isNaN(liczba)) ERR=1;
  if (text.length!=liczbatxt.length) ERR=1;
  if (ERR) {
   alert("W tym polu naleПy wpisac liczbъ caГkowitЙ");
   obj.focus();
   return ERR;
  }
if (min!=-1) {
  if (obj.value.length < min) {
    alert("Za maГo znakѓw, proszъ poprawnie wypeГniц pole. ");
    obj.focus();
    return  true;
  }
  if (max!=-1) 
    if (obj.value.length > max) {
      alert("Za duПo znakѓw, proszъ poprawnie wypeГniц pole ");
      obj.focus();
      return  true;
    }
}
else
if (max!=-1)
  if (obj.value.length > max) {
    alert("Za duПo znakѓw,, proszъ poprawnie wypeГniц pole");
    obj.focus();
    return  true;
  }
return false;
}

//************************* checkFloat
function checkFloat(obj, min, max)
//gdy min,max=-1 brak ograniczenia
{
var text, znak, ERR;

  text=obj.value;
  ERR=0;
  liczba=parseFloat(text);
  liczbatxt=''+liczba;
  if (isNaN(liczba)) ERR=1;
  if (ERR) {
   alert("W tym polu naleПy wpisac liczbъ zmiennoprzecinkowЙ z kropkЙ.");
   obj.focus();
   return ERR;
  }
if (min!=-1) {
  if (obj.value.length < min) {
    alert("Za maГo znakѓw, proszъ poprawnie wypeГniц pole. ");
    obj.focus();
    return  true;
  }
  if (max!=-1) 
    if (obj.value.length > max) {
      alert("Za duПo znakѓw, proszъ poprawnie wypeГniц pole ");
      obj.focus();
      return  true;
    }
}
else
if (max!=-1)
  if (obj.value.length > max) {
    alert("Za duПo znakѓw,, proszъ poprawnie wypeГniц pole");
    obj.focus();
    return  true;
  }
return false;
}

function checkEmail(obj)
{
	re = new RegExp("[a-z|A-Z|0-9|\.|\-|\_]+@[a-z|A-Z|0-9|\.|\-|\_]+");

	if (!re.test(obj.value))
	{
	 	obj.focus();
		return true;
	}
	return false;
}
