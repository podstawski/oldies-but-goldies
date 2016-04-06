<style>
.tf input {
	border:1px solid black;
	width:180px;
	}

.tf .bt {
	border:1px solid black;
	width:100px;
	}
.k_button {
	background-color:#F0F0F0;
	border-style:outset;
	color:black;
	font-family:Verdana;
	font-size:10px;
	font-size-adjust:none;
	font-stretch:normal;
	font-style:normal;
	font-variant:normal;
	font-weight:normal;
	line-height:normal;
	text-decoration:none;
	}
</style>

<?
$method=($KAMELEON_MODE?"POST":"GET");

global $szukaj, $form;

if(!isset($form[projekt_id])) {
	echo '
	<div align="right">
	<table border="0">
	<form method=get action="'.$self.'">
	<tr>
		<td align="right">wielkość domu :</td>
		<td>
		<select name="szukaj[pu]">
			<option value="1" '.((htmlspecialchars($szukaj[pu])==1)?"selected":"").'>Do 100 m2</option>
			<option value="2" '.((htmlspecialchars($szukaj[pu])==2)?"selected":"").'>100 - 150 m2</option>
			<option value="3" '.((htmlspecialchars($szukaj[pu])==3)?"selected":"").'>Powyżej 150 m2</option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right">piwnica :</td>
		<td>
		<select name="szukaj[pp]">
			<option value="1" '.((htmlspecialchars($szukaj[pp])==1)?"selected":"").'>TAK</option>
			<option value="0" '.((htmlspecialchars($szukaj[pp])==0)?"selected":"").'>NIE</option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right">garaż :</td>
		<td>
		<select name="szukaj[pg]">
			<option value="1" '.((htmlspecialchars($szukaj[pg])==1)?"selected":"").'>TAK</option>
			<option value="0" '.((htmlspecialchars($szukaj[pg])==0)?"selected":"").'>NIE</option>
		</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><INPUT TYPE="submit" value="Szukaj projektu" class="k_button" onClick="submit()"></td>
	</tr>
	</FORM>
	</TABLE>
	</div>';
	}
?>