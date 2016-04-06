<?php
class CreateTableLifebuoyTypes extends Doctrine_Migration_Base
{
    private $_tableName = 'lifebuoy_types';
    
    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            )
        ));

    }

    public function postUp()
    {
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
?>
