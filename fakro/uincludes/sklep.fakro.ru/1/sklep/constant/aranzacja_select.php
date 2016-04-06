<?

//08.06.2006, 10:30, autor: Robson, modyfikacja selectow aby byГo rѓwnieП bez $typ i wtedy tylko jeden select

//by Robson
$selectKolStyle="style=\"width:250px;\"";
$selectTypStyle="style=\"display:none;\"";


if ($ver==10) return;

parse_str($costxt);

if (!strlen($kolor) || !strlen($typ))
{
	echo "Nie zdefiniowano typu lub koloru...";
	return;
}

$selectTypStyle="style=\"display:none;\"";

function getAllSubcategories($id)
{
	global $adodb;
	
	$sql = "SELECT ka_id FROM kategorie WHERE ka_parent = $id";
	$res = $adodb->execute($sql);
	if ($res->RecordCount())
	{
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$ret.= ",".getAllSubcategories($ka_id);
		}
		return substr($ret,1);
	}
	else
		return $id;
}	

$sel_kolor = "<select $selectKolStyle name=\"kolor\" id=\"kol\" onChange=\"wSetSelect(this,document.getElementById('typ'),'typ')\"><option>".sysmsg('Choose','system')."</option>";
$sel_typ = "<select $selectTypStyle name=\"typ\" id=\"typ\" onChange=\"wSetSelect(document.getElementById('kol'),this,'kolor')\"><option>".sysmsg('Choose','system')."</option>";


$sql = "SELECT ka_nazwa, ka_id FROM kategorie WHERE ka_parent = $kolor ORDER BY ka_nazwa";
$res = $adodb->execute($sql);

$js = "var _produkty = new Array();
	   var kolory = new Array();
	   var typy = new Array();	   
	   ";
for ($i=0; $i < $res->RecordCount(); $i++)
{
	parse_str(ado_explodename($res,$i));
	$sub_kolory[$ka_id] = "$ka_id,".getAllSubcategories($ka_id);
	$js.= "kolory['$ka_id'] = '".sysmsg($ka_nazwa,'palety')."'\n";

	$sql = "SELECT COUNT(tk_to_id) AS anyone FROM towar_kategoria WHERE tk_ka_id IN (".$sub_kolory[$ka_id].")";
	parse_str(ado_query2url($sql));
	if (!$anyone) continue;

	$sel_kolor.= "<option value=\"$ka_id\" ".($skolor==$ka_id?"selected":"").">".sysmsg($ka_nazwa,'palety')."</option>";



}

$sql = "SELECT ka_nazwa, ka_id FROM kategorie WHERE ka_parent = $typ ORDER BY ka_nazwa";
$res = $adodb->execute($sql);


for ($i=0; $i < $res->RecordCount(); $i++)
{
	parse_str(ado_explodename($res,$i));
	$sub_typy[$ka_id] = "$ka_id,".getAllSubcategories($ka_id);
	$js.= "typy['$ka_id'] = '".sysmsg($ka_nazwa,'palety')."'\n";

	$sql = "SELECT COUNT(tk_to_id) AS anyone FROM towar_kategoria WHERE tk_ka_id IN (".$sub_typy[$ka_id].")";
	parse_str(ado_query2url($sql));
	if (!$anyone) continue;

	$sel_typ.= "<option value=\"$ka_id\" ".($styp==$ka_id?"selected":"").">".sysmsg($ka_nazwa,'palety')."</option>";

}


//echo "<pre>";print_r($sub_typy); echo "</pre>";

reset($sub_kolory);

while(list($kolor_key, $kolor_val) = each ($sub_kolory))
{
	$kolor_val = implode(",",array_unique(explode(",",$kolor_val)));

	$sql = "SELECT tk_to_id FROM towar_kategoria WHERE tk_ka_id IN ($kolor_val)";
	$res = $adodb->execute($sql);
	$znalezione_kolory = array();
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$znalezione_kolory[] = $tk_to_id;
	}

	$js.= "_produkty['$kolor_key'] = new Array();\n";
	reset($sub_typy);
	if (is_array($sub_typy))
	while(list($typ_key, $typ_val) = each ($sub_typy))
	{
		$typ_val = implode(",",array_unique(explode(",",$typ_val)));

		$sql = "SELECT tk_to_id FROM towar_kategoria WHERE tk_ka_id IN ($typ_val)";
		$res = $adodb->execute($sql);
		$znalezione_typy = array();
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$znalezione_typy[] = $tk_to_id;
		}
