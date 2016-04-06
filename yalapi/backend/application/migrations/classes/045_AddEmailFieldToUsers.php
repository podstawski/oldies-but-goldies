<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class AddEmailFieldToUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_colName = 'email';
    private $_constraintName = 'uq_email';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName, 'varchar', 256, array('notnull' => false));
        $this->createConstraint($this->_tableName, $this->_constraintName, array(
            'fields' => array(
                'email' => array()
            ),
            'unique' => true
        ));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName);
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
