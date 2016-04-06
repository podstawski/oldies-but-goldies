<?php

abstract class RestController extends Zend_Rest_Controller
{
    const HTTP_OK                   = 200;
    const HTTP_CREATED              = 201;
    const HTTP_NO_CONTENT           = 204;
    const HTTP_BAD_REQUEST          = 400;
    const HTTP_UNAUTHORIZED         = 401;
    const HTTP_NOT_FOUND            = 404;
    const HTTP_METHOD_NOT_ALLOWED   = 405;
    const HTTP_NOT_ACCEPTABLE       = 406;
    const HTTP_CONFLICT             = 409;
    const HTTP_SERVER_ERROR         = 500;

    const DEFAULT_OUTPUT_FORMAT = 'json';

    protected $_modelName = null;
    protected $_autoPager = true;

    protected $_forceClearIdentity = false;

    public function init()
    {
        $actionType = $this->getRequest()->getActionName();
        if (strpos('put,post,delete', $actionType) !== false) {
            try {
                Log::create(array(
                    'url'      => $_SERVER['REQUEST_URI'],
                    'type'     => $actionType,
                    'data'     => json_encode($this->_getRequestData(strtoupper($actionType))),
                    'username' => Yala_User::getUsername(),
                    'ip'       => $_SERVER['REMOTE_ADDR'],
                    'date'     => date('Y-m-d H:i:s')
                ));
            } catch (Exception $e) {

            }
        }

        if ($this->_modelName === null)
        {
            $controllerName = $this->_request->getControllerName();
            if (substr($controllerName, -1) == 's') {
                $modelName = ucfirst(substr($controllerName, 0, -1));
            } else {
                $modelName = ucfirst($controllerName);
            }
            $this->_modelName = $modelName;
        }

        $googleapps = (object) Zend_Registry::get('oauth_options');

        if ($this->_forceClearIdentity = (Yala_User::getEmail() == null && $this->_hasParam('email') && $this->_hasParam('sig')))
        {
            $email  = $this->_getParam('email');
            list ($login, $domain) = explode('@', $email);

            if ($this->_getParam('sig') != GN_User::getSig($email, $googleapps->json_hash)) {
                $this->setRestResponseAndExit('invalid signature', self::HTTP_OK);
            }

            Yala_User::init(null, $domain);
            Yala_User::setIdentity('admin');

            if ($userRow = User::find_by_email($email)) {
                $userData = $userRow->to_array();
                $userData['plain_password'] = User::generatePassword($email);
                Yala_User::init($userData);
            } else {
                $this->setRestResponseAndExit('invalid email', self::HTTP_OK);
            }
        }

        if ($this->getRequest()->getParam('pager') && $this->_autoPager === true) {
            $this->_pagedData();
        }
    }

    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function postDispatch()
    {
        if ($this->_forceClearIdentity) {
            //Yala_User::getInstance()->clearIdentity();
        }
    }

    public function indexAction()
    {
        $columns = call_user_func(array($this->_modelName, 'table'))->columns;

        if ($conditions = array_intersect_key($this->getRequest()->getParams(), $columns)) {
            $conditions = array_filter($conditions, function($value){
                return !empty($value);
            });
            if ($conditions) {
                $expr = trim(implode(' = ? AND ', array_keys($conditions)) . ' = ?');
                $conditions = array_values($conditions);
                array_unshift($conditions, $expr);
            }
        } else {
            $conditions = array();
        }

        $params = array(
            'from' => $this->_getTableNameFromModelClass($this->_modelName),
            'conditions' => $conditions,
        );

        $data = call_user_func_array(
            array($this->_modelName, 'all'),
            array($params)
        );

        array_walk($data, function(&$item) {
            $item = $item->to_array();
        });

        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }


    public function getAction()
    {
        try {
            $row = $this->_getById();
            $this->setRestResponseAndExit($row->to_array(), self::HTTP_OK);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error retrieving record', self::HTTP_NOT_FOUND);
        }
    }

