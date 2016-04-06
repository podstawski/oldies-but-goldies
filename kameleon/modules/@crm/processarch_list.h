<?
	global $size;
	global $PROC,$MODULES;

	$size=$WEBTD->size;
	if (!$size) $size=5;

	$PROC[search]=trim(addslashes(stripslashes($PROC[search])));


	
	if (!function_exists("arch_process_list_init"))
	{
		function arch_process_list_init(&$iter_obj)
		{
			global $MODULES,$page,$adodb,$SERVER_ID,$PROC;

			$iter_obj->i=0;
			$iter_obj->count=0;

			$s=$PROC[search];
			if (!strlen($s)) return;			

			$customer=customer_id_on_page($page)+0;
			if ($customer) $c_warunek="AND p_customer=$customer";


				$query="SELECT * 
					FROM crm_proc
					WHERE p_server=$SERVER_ID
					AND p_d_end IS NOT NULL
					$c_warunek
					AND p_desc ~* '$s'
					ORDER BY p_d_end DESC,p_id DESC";

	
			$res=$adodb->Execute($query);

			if ($res)
			{
				$iter_obj->count=$res->RecordCount();
				$iter_obj->result=$res;
			}
		}
	}

	if (!function_exists("arch_process_list_item"))
	{
		function arch_process_list_item(&$iter_obj)
		{
			$PROC=$iter_obj->result->FetchRow($iter_obj->i);
			$iter_obj->i++;
			$PROC[lp]=$iter_obj->i;
			$PROC[p_href]="javascript:CrmReport('proc',$PROC[p_id])";

			_RevertDate($PROC[p_d_deadline]);
			_RevertDate($PROC[p_d_start]);
			_RevertDate($PROC[p_d_end]);
			_RevertDate($PROC[p_d_create]);
			return ($PROC);
		}
	}

	if (!function_exists("arch_process_list_end"))
	{
		function arch_process_list_end(&$iter_obj)
		{
			return ($iter_obj->count <= $iter_obj->i);
		}
	}
	



	$PROC[function_loop_begin]="arch_process_list_init";
	$PROC[function_loop_item]="arch_process_list_item";
	$PROC[function_loop_end]="arch_process_list_end";
	
	$PROC[search].="";
	$PROC[self]=$self;
	$PROC[next]=$next;

	_display_view($MODULES->crm->files->procarch_list);


?>
