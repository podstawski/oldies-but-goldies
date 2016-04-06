<?
	global $size;
	$size=$WEBTD->size;
	if (!$size) $size=5;
	
	if (!function_exists("customer_list_init"))
	{
		function customer_list_init(&$iter_obj)
		{
			global $MODULES,$page,$adodb,$SERVER_ID,$CUSTOMER,$KAMELEON,$size;

			$customer=customer_id_on_page($page)+0;

			$iter_obj->i=0;
			$iter_obj->count=0;

			$s=trim(addslashes(stripslashes($CUSTOMER[search])));

			if (strlen($s))
			{
				$query="SELECT c_id,c_parent 
					FROM crm_customer
					WHERE c_server=$SERVER_ID
					AND (c_name ~* '$s' OR c_person ~* '$s' OR c_xml ~* '$s' OR c_name2 ~* '$s')
					ORDER BY c_name";
			}
			else
			{
				$query="SELECT cr_id AS c_id
					 FROM crm_recent
					 WHERE cr_server=$SERVER_ID
					 AND cr_username='$KAMELEON[username]'
					 AND cr_file_id='customer_master'
					 ORDER BY cr_timestamp DESC
					 LIMIT $size";
			}

			$res=$adodb->Execute($query);
			if ($res)
			{
				$iter_obj->count=$res->RecordCount();
				$iter_obj->result=$res;
				$iter_obj->lp=0;
			}
		}
	}

	if (!function_exists("customer_list_item"))
	{
		function customer_list_item(&$iter_obj)
		{
			global $CUSTOMER,$MODULES;
			global $adodb;
			
			$CUSTOMER=$iter_obj->result->FetchRow($iter_obj->i);
			if ($CUSTOMER[c_parent]) $CUSTOMER[c_id]=$CUSTOMER[c_parent];
			$id=$CUSTOMER[c_id];
			$iter_obj->i++;
			if (!$iter_obj->c[$id])
			{		
				$CUSTOMER[lp]=++$iter_obj->lp;
				module_select($MODULES->crm->files->customer_list,"c_id=$id");		
				$iter_obj->c[$id]=1;
				$CUSTOMER[tr]="";
			}
			else
			{
				$CUSTOMER[tr]="style='display:none'";
			}
			
			return ($CUSTOMER);
		}
	}

	if (!function_exists("customer_list_end"))
	{
		function customer_list_end(&$iter_obj)
		{
			return ($iter_obj->count <= $iter_obj->i);
		}
	}
	


	global $CUSTOMER,$MODULES;

	$CUSTOMER[function_loop_begin]="customer_list_init";
	$CUSTOMER[function_loop_item]="customer_list_item";
	$CUSTOMER[function_loop_end]="customer_list_end";

	$CUSTOMER[search].="";
	$CUSTOMER[self]=$self;
	$CUSTOMER[next]=$next;

	$CUSTOMER[toolbar] = crm_toolbar($MODULES->crm->files->customer_list->toolbar,$self,null);

	_display_view($MODULES->crm->files->customer_list);


?>
