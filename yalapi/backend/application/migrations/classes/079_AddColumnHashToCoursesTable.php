<?php

class AddColumnHashToCoursesTable extends Doctrine_Migration_Base
{
    private $_tableName = 'courses';
    private $_columName = 'hash';

    private $_hashSalt = 'sxGFuk5PMaOZjYZV3hy3y/n6Ul3bRc+Q';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columName, 'varchar', '256', array('notnull' => true, 'default' => 'empty'));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columName);
    }

    public function postDown()
    {
        $this->_makeQuery("SELECT create_acl_view('".$this->_tableName."')");
    }

    public function preUp()
    {
        $this->preDown();
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute("UPDATE ".$this->_tableName." SET ". $this->_columName ." = md5(" . $this->_tableName . ".id || '". $this->_hashSalt ."') WHERE ". $this->_columName ." = 'empty'");
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



