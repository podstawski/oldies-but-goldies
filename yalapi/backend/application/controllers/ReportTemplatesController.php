<?php

/**
 * Class for managing report uploads
 * @author Radosław Benkel <radek@gammanet.pl>
 */

require_once 'RestController.php';

class ReportTemplatesController extends RestController
{
    const UPLOAD_FILE_NAME = 'template_file';

    protected $_modelName = 'Report';

    public function getAction()
    {
        if ($this->_hasParam('download')) {
            $row = $this->_getById();
            if (!$row->project_id) {
                $this->setRestResponseAndExit(null, self::HTTP_NOT_FOUND);
            }
            $this->_outputReportFile($row);
        } else {
            parent::getAction();
        }
    }

    public function postAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_BAD_REQUEST);
    }

    public function putAction()
    {
        //RB Reroute action when copy paramter is set
        if ($this->_hasParam('copy')) {
            $this->_copyTemplate();
        } else {
            $row = $this->_getById(true);
            //RB you can't edit base template
            if (!$row->project_id) {
                $this->setRestResponseAndExit('You can\'t edit base template', self::HTTP_BAD_REQUEST);
            }

            //RB in this case we must take data from POST, because we're sending file
            $input = $this->_getRequestData('PUT');
            $row->name = $input['name'];
            $row->description = $input['description'];

            try {
                $destination = $this->getInvokeArg('bootstrap')->getOption('reports');
                $destination = $destination['project_path'] . DIRECTORY_SEPARATOR . $row->project_id . DIRECTORY_SEPARATOR;

                //RB trick for frontend sending template_name field even when it's not set.
                if (empty($_FILES[self::UPLOAD_FILE_NAME]['name'])) {
                    unset($_FILES[self::UPLOAD_FILE_NAME]);
                } else {
                    $_FILES[self::UPLOAD_FILE_NAME]['name'] = $row->path;
                }
                
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination($destination);
                $upload->setValidators(array(
                   'Size'  => array('min' => 1, 'max' => 10 * 1000 * 1000),
                   'Count' => array('min' => 0, 'max' => 1),
                   'Extension' => array('jrxml')
                ));

                if ($upload->isUploaded(self::UPLOAD_FILE_NAME)) {

                    if (!$upload->isValid(self::UPLOAD_FILE_NAME)) {
                        $this->setRestResponseAndExit($upload->getMessages(), self::HTTP_NOT_ACCEPTABLE);
                        return;
                    }

                    $filename = $destination . DIRECTORY_SEPARATOR . $row->path;
                    if (file_exists($filename)) {
                        unlink($filename);
                    }

                    $upload->receive(self::UPLOAD_FILE_NAME);
                }

                $row->save();

            } catch (ActiveRecord\UndefinedPropertyException $e) {
                $this->setRestResponseAndExit($row->errors->get_raw_errors(), self::HTTP_NOT_ACCEPTABLE);
            } catch (ActiveRecord\RecordNotFound $e) {
                $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
            } catch (Exception $e) {
                $this->setRestResponseAndExit($e, self::HTTP_NOT_ACCEPTABLE);
            }


        }
    }

    public function deleteAction()
    {
        $row = $this->_getById(true);
        //RB you can't delete base template
        if (!$row->project_id) {
          $this->setRestResponseAndExit('You can\'t delete base template', self::HTTP_BAD_REQUEST);
        }
        $options = $this->getInvokeArg('bootstrap')->getOption('reports');
        $pathDir = $options['project_path'] . DIRECTORY_SEPARATOR . $row->project_id;

        if (file_exists($pathDir . DIRECTORY_SEPARATOR . $row->path)) {
            unlink($pathDir . DIRECTORY_SEPARATOR . $row->path);
            if (count(scandir($pathDir)) <= 2) {
                rmdir($pathDir);
            }
        }
        parent::deleteAction();
    }

    protected function _getPagerOptionsForModel()
    {
        $options = parent::_getPagerOptionsForModel();
        if (!array_key_exists('total_records', $options)) {
            $options['joins'] = 'LEFT JOIN projects ON project_id = projects.id';
            $options['select'] = 'reports.*, projects.name AS project_name';
        }
        return $options;
    }


    protected function _copyTemplate()
    {
        $baseReport = $this->_getById();
        if (!$baseReport) {
            $this->setRestResponseAndExit(null, self::HTTP_NOT_FOUND);
        }

        if (!($projectId = (int)$this->_getParam('project_id'))) {
            $this->setRestResponseAndExit('Project id missing', self::HTTP_NOT_ACCEPTABLE);
        }

        if (Report::find(array('conditions' => array('project_id = ? AND path = ?', $projectId, $baseReport->path)))) {
            $this->setRestResponseAndExit('File already copied to that project', self::HTTP_CONFLICT);
        }

        $copyData = $baseReport->to_array(array('except' => array('id')));
        $copyData['project_id'] = $projectId;
        $copyData['parent_id'] = $baseReport->id;
        $reportCopy = Report::create($copyData);

        try {
            $reportOptions = $this->getInvokeArg('bootstrap')->getOption('reports');
            $basePath = $reportOptions['path'] . DIRECTORY_SEPARATOR . $baseReport->path;
            $destPath = $reportOptions['project_path'] . DIRECTORY_SEPARATOR . (string)$projectId;

            if (!file_exists($basePath)) {
                throw new LogicException('Base template not found');
            }

            //RB make directory if not exsists
            if (!file_exists($destPath)) {
                mkdir($destPath, 0777);
            }

            $destPath .= DIRECTORY_SEPARATOR . $baseReport->path;

            if (!copy($basePath, $destPath)) {
                throw new LogicException('Error during copying template');
            }
        } catch (Exception $e) {
            $reportCopy->delete();
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_SERVER_ERROR);
        }
    }

    protected function _outputReportFile($row)
    {
        $reportOptions = $this->getInvokeArg('bootstrap')->getOption('reports');
        $path = $reportOptions['project_path'] . DIRECTORY_SEPARATOR
                . $row->project_id . DIRECTORY_SEPARATOR . $row->path;

        if (!file_exists($path)) {
            $this->setRestResponseAndExit('Template not found', self::HTTP_NOT_FOUND);
        }

        $this->_response->setHeader('Content-Disposition', 'attachment;filename="' . $row->path . '"', true)
                        ->setHeader('Content-Type', 'application/octet-stream', true)
                        ->setHeader('Content-Length', filesize($path), true);
        $this->_response->sendHeaders();
        readfile($path);
        die();
    }
}

