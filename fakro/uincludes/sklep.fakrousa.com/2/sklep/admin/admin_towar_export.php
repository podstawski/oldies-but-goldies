<?
	
	$form = "
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TowarEksport\">
	<INPUT TYPE=\"radio\" NAME=\"form[all]\" value=\"1\" checked> Wszystkie towary<br>
	<INPUT TYPE=\"radio\" NAME=\"form[all]\" value=\"0\"> Tylko aktywne<br>
	<INPUT TYPE=\"text\" NAME=\"form[podmiot]\"> Nazwa podmiotu<br>
	<INPUT TYPE=\"submit\" value=\"Eksportuj\">	
	</FORM>
	";

	echo $form;
?>
