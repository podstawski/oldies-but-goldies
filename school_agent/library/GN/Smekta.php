<?php

class GN_Smekta
{
    public static function smektuj($txt, $vars, $add_globals = false, $filedir=null)
    {
	$txt=self::_general_replace($txt,$filedir);
	
	
        $ret = self::_replace_tokens($txt, $vars, $add_globals);
        return $ret;
    }
    
    private static function _general_replace($txt,$filedir=null) {

	static $dir;
	if ($filedir) $dir=$filedir;
	

	
	if (!strstr($txt,"\n")) {
	    $path='';
	    if ($dir) {
		if (!is_array($dir)) $dir=array($dir);
		foreach ($dir AS $d) {
		    if ($d && $txt[0]!='/') $path="$d/$txt";
		    else $path=$txt;
		    
		    if (file_exists($path)) {
			$txt=file_get_contents($path);
			break;
		    }
		}
	    } else {
		$path=$txt;
		if (file_exists($path)) {
		    $dir=dirname($path);
		    $txt=file_get_contents($path);
		}		
	    }
	}	
	
	
        $txt = preg_replace("#<!--[ ]*loop:([^> -]+)[ ]*-->#", "{loop:\\1}", $txt);
        $txt = preg_replace("#<!--[ ]*with:([^> -]+)[ ]*-->#", "{with:\\1}", $txt);
        $txt = preg_replace("#<!--[ ]*if:([^> -]+)[ ]*-->#", "{if:\\1}", $txt);
        $txt = preg_replace("#<\!--[ ]*end([a-z]+):([^> \-]+)[ ]*-->#", "{end\\1:\\2}", $txt);
        $txt = preg_replace("#<!--[ ]*([a-z_]+)[ ]*-->#", "{\\1}", $txt);
        //$txt=preg_replace("#%7B([^%]*)%7D#","{\\1}",$txt);
        $txt = str_replace('%7B', '{', $txt);
        $txt = str_replace('%7D', '}', $txt);
        $txt = str_replace('%7C', '|', $txt);

        while (1) {
            $__p = $txt;
            $txt = preg_replace("#\[\!(.*)\!\]#", "{\\1}", $__p);
            if (strlen($txt) == strlen($__p)) break;
        }
	return $txt;
    }

    private static function _post_parse_token($token, &$vars, $fun = array(), $param = array(), $default_value = null)
    {
        $is_obj = is_object($token);

        if (!$token && !is_null($default_value))
            $token = $default_value;

        for ($f = 0; $f < count($fun); $f++) {
            $method = false;
            if ($is_obj && method_exists($token, $fun[$f]))
                $method = true;
            if (!$method) {
                if (strpos($fun[$f], '.')) {
                    $of = explode('.', $fun[$f]);
                    if (isset($vars[$of[0]]) && is_object($vars[$of[0]]) && method_exists($vars[$of[0]], $of[1])) {
                        if (is_array($param[$f])) {
                            $param[$f] = array_merge(array($token), $param[$f]);
                        } elseif (strlen(trim($param[$f]))) {
                            $param[$f] = array(
                                $token,
                                $param[$f]
                            );
                        } else $param[$f] = array($token);

                        $token = call_user_func_array(array(
                            $of[0],
                            $of[1]
                        ), $param[$f]);
                        continue;
                    }
                }
                if (!function_exists($fun[$f]))
                    continue;
            }

            if (!$method) {
                if (is_array($param[$f])) {
                    $param[$f] = array_merge(array($token), $param[$f]);
                } elseif (strlen(trim($param[$f]))) {
                    $param[$f] = array(
                        $token,
                        $param[$f]
                    );
                } else $param[$f] = array($token);

                $token = call_user_func_array($fun[$f], $param[$f]);
            } else {
                $token = call_user_func_array(array(
                    $token,
                    $fun[$f]
                ), $param[$f]);
            }
        }

        return is_array($token) ? self::array_to_string($token) : $token;
    }

