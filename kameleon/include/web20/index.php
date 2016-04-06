<?php
    if (file_exists('../../const.php')) include_once('../../const.php'); else include_once('../../const.h');
    
    define ('ADODB_DIR',dirname(__FILE__).'/../../adodb/');
    
        
    include_once ("../adodb.h");
    include_once ("../request.h");
    include_once ("../kameleon.h");
    

    $KAMELEON_MODE=1;
    include_once ("../auth.h");
    

    
    
    function web20_index()
    {
        global $kameleon;
        
        $wynik=array();
        
        if ($dh = opendir(dirname(__FILE__)))
        {
            while (($file = readdir($dh)) !== false)
            {
                if ($file[0]=='.') continue;
                if (is_dir( dirname(__FILE__).'/'.$file))
                {
                    $title=(file_exists(dirname(__FILE__).'/'.$file.'/title'))? trim(file_get_contents(dirname(__FILE__).'/'.$file.'/title')):('web20_'.$file);
                    $index=(file_exists(dirname(__FILE__).'/'.$file.'/pri'))? trim(file_get_contents(dirname(__FILE__).'/'.$file.'/pri')):count($wynik);
                    
                    while (is_array($wynik[$index])) $index++;
                    $wynik[$index]['title']=$kameleon->label($title);
                    $wynik[$index]['module']=$file;
                }
                
            }
            closedir($dh);
        }
        
        return $wynik;
    }
    
    function web20_options($module,$current_options)
    {
        global $kameleon;

        $options=array();
        if (file_exists(dirname(__FILE__).'/'.$module.'/options.php')) include(dirname(__FILE__).'/'.$module.'/options.php');
        
        
	foreach ($options AS $name=>$v)
	{
            $token='_web20['.$name.']';
            $display[$token]['label']=$v[0];
            if (!isset($current_options[$name])) $current_options[$name]=$v[3];
            
            if (strlen($v[2]))
            {
                    $display[$token]['input']=str2input($v[2],$token,$current_options[$name],$v[1]);
            }
            else
            {
                    if (strlen($v[1])) $display[$token]['arg']['style']=$v[1];
                    $display[$token]['arg']['value']=$current_options[$name];
            }

            if(strlen($v[4])) $display[$token]['icon']=$v[4];

	}
        
        if (isset($display)) return display_opt($display);
        
    }
    
    if (!isset($_REQUEST['action']) && isset($_REQUEST['module'])) $_REQUEST['action']='options';
    
    switch ($_REQUEST['action'])
    {
        case 'options':
            $module='';
            if ($_REQUEST['options'])
            {
                $op=unserialize(base64_decode($_REQUEST['options']));
                $module=$op['module'];
            }
            if ($_REQUEST['module']) $module=$_REQUEST['module'];
            $options=$op['options'][$module];

        
            $wynik=web20_options($module,$options);
            break;
        
        case 'changeOption':
            $sid=0+$_REQUEST['sid'];
            if (!$sid)
            {
                $wynik=0;
                break;
            }
            $sql="SELECT web20 FROM webtd WHERE sid=$sid";
            parse_str(ado_query2url($sql));
            $op=unserialize(base64_decode($web20));
            $module=$op['module'];
            
            if (file_exists($module."/options.php"))
            {
                    include $module."/options.php";
                    foreach ($options as $key=>$v)
                    {
                            if (!isset($op['options'][$module][$key])) $op['options'][$module][$key]=$v[4];
                    }
            } 
			
            if (!isset($op['options'][$module][$_REQUEST['key']]))
            {
                $wynik=-1;
            }
            else
            {
                $op['options'][$module][$_REQUEST['key']]=$_REQUEST['val'];
                $web20=base64_encode(serialize($op));
                $sql="UPDATE webtd SET web20='$web20' WHERE sid=$sid";
                $adodb->execute($sql);
                $wynik=1;
            }
            break;
        
        default:
            $wynik=web20_index();
            break;
    }
    
    
    
    if ($_REQUEST['debug'])
    {
        echo '<pre>'.print_r($wynik,true).'</pre>';
    }
    else
    {
        echo json_encode($wynik);
    }