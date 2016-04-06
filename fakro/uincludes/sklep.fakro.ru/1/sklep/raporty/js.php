<script>
function objToClipboard(id)
{

	obj = getObject(''+id+'');

	window.clipboardData.setData("Text",'<table id="wydruk" cellspacing=0 cellpadding=0 class="list_table">'+obj.innerHTML+'</table>');
	
	alert('<? echo sysmsg("Copy succesful","system"); ?>');
}
</script>
