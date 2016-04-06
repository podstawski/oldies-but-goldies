<?php

class AppendStatusColumnToQuizScoresTable extends Doctrine_Migration_Base
{
    private $_tableName       = 'quiz_scores';
    private $_columnName      = 'status';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName, 'integer', null, array('notnull' => false, 'default' => 0));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName);
    }
}
