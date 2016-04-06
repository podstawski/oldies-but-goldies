<?php

require_once 'RestController.php';

class TrainingCentersController extends RestController
{
    protected $_modelName = 'TrainingCenter';

    public function postAction()
    {
        $post = $this->_getRequestData('POST');

        if (strlen($post['data']) === 0) {
            $this->setRestResponseAndExit(array('message' => 'No data in POST: '), self::HTTP_NOT_ACCEPTABLE);
        }

        $data = json_decode($post['data']);

        $connection = ActiveRecord\Model::connection();
        $connection->transaction();
        try
        {
            $training_center = new TrainingCenter((array)$data->training_center);
            if ($training_center->is_valid()) {
                $training_center->save();

                if (isset($data->resources)) {
                    foreach ($data->resources as $resource)
                    {
                        $row = new Resource();
                        $row->resource_type_id = $resource->type;
                        $row->training_center_id = $training_center->id;
                        $row->amount = $resource->quantity;
                        $row->save();
                    }
                }

                if (isset($data->rooms)) {
                    foreach ($data->rooms as $room) {
                        $data = array_intersect_key((array) $room, Room::table()->columns);
                        $data['training_center_id'] = $training_center->id;
                        Room::create($data);
                    }
                }

                $connection->commit();
            }
            else {
                $this->setRestResponseAndExit(
                    array('message' => $training_center->errors->get_raw_errors()),
                    self::HTTP_NOT_ACCEPTABLE);
            }
        }
        catch (Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit(null, self::HTTP_SERVER_ERROR);
        }

        $this->setRestResponseAndExit(null, self::HTTP_OK);
    }

    public function putAction()
    {
        try {
            $trainingCenterModel = $this->_getById(true);
            $data = $this->_getRequestData('PUT');
            $data = json_decode($data['data']);

            $trainingCenterData = get_object_vars($data->training_center);
            unset($trainingCenterData['city_zip_code']);
            unset($trainingCenterData['extra_buttons']);
            unset($trainingCenterData['selected_row']);

            $trainingCenterModel->set_attributes($trainingCenterData);

            $connection = ActiveRecord\Model::connection();
            $connection->transaction();

            if ($trainingCenterModel->is_valid()) {
                $trainingCenterModel->save();

                $oldResources = Resource::all(array('training_center_id' => $trainingCenterModel->id));
                $oldResKeys = Array();

                if (sizeof($oldResources) !== 0) {
                    foreach ($oldResources as $resource) {
                        array_push($oldResKeys, $resource->id);
                    }
                    Resource::table()->delete(array('id' => $oldResKeys));
                }

                foreach ($data->resources as $resource)
                {
                    $row = new Resource();
                    $row->resource_type_id = $resource->type;
                    $row->training_center_id = $trainingCenterModel->id;
                    $row->amount = $resource->quantity;

                    $row->save();
                }

                $connection->commit();
                $this->setRestResponseAndExit(null, self::HTTP_OK);
            }
            else {
                $connection->rollback();
                $this->setRestResponseAndExit($trainingCenterModel->errors->get_raw_errors(), self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (ActiveRecord\UndefinedPropertyException $e) {
            $connection->rollback();
            $this->setRestResponseAndExit(null, self::HTTP_NOT_ACCEPTABLE);
        } catch (ActiveRecord\ActiveRecordException $e) {
            $connection->rollback();
            $this->setRestResponseAndExit(null, self::HTTP_NOT_FOUND);
        }
    }

    public function deleteAction()
    {
        try {
            $row = $this->_getById(true);
            if (Course::find_by_training_center_id($row->id)) {
                throw new ActiveRecord\DatabaseException('training center is assigned to some courses');
            }
            $row->delete();
            $this->setRestResponseAndExit(null, self::HTTP_NO_CONTENT);
        } catch (ActiveRecord\DatabaseException $e) {
            //RB constraint fail
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_CONFLICT);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }

    }
}