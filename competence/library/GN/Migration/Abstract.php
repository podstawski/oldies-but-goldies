<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class GN_Migration_Abstract extends Doctrine_Migration_Base
{
    /**
     * @param string $query
     * @param array $params
     * @return int
     */
    public function exec($query, array $params = array())
    {
        return Doctrine_Manager::connection()->exec($query, $params);
    }

    public function up()
    {
        $this->executeMigrationSQL('up');
    }

    public function down()
    {
        $this->executeMigrationSQL('down');
    }

    /**
     * @param string $direction
     */
    protected function executeMigrationSQL($direction)
    {
        $reflection = new ReflectionClass($this);
        $fileName = $reflection->getFileName();
        $fileName = dirname($fileName) . DIRECTORY_SEPARATOR . substr(basename($fileName), 0, -4) . '_' . $direction . '.sql';
        if (file_exists($fileName))
            $this->exec(file_get_contents($fileName));
    }
}