<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class AddKeyFieldToUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_colName = 'key';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName, 'varchar', 256, array('notnull' => false));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName);
    }

    public function preUp()
    {
        $this->preDown();
    }

    public function postUp()
    {
        $this->postDown();
    }
    
    public function postDown()
    {
        $this->_makeQuery("SELECT create_acl_view('".$this->_tableName."')");
    }
    
    public function preDown()
    {
        $this->_makeQuery("SELECT drop_acl_view('".$this->_tableName."')");
    }
    
    private function _makeQuery($q)
    {
        Doctrine_Manager::connection()->exec($q);
    }    
}
