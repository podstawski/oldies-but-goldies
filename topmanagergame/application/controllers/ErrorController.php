<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $this->_helper->layout->disableLayout();

        $errors = $this->_getParam('error_handler');
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        $this->view->title = 'Sorry...';
        $this->view->message = 'Application error';

        if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER) {
            $this->getResponse()->setHttpResponseCode(500);
        } else {
            $this->getResponse()->setHttpResponseCode(404);
            $this->view->message = 'Page not found';
        }

        if ($errors->exception instanceof Exception) {
            // SIM log errors
            Zend_Registry::get('logger')->log($errors->exception, Zend_Log::ERR, array(
                'uid'      => Model_Player::getUserId(),
                'username' => Model_Player::getUsername()
            ));
            $this->view->message = $errors->exception->getMessage();
        }

        if ($errors->exception instanceof Game_Exception_NoGameServer) {
            $this->view->backUrl = Game_Server::getDefaultGameServerUrl();
        }
        
        if ($this->getInvokeArg('displayExceptions')) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }
}

