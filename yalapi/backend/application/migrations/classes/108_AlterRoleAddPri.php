<?php
/*

*/


class AlterRoleAddPri extends Doctrine_Migration_Base
{
    private $_tableName = 'roles';
    private $_colName = 'pri';

    public function preUp()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'' . $this->_tableName . '\')');
    }

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' Smallint');
    }

    public function postUp()
    {
	require_once __DIR__.'/../../../library/php-activerecord/lib/Singleton.php';
	require_once __DIR__.'/../../../library/php-activerecord/lib/Config.php';
	require_once __DIR__.'/../../../library/php-activerecord/lib/Exceptions.php';
	require_once __DIR__.'/../../../library/php-activerecord/lib/Connection.php';
	require_once __DIR__.'/../../../library/php-activerecord/lib/ConnectionManager.php';
	require_once __DIR__.'/../../../library/php-activerecord/lib/Reflections.php';
	require_once __DIR__.'/../../../library/php-activerecord/lib/Table.php';
	require_once __DIR__.'/../../../library/php-activerecord/lib/Model.php';
	require_once __DIR__.'/../../models/AppModel.php';
	require_once __DIR__.'/../../models/AclModel.php';
	require_once __DIR__.'/../../models/Role.php';	
	
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'' . $this->_tableName . '\')');
			
	Doctrine_Manager::connection()->execute('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . ' = 10 WHERE id = '.Role::ADMIN);
	Doctrine_Manager::connection()->execute('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . ' = 20 WHERE id = '.Role::PROJECT_LEADER);
	Doctrine_Manager::connection()->execute('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . ' = 30 WHERE id = '.Role::CENTER_LEADER);
	Doctrine_Manager::connection()->execute('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . ' = 40 WHERE id = '.Role::COACH);
	Doctrine_Manager::connection()->execute('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . ' = 50 WHERE id = '.Role::USER);
	
	$sql=file_get_contents(preg_replace('/\.php$/','_up.sql',__FILE__));
        Doctrine_Manager::connection()->exec($sql);
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'' . $this->_tableName . '\')');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }

    public function postDown()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'' . $this->_tableName . '\')');
	
	$sql=file_get_contents(preg_replace('/\.php$/','_down.sql',__FILE__));
        Doctrine_Manager::connection()->exec($sql);
    }
}
