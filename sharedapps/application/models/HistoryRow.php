<?php
class Model_HistoryRow extends GN_Model_DomainRow {
	public $object = null;

	public function init() {
		parent::init();
		$this->data = unserialize(base64_decode($this->data));

		if (isset($this->data['contact-group-id'])) {
			$modelContactGroups = new Model_ContactGroups();
			$this->object = $modelContactGroups->getByID($this->data['contact-group-id']);
		} elseif (isset($this->data['label-id'])) {
			$modelLabels = new Model_Labels();
			$this->object = $modelLabels->getByID($this->data['label-id']);
		}
	}

	public function save() {
		$this->data = base64_encode(serialize($this->data));
		return parent::save();
	}
}
