<?php

require_once 'RestController.php';

class ReportsController extends RestController
{
    const DEFAULT_FORMAT = 'pdf';

    /**
     * @var string
     */
    protected $_modelName = 'Report';

    /**
     * @var array
     */
    protected $_templatesMap = array(
        1  => 'PresenceList',
        2  => 'DoorList',
        3  => 'Certificates',
        4  => 'CertificatesReceiveConfirmation',
        5  => 'LoginsReceiveConfirmation',
        6  => 'TrainingMaterialsReceiveConfirmation',
        7  => 'CourseSchedule',
        8  => 'RegistrationForm',
        9  => 'SurveyResults',
        10 => 'NewStudent',
        11 => 'PefsForAll',
        12 => 'Ejournal'
    );

    /**
     * @var array
     */
    protected $_validFormats = array('pdf', 'doc', 'xls');

    public function getAction()
    {
        $data = $this->_request->getParams();

        $this->_validateInputData($data);

        try {
            if (isset($data['project_id']))
                $projectId = $data['project_id'];
            else if (isset($data['course_id']))
                $projectId = Course::find($data['course_id'])->project_id;
            else
                $projectId = null;

            $template = $this->_getTemplateObject($data['id'], $projectId);
            unset($data['id']);

            if (array_key_exists('preview', $data) == false && ($errors = $template->report->isValid($data)) !== true)
                $this->setRestResponseAndExit($errors, self::HTTP_BAD_REQUEST);

            $report = $this->_generateReport($template->path, $data);
            if (strstr($report->output, 'Exception in thread'))
                $this->setRestResponseAndExit($report->output, self::HTTP_SERVER_ERROR);

            $this->_respondWithDocument($report->pdf, $data['report_format']);
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    public function indexAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_BAD_REQUEST);
    }

    public function postAction()
    {
        $this->indexAction();
    }

    public function putAction()
    {
        $this->indexAction();
    }

    public function deleteAction()
    {
        $this->indexAction();
    }

    /**
     * @param int $templateId
     * @param int $projectId
     * @return object
     */
    protected function _getTemplateObject($templateId, $projectId)
    {
        //RB $templateId is ID of base template
        $report = Report::first($templateId);

        if ($report == null)
            $this->setRestResponseAndExit('Template doesnt exsists', self::HTTP_BAD_REQUEST);

        if (array_key_exists($report->id, $this->_templatesMap) == false)
            $this->setRestResponseAndExit($this->view->translate('Report wih ID %s is not defined', $report->id), self::HTTP_BAD_REQUEST);

        $reportOptions = $this->_getOptions();
        $reportPath = $reportOptions['path'] . DIRECTORY_SEPARATOR . $report->path;

        if ($projectId) {
            $project = Project::find($projectId);
            if ($project) {
                $projectReportPath = $reportOptions['project_path'] . DIRECTORY_SEPARATOR . $project->code . DIRECTORY_SEPARATOR . $report->path;
                if (file_exists($projectReportPath)) {
                    $reportPath = $projectReportPath;
                }
            }
        }

        $className = 'Report_' . $this->_templatesMap[$report->id];
        return (object) array('report' => new $className(), 'path' => $reportPath);
    }

    private function _validateInputData(&$data)
    {
        unset($data['action'], $data['module'], $data['controller']);

        if (array_key_exists('id', $data) == false)
            $this->setRestResponseAndExit('Missing template_id', self::HTTP_NOT_FOUND);

        if (isset($data['report_format']) == false)
            $data['report_format'] = self::DEFAULT_FORMAT;
        else if (array_search($data['report_format'], $this->_validFormats) === false)
            $this->setRestResponseAndExit('Unsupported report output format: ' . $data['report_format'], self::HTTP_NOT_ACCEPTABLE);

        // jeśli karta zgłoszeniowa to ustaw id projektu na domyślny
        if ($data['id'] == 10 && $project = Project::findDefault()) {
            $data['project_id'] = $project->id;
        }
    }

    /**
     * @param string $path
     * @param array $params
     * @return object
     */
    private function _generateReport($path, array $params)
    {
        require_once APPLICATION_PATH . '/../library/jasper/RunJasperReports.php';
        $db = (object) $this->_getOptions('db');
        $dbname = Yala_User::getDbname(Yala_User::getDomain());
        $username = $dbname . '_' . Yala_User::getUsername();
        $jasper = new RunJasperReports('postgresql', $db->host, $db->port, $dbname, $username, Yala_User::getPassword());
        $format = $params['report_format'];
        unset($params['report_format']);
        return $jasper->generate($format, $path, $params);
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function _getOptions($key = 'reports')
    {
        return $this->getFrontController()->getParam("bootstrap")->getOption($key);
    }

    private function _respondWithDocument($content, $format)
    {
        $mimes = array(
            'pdf'   => 'application/pdf',
            'xls'   => 'application/vnd.ms-excel'
        );
        $this->_response->setHeader('Content-Disposition', 'attachment;filename="report.' . $format . '"', true)
                        ->setHeader('Content-Type', $mimes[$format], true);
        $this->_response->sendHeaders();
        echo $content;
        die();
    }
}