    public function postAction()
    {
        $row = call_user_func_array(array($this->_modelName, 'create'), array($this->_getRequestData('POST')));
        if ($row->is_valid()) {
            $row->save();
            $this->setRestResponseAndExit($row->to_array(), self::HTTP_CREATED);
        } else {
            $this->_log($row);
            $this->setRestResponseAndExit('there was an error adding new record', self::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function putAction($putData = null)
    {
        try {
            $row = $this->_getById(true);
            if ($putData == null) {
                $putData = $this->_getRequestData('PUT');
            }
            $row->set_attributes($putData);
            if ($row->is_valid()) {
                $row->save();
                $this->setRestResponseAndExit(null, self::HTTP_OK);
            } else {
                $this->_log($row);
                $this->setRestResponseAndExit('there was an error updating record', self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (ActiveRecord\UndefinedPropertyException $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error updating record', self::HTTP_NOT_ACCEPTABLE);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error retrieving record', self::HTTP_NOT_FOUND);
        }
    }

    public function deleteAction()
    {
        try {
            $row = $this->_getById(true);
            $row->delete();
            $this->setRestResponseAndExit(null, self::HTTP_NO_CONTENT);
        } catch (ActiveRecord\DatabaseException $e) {
            //RB constraint fail
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error retrieving record', self::HTTP_CONFLICT);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->_log($e);
            $this->setRestResponseAndExit('there was an error retrieving record', self::HTTP_NOT_FOUND);
        }

    }

    // RB that long method name is intenionally (another are taken by Zend_Controller_Action) and it's public by purpose
    public function setRestResponseAndExit($data = null, $code = null, $format = null)
    {
        $this->postDispatch();

        if (!is_null($data)) {

            if (is_object($data)) {
                $data = (array) $data;
            }

            if (is_string($data)) {
                if (($translator = Zend_Registry::get('Zend_Translate'))) {
                    $data = $translator->translate($data);
                }
                $data = array('message' => $data);
            }

            if (is_null($format)) {
                $format = $this->_request->getParam('format', self::DEFAULT_OUTPUT_FORMAT);
            }

            $responseBody = $this->_formatResponseData($data, $format);
            $this->_response->setBody($responseBody);
        }
        if (!is_null($code)) {
            $this->_response->setHttpResponseCode($code);
        }
        $this->_response->sendHeaders();
        $this->_response->sendResponse();
        exit();
    }

    /**
     * @return ActiveRecord\Model
     */
    protected function _getById($ignoreView = false)
    {
        $tableName = $this->_getTableNameFromModelClass($this->_modelName, $ignoreView);
        $id = $this->_getParam('id');
        $row = call_user_func_array(
            array($this->_modelName, 'find'),
            array(
                $id,
                array('from' => $tableName)
            )
        );

        if (is_null($row)) {
            throw new ActiveRecord\RecordNotFound($this->view->translate('there was an error retrieving record'));
        }
        
        return $row;
    }

    /**
     * Gets param by $paramName and transforms it like this:
     * '1,2,3' -> array(1,2,3)
     * @param string $paramName
     * @param string|array $dataOrType
     * @return array
     */
    protected function _getParamArray($paramName = 'id', $dataOrType = 'GET')
    {
        $data = array();
        if (is_array($dataOrType)) {
            $data = $dataOrType;
        } elseif ($dataOrType === 'GET') {
            $data = array($paramName => $this->_getParam($paramName));
        } elseif ($dataOrType === 'POST' || $dataOrType === 'PUT') {
            $data = $this->_getRequestData($dataOrType);
        }
        $result = array();
        if (isset($data[$paramName])) {
            $result = explode(',', $data[$paramName]);
            foreach ($result as $key => $value) {
                if (!is_numeric($value)) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

    protected function _getTableNameFromModelClass($className, $ignoreView = false)
    {
        $tableName = (!is_null($className::$table_name))
            ? $className::$table_name
            : call_user_func(array($className, 'table'))->table;

        if (!$ignoreView && property_exists($className, 'use_view') && $className::$use_view === true) {
            $tableName .= '_view';
        }

        return $tableName;
    }

    protected function _formatResponseData($data = array(), $format)
    {
        switch ($format) {
            case 'json':
                return json_encode($data);
                break;
            case 'xml':
                return Yala_Tools_Array::toXml($data);
                break;
            default:
                throw new InvalidArgumentException('That format is not supported');
        }
    }

    protected function _pagedData(\Closure $dataFilter = null)
    {
        if ($this->_modelName) {
            $options = $this->_getPagerOptionsForModel();

            if (array_key_exists('total_records', $options)) {
                unset($options['total_records']);
                unset($options['include']); //RB library is broken, with include key it fails to make a query
                $options['select'] = 'COUNT(*) AS total_records';

                if (!isset($options['from'])) {
                    $options['from'] = $this->_getTableNameFromModelClass($this->_modelName);
                }
                
                $data = call_user_func_array(
                    array($this->_modelName, 'all'),
                    array($options)
                );

                $sumTotal = isset($options['group']) ? count($data) : $data[0]->total_records;

                $this->setRestResponseAndExit(array('total_records' => $sumTotal), self::HTTP_OK);
            } else {

                if (!isset($options['from'])) {
                    $options['from'] = $this->_getTableNameFromModelClass($this->_modelName);
                }

                $data = call_user_func_array(
                    array($this->_modelName, 'all'),
                    array($options)
                );

                $dataFilter = $dataFilter ?: function(&$item) use ($options) {
                    static $counter = 1;
                    $item = $item->to_array();
                    $item['_lp'] = ($options['offset'] + $counter);
                    $counter++;
                };
                
                array_walk($data, $dataFilter);

                $this->setRestResponseAndExit($data, self::HTTP_OK);
            }
        }
    }

    protected function _getPagerOptionsForModel()
    {
        $pager = $this->getRequest()->getParam('pager', array());

        if (!is_array($pager)) {
            $this->setRestResponseAndExit(null, self::HTTP_NOT_ACCEPTABLE);
        }

        $options = array();
        $tableName = $this->_getTableNameFromModelClass($this->_modelName);
        $columns = call_user_func(array($this->_modelName, 'table'))->columns;

        if (array_key_exists('search', $pager) && !empty($pager['search'])) {
            $search     = $pager['search'];
            $conditions = array();
            foreach ($columns as $name => $column) {
                $conditions[$name] = "CAST(" . $tableName . "." . $name . " AS VARCHAR) ILIKE '%" . $search . "%'";
            }
            if ($conditions) {
                $options['conditions'] = '(' . implode(' OR ', $conditions) . ')';
            }
        }

        $conditions = array();
        
        array_walk($pager, function($value, $key) use (&$conditions) {
            if ((is_null($value) || !empty($value) || (string) $value === '0')
                && !in_array($key, array('total_records', 'search', 'offset', 'limit', 'order'))
            ) {
                if (is_numeric($value)) {
                    $conditions[$key] = $key . ' = ' . $value;
                } elseif (is_null($value)) {
                    $conditions[$key] = $key . ' IS NULL';
                } else {
                    $conditions[$key] = $key . " = '" . $value . "'";
                }
            }
        });
        
        if (!empty($conditions)) {
            $conditions = implode(' AND ', $conditions);
            if (array_key_exists('conditions', $options)) {
                $options['conditions'] = $conditions . ' AND (' . $options['conditions'] . ')';
            } else {
                $options['conditions'] = $conditions;
            }
        }

        if (array_key_exists('total_records', $pager)) {
            $options['total_records'] = true;
            return $options;
        }

        $options['offset'] = array_key_exists('offset', $pager) ? $pager['offset'] : 0;
        $options['limit']  = array_key_exists('limit', $pager)  ? $pager['limit']  : 50;
        $options['order']  = '1 ASC';

        if (array_key_exists('order', $pager)) {
            $sortColumn    = $pager['order'];
            $sortDirection = 'ASC';
            if (substr($sortColumn, 0, 1) == '-') {
                $sortColumn    = substr($sortColumn, 1);
                $sortDirection = 'DESC';
            }
            if (array_key_exists($sortColumn, $columns) && $this->_isStringTypeColumn($columns[$sortColumn])) {
                $options['order'] = "lower(" . $tableName . "." . $sortColumn . ") " . $sortDirection;
            } else {
                $options['order'] = $sortColumn . ' ' . $sortDirection;
            }
        }

        return $options;
    }

    private function _isStringTypeColumn(ActiveRecord\Column $column)
    {
        return $column::STRING === $column->map_raw_type();
    }

    /**
     * Replaces string null values with real NULL values, because HTTP sends null values as strings:
     * {a: null, b: 'foo'} => a=null&b=foo
     * @param $type string POST or PUT
     * @return array
     */
    protected function _getRequestData($type)
    {
        $data = array();
        //RB if we send multipart/form-data (files with form values), we must take data from POST
        if ($type === 'POST' || strstr($this->_request->getHeader('Content-Type'), 'multipart/form-data')) {
            $data = $this->_request->getPost();
        } elseif ($type === 'PUT') {
            parse_str($this->_request->getRawBody(), $data);
        }
        unset($data['_method'], $data['__formatted']);
        array_walk($data, function(&$item) {
            if ($item === 'null') {
                $item = NULL;
            }
        });
        return $data;
    }

    protected function _getBaseUrl($replace = false)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 's' : '';
        $url = "http$url://" . $_SERVER['HTTP_HOST'] . $this->_request->getBaseUrl();
        if ($replace) {
            $url = str_replace('index.php', '', $url);
        }
        return $url;
    }

    /**
     * @return GN_Logger
     */
    protected function _getLogger()
    {
        if (Zend_Registry::isRegistered('logger'))
            return Zend_Registry::get('logger');
    }

    /**
     * @param String|Exception|ActiveRecord\Model $e
     */
    protected function _log($e)
    {
        if ($logger = $this->_getLogger())
        {
            if ($e instanceof ActiveRecord\Model)
                $e = $e->errors->__toString();

            $logger->log($e, Zend_Log::ERR, array(
                'uid'      => Yala_User::getUid(),
                'username' => Yala_User::getUsername()
            ));
        }
    }
}

