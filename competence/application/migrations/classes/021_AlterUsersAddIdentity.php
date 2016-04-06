<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterUsersAddIdentity extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_colName = 'identity';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName, 'varchar(255)');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName);
    }
}