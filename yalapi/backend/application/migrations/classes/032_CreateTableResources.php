<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableResources extends Doctrine_Migration_Base
{
    private $_tableName = 'resources';
    private $_fk_name_1 = 'fk_training_centers_resources';
    private $_fk_name_2 = 'fk_resource_types';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'training_center_id' => array(
                'type' => 'integer',
            ),
            'resource_type_id' => array(
                'type' => 'integer',
            ),
            'amount' => array(
                'type' => 'integer'
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fk_name_1, array(
            'local'         => 'training_center_id',
            'foreign'       => 'id',
            'foreignTable'  => 'training_centers',
            'onDelete'      => 'SET NULL',
            'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fk_name_2, array(
            'local'         => 'resource_type_id',
            'foreign'       => 'id',
            'foreignTable'  => 'resource_types',
            'onDelete'      => 'SET NULL',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fk_name_1);
        $this->dropForeignKey($this->_tableName, $this->_fk_name_2);

        $this->dropTable($this->_tableName);
    }
}
