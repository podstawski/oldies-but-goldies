<?
	if (!function_exists("article_list_init"))
	{
		function article_list_init(&$iter_obj)
		{

			$iter_obj->i=0;
			$iter_obj->count=0;

			$where="ls_c_id = $AUTH[id]";
			if ($also_students) 
				$where="($where OR ls_c_id IN 
										(SELECT c_id FROM crm_customer
											WHERE c_parent=$AUTH[id]
											AND c_id=learn_student.ls_c_id ) 
						)";

			$where.=" AND ls_server=$SERVER_ID 
						AND (ls_end>=CURRENT_DATE OR ls_end IS NULL)";

			$query="SELECT * FROM learn_student 
					WHERE $where
					ORDER BY ls_c_id,ls_begin";


			$res=$adodb->Execute($query);

			if ($res)
			{
				$iter_obj->count=$res->RecordCount();
				$iter_obj->result=$res;
			}
			
		}
	}

	if (!function_exists("article_list_item"))
	{
		function article_list_item(&$iter_obj)
		{
			global $AUTH,$MODULES,$SERVER_ID,$ARTICLE,$CUSTOMER;
			global $also_students;


			$LS=$iter_obj->result->FetchRow($iter_obj->i);
			_RevertDate($LS[ls_begin]);
			_RevertDate($LS[ls_end]);
			$iter_obj->i++;

			module_select($MODULES->learn->files->article_master,"sa_server=$SERVER_ID AND sa_id=$LS[ls_sa_id]");
			module_select($MODULES->learn->files->customer_form,"c_server=$SERVER_ID AND c_id=$LS[ls_c_id]");


			$query="SELECT count(*) AS lesson_count FROM learn_report WHERE lr_ls_id=$LS[ls_id]";
			parse_str(ado_query2url($query));

			$query="SELECT count(*) AS total_lessons 
					FROM weblink,webpage
					WHERE weblink.server=$SERVER_ID 
					AND webpage.server=$SERVER_ID
					AND webpage.menu_id=weblink.menu_id
					AND webpage.id=$ARTICLE[sa_page_id]";
			parse_str(ado_query2url($query));

			$ARTICLE=array_merge($ARTICLE,$LS);
			$ARTICLE=array_merge($ARTICLE,$CUSTOMER);
			$ARTICLE[lp]=$iter_obj->i;
			$ARTICLE[lesson_count]=$lesson_count;
			$ARTICLE[total_lessons]=$total_lessons;

			$ARTICLE[href_disabled]=($ARTICLE[ls_c_id]==$AUTH[id])?"":"disabled";

			return ($ARTICLE);
		}
	}

	if (!function_exists("article_list_end"))
	{
		function article_list_end(&$iter_obj)
		{
			return ($iter_obj->count <= $iter_obj->i);
		}
	}
	


	$ARTICLE[function_loop_begin]="article_list_init";
	$ARTICLE[function_loop_item]="article_list_item";
	$ARTICLE[function_loop_end]="article_list_end";


	_display_view($MODULES->learn->files->article_list);

?>