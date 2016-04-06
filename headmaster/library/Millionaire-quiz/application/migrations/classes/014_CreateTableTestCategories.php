<?php
class CreateTableTestCategories extends Doctrine_Migration_Base
{
    private $_tableName = 'test_categories';
    
    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'author_id' => array(
                'type' => 'integer'
            )
        ));

    }

    public function postUp()
    {
        // Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `author_id` ');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
?>