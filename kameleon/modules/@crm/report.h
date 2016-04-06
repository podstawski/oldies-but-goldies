<?
	global $REPORT,$MODULES;

	switch ($REPORT[obj])
	{
		case "task":
			global $TASK;
			module_select($MODULES->crm->files->task_master,"t_server=$SERVER_ID AND t_id=$REPORT[id]");
			$wynik = $TASK[t_desc];
			$title = $TASK[t_title];
			break;

		case "proc":
			global $PROC;
			module_select($MODULES->crm->files->proc_master,"p_server=$SERVER_ID AND p_id=$REPORT[id]");
			$wynik = $PROC[p_desc];
			$title = $PROC[p_title];
			break;
	
	}


	
?>
<div id="report_div" style="visibility:hidden">
<span style="cursor:hand" onClick="print();window.close();">print</span>
<?echo $wynik?>
<script>

	function CrmReport(obj,id)
	{
 		okno=open('<?echo $self; echo strstr($self,"?")?"&":"?";?>REPORT[id]='+id+'&REPORT[obj]='+obj+'&REPORT[i]=<?echo 1+$REPORT[i]?>',
			'report_<?echo time()?>',
                        "toolbar=0,location=0,directories=0,\
                        status=0,menubar=0,scrollbars=1,resizable=1,\
                        width=500,height=300, left=<?echo 100*($REPORT[i]+1)?>, top=<?echo 100*($REPORT[i]+1)?>");


	}
</script>
</div>

<?if (!strlen($wynik)) return;?>

<script>

	function onLoadReport()
	{
	  document.body.innerHTML = document.all["report_div"].innerHTML;
	  document.title = "<?echo $title?>";
	}
	setTimeout("onLoadReport()",10);
</script>
<?
	exit();
?>