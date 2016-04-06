<?php

class GN_Smekta
{
    
    public static function smektuj($txt,$vars,$add_globals=false)
    {
        $txt=preg_replace("#<!--[ ]*loop:([^> -]+)[ ]*-->#","{loop:\\1}",$txt);
        $txt=preg_replace("#<!--[ ]*with:([^> -]+)[ ]*-->#","{with:\\1}",$txt);
        $txt=preg_replace("#<!--[ ]*if:([^> -]+)[ ]*-->#","{if:\\1}",$txt);
        $txt=preg_replace("#<\!--[ ]*end([a-z]+):([^> \-]+)[ ]*-->#","{end\\1:\\2}",$txt);
        $txt=preg_replace("#<!--[ ]*([a-z_]+)[ ]*-->#","{\\1}",$txt);
        //$txt=preg_replace("#%7B([^%]*)%7D#","{\\1}",$txt);
	$txt=str_replace('%7B','{',$txt);
	$txt=str_replace('%7D','}',$txt);
        $txt=str_replace('%7C','|',$txt);
        
        while (1) 
        {
            $__p=$txt;
            $txt=preg_replace("#\[\!(.*)\!\]#","{\\1}",$__p);
            if (strlen($txt)==strlen($__p)) break;
        }        
        
       	$ret=self::_replace_tokens($txt,self::_obj2arr($vars),$add_globals); 
        
        return $ret;
    }



    private static function _obj2arr($obj)
    {
        static $hash;
        
        if (!function_exists('spl_object_hash')) return $obj;
        if (!is_array($obj) && !is_object($obj)) return $obj;
        if ($depth==0) $hash=array();
        
        $wynik=array();
        foreach ($obj AS $k=>$v)
        {
            if (is_object($v))
            {
                $spl=spl_object_hash($v);
                if (isset($hash[$spl])) continue;
                $hash[$spl]=true;
            }

            $wynik[$k] =  ($k!='GLOBALS' && (is_array($v)||is_object($v)) ) ? self::_obj2arr($v,$depth+1) : $v;
                

        }

        return $wynik;        
    }



    private static function _post_parse_token($token,$fun=array(),$param=array())
    {
        for ($f=0;$f<count($fun);$f++)
        {
            if (!function_exists($fun[$f])) continue;
            if (is_array($param[$f])) $param[$f]=array_merge(array($token),$param[$f]);
            elseif (strlen(trim($param[$f]))) $param[$f]=array($token,$param[$f]);
            else $param[$f]=array($token);
            
            $token=call_user_func_array($fun[$f],$param[$f]);
        }
        return is_array($token)?implode(',',$token):$token;
    }
    
