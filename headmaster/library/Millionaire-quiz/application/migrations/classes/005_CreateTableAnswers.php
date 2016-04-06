<?php

class CreateTableAnswers extends Doctrine_Migration_Base
{
    private $_tableName = 'answers';
    
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
            'answer' => array(
                'type' => 'character varying(256)',
                'notnull' => false
            ),
            'is_correct' => array(
                'type' => 'smallint'
            ),
            'probability' => array(
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
