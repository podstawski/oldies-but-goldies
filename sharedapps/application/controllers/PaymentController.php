<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gammanet.pl>
 */

require_once 'AbstractController.php';

class PaymentController extends AbstractController
{
    const PAYMENT_CREATED   = 'payment-started';
    const PAYMENT_COMPLETED = 'payment-completed';
    const PAYMENT_FRAUD     = 'payment-fraud';

    /**
     * @var Model_Payment
     */
    protected $_modelPayment;

    public function init()
    {
        parent::init();

        $options = $this->getApplicationOptions();
        $this->view->paymentOptions = $options['payment'];

        $this->_modelPayment = new Model_Payment;
    }

    public function indexAction() {
        $tmp = array();
        $start = microtime(true);
        GN_Session::stop();
        while (microtime(true) - $start < 15) {
            if (GN_SessionCache::isFresh('autocomplete')) {
                $autocomplete = GN_SessionCache::get('autocomplete');
                break;
            }
            sleep(1);
        }
        if (!isset($autocomplete) or !is_array($autocomplete)) {
            $autocomplete = array();
        }
        GN_Session::restore();
        foreach ($autocomplete as $user) {
            if ($user['e-mail'] == $this->user->email)
                continue;

            if (in_array($user['src'], array('domain', 'db', 'manual')))
                $tmp[] = $user['e-mail'];
        }
        array_unshift($tmp, $this->user->email);

        $users = array();
        foreach ($tmp as $email) {
            $user = CRM_Misc::findUser($email);
            if ($user)
                $users[$email] = $user->expire;
            else
                $users[$email] = null;
        }

        $this->view->autocheck = !$this->_hasParam('success');
        $this->view->users = $users;
    }

    public function confirmAction() {
        if ($this->_getParam('data') != '') {
            $this->view->type = $type = $this->_getParam('type');
            $this->view->typeString = $typeString = @GN_Model_Payment::$types[$type];

            $this->view->data = $this->_getParam('data');

            $postData = @json_decode(stripslashes($this->_request->getPost('data')), true);
            $amount = 0;
            $custom = array();
            if ($postData) {
                $fees = $this->view->paymentOptions['fee'];
                // sprawdź czy dobry typ opłaty (1 - miesiąc, 2 - rok)
                // zlicz całkowitą wysokość opłaty
                // dopiero tutaj sprawdź czy jest user, jeśli nie - stwórz go
                foreach ($postData as $email => $type) {
                    if (array_key_exists($type, $fees) == false) {
                        unset($postData[$email]);
                        continue;
                    }
                    $amount += $fees[$type]['amount'][$typeString];
                    $user = CRM_Misc::findCreateUser($email);
                    $custom[] = $user->id . ':' . $type;
                }
            }
            if (empty($custom)) {
                $this->addError($this->view->translate('payment please select some users'));
                $this->_redirectExit(null, 'payment');
            }

            $this->view->amount = $amount;
            if (empty($typeString)) {
                $this->addError('payment invalid type');
                $this->_redirectExit('index', 'dashboard');
            }

            $this->view->custom = $custom;
            if ($this->_getParam('submit') == 1) {
                $payment = $this->_modelPayment->newRow($custom, $type, $this->user->id, $amount);
                $this->_report(self::PAYMENT_CREATED, $payment, $this->user->toArray());
                $this->_helper->layout->disableLayout();
                $this->view->payment = $payment;
            }

            $this->renderScript('payment/' . $typeString . '-form.phtml');
        }
    }

    public function successPaypalAction()
    {
        $this->_processPaypal();
        $this->addSuccess('payment paypal ok');
        $this->_redirectExit(null, 'payment', array('success' => 'yes'));
    }

    public function errorPaypalAction()
    {
        $this->addError('payment paypal cancel');
        $this->_redirectExit(null, 'payment', array('cancel' => 'yes'));
    }

    public function notifyPaypalAction()
    {
        $this->_processPaypal();
        exit;
    }

    public function successPayuAction() {
        $this->_processPayu();
        $this->addSuccess('payment payu ok');
        $this->_redirectExit(null, 'payment', array('success' => 'yes'));
    }

    public function errorPayuAction() {
        $this->addError('payment payu error', $this->_getParam('error'));
        $this->_redirectExit(null, 'payment', array('cancel' => 'yes'));
    }

    public function notifyPayuAction() {
        $this->_processPayu();
        $this->_helper->layout->disableLayout();
        header('Content-Type: text/plain; charset=utf-8');
        echo 'OK';
        exit;
    }

