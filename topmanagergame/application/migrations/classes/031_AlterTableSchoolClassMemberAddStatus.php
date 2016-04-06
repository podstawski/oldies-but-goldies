<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterTableSchoolClassMemberAddStatus extends Doctrine_Migration_Base
{
    private $_tableName = 'school_class_member';
    private $_colName = 'status';

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' SMALLINT NOT NULL DEFAULT 0');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }
}