<?php
class Model_Worksheet {
	private $_spreadsheetId;
	private $_worksheetId;
	private GN_GClient $client;

	public function __construct(GN_GClient $client = null) {
		$this->client = $client;
		$this->init();
	}

	public function getClient() {
		return $this->client;
	}

	public function setClient(GN_GClient $client) {
		$this->client = $client;
	}

	public function init() {
	}
}
