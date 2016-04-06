<?php
class Model_ActionStep extends Model_Abstract
{
	const TYPE_SPREADSHEET_UPDATE = 'spreadsheet-update';
	const TYPE_GROUP_REMOVE = 'group-remove';
	const TYPE_GROUP_CREATE = 'group-create';
	const TYPE_USER_REMOVE = 'user-remove';
	const TYPE_USER_CREATE = 'user-create';
	const TYPE_GROUP_MEMBER_REMOVE = 'group-member-remove';
	const TYPE_GROUP_OWNER_REMOVE = 'group-owner-remove';
	const TYPE_GROUP_MEMBER_ADD = 'group-member-add';
	const TYPE_GROUP_OWNER_ADD = 'group-owner-add';

	public static function getStepName($step, $dir)
	{
		if ($dir == Model_Action::DIRECTION_FORWARD)
		{
			return $step;
		}
		switch ($step)
		{
			case Model_ActionStep::TYPE_SPREADSHEET_UPDATE: return Model_ActionStep::TYPE_SPREADSHEET_UPDATE;
			case Model_ActionStep::TYPE_GROUP_REMOVE: return Model_ActionStep::TYPE_GROUP_CREATE;
			case Model_ActionStep::TYPE_GROUP_CREATE: return Model_ActionStep::TYPE_GROUP_REMOVE;
			case Model_ActionStep::TYPE_USER_REMOVE: return Model_ActionStep::TYPE_USER_CREATE;
			case Model_ActionStep::TYPE_USER_CREATE: return Model_ActionStep::TYPE_USER_REMOVE;
			case Model_ActionStep::TYPE_GROUP_MEMBER_REMOVE: return Model_ActionStep::TYPE_GROUP_MEMBER_ADD;
			case Model_ActionStep::TYPE_GROUP_MEMBER_ADD: return Model_ActionStep::TYPE_GROUP_MEMBER_REMOVE;
			case Model_ActionStep::TYPE_GROUP_OWNER_REMOVE: return Model_ActionStep::TYPE_GROUP_OWNER_ADD;
			case Model_ActionStep::TYPE_GROUP_OWNER_ADD: return Model_ActionStep::TYPE_GROUP_OWNER_REMOVE;
		}
		return null;
	}

	protected $_name = 'action_steps';
	protected $_rowClass = 'Model_ActionStepRow';
}
?>
