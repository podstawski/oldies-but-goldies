<?php
class CRM_History {
	public static function observe($user, $key, $data) {
		$ids = array();
		$ids []= $user->id;
		if (isset($data['contact-group-id'])) {
			$modelContactGroups = new Model_ContactGroups();
			$contactGroup = $modelContactGroups->getByID($data['contact-group-id']);
			if ($contactGroup) {
				foreach ($contactGroup->getUserContactGroups(true) as $userContactGroup) {
					$ids []= $userContactGroup->user_id;
				}
			}
		}
		if (isset($data['label-id'])) {
			$modelLabels = new Model_Labels();
			$label = $modelLabels->getByID($data['label-id']);
			if ($label) {
				foreach ($label->getUserLabels(true) as $userLabel) {
					$ids []= $userLabel->user_id;
				}
			}
		}
		$ids = array_unique($ids);

		#GN_Debug::debug(join(',', $ids));
		$modelHistory = new Model_History();
		foreach ($ids as $id) {
			$row = $modelHistory->createRow();
			$row->date = 'NOW()';
			$row->data = $data;
			$row->key = $key;
			$row->user_id = $id;
			$row->save();
		}

		$observer = Zend_Registry::get('observer');
		if ($key == 'labels-member-add') { $key = 'member-add'; } //kompatybilność wsteczna z db Piotra
		$observer->observe($key, true, $data);
	}

}
