<?php

class CreateTableExams extends Doctrine_Migration_Base
{
    private $_tableName = 'exams';
    private $_fkName1 = 'fk_exams_standards';

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
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'Text',
                'notnull' => false,
            ),
			'standard_id' => array(
				'type' => 'integer',
				'notnull' => false
			),
			'date_opened' => array(
				'type' => 'timestamp',
				'notnull' => true,
			),
			'date_closed' => array(
				'type' => 'timestamp',
				'notnull' => false,
			),

        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'standard_id',
             'foreign'       => 'id',
             'foreignTable'  => 'standards',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

	public function postUp()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN date_opened SET DEFAULT NOW()');
	}



    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
