<?php

class AlterAnswerChangeAnswer extends Doctrine_Migration_Base
{
    private $_tableName1 = 'answers';
    private $_tableName2 = 'questions';
    private $_columnName1 = 'answer';
    private $_columnName2 = 'media';

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName1 . ' ALTER COLUMN '.$this->_columnName1.' TYPE text');
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName2 . ' ALTER COLUMN '.$this->_columnName2.' TYPE text');
	}

}
