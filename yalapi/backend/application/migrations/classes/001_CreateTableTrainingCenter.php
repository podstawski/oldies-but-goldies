<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
class CreateTableTrainingCenter extends Doctrine_Migration_Base
{
    private $_tableName = 'training_centers';
    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'street' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'zip_code' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'city' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            )
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
