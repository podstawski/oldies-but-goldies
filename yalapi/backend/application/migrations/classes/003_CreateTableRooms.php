<?php
/**
 * Description
 * @author Radosław Benkel
 */
 
class CreateTableRooms extends Doctrine_Migration_Base
{
    private $_tableName = 'rooms';
    private $_fkName = 'fk_rooms_training_centers';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'training_center_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'symbol' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'available_space' => array(
                'type' => 'integer',
                'notnull' => true,
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
             'local'         => 'training_center_id',
             'foreign'       => 'id',
             'foreignTable'  => 'training_centers',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropTable($this->_tableName);
    }
}
