<?php

require 'RestController.php';

class QuizUsersController extends RestController
{
    protected $_modelName = 'QuizUser';

    public function postAction()
    {
        $post = $this->_getRequestData('POST');
        $post = json_decode($post['data'], true);

        if (!isset($post['groups']) || !isset($post['quiz_id'])) {
            $this->setRestResponseAndExit(null, self::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $groupIds = array_map('array_pop', $post['groups']);
            $inserts = QuizUser::insert_by_groups($groupIds, $post['quiz_id']);
        } catch (ActiveRecord\DatabaseException $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_CONFLICT);
        }
        $this->setRestResponseAndExit($inserts, self::HTTP_OK);
    }

    public function putAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_BAD_REQUEST);
    }

    public function deleteAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_BAD_REQUEST);
    }
}
