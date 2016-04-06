<?php

require_once 'RestController.php';

class ProjectsController extends RestController
{
    public function postAction()
    {
        $postData = $this->_getRequestData('POST');

        $leaders = array();
        if (isset($postData['leaders'])) {
            $leaders = json_decode($postData['leaders']);
            unset($postData['leaders']);
        }

        $connection = ActiveRecord\Model::connection();
        $connection->transaction();

        try {
            $project = Project::create($postData);
            $project->save();

            foreach ($leaders as $leaderID) {
                $leader = new ProjectLeaders;
                $leader->project_id = $project->id;
                $leader->user_id = $leaderID;
                $leader->save();
            }

            $connection->commit();
            $this->setRestResponseAndExit($project->to_array(), self::HTTP_CREATED);
        } catch (Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_SERVER_ERROR);
        }
    }

    public function putAction($putData = null)
    {
        $project = $this->_getById(true);

        $putData = $this->_getRequestData('PUT');

        $leaders = array();
        if (isset($putData['leaders'])) {
            $leaders = json_decode($putData['leaders']);
            unset($putData['leaders']);
        }

        $connection = ActiveRecord\Model::connection();
        $connection->transaction();

        try {

            $project->set_attributes($putData);
            $project->save();

            foreach ($leaders as $leaderID) {
                $leader = ProjectLeaders::find_by_project_id_and_user_id($project->id, $leaderID);
                if ($leader == null) {
                    $leader = new ProjectLeaders;
                    $leader->project_id = $project->id;
                    $leader->user_id = $leaderID;
                    $leader->save();
                }
            }

            foreach ($project->project_leaders as $leader) {
                if (!in_array($leader->user_id, $leaders)) {
                    $leader->delete();
                }
            }

            $connection->commit();
            $this->setRestResponseAndExit($project->to_array(), self::HTTP_OK);
        } catch (Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_SERVER_ERROR);
        }
    }
}

