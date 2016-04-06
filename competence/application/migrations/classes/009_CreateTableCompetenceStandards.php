<?php

class CreateTableCompetenceStandards extends Doctrine_Migration_Base
{
    private $_tableName = 'competence_standards';
    private $_fkName1 = 'fk_competence_standards_competencies';
    private $_fkName2 = 'fk_competence_standards_standards';

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
            'standard_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'value' => array(
                'type' => 'Decimal',
                'notnull' => true,
            ),


        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'competence_id',
             'foreign'       => 'id',
             'foreignTable'  => 'competencies',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
             'local'         => 'standard_id',
             'foreign'       => 'id',
             'foreignTable'  => 'standards',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

    }


    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropTable($this->_tableName);
    }


}
