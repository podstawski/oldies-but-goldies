<?php

class CreateTableQuestions extends Doctrine_Migration_Base
{
	private $_tableName = 'questions';
	
	function up()
	{
		$this->createTable($this->_tableName, array(
			'id' => array(
				'type' => 'integer',
				'notnull' => true,
				'primary' => true,
				'autoincrement' => true,
			),
			/*
			'correct_answer_id' => array(
				'type' => 'integer'
			),
			 */
			'author_id' => array(
				'type' => 'integer'
			),
			'question' => array(
				'type' => 'text'
			),
			'question_hash' => array(
				'type' => 'character varying(32)'
			),
			/*
			'created' => array(
				'type' => 'timestamp',
				'notnull' => true
			),
			 */
			'media' => array(
				'type' => 'character varying(256)'
			),
			'source' => array(
				'type' => 'character varying(256)'
			),
		));
	
	}

	public function postUp()
	{
		// Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `question` ');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');							
	}

	public function down()
	{
		//$this->dropForeignKey($this->_tableName, $this->_fkName);
		$this->dropTable($this->_tableName);
	}
}

?>
