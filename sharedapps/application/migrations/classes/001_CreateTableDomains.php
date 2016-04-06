<?php
class CreateTableDomains extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';

	public function up()
	{
		$this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'domain_name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'org_name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'oauth_token' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            ),
            'admin_email' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            ),
            'active' => array(
                'type' => 'smallint default 1',
                'notnull' => true,
            ),
            'create_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'settings' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'marketplace' => array(
                'type' => 'smallint default 0',
                'notnull' => true,
            ),
        ));
	}

	public function down()
	{
		$this->dropTable($this->_tableName);
	}
}

