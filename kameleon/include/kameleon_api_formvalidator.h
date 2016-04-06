<?
	global $editmode;
	global $WEBTD,$adodb,$SERVER_ID,$ver,$lang,$page;


	if (!$editmode)
	{
		include('include/formvalidator.h');
		return;
	}

	$save_validation=label('Save validation');
	$value_matches_regex_pattern=label('Value must match the regex pattern. Dblclick for examples.');
	$warrning_when_empty=label('Notice when empty value');
	$warrning_when_mismatch=label('Notice when pattern mismatch');
	$no_empties=label('No empty values');

	
	$xml = $WEBTD->xml;

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

		$xml = serialize(array($formname,$NOEMPTY,$REREG,$ALERT_NOEMPTY,$ALERT_EREG));
		$sql = "UPDATE webtd SET xml = '$xml' WHERE sid = ".$sid;
		$adodb->execute($sql);
	}


//	echo "<pre>";print_r(unserialize(stripslashes($costxt)));echo "</pre>";
	list($formname,$NOEMPTY,$REREG,$ALERT_NOEMPTY,$ALERT_EREG) = unserialize(stripslashes($xml));
	
//	print_r($NOEMPTY);
//	print_r($REREG);
//	print_r($ALERT_NOEMPTY);
//	print_r($ALERT_EREG);

	$_js = "var NOEMPTY".$sid." = new Array;\n";
	if (is_array($NOEMPTY))
	{
		reset($NOEMPTY);
		while (list($key,$val) = each($NOEMPTY))
		{			
			$key = str_replace("[","_",$key)."_";
			$_js.= "NOEMPTY".$sid."['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	$_js.= "var REREG".$sid." = new Array;\n";
	if (is_array($REREG))
	{
		reset($REREG);
		while (list($key,$val) = each($REREG))
		{
			$key = str_replace("[","_",$key)."_";
			$_js.= "REREG".$sid."['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	$_js.= "var ALERT_NOEMPTY".$sid." = new Array;\n";
	if (is_array($ALERT_NOEMPTY))
	{
		reset($ALERT_NOEMPTY);
		while (list($key,$val) = each($ALERT_NOEMPTY))
		{
			$key = str_replace("[","_",$key)."_";
			$_js.= "ALERT_NOEMPTY".$sid."['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	$_js.= "var ALERT_EREG".$sid." = new Array;\n";
	if (is_array($ALERT_EREG))
	{
		reset($ALERT_EREG);
		while (list($key,$val) = each($ALERT_EREG))
		{
			$key = str_replace("[","_",$key)."_";
			$_js.= "ALERT_EREG".$sid."['$key'] = '".addslashes(stripslashes(urldecode($val)))."';\n";
		}
	}
	if (strlen($formname) && !strlen($formtovalidate))
		$formtovalidate = $formname;

?>
<!--
<form method="post" action="<? echo $self ?>">
<input type="hidden" name="frmsid" value="<? echo $sid ?>">
<xselect id="formatka_<? echo $sid ?>" name="formtovalidate_<? echo $sid ?>" onChange="submit()"></select>
<xinput type="submit" value="Ustaw walidacjê">
</form>
-->
<fieldset style="width:99%; margin-left:2px;">
<legend><?echo label('Kameleon form validation')?></legend>
<?
	echo label('Looking for elements with the name declared as a table, e.g. general[name]');
?>

<form method="post" action="index.php?page=<? echo $page ?>">
<input type="hidden" name="formsid" value="<? echo $sid ?>">

<div id="contener_<? echo $sid ?>"></div>
<div id="hc_<? echo $sid ?>" style="position:relative;top:0;left:0;visible:hidden;height:0px"></div>
</form>
</fieldset>
<br />&nbsp;
<?
	$sql = "SELECT title, sid FROM webtd WHERE lang = '$lang'
			AND server = $SERVER_ID AND ver = $ver AND page_id = $page";

	$res = $adodb->execute($sql);

	$tmpsid = $sid;
	$js.= "var formatki".$sid." = new Array;\n";
	for ($i=0 ; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$js.= "formatki".$tmpsid."['kameleon_form_sid_$sid'] = '".addslashes(stripslashes($title))."'\n";
	}
	$sid=$tmpsid;

?>
<script>

	<? echo $js ?>

	var formy = document.forms;
//	var form_select = document.getElementById('formatka_<? echo $sid ?>');
//	form_select.length = 0;
	for (i=0; i < formy.length ;i++ )
	{

//		if (null==prompt(i+'/'+formy.length,'pre')) break;

		id = formy[i].name;
		_id = formy[i].id;

		if (!formy[i].name.length || !formy[i].id.length || formy[i].parentNode.tagName == 'BODY')
			continue;

		_tmp = _id.split("_");		
		sid = _tmp[_tmp.length-1];
//		alert(sid);

		nazwa_formatki = formatki<? echo $sid ?>[_id];
//		form_select.length++;
//		form_select.options[form_select.length-1].value = id
//		form_select.options[form_select.length-1].text = nazwa_formatki;

		<?
			if (strlen($formtovalidate) || strlen($sid))			
			{	
		?>
			if (id == '<? echo $formtovalidate ?>' || sid == '<? echo $sid ?>')
			{
//				form_select.options[form_select.length-1].selected = true;
				getFormElements_<? echo $sid ?>(id);
			}
		<?
			}
		?>
//		if (null==prompt(i+'/'+formy.length,'post')) break;
	}

	function getFormElements_<? echo $sid ?>(id)
	{
		_form = document.getElementsByName(''+id+'');
		form = _form[0];
		contener = document.getElementById('contener_<? echo $sid ?>');
		var _html = '<input type="hidden" name="formname" value="'+id+'">';
		_html+= '<table><tr>';

		<?	echo $_js ?>
		if (NOEMPTY<?	echo $sid ?> == null)
			var NOEMPTY<? echo $sid ?> = new Array();
		if (ALERT_NOEMPTY<?	echo $sid ?> == 'undefined')
			var ALERT_NOEMPTY<?	echo $sid ?> = new Array;
		if (REREG<?	echo $sid ?> == 'undefined')
			var REREG<?	echo $sid ?> = new Array;
		if (ALERT_EREG<? echo $sid ?> == 'undefined')
			var ALERT_EREG<? echo $sid ?> = new Array;

		for (subi=0; subi < form.elements.length ;subi++ )
		{
			//alert(form.elements[i].name.length);
			
			if (!form.elements[subi].name.length) continue;
			_html+= '<tr>';
			_html+= '<td>'+form.elements[subi].name+'</td>';
			val = '';
			checked = '';

			_indx = form.elements[subi].name;
			indx = _indx.replace('[','_');
			indx = indx.replace(']','_');
//			alert(_indx);
//			alert(REREG[indx]);
			if (NOEMPTY<?	echo $sid ?>[''+indx+''])
				checked = 'checked';
			if (REREG<?	echo $sid ?>[''+indx+''] == null)
				REREG<?	echo $sid ?>[''+indx+''] = "";
			if (ALERT_NOEMPTY<?	echo $sid ?>[''+indx+''] == null)
				ALERT_NOEMPTY<?	echo $sid ?>[''+indx+''] = "";
			if (ALERT_EREG<? echo $sid ?>[''+indx+''] == null)
				ALERT_EREG<?echo $sid ?>[''+indx+''] = "";
		
			_html+= '<td><input type="checkbox" name="NOEMPTY['+_indx+']" value="1" '+checked+' title=\"<?echo $no_empties?>\"></td>';
			_html+= '<td><input size="15" type="input" id="'+indx+'<? echo $sid ?>" ondblclick="getPatternHelper_<? echo $sid ?>(this.id)" name="REREG['+_indx+']" value="'+REREG<? echo $sid ?>[''+indx+'']+'" title=\"<? echo $value_matches_regex_pattern; ?>\"></td>';
			_html+= '<td><input size="15" type="input" name="ALERT_NOEMPTY['+_indx+']" value="'+ALERT_NOEMPTY<? echo $sid ?>[''+indx+'']+'" title=\"<?echo $warrning_when_empty?>\"></td>';
			_html+= '<td><input size="15" type="input" name="ALERT_EREG['+_indx+']" value="'+ALERT_EREG<? echo $sid ?>[''+indx+'']+'" title=\"<? echo $warrning_when_mismatch?>\"></td>';

		}

		_html+= '</tr></table><INPUT TYPE="submit" value="<? echo $save_validation?>">';
//		alert(_html);
		contener.innerHTML = _html;
		
	}
	
	var patname = '';

	function getPatternHelper_<? echo $sid ?>(id)
	{
		helpercontener = document.getElementById('hc_<? echo $sid ?>');		
		opt_names = new Array('Email','NIP','Pesel','Regon','Kod pocztowy');

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