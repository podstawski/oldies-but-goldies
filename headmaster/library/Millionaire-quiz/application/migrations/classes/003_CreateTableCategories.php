<?php

class CreateTableCategories extends Doctrine_Migration_Base
{
    private $_tableName = 'categories';
    
    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'parent_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'category_type_id' => array(
                'type' => 'integer',
                'notnull' => false
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