    private static function _dig_deep_in_array($vars_array,$key_array)
    {
        if (!is_array($key_array)) $key_array=array($key_array);
        $wynik=$vars_array;
        foreach ($key_array AS $key)
        {
                $wynik=$wynik[$key];
        }
        return $wynik;
    }
    
    
    private static function _replace_tokens($parser_content,$vars,$add_globals=false)
    {
        $parser_startpos=0;


	if ($add_globals)
	{
        	foreach ($_SERVER AS $k=>$v ) if (!isset($vars[$k]) && !isset($vars->$k) ) @$vars[$k]=$v;
        	foreach ($_REQUEST AS $k=>$v ) if (!isset($vars[$k]) && !isset($vars->$k) ) @$vars[$k]=$v;
        }

        while (1)
        {
            $parser_content=substr($parser_content,$parser_startpos);
            $parser_proc1=strpos($parser_content,"{");
            $parser_proc2=strpos(substr($parser_content,$parser_proc1+1),"}");
            $parser_proc3=strpos(substr($parser_content,$parser_proc1+1),"{");
            
            if (!strlen($parser_proc1) || !strlen($parser_proc2) )
            {
                $wynik.=$parser_content;
                break;
            }
            
            

            if ( strlen($parser_proc3) && $parser_proc3<$parser_proc2 )
            {
                $wynik.=substr($parser_content,0,$parser_proc1+1);
                $parser_startpos=$parser_proc1+1;
                continue;
            }


            $parser_token=substr($parser_content,$parser_proc1+1,$parser_proc2);
            $parser_startpos=$parser_proc1+$parser_proc2+2;
            $wynik.=substr($parser_content,0,$parser_proc1);


            if (substr(strtolower($parser_token),0,5)=='with:')
            {
                    
                $arrayname=substr($parser_token,5);
                
                $end_token=strtolower("{endwith:$arrayname}");
                $pos=strpos(strtolower($parser_content),$end_token);
                if ($pos)
                {
                    $inside_content=substr($parser_content,$parser_startpos,$pos-$parser_startpos);
                    $parser_startpos=$pos+strlen($end_token);

                    $arrayname_array=explode(':',$arrayname);
                    $arrayname=$arrayname_array[0];

                    if (is_array($vars[$arrayname]))
                    {
                        $varset=$vars[$arrayname];
                        foreach($vars AS $k=>$v)
                        {	
                                if (!is_object($v) && !is_array($v) && !isset($varset[$k])) $varset[$k]=$v;
                        }
                        
                        $wynik.=self::_replace_tokens($inside_content,$varset,$add_globals);	
                    }
                }
            }
            elseif (substr(strtolower($parser_token),0,5)=='loop:')
            {
                    $arrayname=substr($parser_token,5);
                    
                    $end_token=strtolower("{endloop:$arrayname}");
                    $pos=strpos(strtolower($parser_content),$end_token);

                    if ($pos)
                    {
                            $inside_content=substr($parser_content,$parser_startpos,$pos-$parser_startpos);
                            $parser_startpos=$pos+strlen($end_token);

                            $arrayname_array=explode(':',$arrayname);
                            
                            $_loop_var=explode('.',$arrayname_array[0]);
                            $loop_var = (count($_loop_var)==1)?$vars[$_loop_var[0]]:$vars[$_loop_var[0]][$_loop_var[1]];

                            $loop_i=0;
                            if (is_array($loop_var)) 
                                foreach ($loop_var AS $__k__ => $varset)
                                {
                                    if (!is_array($varset))
                                    {
                                        $varset=array('loop'=>$varset);
                                        $varset[$arrayname_array[0]]=$varset['loop'];
                                    }
                                    $varset['__loop__']=$__k__;
                                    foreach($vars AS $k=>$v)
                                    {	
                                        if (!is_array($v) && !isset($varset[$k])) $varset[$k]=$v;
                                    }
                                    
                                    $loop_i++;
                                    if ( preg_match('/^[0-9\-]+$/',$arrayname_array[1]) )
                                    {
                                        $fromto=explode('-',$arrayname_array[1]);
                                        if (!$fromto[1]) $fromto[1]=$fromto[0];
                                        if ($loop_i < $fromto[0] || $loop_i > $fromto[1]) continue;
                                    }
                                    $wynik.=self::_replace_tokens($inside_content,$varset,$add_globals);
                                }
                    }
            
            }
            elseif (substr(strtolower($parser_token),0,3)=='if:')
            {
                $ifname=substr($parser_token,3);
                
                $end_token=strtolower("{endif:$ifname}");
                $pos=strpos(strtolower($parser_content),$end_token);
                
                
                
                $NOT=false;
                if ($ifname[0]=='!')
                {
                        $NOT=true;
                        $ifname=substr($ifname,1);
                }

                if ($pos)
                {
                    $ifname_array=explode(':',$ifname);
                    
                    $_zmienna=explode('=',$ifname_array[0]);
                    $__zmienna=explode('.',$_zmienna[0]);
                    if (count($__zmienna)==1)
                    {
                        $test_zmienna=$vars[$__zmienna[0]];	
                    }
                    else
                    {
                        if (is_object($vars[$__zmienna[0]])) $test_zmienna=$vars[$__zmienna[0]]->$__zmienna[1];
                        else $test_zmienna=$vars[$__zmienna[0]][$__zmienna[1]];
                    }
                    
                    
                    if (count($_zmienna)==1)
                    {
                                                                            
                        if (!$test_zmienna && !$NOT ) $parser_startpos=$pos+strlen($end_token);
                        if ($test_zmienna && $NOT ) $parser_startpos=$pos+strlen($end_token);
                                                            
                    }
                    else
                    {
                            
                        if ($test_zmienna!=$_zmienna[1] && !$NOT ) $parser_startpos=$pos+strlen($end_token);
                        if ($test_zmienna==$_zmienna[1] && $NOT ) $parser_startpos=$pos+strlen($end_token);
                
                    }
                }
            }
            else
            {
                $fun=array();
                $param=array();
                $default_value=null;
                
                if (strstr($parser_token,'?') && !strstr($parser_token,"\n") )
                {
                    $_parser_token=explode('?',$parser_token);
                    $default_value=$_parser_token[1];
                    $parser_token=$_parser_token[0];
                }
                
                if (strstr($parser_token,'|') && !strstr($parser_token,"\n") )
                {
                    $_parser_token=explode('|',$parser_token);
                    $parser_token=$_parser_token[0];
                    
                    for ($f=1;$f<count($_parser_token);$f++)
                    {
                        $_parser_token[$f]=str_replace("\\:",'__dwukropek__',$_parser_token[$f]);
                        $_parser_token_fun=explode(':',$_parser_token[$f]);
                        $_fun=$_parser_token_fun[0];
                        $_parser_token_fun[1]=str_replace("\\,",'__przcinek__',$_parser_token_fun[1]);
                        $_param=explode(',',$_parser_token_fun[1]);
                        if (!strlen($_parser_token_fun[1])) $_param=array();
                        $_param=str_replace('__przcinek__',',',$_param);
                        $_param=str_replace('__dwukropek__',':',$_param);
                        
                        $fun[]=$_fun;
                        $param[]=$_param;
                    }
                }

                if (strstr($parser_token,'.') && !strstr($parser_token,"\n") )
                {
                    $_parser_token=explode('.',$parser_token);
                    $parser_token=$_parser_token[0];

                    if (isset($vars[$parser_token][$_parser_token[1]]))
                    {
                        $wynik.=self::_post_parse_token(self::_dig_deep_in_array($vars,$_parser_token),$fun,$param);	
                    }
                }
                elseif (isset($vars[$parser_token])) $wynik.=self::_post_parse_token($vars[$parser_token],$fun,$param);
                elseif (strstr($parser_token,"\n") ) $wynik.='{'.$parser_token.'}';
                elseif ( !is_null($default_value)) $wynik.= self::_post_parse_token($default_value,$fun,$param);  
		elseif ( $parser_token=='debug' ) $wynik.=print_r($vars,true);
                elseif ($vars['_TOKEN_BLANK'] ) $wynik.='';
                elseif ( !strstr($parser_token,':') ) $wynik.='{'.$parser_token.'}';
                


            }

        }

        return $wynik;
    }














}
