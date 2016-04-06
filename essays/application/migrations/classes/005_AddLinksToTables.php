<?php
class AddDomainIdToExams extends Doctrine_Migration_Base
{
    private $_tableName1 = 'participants';
    private $_tableName2 = 'tests';
    private $_colName1 = 'document_link';
	private $_colName2 = 'document_embed_link';

    public function up()
    {
        $this->addColumn($this->_tableName1, $this->_colName1, 'character varying(256)', null, array('notnull' => false));
        $this->addColumn($this->_tableName1, $this->_colName2, 'character varying(256)', null, array('notnull' => false));
        $this->addColumn($this->_tableName2, $this->_colName1, 'character varying(256)', null, array('notnull' => false));
        $this->addColumn($this->_tableName2, $this->_colName2, 'character varying(256)', null, array('notnull' => false));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName1, $this->_colName1);
        $this->removeColumn($this->_tableName1, $this->_colName2);
        $this->removeColumn($this->_tableName2, $this->_colName1);
        $this->removeColumn($this->_tableName2, $this->_colName2);
    }
}

