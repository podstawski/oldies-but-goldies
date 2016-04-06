<?php
class CRM_EmptyTokenException extends CRM_Exception {
	public function __construct(GN_Model_IDomainUser $user) {
		parent::__construct('No valid token available for user ' . $user->getEmail());
	}
}
