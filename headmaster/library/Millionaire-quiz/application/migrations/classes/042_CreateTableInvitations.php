<?php
/**
 * @author <bohdan.bobrowski@gammanet.pl> Bohdan Bobrowski
 */

class CreateTableInvitations extends Doctrine_Migration_Base
{
    private $_tableName = 'invitations';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'email' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'test_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB',
            'charset' => 'utf8',
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
