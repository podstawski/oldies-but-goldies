<?
	global $WEBTD;
	
	$sid = $WEBTD->sid;
//	print_r($_POST);
	$frmsid = $_POST[frmsid];
	$formsid = $_POST[formsid];
	if ($frmsid == $sid)
		$formtovalidate = $_POST["formtovalidate_".$sid];


	if ($formsid == $sid)
	{
		$formname = $_POST["formname"];
		$NOEMPTY = $_POST["NOEMPTY"];
		$REREG = $_POST["REREG"];
		$ALERT_NOEMPTY = $_POST["ALERT_NOEMPTY"];
		$ALERT_EREG = $_POST["ALERT_EREG"];

		if (is_array($NOEMPTY))
		{
			reset($NOEMPTY);
			while (list($key,$val) = each($NOEMPTY))
				$NOEMPTY[$key] = urlencode($val);
		}

		if (is_array($REREG))
		{
			reset($REREG);
			while (list($key,$val) = each($REREG))
				$REREG[$key] = urlencode($val);
		}

		if (is_array($ALERT_NOEMPTY))
		{
			reset($ALERT_NOEMPTY);
			while (list($key,$val) = each($ALERT_NOEMPTY))
				$ALERT_NOEMPTY[$key] = urlencode($val);
		}

		if (is_array($ALERT_EREG))
		{
			reset($ALERT_EREG);
			while (list($key,$val) = each($ALERT_EREG))
				$ALERT_EREG[$key] = urlencode($val);
		}

//		echo "<pre>";print_r($NOEMPTY);echo "</pre>";
//		echo "<pre>";print_r($EREG);echo "</pre>";
//		echo "<pre>";print_r($ALERT_NOEMPTY);echo "</pre>";
//		echo "<pre>";print_r($ALERT_EREG);echo "</pre>";

		$costxt = serialize(array($formname,$NOEMPTY,$REREG,$ALERT_NOEMPTY,$ALERT_EREG));
		$sql = "UPDATE webtd SET costxt = '$costxt' WHERE sid = ".$sid;
		$adodb->execute($sql);
	}

