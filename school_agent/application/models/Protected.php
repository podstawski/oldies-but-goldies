<?php
class Model_Protected extends Model_Abstract
{
	protected $_name = 'protected';

	public function isProtected($email)
	{
		$select = $this
			->select(true)
			->where('email = ?', $email)
			;
		return $this->fetchRow($select) !== null;
	}
}
?>
