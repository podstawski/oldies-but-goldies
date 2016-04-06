<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class Game_Controller extends Game_Controller_Abstract
{
    /**
     * @var Playgine_TaskManager
     */
    protected $_taskManager;

    /**
     * @var Model_CompanyRow
     */
    protected $_company;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_sessionData;


    public function init()
    {
        parent::init();

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity() == false)
            $this->_redirectToDefaultGameServer();

        Model_Player::init($auth->getIdentity()->id);

        if (Model_Player::getUserId() == null) {
            $auth->clearIdentity();
            $this->_redirectToDefaultGameServer();
        }

        $company = Model_Player::getCompany();
        if ($company == null)
            $this->_redirectExit('welcome', 'user');

        $this->view->company = $this->_company = $company;

        $modelMessageUser = new Model_MessageUser();
        $this->view->newMessagesCount = $modelMessageUser->getNewMessagesCount();

        $this->_sessionData = new Zend_Session_Namespace('companyData');

        $this->view->jQuery()->addOnLoad(<<< JS

$('a[confirm]').each(function(){
    var href    = $(this).attr('href');
    var confirm = $(this).attr('confirm');

    $(this).removeAttr('confirm');
    $(this).click(function(e){
        $("<div>" + confirm + "</div>").dialog({
            modal : true,
            width : 500,
            title : '{$this->view->translate('confirmation')}',
            buttons : {
                '{$this->view->translate('Yes')}' : function(){
                    document.location = href;
                },
                '{$this->view->translate('No')}' : function(){
                    $(this).dialog("close");
                }
            }
        });
        e.preventDefault();
        return false;
    });
});

JS
        );

        if ($this->view->ENGINE_COUNTER = max(0, (Model_GameData::getData(Model_GameData::NEXT_ENGINE_RUN) ?: 0) - time())) {
            $this->view->jQuery()->addOnLoad(<<< JS

var counterDiv = $("#next-engine-run strong");
var counter = {$this->view->ENGINE_COUNTER};

var counterInterval = setInterval(function(){
    if (counter == 0) {
        clearInterval(counterInterval);
        counterDiv.text("---");
        return false;
    }

    counterDiv.text(time_format(counter));
    counter--;
}, 1000);

JS
            );
        }

        $this->_checkCompanyStatus();

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('_partials/paginator.phtml');
        Zend_Paginator::setDefaultItemCountPerPage(20);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
    }

    /**
     * @return Playgine_TaskManager
     */
    protected function _getTaskManager()
    {
        if ($this->_taskManager === null) {
            $this->_taskManager = new Playgine_TaskManager();
        }
        return $this->_taskManager;
    }

    protected function _checkRemoteLogin()
    {
        // SIM sprawdź czy nauczyciel zalogował się na konto ucznia
        $session = new Zend_Session_Namespace('remote-login');
        if ($session->identity && $session->identity->role == Model_Player::ROLE_USER) {
            $this->_flash('you are not allowed to perform this action');
            $this->_redirectExit('index', 'office');
        }
    }

    protected function _checkCompanyOwner()
    {
        if (Model_Player::isCompanyOwner() == false) {
            $this->_flash('you are not allowed to perform this action');
            $this->_redirectExit('index', 'office');
        }
    }

    public function runTask(Playgine_Task_Abstract $task)
    {
        $this->_checkRemoteLogin();

        if ($task->getCompany() === null) {
            $task->setCompany($this->_company);
        }

        return $this->_getTaskManager()->runTask($task);
    }

    /**
     * @throws InvalidArgumentException
     * @return int
     */
    protected function _getId()
    {
        $id = intval($this->_getParam('id'));
        if (!$id) {
            throw new InvalidArgumentException('Please provide ID');
        }
        return $id;
    }

    protected function _checkCompanyStatus()
    {
        if (Model_Param::get('commitment.bankruptcy_delay') > 0 && $this->_company->status == Model_Company::STATUS_BANKRUPT) {

            if (!Model_Player::isAdmin()
            && !($this->_request->getControllerName() == 'office'  && ($this->_request->getActionName() == 'index' || $this->_request->getActionName() == 'restart'))
            ) {
                $this->_redirectExit('index', 'office');
            }

            $closeOnEscape = Model_Player::isAdmin() ? 'true' : 'false';

            if (Model_Param::get('commitment.allow_restart')) {
                $message = $this->view->translate('unpaid commitments bankruptcy allow restart', $this->view->url(array('action' => 'restart', 'controller' => 'office')));
            } else {
                $message = $this->view->translate('unpaid commitments bankruptcy');
            }

            $this->view->jQuery()->addOnLoad(<<< JS

    $("<div title='{$this->view->translate('unpaid commitments bankruptcy title')}'>{$message}</div>").dialog({
        width : 350,
        resizable : false,
        dialogClass : 'commitment-bankruptcy',
        closeOnEscape : {$closeOnEscape}
    });

JS
            );

        } else if (Model_Param::get('commitment.warning_delay') > 0 && $this->_company->status == Model_Company::STATUS_WARNING) {

            $this->view->jQuery()->addOnLoad(<<< JS

    $("<div title='{$this->view->translate('unpaid commitments warning title')}'>{$this->view->translate('unpaid commitments warning')}</div>").dialog({
        width : 350,
        resizable : false,
        dialogClass : 'commitment-warning'
    });

JS
            );
        }
    }
}