//	echo "<pre>";print_r(unserialize($costxt));echo "</pre>";
	list($formname,$NOEMPTY,$REREG,$ALERT_NOEMPTY,$ALERT_EREG) = unserialize($costxt);
	
	$_js = "var NOEMPTY = new Array;\n";
	if (is_array($NOEMPTY))
	{
		reset($NOEMPTY);
		while (list($key,$val) = each($NOEMPTY))
		{
			$key = str_replace("[","_",$key)."_";
			$_js.= "NOEMPTY['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	$_js.= "var REREG = new Array;\n";
	if (is_array($REREG))
	{
		reset($REREG);
		while (list($key,$val) = each($REREG))
		{
			$key = str_replace("[","_",$key)."_";
			$_js.= "REREG['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	$_js.= "var ALERT_NOEMPTY = new Array;\n";
	if (is_array($ALERT_NOEMPTY))
	{
		reset($ALERT_NOEMPTY);
		while (list($key,$val) = each($ALERT_NOEMPTY))
		{
			$key = str_replace("[","_",$key)."_";
			$_js.= "ALERT_NOEMPTY['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	$_js.= "var ALERT_EREG = new Array;\n";
	if (is_array($ALERT_EREG))
	{
		reset($ALERT_EREG);
		while (list($key,$val) = each($ALERT_EREG))
		{
			$key = str_replace("[","_",$key)."_";
			$_js.= "ALERT_EREG['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	if (strlen($formname) && !strlen($formtovalidate))
		$formtovalidate = $formname;

?>
<form method=post action="<? echo $self ?>">
<input type="hidden" name="frmsid" value="<? echo $sid ?>">
<select id="formatka_<? echo $sid ?>" name="formtovalidate_<? echo $sid ?>" onChange="submit()"></select>
<input type="submit" value="Ustaw walidacjê">
</form>
<form method="post" action="<? echo $self ?>">
<input type="hidden" name="formsid" value="<? echo $sid ?>">
<div id="hc_<? echo $sid ?>" style="position:relative;top:0;left:0;visible:hidden;height:30">&nbsp;</div>
<div id="contener_<? echo $sid ?>"></div>
</form>
<?
	$sql = "SELECT title, sid FROM webtd WHERE lang = '$lang'
			AND server = $SERVER_ID AND ver = $ver AND page_id = $page";

	$res = $adodb->execute($sql);

	$js.= "var formatki = new Array;\n";
	for ($i=0 ; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$js.= "formatki['kameleon_form_sid_$sid'] = '".addslashes(stripslashes($title))."'\n";
	}

?>
<script>

	<? echo $js ?>

	var formy = document.forms;
	var form_select = document.getElementById('formatka_<? echo $sid ?>');
	form_select.length = 0;
	for (i=0; i < formy.length ;i++ )
	{
		id = formy[i].name;
		_id = formy[i].id;
		if (!formy[i].name.length || !formy[i].id.length || formy[i].parentNode.tagName == 'BODY')
			continue;

		nazwa_formatki = formatki[_id];
		form_select.length++;
		form_select.options[form_select.length-1].value = id
		form_select.options[form_select.length-1].text = nazwa_formatki;

		<?
			if (strlen($formtovalidate))			
			{	
		?>
			if (id == '<? echo $formtovalidate ?>')
			{
				form_select.options[form_select.length-1].selected = true;
				getFormElements_<? echo $sid ?>(id);
			}
		<?
			}
		?>
	}

	function getFormElements_<? echo $sid ?>(id)
	{
		_form = document.getElementsByName(''+id+'');
		form = _form[0];
		contener = document.getElementById('contener_<? echo $sid ?>');
		var _html = '<input type="hidden" name="formname" value="'+id+'">';
		_html+= '<table><tr>';

		<?	echo $_js ?>
		if (NOEMPTY == null)
			var NOEMPTY = new Array();
		if (ALERT_NOEMPTY == 'undefined')
			var ALERT_NOEMPTY = new Array;
		if (REREG == 'undefined')
			var REREG = new Array;
		if (ALERT_EREG == 'undefined')
			var ALERT_EREG = new Array;
		for (ii=0; ii < form.elements.length ;ii++ )
		{
			if (!form.elements[ii].name.length) continue;
			_html+= '<tr>';
			_html+= '<td>'+form.elements[ii].name+'</td>';
			val = '';
			checked = '';

			_indx = form.elements[ii].name;
			indx = _indx.replace('[','_');
			indx = indx.replace(']','_');
//			alert(_indx);
//			alert(REREG[indx]);
			if (NOEMPTY[''+indx+''])
				checked = 'checked';
			if (REREG[''+indx+''] == null)
				REREG[''+indx+''] = "";
			if (ALERT_NOEMPTY[''+indx+''] == null)
				ALERT_NOEMPTY[''+indx+''] = "";
			if (ALERT_EREG[''+indx+''] == null)
				ALERT_EREG[''+indx+''] = "";
			

			_html+= '<td><input type="checkbox" name="NOEMPTY['+_indx+']" value="1" '+checked+' title=\"Nie dopuszczaj pustych wartosci\"></td>';
			_html+= '<td><input type="input" id="'+indx+'<? echo $sid ?>" ondblclick="getPatternHelper(this.id)" name="REREG['+_indx+']" value="'+REREG[''+indx+'']+'" title=\"Wartosc zgodna tylko z szablonem\"></td>';
			_html+= '<td><input type="input" name="ALERT_NOEMPTY['+_indx+']" value="'+ALERT_NOEMPTY[''+indx+'']+'" title=\"Komunikat przy pustej wartosci\"></td>';
			_html+= '<td><input type="input" name="ALERT_EREG['+_indx+']" value="'+ALERT_EREG[''+indx+'']+'" title=\"Komunikat przy wartosci niezgodnej z szablonem\"></td>';
		}
		_html+= '</tr></table><INPUT TYPE="submit" value="Zapisz walidacjê">';
//		alert(_html);
		contener.innerHTML = _html;
	}
	
	var patname = '';

	function getPatternHelper(id)
	{
		helpercontener = document.getElementById('hc_<? echo $sid ?>');		
		opt_names = new Array('Email','NIP','Pesel');

		start_size_w = 200;
		start_size_h = 50;

		start_popup_left = event.x+document.body.scrollLeft ;
		start_popup_top = event.y+document.body.scrollTop ;

		pos = document.getElementById(''+id+'');
		
		start_popup_left = findPosX(pos)-findPosX(helpercontener)+2;
		start_popup_top = findPosY(pos)-findPosY(helpercontener)+23;

		var _help = '<div style="background-color:#FFF;border:1px solid #000;position:absolute;width:'+start_size_w+';height:'+start_size_h+';top:'+start_popup_top+';left:'+start_popup_left+'">';

		for (i=0;i<opt_names.length; i++)
		{
			_help+= '&nbsp;&nbsp;<span style="cursor:pointer" onMouseDown="document.getElementById(\''+id+'\').value=\'['+opt_names[i]+']\';document.getElementById(\'hc_<? echo $sid ?>\').innerHTML=\'\'">'+opt_names[i]+'</span><br>';
		}

		_help+= '</div>';
		
		helpercontener.innerHTML = _help;

	}

function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1) 
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

 

  function findPosY(obj)
  {
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

</script>
<?
	

?>
