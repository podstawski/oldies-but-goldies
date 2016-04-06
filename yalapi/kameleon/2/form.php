<input type="hidden" id="kluczyk" value="" />

<script>

jQuery(function($)
{
	alertTxt='Formularz nie został poprawnie wypełniony';
	var klucz=document.getElementById('id_klucz');
	var cookie_klucz='<?=$_COOKIE['yala_klucz']?>';

	if (klucz.value=='') klucz.value=cookie_klucz.length>0?cookie_klucz:klucz.title;
	
	
	$('#proba').bind('submit', function() {
	
		inps=$('input');
		$.each(inps, function(i, inp) {
			if (inp.id.substr(0,3)=='id_') 
			{
				em=document.getElementById('i_'+inp.id.substr(3));
				if (em.innerHTML!='&nbsp;' && em.innerHTML!='')
				{
					alert(alertTxt);
					return false;
				}
			}
		});

		if ( document.getElementById('id_klucz').value.length==0 || document.getElementById('id_klucz').value==document.getElementById('id_klucz').title )
		{
			document.getElementById('i_klucz').innerHTML='prosimy podać adres swojej aplikacji';
			alert(alertTxt);
			return false;
		}

		<?php if (!isset($_REQUEST['social']) || !strlen($_REQUEST['social'])){ ?>

		if ( document.getElementById('id_email').value.length==0)
		{
			document.getElementById('i_email').innerHTML='prosimy podać poprawny adres e-mail';
			alert(alertTxt);
			return false;
		}

		if ( document.getElementById('id_pass').value.length==0)
		{
			document.getElementById('i_pass').innerHTML='prosimy podać hasło';
			alert(alertTxt);
			return false;
		}

		if ( document.getElementById('id_pass').value != document.getElementById('id_pass2').value)
		{
			document.getElementById('i_pass2').innerHTML='powtórzenie hasła powinno być takie samo jak hasło';
			alert(alertTxt);
			return false;
		}
		
		<?php } ?>
		if (document.getElementById("kluczyk").value.length==0) return false;
	
		return true;
	});
	

	$('input').bind('click',function() {

		if (this.id.substr(0,3)=='id_') document.getElementById('i_'+this.id.substr(3)).innerHTML='&nbsp;';
	});
;


	$('#id_klucz').bind('blur', function() {
		q=klucz.value;
		if (q=='') klucz.value=klucz.title;
		else
		{
			url='<?=$INCLUDE_PATH;?>/klucz.php?q='+q;
			$.get(url,function(data){
				
				if(data.length>0)
				{
					document.getElementById('i_klucz').innerHTML=data;
					document.cookie='yala_klucz=; path=/';
					document.getElementById('kluczyk').value='';
				}
				else
				{
					document.cookie='yala_klucz=' + q + '; path=/';
					document.getElementById('kluczyk').value='1';
				}
			});
		}
	});


});

</script>