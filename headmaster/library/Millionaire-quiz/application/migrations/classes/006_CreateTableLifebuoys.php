<?php
class CreateTableLifebuoys extends Doctrine_Migration_Base
{
    private $_tableName = 'lifebuoys';
    
    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'question_id' => array(
                'type' => 'integer'
            ),
            'lifebuoy' => array(
                'type' => 'text',
                'notnull' => false
            ),
            'lifebuoy_type' => array(
                'type' => 'integer',
                'notnull' => false
            ),            
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
