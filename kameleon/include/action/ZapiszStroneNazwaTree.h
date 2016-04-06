<?php

    function zapiszStroneNazwaTree($page)
    {
        global $SERVER_ID,$ver,$lang;
        global $C_SHOW_PAGE_FILENAME, $C_DIRECTORY_INDEX;
        global $adodb;
        global $CHARSET;
        global $kameleon;
        
        
        
        
        $action='ZapiszStroneNazwa';
        $may_rewrite=1;
        include(dirname(__FILE__).'/ZapiszStroneNazwa.h');
        
        if (strlen($error)) return $error;
        
        $sql="SELECT id FROM webpage WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND prev=$page";
        $r=$adodb->execute($sql);
        for ($i=0;$i<$r->RecordCount();$i++)
        {
            parse_str(ado_explodeName($r,$i));
            
            $error=zapiszStroneNazwaTree($id);
            if (strlen($error)) return $error;
        }
        
        
        
        return $error;
    }


    if (!$kameleon->checkRight('write','page',$page))
    {
            $error=$norights;
            return;
    }


    $error=zapiszStroneNazwaTree($page);