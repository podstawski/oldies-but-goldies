<?

function db_connect($database = _MYSQL_DATABASE)
{
	static $connection = null;
	if($connection != null)
	{
		db_close($connection);
	}

	$connection = mysql_connect(_MYSQL_HOSTNAME, _MYSQL_USERID, _MYSQL_PASSWORD)
		or die ('Brak połączenia z bazą danych.');

	mysql_select_db($database)
		or die ('Brak połączenia z bazą danych.');

	if(!defined('_MYSQL_SET_NAMES_LATIN2') || _MYSQL_SET_NAMES_LATIN2 !== false)
	{
		mysql_query('SET NAMES latin2');
	}
}

function db_close(&$connection)
{
	mysql_close($connection);
}

function query($query)
{
	mysql_query($query);
}

function insert($query)
{
	mysql_query($query);
	return mysql_insert_id();
}

function insert_array($array, $table)
{
	$fields = array();
	$values = array();
	foreach ($array as $field => $value)
	{
		$fields[] = $field;
		$values[] = '"' . $value . '"';
	}

	return insert('INSERT INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')');
}

function fetch_array($query)
{
	$result = mysql_query($query) or die(mysql_error());
	$array = array();
	if ($result)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$array[] = $row;
		}
	}
	return $array;
}

function fetch_list($query)
{
	$result = mysql_query($query);
	$list = array();
	if ($result)
	{
		while ($row = mysql_fetch_row($result))
		{
			$list[$row[0]] = $row[1];
		}
	}
	return $list;
}

function fetch_single_list($query)
{
	$result = mysql_query($query);
	$list = array();
	if ($result)
	{
		while ($row = mysql_fetch_row($result))
		{
			$list[] = $row[0];
		}
	}
	return $list;
}

function fetch_single($query)
{
	$result = mysql_query($query);
	$row = null;
	if ($result)
	{
		$row = mysql_fetch_row($result);
	}
	return $row[0];
}
?>
