<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_GameServerRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @var array
     */
    public $game_params;

    public function init()
    {
        if ($this->settings)
            $this->game_params = json_decode($this->settings, true);
        else
            $this->game_params = Model_Param::getDefaultParams();
    }

    /**
     * @return mixed
     */
    public function save()
    {
        if ($this->game_params)
            $this->settings = json_encode($this->game_params);

        if ($this->url == null) {
            $options = Zend_Registry::get('application_options');
            $options = $options['topmanager'];
            $this->url = sprintf($options['url'], $this->name);
        }

        return parent::save();
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isAdmin($email)
    {
        return $email == $this->admin_email;
    }

    /**
     * @param $userID
     * @return bool
     */
    public function hasUser($userID)
    {
        return $this->findDependentRowset('Model_GameServerUser', null, $this->select()->where('user_id = ?', $userID, Zend_Db::PARAM_INT))->count() == 1;
    }
}