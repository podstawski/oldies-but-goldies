var schowajTablica=new Array;
var schowajIndeks=0;
var schowajCo=0;


function schowaj(sid)
{
	schowajTablica[schowajIndeks++]=sid;
	setTimeout(naprawdeSchowaj,100);
}
function naprawdeSchowaj()
{
	cos='ne';
	while (schowajIndeks>schowajCo)
	{
		obj=document.getElementById('s'+schowajTablica[schowajCo]);
		obj.style.display='no'+cos;
		schowajCo++;
	}
	
}
