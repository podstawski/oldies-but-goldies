<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_GameServer extends Zend_Db_Table_Abstract
{
    protected $_name = 'game_server';
    protected $_rowClass = 'Model_GameServerRow';

    protected $_dependentTables = array('Model_GameServerUser');

    /**
     * @param $gameServerName
     * @return Model_GameServerRow
     */
    public function fetchGameServer($gameServerName = null)
    {
        if ($gameServerName == null)
            $gameServerName = Game_Server::resolveName();

        return $this->fetchRow(array(
            'name = ?' => $gameServerName
        ));
    }

    /**
     * @param string $gameServerName
     * @param string $gameAdminEmail
     *
     * @return Model_GameServerRow
     */
    public function createGameServer($gameServerName, $gameAdminEmail)
    {
        $gameServer = $this->createRow();
        $gameServer->name = $gameServerName;
        $gameServer->admin_email = $gameAdminEmail;
        $gameServer->save();

        return $gameServer;
    }
}