<?
global $szukaj, $list, $form;

if(!$size) $size=25;
if(!is_array($szukaj) && !strlen($tylko_dla)) return;

if($szukaj[pu] == 1) $cond .= " AND projekt_p_uzytkowa <= '100'";
if($szukaj[pu] == 2) $cond .= " AND (projekt_p_uzytkowa >= '100' AND projekt_p_uzytkowa <= '150')";
if($szukaj[pu] == 3) $cond .= " AND projekt_p_uzytkowa >= '150'";

if(strlen($szukaj[pp]))	$cond .= " AND projekt_piwnica = '".addslashes(stripslashes($szukaj[pp]))."'";
if(strlen($szukaj[pg]))	$cond .= " AND projekt_garaz = '".addslashes(stripslashes($szukaj[pg]))."'";


if(!isset($form[projekt_id])) {
	//$fakrodb->debug=1;
	$sql = "SELECT * FROM dom_projekt WHERE projekt_status='1' $cond ORDER BY projekt_id,projekt_nazwa";
	$res = $fakrodb->execute($sql);
	
	if(!$list[ile]) {
		$query="SELECT count(projekt_id) AS c FROM dom_projekt WHERE projekt_status='1' $cond ";
		$res2 = $fakrodb->execute($query);
		parse_str(ado_explodename($res2,0));
		$list[ile]=$c;
		}
	
	if($KAMELEON_MODE)
		$self .= "&szukaj[pu]=".$szukaj[pu]."&szukaj[pp]=".$szukaj[pp]."&szukaj[pg]=".$szukaj[pg];
		else
		$self .= "?szukaj[pu]=".$szukaj[pu]."&szukaj[pp]=".$szukaj[pp]."&szukaj[pg]=".$szukaj[pg];
	
	$navi=$size?navi($self,$list,$size):"";
	
	if(strlen($navi))
		$res = $fakrodb->SelectLimit($sql,$size,$list[start]+0);
		else
		$res = $fakrodb->Execute($sql);
	
	if(!$res->RecordCount()) {
		echo '<br><br><div align="center"><strong>Nie znaleziono ¿adnych wpisów o podanych kryteriach lub lista jest pusta.</strong></div>';
		return;
		}
	
	echo $navi;
	echo '<br><br>
	<TABLE class="list_table">
	
	<col>
	<col width="120">
	<col width="120">
	
	<thead>
	<TR>
		<TD align="center">nazwa</TD>
		<TD align="center">pow. u¿ytkowa</TD>
		<TD align="center">pow. zabudowy</TD>
	</TR>
	</thead>
	<tbody>';
	
	
	for($i=0; $i < $res->RecordCount(); $i++) {
		parse_str(ado_explodename($res,$i));
		echo '
			<TR class="'.(($i && ($i%2))?"bg1":"bg0").'">
				<TD class="name" valign="top"><a href="'.$self.'&form[projekt_id]='.$projekt_id.'">'.stripslashes($projekt_nazwa).'</a></TD>
				<TD align="center">'.$projekt_p_uzytkowa.' m<sup>2</sup></TD>
				<TD align="center">'.$projekt_p_zabudowy.' m<sup>2</sup></TD>
			</TR>';
		}
	
	echo '</tbody></table>';
	}else{
?>
<style>
.list_table th, .list_table thead td {
	background-color:#EBEBEB;
	color:#000000;
	font-size:9px;
	font-family:verdana;
	font-weight:bold;
	padding:5px 5px;
	text-align:center;
	}
.list_table th, .list_table td {
	border-color:#E5E5E5;
	border-style:solid;
	border-width:0pt 0pt 1px 1px;
	font-size:12px;
	vertical-align:bottom !important;
	padding:2px 2px 2px 2px;
	}
.list_table th, .list_table tbody td {
	text-align:center;
	}
.list_table_dane th, .list_table_dane td {
	color:#000000;
	font-size:9px;
	font-family:verdana;
	padding:5px 5px;
	}
.list_table_dane .cc {
	background-color:#EBEBEB;
	font-weight:bold;
	text-align:right;
	width:65px;
	}
.list_table_dane .cv {
	}
</style>
<?
	$sql = "SELECT * FROM dom_projekt LEFT JOIN dom_firma ON (dom_projekt.firma_id = dom_firma.firma_menu) WHERE projekt_id='$form[projekt_id]'";
	parse_str(query2url($sql));
	
	echo '<br><br>
	<a href="javascript: void(0);" onclick="history.go(-1);return false;">Powrót do listy</a>
	<br><br>
	
	<div align="right"><a href="'.$firma_link.'" target="_blank"><img src="'.$UIMAGES.'/'.$firma_logo.'" alt="'.$firma_nazwa.'" border="0"></a></div>
	
	<TABLE class="list_table">
	<col align="center"><col align="center"><col align="center"><col align="center"><col align="center">
	<thead>
	<tr>
		<td>powierzchnia u¿ytkowa</td>
		<td>powierzchnia zabudowy</td>
		<td>k±t nachylenia dachu</td>
		<td>wysoko¶æ budynku</td>
		<td>minimalne wymiary dzia³ki</td>
	</tr>
	<tr>
		<td>[m<sup>2</sup>]</td>
		<td>[m<sup>2</sup>]</td>
		<td>[&deg;]</td>
		<td>[m]</td>
		<td>[m<sup>2</sup>]</td>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>'.$projekt_p_uzytkowa.'</td>
		<td>'.$projekt_p_zabudowy.'</td>
		<td>'.$projekt_k_nachylenia.'</td>
		<td>'.$projekt_w_budynku.'</td>
		<td>'.$projekt_p_dzialki.'</td>
	</tr>
	</tbody>
	</table>
	
	<br>
	<h2>Nazwa projektu:</h2> '.$projekt_nazwa.'<br><br>
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top" width="290"><img src="" alt="" border="0" width="280" height="200"></td>
		<td valign="top">
		
		<table width="100%" class="list_table_dane">
		<col class="cc"><col class="cv">
		<tr>
			<td>¦ciany:</td>
			<td>'.$projekt_sciany.'</td>
		</tr>
		<tr>
			<td>Stropy:</td>
			<td>'.$projekt_stropy.'</td>
		</tr>
		<tr>
			<td>Dach:</td>
			<td>'.$projekt_dach.'</td>
		</tr>
		<tr>
			<td>Pokrycie dachu:</td>
			<td>'.$projekt_pokrycie.'</td>
		</tr>
		<tr>
			<td>Elewacje:</td>
			<td>'.$projekt_elewacje.'</td>
		</tr>
		<tr>
			<td>Okna dachowe:</td>
			<td>'.$projekt_okna.'</td>
		</tr>
		</table>
		
		</td>
	</tr>
	<tr>
		<td colspan="2"><br><h2>Charakterystyka domu:</h2><br>'.htmlspecialchars($projekt_charakterystyka).'</td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>';
	echo '</table>';
	}
?>
