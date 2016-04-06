<?php

require_once 'RestController.php';

class RoomsController extends RestController
{
    public function indexAction()
    {
        $param = $this->_getParam('training_center_id');
        if($param !== null)
        {
            $rooms = Room::all(array('training_center_id' => $param));

            array_walk($rooms, function(&$item) {
                $item = $item->to_array();
            });

            $this->setRestResponseAndExit($rooms, self::HTTP_OK);
        }
        else
        {
            parent::indexAction();
        }
    }

    public function deleteAction()
    {
        try {
            $row = $this->_getById(true);
            if (Lesson::find_by_room_id($row->id)) {
                throw new ActiveRecord\DatabaseException('room is assigned to some lessons');
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

