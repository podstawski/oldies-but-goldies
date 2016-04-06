<?

function towar_waga($waga) {
    global $adodb;
    
    $sql = "SELECT * FROM tr_waga WHERE tr_waga_od <= $waga AND tr_waga_do >= $waga;";
    parse_str(ado_query2url($sql));
    
    return $tr_waga_cena_brutto;
    }

?>