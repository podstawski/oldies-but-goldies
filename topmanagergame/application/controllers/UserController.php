<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class UserController extends Game_Controller_Abstract
{
    public function registerAction()
    {
        $this->_checkRegistrationClosed();

        $form = new Form_Register();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $userData = $form->getValues();
            $userData['username'] = $userData['email'];
            $userData['passwordClean'] = $userData['password'];

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $user = Game_Server::createGameUser($userData);
                $modelCompany = new Model_Company();
                $modelCompany->createCompany($userData, $user);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->_flash($e->getMessage());
                $this->_redirectExit('register');
            }

            $this->_doLogin($userData);
        }

        $form->setTableLayout();
        $this->view->form = $form;
    }

    public function welcomeAction()
    {
        $this->_checkRegistrationClosed();

        Model_Player::init(Zend_Auth::getInstance()->getIdentity()->id);

        $form = new Form_Company();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $companyData = $form->getValues();
            $modelCompany = new Model_Company;
            $modelCompany->createCompany($companyData, Model_Player::getUser());
            $this->_redirectExit('index', 'office');
        }

        if ($errors = $form->getMessages()) {
            foreach ($errors as $error) {
                foreach ($error as $message) {
                    $this->_flash($message);
                }
            }
        }

        $form->setTableLayout();
        $this->view->form = $form;

        if ($demoUrl = Model_Param::get('general.demo_server')) {
            $this->view->demoUrl = sprintf($demoUrl, Model_Player::getEmail());
        }
    }

    public function indexAction()
    {
        $this->_forward('login');
    }

    public function loginAction()
    {
        $this->_checkLoginClosed();

        $form = new Form_Login();
        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $formData = $form->getValues();
            $this->_doLogin($formData);
        }
        $this->view->form = $form;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $session = new Zend_Session_Namespace('remote-login');
        if ($session->identity) {
            $userID = (int) $session->identity->id;
            $session->unsetAll();
            $this->_doLogin($userID);
        }
        $redirect = $this->view->absoluteUrl(array(
            'action' => 'index',
            'controller' => 'index'
        ));
        $this->rlogout(Model_Player::getEmail(), $redirect);
        $this->_redirect($redirect);
    }

    public function remoteLoginAction()
    {
        $userID = $this->_getParam('user-id');
        if ($userID == null) {
            $this->_flash('no user ID provided');
            $this->_redirectBack();
        }

        $modelUser = new Model_User();
        $user = $modelUser->find($userID)->current();
        if ($user == null) {
            $this->_flash('user not found');
            $this->_redirectBack();
        }

        if (!(Model_Player::isAdmin() || Model_Player::isTeacherOfStudent($userID)))
            $this->getAcl()->denyAccess(true);

        $session = new Zend_Session_Namespace('remote-login');
        $session->identity = Zend_Auth::getInstance()->getIdentity();
        $this->_doLogin($user);
    }
}