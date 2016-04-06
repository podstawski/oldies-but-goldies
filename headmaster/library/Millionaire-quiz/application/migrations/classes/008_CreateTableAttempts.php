<?php
class CreateTableAttempts extends Doctrine_Migration_Base
{
    private $_tableName = 'attempts';
    
    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'test_pass' => array(
                'type' => 'character varying(256)',
                'notnull' => true
			), 
            'nick' => array(
                'type' => 'character varying(256)'
			), 
            'session_hash' => array(
                'type' => 'character varying(256)'
            ),
            // Serialized array
            'questions' => array(
                'type' => 'text'			
            ),
            // Serialized array
            'answers' => array(
                'type' => 'text'			
            ),
            // Serialized array
            'answers_time' => array(
                'type' => 'text'
            ),
	    	// Serialized array
            'lifebuoys' => array(
                'type' => 'character varying(256)'
            ),
            // Serialized array
            'lifebuoys_time' => array(
                'type' => 'text'		
            ),
            'time_left' => array(
                'type' => 'integer'
            ),
            'time_started' => array(
                'type' => 'integer'
            ),
            'points' => array(
                'type' => 'integer'
            ),
            'step' => array(
                'type' => 'integer'
            ),
            'status' => array(
                'type' => 'integer'
            ),            
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN time_left SET DEFAULT 0');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN time_started SET DEFAULT 0');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN points SET DEFAULT 0');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN step SET DEFAULT 0');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN status SET DEFAULT 1');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
?>
