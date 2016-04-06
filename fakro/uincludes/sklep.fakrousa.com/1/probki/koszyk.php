<DIV id="_js_kosz_id"></DIV>
<CENTER id=basketOrderButton><A id=basketOrderButtonHref class=orderButton href="<?php echo $next; ?>"></A></CENTER>
<SCRIPT>
var KOSZJS;
//Delete_Cookie('koszyk_tai');
function init()
{
	KOSZJS= new Koszyk(document.getElementById("_js_kosz_id"), document.getElementById("basketOrderButton"));
	KOSZJS.load();
}

window.setTimeout(init,100);

</SCRIPT>
