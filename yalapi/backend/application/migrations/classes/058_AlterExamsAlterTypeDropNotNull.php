<?php

class AlterExamsAlterTypeDropNotNull extends Doctrine_Migration_Base
{
    private $_tableName = 'exams';
    private $_colName = 'type';

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' DROP NOT NULL');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . " = 'foo'");
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' SET NOT NULL');
    }
}
