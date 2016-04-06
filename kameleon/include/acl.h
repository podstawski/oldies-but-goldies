<?
	$query="SELECT * FROM kameleon_acl WHERE
		ka_server=$SERVER_ID
		AND ka_oid=$resource_id AND ka_resource_name='$resource_name'
		ORDER BY ka_username";

	$res=$adodb->Execute($query);
	for ($i=0;$i<$res->RecordCount();$i++ )
	{
		parse_str(ado_explodeName($res,$i));
		
		echo "
			<tr class=\"k_form\" >
				<td align=\"right\">
					$ka_username <input type=\"hidden\"  name=\"ACL[$ka_username]\" value=\"$ka_username\">

					<img src=\"img/i_delete_n.gif\" 
						onclick=\"this.parentNode.parentNode.disabled=1; 
								this.src='img/spacer.gif';
								this.onmouseout=null;
								this.onmouseover=null;\"
						onmouseover=\"this.src='img/i_delete_a.gif'\" 
						onmouseout=\"this.src='img/i_delete_n.gif'\"  
						border=0 width=23 height=22 align=\"absMiddle\"
						alt=\"".label("Delete whole record for")." $ka_username\">



				</td>
			<td>";

		foreach ($resource_rights AS $right)
		{
			$ch=strstr($ka_rights,$right)?"checked":"";
			echo "<input $ch type=\"checkbox\" value=\"1\" name=\"ACL[$ka_username:$right]\"> ".label("ACL_$right")." &nbsp; ";

		}
		echo "
			</td>
		</tr>";

	}


?>

<tr class="k_form" id="acl_username_tr" style="display:none">
	<td align="right" id="acl_username_td">&nbsp</td>
	<td><input type="hidden" id="acl_username" name="ACL[_new_username_of_kameleon_acl]" value="">
	<?
		foreach ($resource_rights AS $right)
		{
			echo "<input type=\"checkbox\" value=\"1\" name=\"ACL[_new_username_of_kameleon_acl:$right]\"> ".label("ACL_$right")." &nbsp; ";

		}

	?>
	</td>
</tr>

<input type="hidden" name="ACL[resource_name]" value="<?echo $resource_name?>">
<input type="hidden" name="ACL[resource_id]" value="<?echo $resource_id?>">



<script>

    function NewACLuser(u)
    {
	document.all.acl_username_tr.style.display="inline";
	document.all.acl_username_td.innerHTML=u;
	document.all.acl_username.value=u;
    }


</script>