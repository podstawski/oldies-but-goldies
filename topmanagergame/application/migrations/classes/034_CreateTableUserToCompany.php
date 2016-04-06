<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableUserToCompany extends Doctrine_Migration_Base
{
    private $_tableName = 'user_to_company';
    private $_fkName1 = 'fk_user_to_company_user';
    private $_fkName2 = 'fk_user_to_company_company';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'email' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
            'company_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB',
            'charset' => 'utf8',
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
            'local' => 'user_id',
            'foreign' => 'id',
            'foreignTable' => 'users',
            'onDelete' => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('INSERT INTO ' . $this->_tableName . ' (email, user_id, company_id)
            SELECT email, user_id, company.id
            FROM users
            INNER JOIN company ON company.user_id = users.id');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}