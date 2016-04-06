<style>
	.focusInpt {
		background-Color:#FF0000 !important;
	}
</style>
<?
//	echo $costxt;
	list($formname,$NOEMPTY,$REREG,$ALERT_NOEMPTY,$ALERT_EREG) = unserialize($costxt);

	if (!strlen(trim($formname))) return;

	if (is_array($NOEMPTY))
	{
		reset($NOEMPTY);
		while (list($key,$val) = each($NOEMPTY))
		{
			if (!strlen($val)) continue;
			$_key = $key."]";
			$js_val.= "
			if ('$_key' == form.elements[i].name && form.elements[i].value == '')
			{
				alert('".urldecode($ALERT_NOEMPTY[$key])."');
				form.elements[i].onblur = cl$formname;
				form.elements[i].className = 'focusInpt'
				form.elements[i].focus();
				return false;
			}
			";
		}
	}

	if (is_array($REREG))
	{
		reset($REREG);
		while (list($key,$val) = each($REREG))
		{
			if (!strlen($val)) continue;
			$_key = $key."]";
			$js_val.= "
			
			_p = '".str_replace("[","",str_replace("]","",urldecode($val)))."';
			pattern = patterns[''+_p+''];

			if (pattern != null)
				if ('$_key' == form.elements[i].name && !form.elements[i].value.match(pattern))
				{
					alert('".urldecode($ALERT_EREG[$key])."');
					form.elements[i].onblur = cl$formname;
					form.elements[i].className = 'focusInpt'
					form.elements[i].focus();
					return false;
				}
			";		
		}
	}
	

	$js = "

	cl$formname = function clear$formname()
	{
		event.srcElement.className='';
	}

	vf$formname = function validate$formname()
	{		
		patterns = new Array;
		patterns['Email'] = /^[a-zA-Z0-9]{1,30}@[a-zA-Z0-9ąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{1,128}.[a-zA-Z0-9]+\$/;
		patterns['NIP'] = /^[0-9]{3}-[0-9]{2}-[0-9]{2}-[0-9]{3}\$/;
		patterns['Pesel'] = /^[0-9]{11}\$/;

		_form = document.getElementsByName('$formname');
		form = _form[0];
		for (i=0; i < form.elements.length ;i++ )
		{
			if (!form.elements[i].name.length) continue;
			$js_val
		}
		return true;
	}
	document.getElementById('$formname').onsubmit = vf$formname;
	";
	
?>
<script>
	<? echo $js ?>

</script>