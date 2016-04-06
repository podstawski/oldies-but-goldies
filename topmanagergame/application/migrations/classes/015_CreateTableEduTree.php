<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableEduTree extends Doctrine_Migration_Base
{
    private $_tableName = 'edu_params';
    private $_fkName = 'fk_edu_params_parent_id';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'parent_id' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
            'label' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'content' => array(
                'type' => 'text',
                'notnull' => false
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local' => 'parent_id',
            'foreign' => 'id',
            'foreignTable' => $this->_tableName,
            'onDelete' => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropTable($this->_tableName);
    }
}