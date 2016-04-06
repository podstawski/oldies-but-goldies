<?php

class BankController extends Game_Controller
{
    public function indexAction()
    {
        $datagrid = new Grid_Balance();
        $datagrid->deploy();
        $this->view->grid = $datagrid;
    }

    public function viewAction()
    {
        $this->view->bankParams = $bankParams = Model_Param::get('bank');
        $banks = array_keys($bankParams);
        $this->view->bankID = $bankID = $this->_getParam('id');

        if (!in_array($bankID, $banks)) {
            $this->_flash('invalid bank ID');
            $this->_redirectBack();
        }

        $this->view->loanParams = $bankParams[$bankID];
        if (!($this->view->loan = $this->_company->getLoanForBank($bankID))) {
            $form = new Form_BankLoan($bankID);
            if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
                $formData = $form->getValues();
                $formData['bank_id'] = $bankID;
                $task = Playgine_TaskFactory::factory('CreateBankLoan');
                $task->setOptions($formData);
                $this->_flash($this->runTask($task));
                $this->_redirectBack();
            }

            $tmp = array();
            foreach ($form->getMessages() as $errors) {
                foreach ($errors as $error) {
                    $tmp[] = $error;
                }
            }

            if (!empty($tmp)) {
                foreach ($tmp as $error) {
                    $this->_flash($error);
                }
            }

            $this->view->form = $form;
        }
    }
}