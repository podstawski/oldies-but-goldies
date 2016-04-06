<?php
class CreateTableQuestionCategories extends Doctrine_Migration_Base
{
    private $_tableName = 'question_categories';
    
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
                'type' => 'integer',
                'notnull' => true,
            ),
            'category_id' => array(
                'type' => 'integer',
                'notnull' => true,
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
