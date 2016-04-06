<?
	global $page_id,$MODULES;

	$_html="@" . $MODULES->crm->name . "/" . $MODULES->crm->files->proc_list->file;

	$query="SELECT count(*) AS c FROM webtd 
		WHERE server=$SERVER_ID AND page_id=$page
		AND ver=$ver AND lang='$lang'
		AND html='$_html'";
	parse_str(ado_query2url($query));


	global $PHP_AUTH_USER,$DEFAULT_TD_LEVEL,$pri,$page_id;

	push($page);
	push($page_id);
	push($WEBTD);


	if (!$c)
	{
		$page_id=$page;
		$WEBTD="";
		$WEBTD["html"]=$_html;
		$WEBTD["title"]=kameleon_global($MODULES->crm->files->proc_list->label);
		include("include/action/DodajTD.h");
	}

	global $PROC,$HTTP_GET_VARS;

	



	if (isset($HTTP_GET_VARS[p_title]) )
	{
		$PROC[p_title]=$HTTP_GET_VARS[p_title];
	}
	else
	{
		$query="SELECT count(*) AS c FROM crm_proc WHERE 
			 p_server=$SERVER_ID AND p_customer=$PROC[p_customer] ";
		parse_str(ado_query2url($query));
		$PROC[p_title]=label("New process")." ".($c+1);
	}

	$PROC[p_customer]=customer_id_on_page($page);
	$PROC[p_author]=$PHP_AUTH_USER;
	$PROC[p_d_create]="(CURRENT_DATE)";

	$page_id=-1;
	$referer=$page;
	include("include/action/DodajStrone.h");
	module_update($MODULES->crm->files->proc_master);

	$_html="@" . $MODULES->crm->name . "/" . $MODULES->crm->files->proc_master->file;
	$WEBTD="";
	$WEBTD["html"]=$_html;
	$WEBTD["title"]=$PROC[p_title];
	include("include/action/DodajTD.h");

	$WEBTD=pop();
	$page_id=pop();
	$page=pop();
?>