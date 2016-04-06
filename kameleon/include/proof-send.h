


<div id="proofSendDiv" style="position:absolute; display:none; left:0px; top: 0px; width:300px; background-color:white;z-index:1">
	<fieldset style="width:100%; margin: 5px 5px 0px 5px">
	<legend id="proofSendLegend"></legend>
	<form name="proofSendForm" action="<?echo $SCRIPT_NAME?>" method="post" class="k_td" style="margin:0px">
		<input type="hidden" name="page" value="<?if (!is_array($page)) echo $page?>">
		<input type="hidden" name="action" value="">

		<?echo label('Type your comment')?>:<br />
		<textarea class="k_textarea" style="width:100%; height:70px" name="proof_comment"></textarea>

		
		<p align="right">
		<input type="button" class="k_button" value="<?echo label('Cancel')?>" 
			onClick="document.getElementById('proofSendDiv').style.display='none'">
		<input type="submit" class="k_button" value="<?echo label('Send')?>">
		</p>
	</form>
	</fieldset>
	<img src="img/spacer.gif" width="1" height="1">
</div>




<script language="JavaScript">
	
	var commentsArray=new Array();

	//document.getElementById('proofSendDiv').style.visibility='hidden';
	document.getElementById('proofSendDiv').style.display='none';

	function proofSend(action,title,page)
	{
		
		document.proofSendForm.action.value=action;
		
		if (page!=null) document.proofSendForm.page.value=page;
		document.getElementById('proofSendLegend').innerHTML=title;

		div=document.getElementById('proofSendDiv');

		//div.style.top=event.clientY;
		//div.style.left=event.clientX;
		

		div.style.display='block';
		document.proofSendForm.proof_comment.focus();
		return false;
	}
	

	function proofComment(page)
	{

		a=commentsArray[page].split('|');

		if (a.length<2) return;
		
		var txt='';
		for (i=0;i<a.length ;i++ )
		{
			txt+=a[i]+'\r\n';
		}
		alert(txt);

		return false;
	}
</script>

<?

	function sa_osoby_bez_ftpa_ale_proof($page)
	{
		global $SERVER_ID,$adodb,$kameleon;
		static $wynik;

		if (strlen($wynik[$page])) return $wynik[$page];

	
		
		$sql="SELECT proof,username 
				FROM rights WHERE server=$SERVER_ID 
				AND (ftp=0 OR ftp IS NULL)";
		$res=$adodb->execute($sql);
		
		for ($i=0;$i<$res->recordCount() ; $i++)
		{
			$proof='';
			parse_str(ado_explodeName($res,$i));
			//ACL:TODO
			
			//if ($kameleon->checkRight('proof','page',$page))
			if (checkRights($page,$proof)) 
			{
				$wynik[$page]=1;
				return 1;
			}
		}

		$wynik[$page]=0;
		return 0;
	}

