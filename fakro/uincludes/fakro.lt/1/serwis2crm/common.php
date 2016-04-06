<?
function query($query) {
	mysql_query($query);
	}

function insert($query) {
	mysql_query($query);
	return mysql_insert_id();
	}

function insert_array($array, $table) {
	$fields = array();
	$values = array();
	
	foreach ($array as $field => $value) {
		$fields[] = $field;
		$values[] = '"' . $value . '"';
		}
	return insert('INSERT INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')');
	}

function array_keys_recount($array) {
	foreach ($array as $value) {
		$new_array[] = $value;
		}
	return $new_array;
	}

function insert_firmy($data) {
	$data['data_utworzenia'] = date('Y-m-d');
	$id_firmy = insert_array($data, 'firmy');
	/*
	if($id_firmy) {
		insert_array(array('id_firmy' => $id_firmy), 'firma_obroty_potencjalne');
		insert_array(array('id_firmy' => $id_firmy), 'firmy_klient');
		insert_array(array('id_firmy' => $id_firmy), 'jde_sprzedaz');
		insert_array(array('id_firmy' => $id_firmy, 'id_typy_firm' => '1', 'id_podtypy_firm' => 8), 'firma_typ');
		insert_array(array('id_shared' => '0', 'id_firmy' => $id_firmy, 'id_osoby' => _ID_OSOBY, 'typ_dostepu' => '1'), 'shared');
		return $id_firmy;
		}
	*/
	return false;
	}
?>