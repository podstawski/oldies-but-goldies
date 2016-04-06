<?php

class MessagesController extends Game_Controller
{
    /**
     * @var Model_MessageUser
     */
    protected $_modelMessageUser;

    public function init()
    {
        parent::init();
        $this->_modelMessageUser = new Model_MessageUser();
    }

    public function indexAction()
    {
        $this->_forward('inbox');
    }

    public function inboxAction()
    {
        $this->_list(Model_Message::INBOX);
    }

    public function outboxAction()
    {
        $this->_list(Model_Message::OUTBOX);
    }

    private function _cleanRecipientList($recipientList)
    {
        $result = array();
        foreach (explode(',', trim($recipientList)) as $username) {
            $username = trim($username);
            if ($username) {
                $result[$username] = $username;
            }
        }
        return $result;
    }

    public function composeAction()
    {
        $form = new Form_Compose();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $data = $form->getValues();
            $recipientList = $this->_cleanRecipientList($data['recipient_list']);
            $stmt = $this->_db->prepare('SELECT id FROM users WHERE username = ?');
            $invalidRecipients = array();
            foreach ($recipientList as $username => &$id) {
                if ($stmt->execute(array($username)) && $stmt->rowCount() > 0) {
                    $id = $stmt->fetchColumn();
                } else {
                    $invalidRecipients[$username] = $username;
                    unset($recipientList[$username]);
                }
            }
            if (!empty($invalidRecipients)) {
                $form->getElement('recipient_list')->addError(
                    $this->view->translate(
                        'could not find following recipients: %s',
                        implode(', ', $invalidRecipients)
                    )
                );
            } else {
                $modelMessage = new Model_Message();
                $modelMessageUser = new Model_MessageUser();
                $data['send_date'] = date('c');
                $data['sender_id'] = Model_Player::getUserId();
                $messageId = $modelMessage->insert($data);
                $modelMessageUser->insert(array(
                    'message_id' => $messageId,
                    'folder' => Model_Message::OUTBOX,
                    'user_id' => Model_Player::getUserId()
                ));
                foreach ($recipientList as $username => $id) {
                    $modelMessageUser->insert(array(
                        'message_id' => $messageId,
                        'folder' => Model_Message::INBOX,
                        'user_id' => $id
                    ));
                }
                $this->_flash('message sent');
                $this->_redirectExit('outbox');
            }
        }

        $this->view->form = $form;
    }

    public function viewAction()
    {
        $id = $this->_getId();
        $this->view->message = $this->_modelMessageUser->fetchMessageById($id);
    }

    public function deleteAction()
    {
        $id = $this->_getId();
        $message = $this->_modelMessageUser->fetchMessageById($id);
        $message->delete();
        $this->_flash('message deleted');
        $this->_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }

    public function listRecipientsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        foreach ($this->_db->fetchCol(
            $this->_db
                 ->select()
                 ->from('users', array('username'))
                 ->where('username LIKE ?', $this->_getParam('q') . '%')
        ) as $username) {
            echo $username . PHP_EOL;
        }
        die();
    }

    protected function _list($folder)
    {
        $pageID = $this->_getParam('pageID', 1);
        $paginator = new Zend_Paginator(
            new Zend_Paginator_Adapter_DbSelect(
                $this->_modelMessageUser->fetchMessagesByFolder($folder, true)
            )
        );
        $paginator->setCurrentPageNumber($pageID);

        $this->view->paginator = $paginator;
        $this->view->folder    = $folder;

        $this->renderScript('messages/list.phtml');
    }
}