<?
	global $size;

	$size=$WEBTD->size;
	if (!$size) $size=5;

	
	if (!function_exists("process_list_init"))
	{
		function process_list_init(&$iter_obj)
		{
			global $MODULES,$page,$adodb,$SERVER_ID,$KAMELEON,$size;

			$iter_obj->i=0;
			$iter_obj->count=0;

			$customer=customer_id_on_page($page)+0;

			if ($customer) 
			{
				$query="SELECT * 
					FROM crm_proc
					WHERE p_customer=$customer
					AND p_server=$SERVER_ID
					AND p_d_end IS NULL
					ORDER BY p_d_deadline";
			}
			else
			{

				$query="SELECT *
					FROM crm_proc,crm_recent
					WHERE p_id=cr_id
					AND cr_username='$KAMELEON[username]'
					AND p_server=$SERVER_ID
					AND p_d_end IS NULL
					AND cr_file_id='proc_master'
					ORDER BY cr_timestamp DESC
					LIMIT $size";


			}

			$res=$adodb->Execute($query);
			if ($res)
			{
				$iter_obj->count=$res->RecordCount();
				$iter_obj->result=$res;
			}
		}
	}

	if (!function_exists("process_list_item"))
	{
		function process_list_item(&$iter_obj)
		{
			global $MODULES,$CUSTOMER;

			$PROC=$iter_obj->result->FetchRow($iter_obj->i);
			$iter_obj->i++;
			$PROC[p_href]=kameleon_href("","",$PROC[p_page_id]);
			module_select($MODULES->crm->files->customer_master,"c_id=".$PROC[p_customer]);

			$PROC = array_merge($PROC,$CUSTOMER);
			_RevertDate($PROC[p_d_deadline]);
			_RevertDate($PROC[p_d_start]);
			_RevertDate($PROC[p_d_end]);
			_RevertDate($PROC[p_d_create]);
			return ($PROC);
		}
	}

	if (!function_exists("process_list_end"))
	{
		function process_list_end(&$iter_obj)
		{
			return ($iter_obj->count <= $iter_obj->i);
		}
	}
	


	global $PROC,$MODULES;

	$PROC[function_loop_begin]="process_list_init";
	$PROC[function_loop_item]="process_list_item";
	$PROC[function_loop_end]="process_list_end";
	
	_display_view($MODULES->crm->files->proc_list);


?>
