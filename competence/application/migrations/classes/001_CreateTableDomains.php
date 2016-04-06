<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

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
                'notnull' => true,
            ),
            'admin_email' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'active' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'create_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT 1');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
