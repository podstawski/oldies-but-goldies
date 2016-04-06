<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class MillionaireController extends GN_Controller
{
    protected $app;

    /**
     * @var Zend_Session_Namespace
     */
    protected $ZendSession;

    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $db;

    /**
     * @var Millionaire_Model_UserRow
     */
    protected $user;

    public function init()
    {
        parent::init();

        $this->app = Zend_Registry::get('app');

        // Session
        $this->ZendSession = new Zend_Session_Namespace($this->app['namespace']);
		$this->ZendSession->setExpirationSeconds(28800);
		$this->view->language = $this->ZendSession->language;

		// Nick
		if(isset($_GET['name'])) {
			$this->view->gameStartNick = $_GET['name'];
		}
		// Test pass
		if(isset($_GET['pass'])) {
			$this->view->default_pass = $_GET['pass'];
			$this->ZendSession->default_pass = $_GET['pass'];
		}
		if(isset($this->ZendSession->default_pass)&&$this->ZendSession->default_pass!='') {
			$this->view->default_pass = $this->ZendSession->default_pass;
		}

		// DB
        $this->db = Zend_Registry::get('db');

		$this->user = $this->view->user = $this->checkUser();
		if(isset($this->view->userName) && isset($this->user) && $this->user->name == "" && $this->view->userName != "") {
			$dbUsers = new Model_User;
			$data["name"] = $this->view->userName;
			$where = $dbUsers->getAdapter()->quoteInto('id = ?', $this->user->id);
			$dbUsers->update($data, $where);
		}

        if ($this->app['beta_closed']
        && !$this->isAdmin()
        && $this->_request->getControllerName() != 'index'
        && $this->_request->getControllerName() != 'auth'
        ) {
            $this->ZendSession->unsetAll();
            $this->_redirectExit('beta-closed', 'index');
        }
    }

	public function getBrowser()
	{
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes separately and for good reason.
		if (preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif (preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif (preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif (preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif (preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif (preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}

		// Finally get the correct version number.
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// See how many we have.
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}

		// Check if we have a number.
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent' => $u_agent,
			'name'	  => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'	=> $pattern
		);
	}

    public function noAccessAction()
    {

    }

    public function betaClosedAction()
    {

    }

    /**
     * @param array $session
     * @return Zend_Db_Table_Row_Abstract|false
     */
    protected function checkUser($default_user_role = 1)
    {
        $this->view->userName = 'Anonimowy';

        if (isset($this->ZendSession->OPENID)) {
            $openID = & $this->ZendSession->OPENID;
            if (isset($openID['first_name']) && isset($openID['last_name']) && isset($openID['email'])) {
                $modelDomain = new Model_Domain;
                list (, $domainName) = explode('@', $openID['email']);
                $domain = $modelDomain->getByName($domainName);
                if ($domain == null) {
                    $domain = $modelDomain->createRow();
                    $domain->setFromArray(array(
                        'domain_name' => $domainName,
                        'org_name' => $domainName,
                        'admin_email' => $openID['email'],
                        'oauth_token' => ''
                    ));
                    $domain->save();
                }

                $modelUser = new Model_User;
                if ($modelUser->getUsersList()->count() === 0) {
                    $this->installGame();
                }

                $user = $modelUser->findByMail($openID['email']);
                if ($user == null) {
                    $user = $modelUser->createRow();
                    $user->setFromArray(array(
                        'email' => $openID['email'],
                        'domain_id' => $domain->id,
                        'user_role' => $default_user_role,
                        'active' => 1,
                        'name' => ''
                    ));
                    $user->save();
                }

                if (isset($this->ZendSession->OPENID['identity'])
                && !empty($this->ZendSession->OPENID['identity'])
                && (empty($user->identity) or $user->identity != $this->ZendSession->OPENID['identity'])
                ) {
                    $user->identity = $this->ZendSession->OPENID['identity'];
                    $user->save();
                }

                $this->view->userName = $openID['first_name'] . ' ' . $openID['last_name'];

                return $user;
            }
        }

        return null;
    }

	// funkcja ktora 'czyści' bazę danych z nie zakończonych wyników testu
	public function attemptsCleaner($id = false)
	{
		if ($id) {
			$dbAttempts = new Model_Attempt;
			$dbTests = new Model_Test;
			$test = $dbTests->getById($id);
			$attempts = $dbAttempts->getByTestPass($test->pass);
			if ($test->time > 0) {
				$testDuration = $test->time * 60;
				$now = time();
				foreach ($attempts as $key => $attempt) {
					$attemtDuration = $now - $attempt->server_started;
					if($attempt->status == 1) {
						$data['status'] = 0;
					} elseif($attempt->status == 101) {
						$data['status'] = 100;
					}
					$where = $dbAttempts->getAdapter()->quoteInto('id = ?', $attempt->id);
					// zmień status w dwóch przypadkach:
					// I - gdy próba trwa dłużej niż powinna (5 minut sekund zapasu)
					if ($attempt->server_finished == 0 && $attemtDuration > ($testDuration + 300) && ( $attempt->status == 1 || $attempt->status == 101)) {
						$data['time_left'] = 0;
						$data['server_finished'] = $attempt->server_started + $testDuration;
						$dbAttempts->update($data, $where);
					// II - jeżeli już odpowiedziano na wszystkie pytania
					} elseif ($attempt->step === 10 && ( $attempt->status == 1 || $attempt->status == 101)) {
						$dbAttempts->update($data, $where);
					}
				}
			}
		}
	}

    /**
     * @return bool
     */
    protected function isAdmin()
    {
        return $this->user && $this->user->user_role == 5;
    }

    protected function checkAdmin()
    {
        if (!$this->isAdmin()) {
            $this->_forward('no-access');
        }
    }

    protected function checkTeacher()
    {
        if (!($this->user && $this->user->user_role >= 3)) {
            $this->_forward('no-access');
        }
    }

    protected function checkToken()
    {
        if (!($this->user && $this->user->getAccessToken())) {
            $this->_redirectExit('token', 'auth');
        }
    }

    protected function installGame()
    {

	}
	
}
