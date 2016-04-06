<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class IndexController extends Game_Controller_Abstract
{
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            if (Game_Server::isDefaultGameServer() == true && $this->_isWizardEnabled() == true) {
                $modelGameServer = new Model_GameServer;
                $userGames = $adminGames = array();
                foreach ($modelGameServer->fetchAll() as $gameServer) {
                    if ($gameServer->isAdmin($identity->email) == true)
                        $adminGames[] = $gameServer;
                    else if ($gameServer->hasUser($identity->id) == true)
                        $userGames[] = $gameServer;
                }

                $this->view->adminGames = $adminGames;
                $this->view->userGames  = $userGames;
            } else {
                $this->_redirectExit('index', 'office');
            }
        } else {
            $this->_forward('login', 'user');
        }
    }
}

