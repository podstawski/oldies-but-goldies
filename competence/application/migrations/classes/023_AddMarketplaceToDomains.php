<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AddMarketplaceToDomains extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';
	private $_colName = 'marketplace';

	public function up()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' SMALLINT NOT NULL DEFAULT 0');
	}

	public function down()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
	}
}