<?php

class ProjectLeaders extends AclModel
{
    static $table_name = 'project_leaders';
    static $use_view = true;

    static $after_save = 'RunAcl';
    static $after_destroy = 'RevokeAccess';

    static $belongs_to = array(
        array('project', 'class_name' => 'Project'),
        array('leader', 'class_name' => 'User')
    );

    public function RunAcl()
    {
        $this->grant(Role::PROJECT_LEADER,$this->user_id, $this->id);
	    Project::GrantRevokeRightsToLeaders($this->project_id);
    }
    
    public static function findLeaderIds($project_id=0)
    {
	if (!$project_id) return Role::findAllUserIds(Role::PROJECT_LEADER);
     
        $users=self::find('all',array('conditions' => array('project_id=?',$project_id) ));
        return self::getArrayOfFieldValues($users,'user_id');	
    }

    public function RevokeAccess()
    {
    	Project::GrantRevokeRightsToLeaders($this->project_id);
    }
}