    public function _processPayu() {
        $this->_log('$_POST', $_POST);

        if (empty($_POST)) {
            return;
        }

        $req = array();
        $req['pos_id'] = $_POST['pos_id'];
        $req['session_id'] = $_POST['session_id'];
        $req['ts'] = time() + microtime(true);
        $req['sig'] = md5($req['pos_id'] . $req['session_id'] . $req['ts'] . $this->view->paymentOptions['payu']['key1']);
        $req = http_build_query($req);

        $host = 'www.platnosci.pl';
        $url = 'https://' . $host . '/paygw/UTF/Payment/get/txt';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Host: ' . $host,
            'Connection: close'
        ));
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        $this->_log('req', $req);
        $this->_log('res', $res);
        $this->_log('curl error', $err);

        $res2 = array();
        foreach (explode("\n", str_replace("\r", '', $res)) as $line) {
            list($key, $val) = explode(': ', $line);
            $res2[$key] = $val;
        }
        $res = $res2;

        if ($res['status'] != 'OK') {
            return;
        }

        if ($res['trans_status'] != 99) {
            return;
        }

        $payment = $this->_modelPayment->findCustom($res['trans_session_id']);
        if ($payment == null) {
            return $this->_report(self::PAYMENT_FRAUD, $payment, $_POST, 'payment not found');
        }
        if ($payment->amount * 100 != $res['trans_amount']) {
            return $this->_report(self::PAYMENT_FRAUD, $payment, $_POST, 'payment amount mismatch');
        }

        if (!empty($payment->data)) {
            return; //already paid
        }

        $payment->transaction_id = $res['trans_id'];
        #$payment->payer_email = $_POST['payer_email'];
        $payment->data = json_encode($res);
        $payment->save();

        $this->paymentDone($payment);
        $this->_report(self::PAYMENT_COMPLETED, $payment);
    }

    protected function _processPaypal() {
        $this->_log('$_POST', $_POST);

        if (empty($_POST)) {
            return;
        }

        $payment = $this->_modelPayment->findTransaction($_POST['txn_id']);
        if ($payment != null) {
            return;
        }

        $req = array();
        $req['cmd'] = '_notify-validate';
        foreach ($_POST as $key => $value) {
            $req[$key] = stripslashes($value);
        }
        $req = http_build_query($req);

        if ($this->view->paymentOptions['paypal']['sandbox']) {
            $host = 'www.sandbox.paypal.com';
        } else {
            $host = 'www.paypal.com';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $host . '/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Host: ' . $host,
            'Connection: close'
        ));
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        $this->_log('req', $req);
        $this->_log('res', $res);
        $this->_log('curl error', $err);

        if (strcmp($res, 'VERIFIED') != 0) {
            return;
        }

        if ($_POST['payment_status'] != 'Completed') {
            return;
        }

        $payment = $this->_modelPayment->findCustom($_POST['custom']);
        if ($payment == null) {
            return $this->_report(self::PAYMENT_FRAUD, $payment, $_POST, 'payment not found');
        }
        if ($_POST['mc_currency'] != $this->view->paymentOptions['paypal']['currency']) {
            return $this->_report(self::PAYMENT_FRAUD, $payment, $_POST, 'payment currency mismatch');
        }
        if ($payment->amount != $_POST['mc_gross']) {
            return $this->_report(self::PAYMENT_FRAUD, $payment, $_POST, 'payment amount mismatch');
        }

        $payment->transaction_id = $_POST['txn_id'];
        $payment->payer_email = $_POST['payer_email'];
        $payment->data = json_encode($_POST);
        $payment->save();

        $this->paymentDone($payment);
        $this->_report(self::PAYMENT_COMPLETED, $payment);
    }

    public function paymentDone($payment) {
        $fees = $this->view->paymentOptions['fee'];
        foreach ($payment->getUsers() as $user) {
            
            $now = new DateTime;
            $expire = new DateTime($user->expire);
            if ($expire < $now) {
                $expire = $now;
            }
            $expire->add(new DateInterval('P' . $fees[$user->fee_type]['interval']));
            $user->expire = $expire->format('c');
            $user->save();
        }
    }

    /**
     * @param string $what
     * @param mixed $data
     */
    protected function _log($what, $data)
    {
        file_put_contents(APPLICATION_PATH . '/logs/payments.log', date('Y-m-d H:i:s') . ' ' . $what . ': ' . print_r($data, 1) . PHP_EOL, FILE_APPEND);
    }

    /**
     * @param string $action
     * @param Model_PaymentRow $payment
     * @param array $additional_data
     */
    protected function _report($action, Model_PaymentRow $payment = null, $extra_data = null, $extra_message = null)
    {
        $options = $this->getApplicationOptions();
        $options = $options['googleapps'];

        if (!(isset($options['json_link']) && isset($options['json_hash'])))
            return;

        if ($this->user)
            $payer = $this->user;
        else if ($payment)
            $payer = $payment->getPayer();
        else
            $payer = null;

        $observer = new GN_Observer(
            $options['json_link'],
            $options['json_hash'],
            $payer ? $payer->email : $this->view->paymentOptions['paypal']['account'],
            Zend_Registry::get('Zend_Locale')->getLanguage(),
            'sharedapps'
        );

        $data = array();
        $data['currency'] = $this->view->paymentOptions['paypal']['currency'];

        if ($payment)
            $data['payment'] = $payment->toArray();

        if ($payer) {
            $data['payer'] = $payer->toArray();
            $domain = $payer->getDomain();
            $data['user-mp'] = $domain->marketplace;
            $data['user-domain'] = $domain->domain_name;
            $data['user-name'] = $payer->name;
            $data['user-id'] = $payer->id;
            $data['user-email'] = $payer->email;
        }

        if ($extra_data)
            $data['extra'] = $extra_data;

        if ($extra_message)
            $data['message'] = $extra_message;

        $ret = $observer->observe($action, null, $data);
        $this->_log('observer ' . $action, $ret);
        return $ret;
    }
}
