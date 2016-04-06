<?php

class AddColumnGoogleDocsLinkToUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_columnName1 = 'google_docs_link';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'varchar(256)');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
