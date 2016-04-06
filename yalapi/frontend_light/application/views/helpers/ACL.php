<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Zend_View_Helper_ACL extends Zend_View_Helper_Abstract
{
	private $requests = array();

	public function hasPrivilege($tableName, $privilege)
	{
		if (!isset($_SESSION['acl']))
		{
			return false;
		}
		$acl = $_SESSION['acl']['outputJSON']['acl'];
		if (!isset($acl[$tableName]))
		{
			return false;
		}
		$privileges = strtolower($acl[$tableName]);
		$privilege = strtolower($privilege);
		if (strpos($privileges, $privilege) !== false)
		{
			return true;
		}
		return false;
	}
}
?>
