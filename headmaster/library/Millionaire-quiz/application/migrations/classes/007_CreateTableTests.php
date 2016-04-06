<?php
class CreateTableTests extends Doctrine_Migration_Base
{
    private $_tableName = 'tests';
    
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
                'notnull' => true
            ),
            'pass' => array(
                'type' => 'character varying(256)',
                'notnull' => true
            ),
            'author_id' => array(
                'type' => 'integer'
            ),
            // Serialized array
            'categories' => array(
                'type' => 'text'
            ),
            // Serialized array
            'questions' => array(
                'type' => 'text'
            ),
			// Zestaw pytań
			// 1 - jeden dla wszystkich
			// 2 - różne dla wszystkich
			// 3 - jeden dla wszystkich, ale w różnej kolejności
            'mode_questions' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            // Zła odpowiedź
            // 1 - kończy grę
            // 2 - nie kończy gry
            'mode_end' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            // Tryb gry
            // 1 - indywidualny
            // 2 - grupowy
            'mode_players' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            // Status testu
            // 0 - nieaktywny
            // 1 - aktywny
            'status' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'time' => array(
                'type' => 'integer',
                'notnull' => true
            ),
        ));
    }

    public function postUp()
    {
        // Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `author_id` ');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');        
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
?>
