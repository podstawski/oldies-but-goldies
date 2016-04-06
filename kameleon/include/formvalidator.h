<style rel="stylesheet" type="text/css">
	._focusInpt { background-Color:#FF0000 !important; color:#FFFFFF !important; }
</style>
<?
	global $WEBTD;
	$xml = $WEBTD->xml;
	$sid = $WEBTD->sid;
	
	list($formname,$NOEMPTY,$REREG,$ALERT_NOEMPTY,$ALERT_EREG) = unserialize(stripslashes($xml));

	$formname = "kameleon_form_sid_".$sid;

	if (!strlen(trim($formname))) return;

	if (is_array($NOEMPTY))
	{
		reset($NOEMPTY);
		while (list($key,$val) = each($NOEMPTY))
		{
			if (!strlen($val)) continue;
			$_key = $key."]";
			$js_val.= "
			if (('$_key' == form.elements[i].name && form.elements[i].value == '') || ('$_key' == form.elements[i].name && form.elements[i].type.toLowerCase() == 'checkbox' && form.elements[i].checked == false))
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

			$val=urldecode($val);

			if ($val[0]=='=')
				$js_val.= "
				if ('$_key' == form.elements[i].name)
				{
					for (j=0; j<form.elements.length; j++)
					{
						if (form.elements[j].name=='".substr($val,1)."' && form.elements[j].value!=form.elements[i].value)
						{
								alert('".urldecode($ALERT_EREG[$key])."');
								form.elements[i].onblur = cl$formname;
								form.elements[i].className = 'focusInpt'
								form.elements[i].focus();
								return false;
						}
					}
				}
				";
			else
				$js_val.= "
				
				_p = '".str_replace("[","",str_replace("]","",$val))."';

				pattern = patterns[''+_p+''];
				if (pattern == null)
					pattern= new RegExp('$val');

				if (pattern != null)
				{
					var formVal=form.elements[i].value;
					if ('$_key' == form.elements[i].name && form.elements[i].value != '' && !formVal.match(pattern))
					{
						if (form.elements[i].type.toLowerCase() == 'checkbox' && form.elements[i].checked == true)
						{
							alert('".urldecode($ALERT_EREG[$key])."');
							form.elements[i].onblur = cl$formname;
							form.elements[i].className = 'focusInpt'
							form.elements[i].focus();
							return false;
						}
						if (form.elements[i].type.toLowerCase() != 'checkbox')
						{
							alert('".urldecode($ALERT_EREG[$key])."');
							form.elements[i].onblur = cl$formname;
							form.elements[i].className = 'focusInpt'
							form.elements[i].focus();
							return false;
						}

					}
				}
				";		
		}
	}
	

	$js = "

	 cl$formname = function clear$formname(e)
	 {
		var targ;
		if (!e) var e = window.event;
		if (e.target) targ = e.target;
			else if (e.srcElement) targ = e.srcElement;
		targ.className='';
	 }

	vf$formname = function validate$formname()
	{		
		patterns = new Array;
		//patterns['Email'] = /^[a-zA-Z0-9\_\.\-]{1,90}@[a-zA-Z0-9\-]{1,128}.[a-zA-Z0-9]+\$/;
		patterns['Email'] = /^[a-z|A-Z|0-9|\.|\-|\_]{1,90}@[a-zA-Z0-9\-\.]{1,128}.[a-zA-Z0-9]+\$/;
		patterns['NIP'] = /^[0-9]{3}-[0-9]{2,3}-[0-9]{2,3}-[0-9]{2,3}\$/;
		patterns['Pesel'] = /^[0-9]{11}\$/;
		patterns['Regon'] = /^[0-9]{9}\$/;
		patterns['Kod pocztowy'] = /^[0-9]{2}-[0-9]{3}\$/;

		form = document.getElementById('$formname');

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
<script language="javascript">
	<? echo $js ?>

</script>