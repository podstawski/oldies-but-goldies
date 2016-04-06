<?php

class CreateTableCompetencies extends Doctrine_Migration_Base
{
    private $_tableName = 'competencies';
    private $_fkName1 = 'fk_competencies_projects';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
			'project_id' => array(
				'type' => 'integer',
				'notnull' => true
			),
            'name' => array(
                'type' => 'character varying(255)',
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'text',
                'notnull' => false,
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'project_id',
             'foreign'       => 'id',
             'foreignTable'  => 'projects',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }


    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
?>
