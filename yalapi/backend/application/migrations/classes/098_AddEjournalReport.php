<?php

class AddEjournalReport extends Doctrine_Migration_Base
{
    private $_tableName = 'reports';
    private $_reportID  = 12;

    public function up()
    {
        if (!Doctrine_Manager::connection()->fetchRow('SELECT * FROM ' . $this->_tableName . ' WHERE id = ' . $this->_reportID)) {
            Doctrine_Manager::connection()->execute('INSERT INTO ' . $this->_tableName . ' (id, name, description, path) VALUES (' . $this->_reportID . ', \'E-dziennik\', \'E-dziennik\', \'e-dziennik.jrxml\')');
        }
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('DELETE FROM ' . $this->_tableName . ' WHERE id = ' . $this->_reportID);
    }
}
