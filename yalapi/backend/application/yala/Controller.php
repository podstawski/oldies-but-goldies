<?php

abstract class Yala_Controller extends Zend_Controller_Action
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    /**
     * @var Zend_View_Helper_Translate
     */
    protected $_translator;

    /**
     * @var array
     */
    protected $_inputFilters = array();

    /**
     * @var array
     */
    protected $_inputValidators = array();

    public function init()
    {
        $this->_flashMessenger = $this->getHelper('FlashMessenger');
        $this->_translator     = Zend_Registry::get('Translator');

        $db = $this->getInvokeArg('bootstrap')->getOption('db');
        $adapter = $db['adapter'];
        unset($db['adapter'], $db['prefix']);
        $this->_db = Zend_Db::factory('pdo_' . $adapter, $db);
        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
    }

    /**
     * @return Zend_Filter_Input
     */
    protected function _filterInput($paramNames)
    {
        $input = new Zend_Filter_Input($this->_inputFilters, $this->_inputValidators);
        $data = array();
        foreach (func_get_args() as $paramName) {
            $data[$paramName] = $this->_getParam($paramName);
        }
        if ($data) {
            $input->setData($data);
        }
        return $input;
    }

    protected function _flash($message, $namespace = 'default')
    {
        if (is_array($message)) {
            $message = call_user_func_array(
                array(
                     $this->_translator,
                     'translate'
                ), $message
            );
        } else {
            $message = $this->_translator->translate($message);
        }
        if ($namespace) {
            $this->_flashMessenger->setNamespace($namespace);
        }
        $this->_flashMessenger->addMessage($message);
    }

    protected function _redirectExit($action, $controller = null, array $params = array())
    {
        $url = str_replace('/index.php', '', $this->view->makeUrl($action, $controller, $params));
        $this->_helper->redirector->gotoUrlAndExit($url);
    }

    protected function _getBaseUrl()
    {
        $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 's' : '';
        return "http$secure://" . $_SERVER['HTTP_HOST'] . $this->_request->getBaseUrl();
    }
}