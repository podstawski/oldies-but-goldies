<?php
/**
 * @property string $scheduled_date_opening
 * @property string $scheduled_date_closing
 */

class Model_TestsRow extends Zend_Db_Table_Row
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function getAssociatedParticipants()
    {
        $modelParticipants = new Model_Participants();
        $select = $modelParticipants
            ->select()
            ->where('test_id = ?', $this->id);
        return $modelParticipants->fetchAll($select);
    }

	private function transform($var, $mode) {
		if ($this->$var === null) {
			return;
		}
		$this->$var = GN_Tools::switchTimezone($this->$var, $mode);
	}

    public function init()
    {
		$this->transform('scheduled_date_opening', GN_Tools::TZ_SERVER_TO_USER);
		$this->transform('scheduled_date_closing', GN_Tools::TZ_SERVER_TO_USER);
		$this->transform('date_opened', GN_Tools::TZ_SERVER_TO_USER);
		$this->transform('date_closed', GN_Tools::TZ_SERVER_TO_USER);
        parent::init();
    }

    public function save() {
		$this->requested_date_opening = $this->scheduled_date_opening;
		$this->requested_date_closing = $this->scheduled_date_closing;
		$this->transform('scheduled_date_opening', GN_Tools::TZ_USER_TO_SERVER);
		$this->transform('scheduled_date_closing', GN_Tools::TZ_USER_TO_SERVER);
		$this->transform('date_opened', GN_Tools::TZ_USER_TO_SERVER);
		$this->transform('date_closed', GN_Tools::TZ_USER_TO_SERVER);
		return parent::save();
	}

	public function getUser() {
		$modelUsers = new Model_Users();
		return $modelUsers->find($this->user_id)->current();
	}

	public function doStar($userID, $isStarred) {
		$model = new Model_Stars();
		$select = $model
			->select(true)
			->where('test_id = ?', $this->id)
			->where('user_id = ?', $userID);
		$row = $model->fetchRow($select);
		if ($row === null) {
			$row = $model->createRow();
		}
		$row->user_id = $userID;
		$row->test_id = $this->id;
		$row->star = $isStarred ? 1 : 0;
		$row->save();
	}

	public function isStarred($userID) {
		$model = new Model_Stars();
		$select = $model
			->select(true)
			->where('test_id = ?', $this->id)
			->where('user_id = ?', $userID)
			;
		$row = $model->fetchRow($select);
		if ($row === null) {
			return false;
		}
		return $row->star == 1;
	}
}
