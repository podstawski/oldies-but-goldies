<?php

class CreateTableSkills extends Doctrine_Migration_Base
{
    private $_tableName = 'skills';
    private $_fkName1 = 'fk_skills_competencies';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'competence_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'min' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
            'max' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
            'description' => array(
                'type' => 'Text',
                'notnull' => false,
            ),

        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'competence_id',
             'foreign'       => 'id',
             'foreignTable'  => 'competencies',
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
