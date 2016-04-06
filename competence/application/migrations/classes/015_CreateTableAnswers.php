<?php

class CreateTableAnswers extends Doctrine_Migration_Base
{
    private $_tableName = 'answers';
    private $_fkName1 = 'fk_answers_questions';
    private $_fkName2 = 'fk_answers_users';
    private $_fkName3 = 'fk_answers_exams';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'user_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'question_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'exam_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'answer_value' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
			'answer_time' => array(
				'type' => 'timestamp',
				'notnull' => true,
			),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array
		(
             'local'         => 'question_id',
             'foreign'       => 'id',
             'foreignTable'  => 'questions',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

		$this->createForeignKey($this->_tableName, $this->_fkName2, array
		(
			'local'		=> 'user_id',
			'foreign'	=> 'id',
			'foreignTable'	=> 'users',
			'onDelete'	=> 'CASCADE',
			'onUpdate'	=> 'CASCADE'
		));


		$this->createForeignKey($this->_tableName, $this->_fkName3, array
		(
			'local'		=> 'exam_id',
			'foreign'	=> 'id',
			'foreignTable'	=> 'exams',
			'onDelete'	=> 'CASCADE',
			'onUpdate'	=> 'CASCADE'
		));

    }

	public function postUp()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN answer_time SET DEFAULT NOW()');
	}

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropForeignKey($this->_tableName, $this->_fkName3);
        $this->dropTable($this->_tableName);
    }
}
