<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class Game_Server_Db
{
    /**
     * @param string $gameServerName
     * @return Zend_Db_Adapter_Abstract
     */
    public static function factory($gameServerName = Game_Server::DEFAULT_NAME)
    {
        $options = Zend_Registry::get('application_options');
        $options = $options['db'];
        $adapter = 'pdo_' . $options['adapter'];
        unset($options['adapter']);

        if ($gameServerName != Game_Server::DEFAULT_NAME) {
            $options['dbname'] .= '_' . $gameServerName;

            if ($options['prefix'])
                $options['dbname'] = $options['prefix'] . '_' . $options['dbname'];
        }

        unset($options['prefix']);

        $db = Zend_Db::factory($adapter, $options);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        return $db;
    }
}