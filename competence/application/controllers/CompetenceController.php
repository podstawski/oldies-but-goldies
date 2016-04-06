<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 *
 * @method addAlert($message)
 * @method addError($message)
 * @method addSuccess($message)
 * @method addInfo($message)
 */

require_once APPLICATION_PATH . '/views/helpers/FlashMessenger.php';

class CompetenceController extends GN_Controller
{
	/**
	 * @var Model_UsersRow
	 */
	protected $user;

	/**
     * @var GN_Observer
     */
	protected $observer;

	public function init()
	{
		parent::init();

		if (Zend_Auth::getInstance()->hasIdentity())
		{
			$modelUsers = new Model_Users();
			$this->user = $this->view->user = $modelUsers->find(Zend_Auth::getInstance()->getIdentity())->current();
		}

		Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
		Zend_Paginator::setDefaultItemCountPerPage(20);
		Zend_Paginator::setDefaultScrollingStyle('Sliding');

		if (!empty($this->user) and ($this->user->role == Model_Users::ROLE_SUPER_ADMINISTRATOR))
		{
			$modelDomains = new Model_Domains();
			$this->domains = $this->view->domains = $modelDomains->fetchAll($modelDomains->select()->order('domain_name ASC'));
			if (isset($_SESSION['domain-id']) and ($modelDomains->find($_SESSION['domain-id'])->current() !== null))
			{
				$this->user->domain_id = $_SESSION['domain-id'];
				$this->user->token = $this->user->getDomain()->oauth_token;
			}
		}

		if ($this->user) {
		    $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
            if (isset($googleapps['json_link']) && isset($googleapps['json_hash'])) {
                $this->observer = new GN_Observer(
                    $googleapps['json_link'],
                    $googleapps['json_hash'],
                    $this->user->email,
                    Zend_Registry::get('Zend_Locale')->getLanguage(),
                    'competence'
                );
            }
		}

		//zapamiętuj poprzednie adresy
		if (!empty($_SESSION['url']))
		{
			$_SESSION['previous-url'] = $_SESSION['url'];
		}
		$_SESSION['url'] = $this->view->url();
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, $arguments)
	{
		if (substr($name, 0, 3) == 'add')
		{
			$messageType = strtolower(substr($name, 3));
			if ($messageType == 'error')
			{
				$this->view->errors = true;
			}
			if (in_array($messageType, Zend_View_Helper_FlashMessenger::$messageTypes))
			{
				$this->_flash($arguments, $messageType);
			}
		}
	}
}