/*		
		$sql = "SELECT COUNT(jest) AS ile FROM (SELECT COUNT(tk_to_id) AS jest FROM towar_kategoria WHERE tk_ka_id IN ($kolor_val,$typ_val) GROUP BY tk_to_id) AS ilosci WHERE jest > 1";
		$adodb->debug=1;
		parse_str(ado_query2url($sql));
		$adodb->debug=0;
*/
		$js.= "_produkty['$kolor_key']['$typ_key'] = ".count(array_intersect($znalezione_kolory,$znalezione_typy)).";\n";
	}
}



$sel_kolor.= "</select>";
$sel_typ.= "</select>";


$style=$cos?'visibility:hidden':'';

$ret.= "<form style=\"$style\" method=".($KAMELEON_MODE?"post":"get")." name=\"arange\" action=\"$next\"><table class=\"asel\">";
$ret.= "<tr>";
$ret.= "<td>".sysmsg('Choose','system')."</td>";
$ret.= "<td>";
$ret.= $sel_kolor;
$ret.= "</td>";
$ret.= "<td>";
$ret.= $sel_typ;
$ret.= "</td>";
$ret.= "<td>";
//$ret.= "<img onClick=\"validateSel()\" style=\"cursor:pointer\" src=\"".$IMAGES."/sklep/sort_$lang.gif\">";
$ret.= "<img onClick=\"validateSel()\" style=\"cursor:pointer\" src=\"".$UIMAGES."/sb/sort_$lang.gif\">";
$ret.= "</td>";
$ret.= "</tr>";
$ret.= "</table></form>";
echo $ret;
?>
<script>
	<? echo $js;?>
	function validateSel()
	{
		if (document.getElementById('kol').value == "")
		{
			//document.getElementById('typ').value == ""		
			alert('Proszъ wybraц kolor.');
			return;
		}	
		document.arange.submit();
	}

	function keyExists(sel,key)
	{
		var i,len,o_key, result;
		
		result=false;
		len=sel.length;
		for (i=0;i<len;i++)
		{
			o_key=sel.options[i].value;
			if (key==o_key)
			{ 
				result=true;
				break;
			}
		}
		return result;
	}

	function wResetSelect(obj,txt)
	{
		if (obj == null) return false;
		sel_len=obj.length;
		for (i=0;i<sel_len;i++)
		{
			obj[i]=null;
		}
		obj.length=0;
		last=obj.length;
		nowy= new Option(txt,'',0,1);
		obj[last] = nowy;
	}

	function findAndSelect(obj,val)
	{
		if (obj == null || val == null)
			return false;

		for (i=0; i < obj.length; i++)
		{
			if (obj.options[i].value == val)
			{
				obj.options[i].selected = true;
				return true;
			}
		}
		return false;
	}

	function wSetSelect(obj1,obj2,co)
	{
		dane = _produkty;
		if (co=='kolor')
		{
			var poszukaj_potem = obj1.value;
			wResetSelect(obj1,'kolorѓw');
			var klucz = obj2.value;
		}
		else
		{
			var poszukaj_potem = obj2.value;
			wResetSelect(obj2,'typѓw');
			var klucz = obj1.value;
		}

		var kolor = "";
		var typ = "";
		var jest = null;
	
		for(kolor in dane)
		{
			if (isNaN(kolor)) continue;
			for(typ in dane[''+kolor+''])
			{
				if (isNaN(typ)) continue;
				if (klucz != '')
				{
					if (co=='kolor')
						jest = dane[''+kolor+''][''+klucz+''];
					else
						jest = dane[''+klucz+''][''+typ+''];
				}
				else
					jest = dane[''+kolor+''][''+typ+''];

				if (jest > 0)
				{
					if (co=='kolor' && !keyExists(obj1,kolor))
					{
						obj1.length++;
						obj1.options[obj1.length-1].value = kolor;
						obj1.options[obj1.length-1].text = kolory[''+kolor+''];
					}
					else if (!keyExists(obj2,typ))
					{
						obj2.length++
						obj2.options[obj2.length-1].value = typ;
						obj2.options[obj2.length-1].text = typy[''+typ+''];

					}
				}

			}
		}

		if (co=='kolor')
			findAndSelect(obj1,poszukaj_potem);
		else
			findAndSelect(obj2,poszukaj_potem);

	}
		function setColor(id)
		{
			wResetSelect(document.getElementById('typ'),'typѓw');
			wSetSelect(document.getElementById('kol'),document.getElementById('typ'),'kolor');
			findAndSelect(document.getElementById('kol'),''+id+'');
			wSetSelect(document.getElementById('kol'),document.getElementById('typ'),'typ');
			//alert('Wybierz typ.');
			document.arange.submit();
		}

</script>
