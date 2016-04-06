<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GameWizardController extends Game_Controller
{
    public function init()
    {
        parent::init();

        $this->view->layout()->setLayout('simple');

        if (Game_Server::isDefaultGameServer() == false || $this->_isWizardEnabled() == false)
            $this->_redirectToDefaultGameServer();
    }

    public function indexAction()
    {
        $form = new Form_GameWizard;

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            Game_Server::createGameServer($form->getValue('name'), Model_Player::getEmail());
            $this->_flash('game server created');
            $this->_redirectToDefaultGameServer();
        }

        $this->view->form = $form;
    }
}