    /**
     * @param array $array
     * @param string $separator
     * @return string
     */
    private static function array_to_string(array $array, $separator = ',')
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $array[$k] = self::array_to_string($v, $separator);
            }
        }
        return implode($separator, $array);
    }

    private static function _dig_deep_in_array($vars_array, $key_array)
    {
        if (!is_array($key_array)) $key_array = array($key_array);
        $wynik = $vars_array;
        foreach ($key_array AS $key) {
            $wynik = $wynik[$key];
        }

        return $wynik;
    }

    private static function _dig_deep_in_obj($vars_array, $obj)
    {
	$class=$obj[0];
	$pole=$obj[1];
	
	return $vars_array[$class]->$pole;
    }    
    
    private static function _assign_variable($txt,&$vars) {

	$key=substr($txt,1);
	foreach ($vars AS $k=>$v) if ($k==$key) return $v;
	
	$txt=preg_replace('/\$([a-z_]+[a-z0-9_]*)/i','${\\1}',$txt);
	
	foreach ($vars AS $k=>$v) {
	    if (!is_array($v) && !is_object($v)) {
		$txt=str_replace('${'.$k.'}',$v,$txt);
	    }
	}
	return $txt;
	
    }
    
    private static function clearvars(&$vars) {
	foreach ($vars AS $k=>$v) {
	    if (is_null($v)) $vars[$k]='';
	    if (is_string($v)) $vars[$k]=trim($v);
	    if (is_array($v)) self::clearvars($vars[$k]);
	}
    }
    
    
    private static function _get_value($parser_token,&$vars,$add_brackets_if_not_found=true) {
	$fun = array();
	$param = array();
	$default_value = null;
	$wynik='';
	
	$first_check=explode(':',$parser_token);
	if (count($first_check)>1 && in_array($first_check[0],array('endwith','endif','endloop'))) return $wynik;

	if (strstr($parser_token, '?') && !strstr($parser_token, "\n")) {
	    $_parser_token = explode('?', $parser_token);
	    $default_value = $_parser_token[1];
	    $parser_token = $_parser_token[0];
	}	
    
	if (strstr($parser_token, '|') && !strstr($parser_token, "\n")) {
	    $_parser_token = explode('|', $parser_token);
	    $parser_token = $_parser_token[0];

	    for ($f = 1; $f < count($_parser_token); $f++) {
		$_parser_token[$f] = str_replace("\\:", '__dwukropek__', $_parser_token[$f]);
		$_parser_token_fun = explode(':', $_parser_token[$f]);
		$_fun = $_parser_token_fun[0];

		if (isset($_parser_token_fun[1]))
		{
		    $_parser_token_fun[1] = str_replace("\\,", '__przcinek__', $_parser_token_fun[1]);
		    $_param = explode(',', $_parser_token_fun[1]);
		}
		else $_param = array();
		$_param = str_replace('__przcinek__', ',', $_param);
		$_param = str_replace('__dwukropek__', ':', $_param);
		

		foreach ($_param AS $i=>$_p) {
		    if (strstr($_p,'$')) {
			$_param[$i]=self::_assign_variable($_p,$vars);
		    }
		}

		$fun[] = $_fun;
		$param[] = $_param;
	    }
	}

	if (strstr($parser_token, '.') && !strstr($parser_token, "\n")) {
	    $_parser_token = explode('.', $parser_token);
	    $parser_token = $_parser_token[0];

	    
	    if (isset($vars[$parser_token]) && is_array($vars[$parser_token]))
	    {
		if (isset($vars[$parser_token][$_parser_token[1]])) {
		    $wynik .= self::_post_parse_token(self::_dig_deep_in_array($vars, $_parser_token),$vars, $fun, $param,$default_value);
		} elseif($default_value) {
		    $wynik.=$default_value;
		}
	    }
	   
	    if (isset($vars[$parser_token]) && is_object($vars[$parser_token]))
	    {
		$wynik .= self::_post_parse_token(self::_dig_deep_in_obj($vars, $_parser_token), $vars, $fun, $param,$default_value);
		
	    }
	
	} elseif (isset($vars[$parser_token])) {
	    $wynik .= self::_post_parse_token($vars[$parser_token], $vars, $fun, $param);
	} elseif (strstr($parser_token, "\n")) {
	    $wynik .= '{' . $parser_token . '}';
	} elseif (!is_null($default_value)) {
	    $wynik .= self::_post_parse_token($default_value, $vars, $fun, $param);
	} elseif ($parser_token == 'debug') {
	    $wynik .= print_r($vars, true);
	} elseif (isset($vars['_TOKEN_BLANK']) && $vars['_TOKEN_BLANK']) {
	    $wynik .= '';
	} elseif (!strstr($parser_token, ':') && $add_brackets_if_not_found) {
	    $wynik .= '{' . $parser_token . '}';
	}

	return $wynik;
    
    }
    
    private static function _replace_tokens($parser_content, $vars, $add_globals = false)
    {
        $parser_startpos = 0;
	
	self::clearvars($vars);

        if ($add_globals) {
            foreach ($_SERVER AS $k => $v) if (!isset($vars[$k]) && !isset($vars->$k)) @$vars[$k] = $v;
            foreach ($_REQUEST AS $k => $v) if (!isset($vars[$k]) && !isset($vars->$k)) @$vars[$k] = $v;
        }

	$wynik='';

        while (1) {
            $parser_content = substr($parser_content, $parser_startpos);
            $parser_proc1 = strpos($parser_content, "{");
            $parser_proc2 = strpos(substr($parser_content, $parser_proc1 + 1), "}");
            $parser_proc3 = strpos(substr($parser_content, $parser_proc1 + 1), "{");

            if (!strlen($parser_proc1) || !strlen($parser_proc2)) {
                $wynik .= $parser_content;
                break;
            }

            if (strlen($parser_proc3) && $parser_proc3 < $parser_proc2) {
                $wynik .= substr($parser_content, 0, $parser_proc1 + 1);
                $parser_startpos = $parser_proc1 + 1;
                continue;
            }

            $parser_token = substr($parser_content, $parser_proc1 + 1, $parser_proc2);
            $parser_startpos = $parser_proc1 + $parser_proc2 + 2;
            $wynik .= substr($parser_content, 0, $parser_proc1);

	    
	    
	    if (substr(strtolower($parser_token), 0, 8) == 'include:') {
		$include=substr($parser_token, 8);
		$wynik.=self::smektuj($include,$vars,$add_globals);
		
	    } elseif (substr(strtolower($parser_token), 0, 5) == 'with:') {

                $arrayname = substr($parser_token, 5);

                $end_token = strtolower("{endwith:$arrayname}");
                $pos = strpos(strtolower($parser_content), $end_token);
                if ($pos) {
                    $inside_content = substr($parser_content, $parser_startpos, $pos - $parser_startpos);
                    $parser_startpos = $pos + strlen($end_token);

                    $arrayname_array = explode(':', $arrayname);
                    $arrayname = $arrayname_array[0];

                    if (isset($vars[$arrayname]) && is_array($vars[$arrayname])) {
                        $varset = $vars[$arrayname];
                        foreach ($vars AS $k => $v) {
                            //if (!is_object($v) && !is_array($v) && !isset($varset[$k])) $varset[$k] = $v;
			    if (!isset($varset[$k])) $varset[$k] = $v;
                        }
			$varset['__with__']='ala ma kota';
                        $wynik .= self::_replace_tokens($inside_content, $varset, $add_globals);
                    }
		    
                }
            } elseif (substr(strtolower($parser_token), 0, 5) == 'loop:') {
                $arrayname = substr($parser_token, 5);

		
                $end_token = strtolower("{endloop:$arrayname}");
                $pos = strpos(strtolower($parser_content), $end_token);

                if ($pos) {
                    $inside_content = substr($parser_content, $parser_startpos, $pos - $parser_startpos);
                    $parser_startpos = $pos + strlen($end_token);

                    $arrayname_array = explode(':', $arrayname);

                    $_loop_var = explode('.', $arrayname_array[0]);
                    $loop_var = (count($_loop_var) == 1) ? $vars[$_loop_var[0]] :
			((count($_loop_var) == 2) ? $vars[$_loop_var[0]][$_loop_var[1]] :
			    $vars[$_loop_var[0]][$_loop_var[1]][$_loop_var[2]]);

                    $loop_i = 0;
                    if (is_array($loop_var)) {
                        foreach ($loop_var AS $__k__ => $varset) {
                            if (!is_array($varset)) {
                                $varset = array('loop' => $varset);
                                $varset[$arrayname_array[0]] = $varset['loop'];
                            }
                            $varset['__loop__'] = $__k__;
                            foreach ($vars AS $k => $v) {
                                //if (!is_array($v) && !isset($varset[$k])) $varset[$k] = $v;
				if (!isset($varset[$k])) $varset[$k] = $v;
                            }

                            $loop_i++;
                            if (isset($arrayname_array[1]) && preg_match('/^[0-9\-]+$/', $arrayname_array[1])) {
                                $fromto = explode('-', $arrayname_array[1]);
                                if (!$fromto[1]) $fromto[1] = $fromto[0];
                                if ($loop_i < $fromto[0] || $loop_i > $fromto[1]) continue;
                            }
                            $wynik .= self::_replace_tokens($inside_content, $varset, $add_globals);
                        }
                    }
                }

            } elseif (substr(strtolower($parser_token), 0, 3) == 'if:') {
                $ifname = substr($parser_token, 3);

                $end_token = strtolower("{endif:$ifname}");
                $pos = strpos(strtolower($parser_content), $end_token);

                $NOT = false;
                if ($ifname[0] == '!') {
                    $NOT = true;
                    $ifname = substr($ifname, 1);
                }

                if ($pos) {
                    $ifname_array = explode(':', $ifname);

                    $_zmienna = explode('=', $ifname_array[0]);
                    $__zmienna = explode('.', $_zmienna[0]);
		    
		    $test_zmienna=self::_get_value($_zmienna[0],$vars,false);

                    if (count($_zmienna) == 1) {

                        if (!$test_zmienna && !$NOT) $parser_startpos = $pos + strlen($end_token);
                        if ($test_zmienna && $NOT) $parser_startpos = $pos + strlen($end_token);

                    } else {


			if (strstr($_zmienna[1],'$')) $_zmienna[1]=self::_assign_variable($_zmienna[1],$vars);
			
                        if (trim($test_zmienna) != trim($_zmienna[1]) && !$NOT) $parser_startpos = $pos + strlen($end_token);
                        if (trim($test_zmienna) == trim($_zmienna[1]) && $NOT) $parser_startpos = $pos + strlen($end_token);

                    }
                }
            } else {
		
		$wynik.=self::_get_value($parser_token,$vars);

            }

        }

        return $wynik;
    }
    
    
    public static function struktura($parser_content,$token_ereg) {
	$parser_content=self::_general_replace($parser_content);
	$parser_startpos = 0;
	$wynik=array();
        
	while (1) {
            $parser_content = substr($parser_content, $parser_startpos);
            $parser_proc1 = strpos($parser_content, "{");
            $parser_proc2 = strpos(substr($parser_content, $parser_proc1 + 1), "}");
            $parser_proc3 = strpos(substr($parser_content, $parser_proc1 + 1), "{");

            if (!strlen($parser_proc1) || !strlen($parser_proc2)) {
                break;
            }
	    
            if (strlen($parser_proc3) && $parser_proc3 < $parser_proc2) {
                $parser_startpos = $parser_proc1 + 1;
                continue;
            }

            $parser_token = substr($parser_content, $parser_proc1 + 1, $parser_proc2);
            $parser_startpos = $parser_proc1 + $parser_proc2 + 2;
	    
            if (substr(strtolower($parser_token), 0, 5) == 'with:') {

                $arrayname = substr($parser_token, 5);

                $end_token = strtolower("{endwith:$arrayname}");
                $pos = strpos(strtolower($parser_content), $end_token);
                if ($pos) {
                    $inside_content = substr($parser_content, $parser_startpos, $pos - $parser_startpos);
                    $parser_startpos = $pos + strlen($end_token);

		    $wynik[$arrayname]=self::struktura($inside_content,$token_ereg);
		}
	    } elseif (ereg($token_ereg,$parser_token)) {
		$wynik[$parser_token] = false;
	    }    
	}
	
	return $wynik;
    }

}
