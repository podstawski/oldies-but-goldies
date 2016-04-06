function statusbar(_contener, factor)
{	
	factor = Math.ceil(factor * 100);
	_contener.innerHTML = "<div class=\"statusb\"><img class=\"sbi\" style=\"width:"+factor+"%\" src='"+IMAGES+"/sp.gif'></div>";
}
