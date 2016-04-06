<?php
abstract class ClassGroup_ActionStepWrapper
{
    public static function factory($gapps, $actionStepRow)
    {
        $className = 'ClassGroup_Action' . str_replace(' ', '', ucwords(str_replace('-', ' ', $actionStepRow->type))) . 'Wrapper';
        if (!class_exists($className)) {
            throw new Exception(sprintf('Unknown action step type "%s"', $actionStepRow->type));
        }
        return new $className($gapps, $actionStepRow);
    }

    protected function parseException($e)
    {
        return array(
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        );
    }

    /**
     * @var GN_Logger
     */
    protected $logger;

    protected $actionStepRow;

    /**
     * @var ClassGroup_Gapps
     */
    protected $gapps;

    public function getActionStepRow()
    {
        return $this->actionStepRow;
    }

    public function __construct($gapps, $actionStepRow)
    {
        $this->actionStepRow = $actionStepRow;
        $this->gapps = $gapps;
        $this->logger = Zend_Registry::get('logger');
    }

    public abstract function backward();

    public abstract function forward();
}


class ClassGroup_ActionSpreadsheetUpdateWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $httpClient = $this->gapps->getGClient()->getHttpClient();
            $spreadsheetsClient = new Zend_Gdata_Spreadsheets($httpClient);

            $spreadsheetId = $data['spreadsheet-id'];
            $worksheetId = $data['worksheet-id'];
            $rowIndex = $data['row-index'];
            $colIndex = $data['col-index'];
            $value = $data['value-old'];

            $query = new Zend_Gdata_Spreadsheets_CellQuery();
            $query->setMinCol($colIndex);
            $query->setMaxCol($colIndex);
            $query->setMinRow($rowIndex);
            $query->setMaxRow($rowIndex);

            $cell = 'R' . $rowIndex . 'C' . $colIndex;

            $query = new Zend_Gdata_Spreadsheets_CellQuery();
            $query->setSpreadsheetKey($spreadsheetId);
            $query->setWorksheetId($worksheetId);
            $query->setCellId($cell);

            $entry = $spreadsheetsClient->getCellEntry($query);
            $valueOld = $entry->getCell()->getText();
            $entry->setCell(new Zend_Gdata_Spreadsheets_Extension_Cell(null, $rowIndex, $colIndex, $value));
            $response = $entry->save();

            $data['value'] = $valueOld;
            $this->actionStepRow->result = 1;
            $this->actionStepRow->data = $data;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
            $this->actionStepRow->save();
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $httpClient = $this->gapps->getGClient()->getHttpClient();
            $spreadsheetsClient = new Zend_Gdata_Spreadsheets($httpClient);

            $spreadsheetId = $data['spreadsheet-id'];
            $worksheetId = $data['worksheet-id'];
            $rowIndex = $data['row-index'];
            $colIndex = $data['col-index'];
            $value = $data['value'];

            if (!empty($data['rely-on'])) {
                $model = new Model_ActionStep();
                $select = $model->select(true)->where('id = ?', $data['rely-on']);
                $row = $model->fetchRow($select);
                if ($row == null) {
                    throw new Exception(sprintf('Relying on non-existing action step with ID %d', $data['rely-on']));
                }
                if ($row->result == 0) {
                    throw new Exception(sprintf('Action step relied on has failed'));
                }
            }

            $query = new Zend_Gdata_Spreadsheets_CellQuery();
            $query->setMinCol($colIndex);
            $query->setMaxCol($colIndex);
            $query->setMinRow($rowIndex);
            $query->setMaxRow($rowIndex);

            $cell = 'R' . $rowIndex . 'C' . $colIndex;

            $query = new Zend_Gdata_Spreadsheets_CellQuery();
            $query->setSpreadsheetKey($spreadsheetId);
            $query->setWorksheetId($worksheetId);
            $query->setCellId($cell);

            $entry = $spreadsheetsClient->getCellEntry($query);
            $valueOld = $entry->getCell()->getText();
            $entry->setCell(new Zend_Gdata_Spreadsheets_Extension_Cell(null, $rowIndex, $colIndex, $value));
            $response = $entry->save();

            $data['value-old'] = $valueOld;
            $this->actionStepRow->result = 1;
            $this->actionStepRow->data = $data;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
            $this->actionStepRow->save();
        }
        $this->actionStepRow->save();
    }
}


class ClassGroup_ActionGroupRemoveWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->createGroup($data['e-mail'], $data);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        $currentData = $this->gapps->getGroup($data['e-mail']);
        try {
            $this->gapps->removeGroup($data['e-mail']);
            if ($currentData !== null) {
                $this->actionStepRow->data = array_merge($data, $currentData);
            }
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}


class ClassGroup_ActionGroupCreateWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $currentData = $this->gapps->getGroup($data['e-mail']);
            $this->gapps->removeGroup($data['e-mail']);
            $this->actionStepRow->result = 1;
            if ($currentData !== null) {
                $this->actionStepRow->data = array_merge($data, $currentData);
            }
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->createGroup($data['e-mail'], $data);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}


class ClassGroup_ActionUserRemoveWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            //jeśli nie znamy hasła, pobierz je z pierwszej znalezionej akcji stworzenia użytkownika. tam ono będzie
            if (empty($data['password'])) {
                $model = new Model_ActionStep();
                $select = $model
                    ->select(true)
                    ->where('type = user-create')
                    ->order('date_executed desc');
                $rows = $model->fetchAll($select);
                foreach ($rows as $row) {
                    if ($row->data['e-mail'] == $data['e-mail']) {
                        $data['password'] = $row->data['password'];
                        break;
                    }
                }
            }
            $this->gapps->createUser($data['e-mail'], $data);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $currentData = $this->gapps->getUser($data['e-mail']);
            $this->gapps->removeUser($data['e-mail']);
            $this->actionStepRow->result = 1;
            if ($currentData !== null) {
                $this->actionStepRow->data = array_merge($data, $currentData);
            }
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}


class ClassGroup_ActionUserCreateWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $currentData = $this->gapps->getUser($data['e-mail']);
            $this->gapps->removeUser($data['e-mail']);
            $this->actionStepRow->result = 1;
            if ($currentData !== null) {
                $this->actionStepRow->data = array_merge($data, $currentData);
            }
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->createUser($data['e-mail'], $data);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}


class ClassGroup_ActionGroupOwnerAddWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->removeOwnerFromGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->addOwnerToGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}

class ClassGroup_ActionGroupOwnerRemoveWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->addOwnerToGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->removeOwnerFromGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}

class ClassGroup_ActionGroupMemberAddWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->removeMemberFromGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->addMemberToGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}

class ClassGroup_ActionGroupMemberRemoveWrapper extends ClassGroup_ActionStepWrapper
{
    public function backward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->addMemberToGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }

    public function forward()
    {
        $data = $this->actionStepRow->data;
        try {
            $this->gapps->removeMemberFromGroup($data['user'], $data['group']);
            $this->actionStepRow->result = 1;
        } catch (Exception $e) {
            $this->actionStepRow->data['exception'] = $this->parseException($e);
            $this->actionStepRow->result = 0;
        }
        $this->actionStepRow->save();
    }
}
