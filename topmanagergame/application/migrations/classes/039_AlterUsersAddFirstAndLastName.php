<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterUsersAddFirstAndLastName extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_colName1 = 'first_name';
    private $_colName2 = 'last_name';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName1, 'varchar(256)', null, array('notnull' => false));
        $this->addColumn($this->_tableName, $this->_colName2, 'varchar(256)', null, array('notnull' => false));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName2);
        $this->removeColumn($this->_tableName, $this->_colName1);
    }
}