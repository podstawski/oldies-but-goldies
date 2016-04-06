<?php
require_once APPLICATION_PATH . '/views/helpers/FlashMessenger.php';

class AbstractController extends GN_Controller
{
	/**
	 * @var Model_UsersRow
	 */
	protected $user;
	protected $observer;

	protected static function getNow() {
		return strtotime(GN_Tools::switchTimezone(date('Y-m-d H:i:s'), GN_Tools::TZ_SERVER_TO_USER));
	}

	public function init()
	{
		parent::init();
		@GN_Debug::debug('on: ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		register_shutdown_function(function() {
				@GN_Debug::debug('off: ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				});

		if (Zend_Auth::getInstance()->hasIdentity()) {
			$modelUsers = new Model_Users();
			$this->realUser = $this->view->realUser = $modelUsers->find(Zend_Auth::getInstance()->getIdentity())->current();
			if (isset($_SESSION['fake-user'])) {
				$this->user = $this->view->user = $modelUsers->getByEmail($_SESSION['fake-user']);
			}
			if (empty($this->user)) {
				$this->user = $this->view->user = $this->realUser;
			}
			Zend_Registry::set('real-user', $this->realUser);
			Zend_Registry::set('user', $this->user);
		}

		Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
		Zend_Paginator::setDefaultItemCountPerPage(20);
		Zend_Paginator::setDefaultScrollingStyle('Sliding');

		/*
			if (!empty($this->user) and ($this->user->role == Model_Users::ROLE_SUPER_ADMINISTRATOR)) {
				$modelDomains = new Model_Domains();
				$this->domains = $this->view->domains = $modelDomains->fetchAll();
				if (isset($_SESSION['domain-id']) and ($modelDomains->find($_SESSION['domain-id'])->current() !== null)) {
					$this->user->domain_id = $_SESSION['domain-id'];
					//$this->user->token = $this->user->getDomain()->oauth_token;
				}
			}
		*/

		$model = new Model_Tests();
		$role = Zend_Controller_Front::getInstance()->getPlugin('GN_Plugin_Acl')->getRoleName();
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from('tests', array('group_name', 'count(tests.id) as count'));
		switch ($role) {
			case Model_Users::ROLE_TEACHER:
				$select->where('user_id = ?', $this->user->id);
				break;
			case Model_Users::ROLE_ADMINISTRATOR:
			case Model_Users::ROLE_SUPER_ADMINISTRATOR:
				$select
					->join('users', 'user_id = users.id', array())
					->where('domain_id = ?', $this->user->domain_id);
				break;
			default:
				$select = null;
		}
		if ($select !== null) {
			$select->group('group_name');
			$select->order('group_name ASC');
			$this->view->testGroups = $db->fetchAll($select);
		}

		$this->view->miscOptions = $this->getInvokeArg('bootstrap')->getOption('misc');

		/*
		if ($this->_hasParam('search-group')) {
			$select = $db->select()->from('tests', array('group_name', 'id'));
			switch ($role) {
				case Model_Users::ROLE_TEACHER:
					$select->where('user_id = ?', $this->user->id);
					break;
				case Model_Users::ROLE_ADMINISTRATOR:
				case Model_Users::ROLE_SUPER_ADMINISTRATOR:
					$select
						->join('users', 'user_id = users.id', array())
						->where('domain_id = ?', $this->user->domain_id);
					break;
				default:
					$select = null;
			}
			$select->where('group_name = ?', $this->_getParam('search-group'));
		}
		*/

		$this->initObserver();
	}


	protected function initObserver()
	{
		if ($this->observer) {
			return $this->observer;
		}

		if ($this->user && !$this->observer) {
			$googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
			if (isset($googleapps['json_link']) && isset($googleapps['json_hash'])) {
				$this->observer = new GN_Observer(
						$googleapps['json_link'],
						$googleapps['json_hash'],
						$this->user->email,
						Zend_Registry::get('Zend_Locale')->getLanguage(),
						'essays'
						);
				return $this->observer;
			}
		}
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, $arguments)
	{
		$stopped = GN_Session::isStopped();
		if ($stopped) {
			GN_Session::restore();
		}
		if (substr($name, 0, 3) == 'add') {
			$messageType = strtolower(substr($name, 3));
			if ($messageType == 'error') {
				$this->view->errors = true;
			}
			if (in_array($messageType, Zend_View_Helper_FlashMessenger::$messageTypes)) {
				if (!isset($_SERVER['REMOTE_ADDR'])) {
					echo $messageType . ': ' . join(', ', $arguments) . PHP_EOL;
				} else {
					$this->_flash($arguments, $messageType);
				}
			}
		}
		if ($stopped) {
			GN_Session::stop();
		}
	}

	const TRIAL_CHECK_TIME = 1;
	const TRIAL_CHECK_COUNT = 2;

	/**
	 * @param Model_UsersRow|Model_DomainsRow $obj
	 * @param int $flags
	 * @return bool
	 */
	public static function _checkTrial($obj, $flags = null)
	{
		$trial = Zend_Registry::get('Bootstrap')->getOption('trial');

		if ($trial['enabled'] == false)
			return true;

		if ($flags === null)
			$flags = self::TRIAL_CHECK_TIME | self::TRIAL_CHECK_COUNT;

		if (($flags & self::TRIAL_CHECK_TIME) && (strtotime($obj->expire) > time()))
			return true;

		if (($flags & self::TRIAL_CHECK_COUNT) && ($obj->trial_count < $trial['max_count']))
			return true;

		return false;
	}

	/**
	 * @param Model_UsersRow $user
	 * @return bool
	 */
	public static function checkTrial($user)
	{
		$domain = $user->getDomain();

		if ($domain->isSpecial())
			return self::_checkTrial($user);

		return self::_checkTrial($user, self::TRIAL_CHECK_TIME) || self::_checkTrial($domain);
	}
}
