<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

require_once 'sfYaml/sfYamlParser.php';

class AdminController extends Game_Controller
{
    const CSV_DELIMITER = ';';

    /**
     * @var string
     */
    private $_gameParamsFile;

    /**
     * @var string
     */
    private $_gameParamsCommentsFile;

    /**
     * @var array
     */
    private $_paramsTables = array(
        'map_params',
        'tutorial',
    );

    /**
     * @var Zend_Db_Table
     */
    private $_paramsModel;

    /**
     * @var array
     */
    private $_paramsColumns;

    /**
     * @var array
     */
    private $_paramsData;

    /**
     * @var array
     */
    private $_paramsComments;

    public function init()
    {
        parent::init();

        $this->_gameParamsFile = APPLICATION_PATH . '/configs/gameParams.yaml';
        $this->_gameParamsCommentsFile = APPLICATION_PATH . '/language/'.Zend_Registry::get('Zend_Locale')->getLanguage().'/gameParamsComments.php';
        $this->_gameParamsInputsFile = APPLICATION_PATH . '/configs/gameParamsInputs.php';
        $this->view->paramsTables = $this->_paramsTables;
    }

    public function indexAction()
    {
        $this->_forward('list-users');
    }

    public function listUsersAction()
    {
        $datagrid = new Grid_Admin_Users();
        $datagrid->deploy();
        $this->view->grid = $datagrid;

        $this->render('grid');
    }

    public function deleteUserAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $modelUser = new Model_User();
            $user = $this->view->user = $modelUser->find($id)->current();
            if ($user == null) {
                throw new Exception('user with ID: ' . $id . ' was not found');
            }

