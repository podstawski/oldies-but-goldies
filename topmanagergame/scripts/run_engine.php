<?php
/**
 * @author Radosław Benkel
 */

list (, $force) = $argv;

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('APPLICATION_ENV', 'development');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$databases = array();
$databases[] = 'www';

$modelGameServer = new Model_GameServer;
foreach ($modelGameServer->fetchAll() as $gameServer)
    $databases[] = $gameServer->name;

foreach (array_unique($databases) as $gameServerName)
{
    Game_Server::bootstrap($gameServerName);
    Game_Server::init();

    try {
        $runEvery = Model_GameData::getData(Model_GameData::ENGINE_RUN_EVERY);
        $runAt    = Model_GameData::getData(Model_GameData::ENGINE_RUN_AT);

        if ($force || (($runEvery > 0) && (date('i') % $runEvery == 0)) || (($runEvery == 0) && (date('H:i') == $runAt))) {
            $time = time();
            // SIM uaktualnij wpis o ostatnim odpaleniu engine'a
            Model_GameData::setData(Model_GameData::LAST_ENGINE_RUN, $time);

            $engine = new Playgine_Engine();
            $engine->setDay(Model_Day::getToday());
            $counter = $engine->run();

            if ($force == false) {
                // SIM uwaktualnij wpis o następnym odpaleniu engine'a
                if ($runEvery > 0) {
                    $time = $time + $runEvery * 60;
                } else {
                    $time = $time + 86400;
                }
                Model_GameData::setData(Model_GameData::NEXT_ENGINE_RUN, $time);
            }

            echo $gameServerName . ': ' . $counter . ' tasks procesed' . PHP_EOL;
        } else
            echo $gameServerName . ': skipped'. PHP_EOL;

    } catch (Exception $e) {
        echo $gameServerName . ': ' . $e->getMessage() . PHP_EOL;
        file_put_contents(__DIR__ . '/run_engine.log', print_r($e, 1) . PHP_EOL, FILE_APPEND);
    }
}

