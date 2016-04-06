<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterCommitmentAddColumnObjectId extends Doctrine_Migration_Base
{
    private $_tableName = 'commitment';
    private $_colName = 'object_id';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName, 'integer', null, array(
            'notnull' => false
        ));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName);
    }
}