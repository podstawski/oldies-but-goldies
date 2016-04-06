<?php

require_once 'RestController.php';

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        if ($errors->exception instanceof Exception) {
            // SIM log errors
            Zend_Registry::get('logger')->log($errors->exception, Zend_Log::ERR, array(
                'uid'      => Yala_User::getUid(),
                'username' => Yala_User::getUsername()
            ));

            // SIM if error is caused by rest controller, respond with json
            $controllerClass = $this->getFrontController()->getDispatcher()->getControllerClass($errors->request);
            if (is_subclass_of($controllerClass, 'RestController')) {
                $message = $errors->exception->getMessage();
                if (strpos($message, 'no password supplied') !== false) {
                    $message = '#logout#';
                }
                $this->_response->setBody(json_encode(array('message' => $message)));
                $this->_response->setHttpResponseCode(500);
                $this->_response->sendHeaders();
                $this->_response->sendResponse();
                exit();
            }
            // SIM google api
            $this->checkGoogleApiErrors($errors->exception);
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    protected function checkGoogleApiErrors($exception = null)
    {
        if ($exception) {
            $this->view->is_google_exception = true;

            preg_match("/<title>(.*)<\/title>/siU", $exception->getMessage(), $messages);
            $this->view->errorMessage = isset($messages[1]) ? $messages[1] : $exception->getMessage();
            $this->view->errorMessage = self::_clearString($this->view->errorMessage);
            $this->view->errorHints = array_key_exists($this->view->errorMessage, self::$hints) ? self::$hints[$this->view->errorMessage] : null;
        }
    }

    private static function _clearString($message)
    {
        $message = trim($message);
        $message = strip_tags($message);
        $message = str_replace("\r", "", $message);
        $message = str_replace("\n", "", $message);
        $message = str_replace("\t", "", $message) ;
        return $message;
    }

    private static $hints = array(
        'Domain cannot use API' => array(
            '<b>Przejdź do panelu zarzadzania Google Apps</b>',
            '<pre>google.com/a/twoja-domena</pre>',
            '<b>Wybierz kolejno:</b>',
            'Ustawienia domeny',
            'Ustawienia użytkownika <br /><br />',
            '<b>Zaznacz opcję</b>',
            'Włącz interfejs API do obsługi administracyjnej'
        ),
        'OpenID canceled' => array(
            'Następnym razem, wyraź zgodę na wykorzystanie danych<br /><br />',
            '<b>Potrzebne dane to:</b>',
            'imię, nazwisko oraz adres email',
            'wykorzystamy je jedynie do identyfikacji użytkownika'
        ),
        'Could not retrieve a valid Token response from Token URL:The request token is invalid.' => array(
            '<b>Prawdopodobnie nie zgodziłeś się na uzyskanie dostępu do API za pomocą Twojego konta</b>',
            'Powtórz procedurę logowania i wyraź zgodę, o ile jesteś administratorem domeny'
        ),
        'Response from Service Provider is not a valid authorized request token' => array(
            '<b>Prawdopodobnie nastąpiła nieudana próba zalogowania i autoryzacji API</b>',
            'Uruchom przeglądarkę ponownie i powtórz procedurę logowania'
        ),
        'You are not authorized to access this API' => array(
            '<b>Nie jesteś administratorem w tej domenie</b>',
            'nie możesz jako pierwszy zalogować się z tej domeny, poniewaz nie masz odpowiednich uprawnień'
        )
    );
}