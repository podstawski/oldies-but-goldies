<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class Game_Server
{
    const DEFAULT_NAME = 'www';

    /**
     * @var string
     */
    protected static $_name;

    /**
     * @var Model_GameServerRow
     */
    protected static $_gameServer;

    /**
     * @param string $gameServerName
     * @return string
     */
    public static function bootstrap($gameServerName = null)
    {
        if ($gameServerName == null)
            $gameServerName = self::resolveName();

        $db = Game_Server_Db::factory($gameServerName);
        Zend_Registry::set('db', $db);
        Zend_Db_Table::setDefaultAdapter($db);

        self::$_name = $gameServerName;

        return self::$_name;
    }

    /**
     * @param string $gameServerName
     * @return Model_GameServerRow
     */
    public static function init()
    {
        if (self::$_name == null)
            self::bootstrap();

        $modelGameServer = new Model_GameServer;
        self::$_gameServer = $modelGameServer->fetchGameServer(self::$_name);

        Model_Day::reset();
        Model_GameData::reset();
        Model_Param::set(self::$_gameServer->game_params);

        return self::$_gameServer;
    }

    /**
     * @return string
     */
    public static function resolveName()
    {
        $gameServerName = self::DEFAULT_NAME;

        if (isset($_SERVER['HTTP_HOST']) && empty($_SERVER['HTTP_HOST']) == false) {
            $parts = explode('.', $_SERVER['HTTP_HOST']);
            if (count($parts) > 2)
                $gameServerName = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $parts[0]));
        }

        return $gameServerName;
    }

    /**
     * @return string
     */
    public static function getGameServerName()
    {
        return self::$_name;
    }

    /**
     * @return bool
     */
    public static function isDefaultGameServer()
    {
        return self::getGameServerName() == self::DEFAULT_NAME;
    }

    /**
     * @return string
     */
    public static function getDefaultGameServerUrl()
    {
        $options = Zend_Registry::get('application_options');
        $options = $options['topmanager'];
        return sprintf($options['url'], self::DEFAULT_NAME);
    }

    /**
     * @param array $data
     * @param int $role
     * @return Model_UserRow
     */
    public static function createGameUser(array $data, $role = Model_Player::ROLE_USER)
    {
        if (isset($data['passwordClean']))
            $data['password'] = Model_User::encryptPassword($data['passwordClean']);

        $modelUser = new Model_User;
        $gameUser = $modelUser->createRow($data);
        if (self::$_gameServer->admin_email == $data['email'])
            $role = Model_Player::ROLE_USER;
        $gameUser->role = $role;
        $gameUser->save();

        $mainDB = Game_Server_Db::factory(self::DEFAULT_NAME);

        $modelUser = new Model_User($mainDB);
        $modelGameServer = new Model_GameServer($mainDB);
        $modelGameServerUser = new Model_GameServerUser($mainDB);

        $mainUser = $modelUser->fetchUserByEmail($data['email']);
        $gameServer = $modelGameServer->fetchGameServer(self::$_name);

        $gameServerUser = $modelGameServerUser->createRow();
        $gameServerUser->user_id = $mainUser->id;
        $gameServerUser->game_server_id = $gameServer->id;
        $gameServerUser->save();

        return $gameUser;
    }

    /**
     * @param string $gameServerName
     * @param string $gameAdminEmail
     * @return Model_GameServerRow
     * @throws Exception
     */
    public static function createGameServer($gameServerName, $gameAdminEmail)
    {
        $mainDB = Game_Server_Db::factory();
        $gameDB = Game_Server_Db::factory($gameServerName);

        $gameDBname = $gameDB->getConfig();
        $gameDBname = $gameDBname['dbname'];

        try {
            if ($gameServerName != Game_Server::DEFAULT_NAME) {
                $mainDB->query('CREATE DATABASE ' . $gameDBname);

                require_once APPLICATION_PATH . '/../library/Doctrine/Doctrine.php';
                spl_autoload_register(array('Doctrine', 'autoload'));
                $connection = Doctrine_Manager::connection($gameDB->getConnection());
                $migration = new Doctrine_Migration(APPLICATION_PATH . '/migrations/classes', $connection);
                $migration->setTableName('doctrine_migration_version');
                $migration->migrate();

                $modelGameServer = new Model_GameServer($gameDB);
                $modelGameServer->createGameServer($gameServerName, $gameAdminEmail);

                $modelMapParams = new Model_MapParams($mainDB);
                $mapParams = $modelMapParams->fetchAll()->toArray();

                $modelMapParams = new Model_MapParams($gameDB);
                foreach ($mapParams as $mapData) {
                    unset($mapData['id']);
                    $modelMapParams->createRow($mapData)->save();
                }
            }

            $modelGameServer = new Model_GameServer($mainDB);
            return $modelGameServer->createGameServer($gameServerName, $gameAdminEmail);
        } catch (Exception $e) {
            if ($gameServerName != Game_Server::DEFAULT_NAME) {
                $mainDB->query('DROP DATABASE IF EXISTS ' . $gameDBname);
            }
            throw $e;
        }
    }
}