<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gmail.com>
 */

class CreateTableRankSchool extends Doctrine_Migration_Base
{
    private $_tableName = 'rank_school';
    private $_fkName = 'fk_rank_school_school';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'school_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'score' => array(
                'type' => 'bigint',
                'notnull' => true,
            ),
            'employee_amount' => array(
                'type' => 'int',
                'notnull' => true
            ),
            'quiz_score' => array(
                'type' => 'smallint',
                'notnull' => true
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local' => 'school_id',
            'foreign' => 'id',
            'foreignTable' => 'school',
            'onDelete' => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropTable($this->_tableName);
    }
}