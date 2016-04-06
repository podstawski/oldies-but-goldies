<?php
/**
 * Description
 * @author Radosław Benkel
 */
 
class CreateTableQuiz extends Doctrine_Migration_Base
{
    private $_tableName = 'quiz_scores';
    private $_fkName = 'quiz_scores_quizes';
    private $_fkName2 = 'quiz_scores_users';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'quiz_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'level' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'score' => array(
                'type' => 'bigint',
                'notnull' => true,
                'comment' => 'Also used as a best result'
            ),
            'start_time' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
            'total_time' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local'         => 'quiz_id',
            'foreign'       => 'id',
            'foreignTable'  => 'quizzes',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
            'local'         => 'user_id',
            'foreign'       => 'id',
            'foreignTable'  => 'users',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropTable($this->_tableName);
    }
}
