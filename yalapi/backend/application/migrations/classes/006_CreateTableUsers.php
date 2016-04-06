<?php
/**
 * Description
 * @author Tomasz Walotek
 */

class CreateTableUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_fkTableName = 'course_units';
    private $_fkName = 'fk_course_units_coaches';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'username' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'first_name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'last_name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'plain_password' => array(
                'type' => 'varchar(256)',
                'notnull' => false
            )
        ));
    }

    public function postUp()
    {
        //RB add admin user which is default to that connection
        $defaultUsername = $this->_getDefaultUsername();
        Doctrine_Manager::connection()->exec("INSERT INTO $this->_tableName (username, first_name, last_name) VALUES ('$defaultUsername', 'Administrator', 'Systemu')");
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }

    private function _getDefaultUsername()
    {
        $path = __DIR__ . '/../../configs/local.ini';

        if (!file_exists($path)) {
            throw new LogicException('File local.ini not found');
        }
        $config = parse_ini_file($path);
        return $config['db.username'];
    }
}