            if ($this->view->confirm = $this->getRequest()->getParam('confirm', false)) {
                $username = $user->username;
                $user->delete();
                $this->_flash(array('username %s has been deleted', $username));
                $this->_redirectExit('list-users');
            }
        } else {
            throw new Exception("no user ID provided");
        }
    }

    public function runEngineAction()
    {
        $engine = new Playgine_Engine();
        $counter = $engine->run();

        $this->_flash(array('%s tasks processed', $counter));
        $this->_redirectBack();
    }

    public function editGameParamsAction()
    {
        if ($this->_request->isPost('data')) {
            $data = $this->_request->getPost('data');

            $newKeys = $this->_extractKeys($data);
            $oldKeys = $this->_extractKeys($this->_gameServer->game_params);

            if ($newKeys !== $oldKeys)
                throw new Exception('keys do not match');

            $this->_gameServer->game_params = $data;
            $this->_gameServer->save();
        }


        $comments = include_once $this->_gameParamsCommentsFile;
        $input_array = include_once $this->_gameParamsInputsFile;
        $this->view->tree = $this->_drawLevel($this->_gameServer->game_params, null, $comments, $input_array);
    }

    public function exportGameParamsAction()
    {
        $sfYaml = new sfYaml;
        header('Content-Type: text/x-yaml');
        header('Content-Disposition: inline; filename=gameParams.yaml');
        echo $sfYaml->dump($this->_gameServer->game_params, 5);
        die;
    }

    public function editGameParamsYamlAction()
    {
        if ($this->_request->isPost('data')) {
            $data = $this->_request->getPost('data');
            $sfYaml = new sfYaml;
            try {
                rename($this->_gameParamsFile, $this->_gameParamsFile . '.bak');

                if (file_exists($this->_gameParamsFile)) {
                    throw new Exception('cannot create backup of gameParams.yaml');
                }

                file_put_contents($this->_gameParamsFile, $sfYaml->dump($data, 5));

                $newKeys = $this->_extractKeys(sfYaml::load($this->_gameParamsFile));
                $oldKeys = $this->_extractKeys(sfYaml::load($this->_gameParamsFile . '.bak'));

                if ($newKeys !== $oldKeys) {
                    rename($this->_gameParamsFile . '.bak', $this->_gameParamsFile);
                    throw new Exception('keys in both files didnt match');
                }

                if (file_exists($this->_gameParamsFile) && file_exists($this->_gameParamsFile . '.bak')) {
                    unlink($this->_gameParamsFile . '.bak');
                    Zend_Registry::get('cache')->remove('gameParams');
                }

                $this->_flash('changes have been saved');

            } catch (Exception $e) {
                $this->_flash($e->getMessage());
            }
        }

        $sfYaml = new sfYamlParser;
        $gameParams = $sfYaml->parse(file_get_contents($this->_gameParamsFile));

        $comments = include_once $this->_gameParamsCommentsFile;
        $input_array = include_once $this->_gameParamsInputsFile;
        $this->view->tree = $this->_drawLevel($gameParams, null, $comments, $input_array);
    }

    public function importGameParamsAction()
    {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        if ($adapter->isUploaded('params')) {
            $adapter->addValidator('Extension', true, 'yaml');
            if ($adapter->isValid('params') && $adapter->receive('params')) {
                try {
                    $data = sfYaml::load($adapter->getFileName('params'));
                } catch (Exception $e) {
                    $data = null;
                }

                if ($data) {
                    $this->_gameServer->game_params = $data;
                    $this->_gameServer->save();
                    $this->_flash('game params imported');
                } else {
                    $this->_flash('could not parse game params file');
                }
            } else {
                $this->_flash('invalid game params file');
            }
        } else {
            $this->_flash('please choose file to import');
        }
        $this->_redirectBack();
    }

    /**
     * Extracts keys from multi dimensional array, needed to compare arrays
     *
     * @param $array Array to extract keys from
     *
     * @return array Array of keys
     */
    private function _extractKeys($array)
    {
        $keys = array();
        foreach ($array as $key => $value) {
            $keys[] = $key;
            if (is_array($value)) {
                $keys[] = $this->_extractKeys($value);
            }

        }
        return $keys;
    }

    /**
     * Returns HTML ready to echo, with parameters assigned with inputs
     *
     * @param array $data
     * @param array $path
     * @param array $comments Comments file content in specific format
     *
     * @return string HTML ready to draw
     */
    function _drawLevel($data, $parent = array(), $comments, $input_array=array())
    {
        //die('<pre>'.count($parent).print_r($data,1));

        $html = "<ul>\n";
        $header=array();
        $header_count=0;

        foreach ($data as $key => $value) {
            $html .= "<li>" . PHP_EOL;


            $path = $parent;
            $path[] = $key;

            $commentPath = implode('/', $path);
            $comment = array_key_exists($commentPath, $comments) ? $comments[$commentPath] : '';

            if (count($parent)==0)
            {
                $header_count++;
                $header[]='<li title="'.$comment.'" rel="tab_'.$header_count.'">'.$key.'</li>';
            }

            if (is_array($value)) {
                $output_key= (count($path) == 1) ? '<hr/><a name="'.$key.'"><strong>' . $key . ':</strong></a>' : "$key:";
                if (is_int($key)) $output_key='';

                $inside=$this->_drawLevel($value, $path, $comments, $input_array);

                if (count($parent)==0)
                {
                    $html.='<div class="admin_param_tabs_menu_item" id="tab_'.$header_count.'">'."\n".$inside.'</div>';
                }
                else
                {
                    $html .= "$output_key <em>" . $comment . "</em>" . $inside;
                }

            } else {
                $name = '[' . implode('][', $path) . ']';
                $id = str_replace(array('[', ']'), '', $name);
                $checked = $value ? 'checked' : '';



                $input=isset($input_array[$key])?$input_array[$key]:'<input type="text" name="data{name}" id="{id}" value="{value}"/>';

                foreach (array('name','id','value','checked') AS $k) $input=str_replace('{'.$k.'}',$$k,$input);

                $html .= sprintf(
                    '<label for="%s">%s:</label>'.$input.'</label><em>%s</em>', $id, $key, $comment);
            }
            $html .= "</li>" . PHP_EOL;

        }

        $ret=$html . "</ul>" . PHP_EOL;

        if (count($header)>0)
        {
            $ret='<ul class="admin_param_tabs_menu">'.implode(PHP_EOL,$header).'</ul>'.$ret;
        }

        return $ret;
    }

    /**
     * Read params file.
     * First line must be table name,
     * Second line are comments [optional],
     * Third line must be column names,
     * the rest are actual params.
     *
     * @param string $filename
     *
     * @return void
     */
    private function _readParams($filename)
    {
        if (!is_readable($filename) || ($handle = fopen($filename, 'r')) === false) {
            throw new Exception('Reading params file failed');
        }
        $counter = 0;
        $params = array();
        // SIM read each line in params file
        while (($line = fgetcsv($handle, null, ';')) !== false) {
            $params[$counter++] = $line;
        }
        $tableName = array_shift(array_shift($params));
        if (!in_array($tableName, $this->_paramsTables)) {
            throw new Exception('Table "' . $tableName . '" is not allowed for editing');
        }
        $modelClass = 'Model_' . implode('', array_map('ucfirst', explode('_', $tableName)));
        if (!class_exists($modelClass)) {
            throw new Exception('Class "' . $modelClass . '" does not exists');
        }
        $this->_paramsModel = new $modelClass();
        if (!($this->_paramsModel instanceof Zend_Db_Table_Abstract)) {
            throw new Exception(
                'Model "' . get_class($this->_paramsModel) . '" is not an instance of Zend_Db_Table_Abstract class');
        }
        // SIM second line - comments
        $comments = array_shift($params);
        // SIM third line is column definition
        $columns = array_shift($params);
        if (empty($params)) {
            throw new Exception('Params data is empty');
        }
        foreach ($columns as $k => $col) {
            // SIM remove empty columns
            if (empty($col)) {
                unset($columns[$k]);
                // SIM also remove corresponding columns in params
                $params = array_map(
                    function($val) use ($k)
                    {
                        unset($val[$k]);
                        return $val;
                    }, $params
                );
            }
        }
        if (count($columns) != count(current($params))) {
            throw new Exception('Params columns count and columns count are not equal');
        }
        reset($params);

        $this->_paramsComments = $comments;
        $this->_paramsColumns = $columns;
        $this->_paramsData = $params;
    }

    /**
     * Import params into DB using model.
     */
    private function _importParams()
    {
        $tableName = $this->_paramsModel->info('name');

        //RB not possible to import table 'log'
        if ($tableName == 'log') {
            $this->_redirectExit('import-params');
        }

        $db = $this->_paramsModel->getDefaultAdapter();
        $db->beginTransaction();
        $counter = 0;

        $tablePrimaryKey = array_shift($this->_paramsModel->info('primary'));
        try {
            foreach ($this->_paramsData as $values) {
                // SIM combine data for single row
                $data = array_combine($this->_paramsColumns, $values);
                // SIM get primary key value
                if (array_key_exists($tablePrimaryKey, $data)) {
                    $id = $data[$tablePrimaryKey];
                } else {
                    reset($data);
                    $id = current($data);
                }
                foreach ($data as $k => $d) {
                    if (empty($d) && $d !== 0) {
                        $data[$k] = null;
                    }
                }
                $param = $this->_paramsModel->find($id)->current();
                // SIM if param exists, update it; insert new row otherwise
                if ($param == null) {
                    $param = $this->_paramsModel->createRow();
                }
                $param->setFromArray($data);
                $param->save();
                $counter++;
            }
            $db->commit();
            $this->_flash('params have been succesfuly imported into table %s', $tableName);
        }
        catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($counter != $this->_paramsModel->fetchAll()->count()) {
            $this->_flash('notice that not all records have been updated');
        }
        $this->_redirectExit('import-params');
    }

    public function importParamsAction()
    {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        if ($adapter->isUploaded()) {
            $adapter->addValidator('Extension', true, 'csv');
            $fileinfo = $adapter->getFileInfo();
            $file = array_shift($fileinfo);

            if ($adapter->isValid($file['name']) && $adapter->receive($file['name'])) {
                $filename = $adapter->getDestination() . '/' . $file['name'];
                $this->_readParams($filename);
                $this->_importParams();
            }
        }
    }

    public function exportParamsAction()
    {
        if (($tableName = $this->_request->getParam('table', null)) !== null) {
            if (!in_array($tableName, $this->_paramsTables)) {
                throw new Exception('Invalid params table name');
            }
            $modelClass = 'Model_' . implode('', array_map('ucfirst', explode('_', $tableName)));
            if (!class_exists($modelClass)) {
                throw new Exception('Class "' . $modelClass . '" does not exists');
            }
            $this->_paramsModel = new $modelClass();
            $data = $this->_paramsModel->fetchAll()->toArray();
            $columns = $this->_paramsModel->info('cols');
            array_unshift($data, array($tableName), array(''), $columns);
            $fileName = $tableName . '.csv';
            $filePath = APPLICATION_PATH . '/cache/' . $fileName;
            $file = fopen($filePath, 'w');
            foreach ($data as $line) {
                fputcsv($file, $line, self::CSV_DELIMITER);
            }
            fclose($file);
            $content = file_get_contents($filePath);
            @unlink($filePath);
            header("Content-Type: text/csv");
            header("Content-Disposition: inline; filename=$fileName");
            die($content);
        }
    }

    public function schoolsAction()
    {
//        $datagrid = new Grid_Admin_Schools();
//        $datagrid->deploy();
//        $this->view->grid = $datagrid;
//
//        $this->render('grid');

        $this->view->schools = $this->_db->fetchPairs(
            $this->_db
                 ->select()
                 ->from('school', array('id', 'name'))
        );
    }

    public function addSchoolAction()
    {
        $form = new Form_School();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $formData = $form->getValues();

            $modelSchool = new Model_School();
            $school = $modelSchool->createRow($formData);
            $school->save();

            $this->_flash('school has been added');
            $this->_redirectExit('schools');
        }

        $this->view->form = $form;
    }

    public function editSchoolAction()
    {
        $modelSchool = new Model_School();
        $school = $modelSchool->find(
            $this->_getParam('id')
        )->current();

        $form = new Form_School();
        $form->populate($school->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $formData = $form->getValues();
            $school->setFromArray($formData);
            $school->save();

            $this->_flash('changes have been saved');
            $this->_redirectExit('schools');
        }

        $this->view->form = $form;

        $this->render('add-school');
    }

    public function addSchoolClassAction()
    {
        $form = new Form_SchoolClass();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $formData = $form->getValues();

            $modelSchoolClass = new Model_SchoolClass();
            $schoolClass = $modelSchoolClass->createRow($formData);
            $schoolClass->school_id = $this->_getParam('id');
            $schoolClass->save();

            $this->_flash('school class has been added');
            $this->_redirectExit('schools');
        }

        $this->view->form = $form;
    }

    public function editSchoolClassAction()
    {
        $modelSchoolClass = new Model_SchoolClass();
        $schoolClass = $modelSchoolClass->find(
            $this->_getParam('id')
        )->current();

        $form = new Form_SchoolClass();
        $form->populate($schoolClass->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $formData = $form->getValues();

            $schoolClass->setFromArray($formData);
            $schoolClass->save();

            $this->_flash('changes have been saved');
            $this->_redirectExit('schools');
        }

        $this->view->form = $form;

        $this->render('add-school-class');
    }

    public function getMembersFromClassAction()
    {
        echo json_encode($this->_db->fetchAll(
            $this->_db
                 ->select()
                 ->from('school_class_member', array('user_id', 'is_teacher'))
                 ->join('users', 'users.id = user_id', array('username'))
                 ->where('class_id = ?', $this->_getParam('id'))
        ));
        die();
    }

    public function setSchoolClassMemberRoleAction()
    {
        $this->_db->update('school_class_member', array(
            'is_teacher' => $this->_getParam('is_teacher')
        ), array(
            'user_id = ?' => $this->_getParam('user_id')
        ));
        die();
    }

    public function editUserProfileAction()
    {
        $form = new Form_Admin_UserProfile($this->_getParam('id'));
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $form->save();
            $this->_flash('changes have been saved');
            $this->_redirectExit('schools');
        }
        $this->view->form = $form;
    }

    public function engineConfigAction()
    {
        $form = new Form_Admin_EngineConfig();
        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            foreach ($form->getValues() as $key => $value) {
                Model_GameData::setData($key, $value);
            }
            Model_GameData::setData(
                Model_GameData::NEXT_ENGINE_RUN,
                Model_GameData::getData(Model_GameData::LAST_ENGINE_RUN) + Model_GameData::getData(Model_GameData::ENGINE_RUN_EVERY) * 60
            );
            $this->_flash('changes saved');
            $this->_redirectBack();
        }
        $this->view->form = $form;
    }

    public function mapParamsAction()
    {
        $modelMapParams = new Model_MapParams();
        $this->view->flashVars = $modelMapParams->getMapFlashVars(true);

        $type = $this->view->type = $this->_getParam('type');
        if ($type) {
            $mapParams = $modelMapParams->fetchRow(array('type = ?' => $type)) ?: $modelMapParams->createRow(array('type' => $type));

            $form = new Form_Admin_MapParam();

            if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
                $formData = $form->getValues();

                if ($formData['delete']) {
                    if ($mapParams->id) {
                        $mapParams->delete();
                        $this->_flash('map building deleted');
                    }
                    $this->_redirectExit('map-params');
                }

                $mapParams->setFromArray($formData);
                $mapParams->save();

                $this->_flash('changes saved');
                $this->_redirectExit('map-params');
            }

            if ($mapParams->id) {
                $form->populate($mapParams->toArray());
            }

            $this->view->form = $form;
        }
    }

    protected function _updateGameData($key)
    {
        foreach (func_get_args() as $key) {
            if ($this->_request->isPost($key))
                $this->view->$key = Model_GameData::setData(strtoupper($key), $this->_request->getPost($key));
            else
                $this->view->$key = Model_GameData::getData(strtoupper($key));
        }
    }

    public function usAction()
    {
        $this->_updateGameData('us_text');
    }

    public function editLoginEmailsAction()
    {
        $this->_updateGameData('login_emails', 'login_emails_error');
    }

    public function listCompaniesAction()
    {
        $datagrid = new Grid_Admin_Companies();
        $datagrid->deploy();
        $this->view->grid = $datagrid;

        $this->render('grid');
    }

    public function editCompanyCoownersAction()
    {
        if ($id = $this->_request->getParam('id')) {
            $modelCompany = new Model_Company;
            $modelUserToCompany = new Model_UserToCompany;
            $company = $modelCompany->find($id)->current();
            $coowners = array();
            if ($this->_request->isPost('coowners')) {
                foreach (explode(PHP_EOL, $this->_request->getPost('coowners')) as $email) {
                    $email = trim($email);
                    if (empty($email))
                        continue;
                    $row = $modelUserToCompany->fetchRow(array('company_id = ?' => $id, 'email = ?' => $email));
                    if ($row) {
                        if ($row->company_id != $id) {
                            $this->_flash(array('email %s is already assign to other company', $email));
                        }
                    } else {
                        $row = $modelUserToCompany->createRow();
                        $row->email = $email;
                        $row->company_id = $id;
                        $row->save();
                    }
                    $coowners[] = $row->id;
                }

                $modelUserToCompany->delete(array(
                    'company_id = ?' => $id,
                    'id NOT IN (?)' => $coowners,
                    'COALESCE(user_id, 0) <> ?' => $company->user_id
                ));
                $this->_flash('changes saved');
            }
            $coowners = array();
            foreach ($company->findDependentRowset('Model_UserToCompany', null, $modelUserToCompany->select()->where('COALESCE(user_id, 0) <> ?', $company->user_id, Zend_Db::PARAM_INT)) as $row)
                $coowners[] = $row->email;

            $this->view->coowners = implode(PHP_EOL, $coowners);
            $this->view->company = $company;
        } else {
            throw new Exception('No comany ID provided');
        }
    }

    public function gameResetAction()
    {
        $this->_db->query('DELETE FROM users WHERE id <> ' . $this->_company->user_id);
        $this->_db->query('UPDATE day SET day = 1');
        $this->_db->query('ALTER SEQUENCE rank_id_seq RESTART WITH 1');
        $this->_company->resetCompany();

        $this->_flash('game reseted');
        $this->_redirectBack();
    }
}