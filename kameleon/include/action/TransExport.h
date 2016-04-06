<?
	include_once('include/transfun.h');
	include_once('include/utf8.h');



	$name=label8($lang).'.doc';

	Header("Content-Type: application/ms-word ; name=\"$name\"");
	Header("Content-Disposition: attachment; filename=\"$name\"");

?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="WebKameleon">
<meta name=Originator content="WebKameleon">

<!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:View>Normal</w:View>
  <w:SpellingState>Clean</w:SpellingState>
  <w:HyphenationZone>21</w:HyphenationZone>
  <w:ValidateAgainstSchemas/>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
 </w:LatentStyles>
</xml><![endif]--><!--[if !mso]><object
 classid="clsid:38481807-CA0E-42D2-BF39-B33AF135CC4D" id=ieooui></object>
<style>
st1\:*{behavior:url(#ieooui) }
</style>
<![endif]-->


<style>
 /* Style Definitions */
 .wkmain
	{
	mso-style-name:Standardowy;
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:"";
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";
	mso-ansi-language:#0400;
	mso-fareast-language:#0400;
	mso-bidi-language:#0400;
	width:18.5cm;
	}
</style>


<body>
<table border=1 cellspacing=0 cellpading=2 width="21.5cm" >
<tr>
	<td style='widtd:8.5cm' nowrap>----------------------------------------------------------------</td>
	<td style='widtd:8.5cm' nowrap>----------------------------------------------------------------</td>
	<td style='widtd:4.5cm' nowrap>-----------------------------------</td>
</tr>
<tr>
	<th style='width:8.5cm'><?echo label8($trans_srclang)?></th>
	<th style='width:8.5cm'><?echo label8($lang)?></th>
	<th style='width:4.5cm'><?echo label8('context')?></th>
</tr>

<?

	if (strlen($trans_order)) $trans_order.=',wt_sid';
	$moreand="";

	if ($trans_ch_nt) $moreand.=" AND wt_translation IS NULL";
	if (is_array($trans_ch_ex) && count($trans_ch_ex)) $moreand.=" AND wt_table NOT IN ('".implode("','",array_keys($trans_ch_ex))."')";

	if (strlen($trans_search))
	{
		$s=addslashes(utf82lang(stripslashes($trans_search),$trans_srclang));
		$moreand.=" AND wt_o_plain~*'$s'";
	}


	$sql="SELECT wt_o_plain,wt_sid,wt_t_plain,wt_table,wt_context,wt_similar,wt_path
			FROM webtrans 
			WHERE wt_server=$SERVER_ID AND wt_lang='$lang'
			AND wt_o_plain<>'' AND wt_verification IS NULL $moreand
			ORDER BY $trans_order,wt_sid";
	$res=$adodb->Execute($sql);
	$rc=$res->RecordCount();

	for ($i=0;$i<$rc;$i++)
	{
		$aen=ado_ExplodeName($res,$i);
		parse_str($aen);

		if (!strlen($wt_o_plain)) continue;

		if ($ch_saveall && strlen($wt_similar))
			foreach(explode(',',$wt_similar) AS $sim)
				if ($all[$sim]) continue 2;

		$all[$wt_sid]=1;

		$md5=md5(stripslashes($wt_o_plain));

		$wt_o_plain=nl2br(lang2utf8(stripslashes($wt_o_plain),$trans_srclang));
		$wt_t_plain=nl2br(lang2utf8(stripslashes($wt_t_plain)));
		$wt_path=lang2utf8(stripslashes($wt_path),$trans_srclang);

		if ($wt_context+0<0) $wt_context='Header/footer';
		if (strlen($wt_table)) $wt_context="$wt_table<br>$wt_context";
		
		echo "
		<tr>
			<td width='8.5cm' style='width:8.5cm' valign='top'>$wt_o_plain</td>
			<td width='8.5cm' id='$md5-$wt_sid' style='width:8.5cm' valign='top'>$wt_t_plain</td>
			<td width='4.5cm' style='width:4.5cm' valign='top'>$wt_context<br>$wt_path</td>
		</tr>
		";
	}

?>


</table>
</body>
</html>


<?
	exit();
?>