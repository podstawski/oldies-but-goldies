<?php
class GN_Pararell
{
	private $cliens;

	public function __construct() {
		$this->clients = array();
		$this->debug('pararell', 'init');
	}

	public function __destruct() {
		$this->debug('pararell', 'destruct');
	}

	private function debug() {
		call_user_func_array(array('GN_Debug', 'debug'), func_get_args());
	}

	public function request(GN_GClient $g, $description, $requestFunc, $successFunc = null , $errorFunc = null) {
		$ng = new GN_GClient($g->getUser(), $g->getMode(), true);
		$this->clients[] = array (
			'client' => $ng,
			'exceptions' => array(),
			'description' => $description,
			'request-func' => $requestFunc,
			'success-func' => $successFunc,
			'error-func' => $errorFunc,
			'running' => true,
			'num' => 1,
			'error-num' => 0);
	}

	public function work($maxAttempts = 1000) {
		$running = true;
		$attempt = 0;
		while ($running) {
			$running = false;
			foreach ($this->clients as $id => $c) {
				if (!$c['running']) {
					continue;
				}
				$running = true;
				try {
					$this->debug($c['description'], 'sending request');
					$c['request-func']($c);
					$this->debug($c['description'], 'done');
					$c['running'] = false;
					if ($c['success-func'] != null) {
						$c['success-func']($c);
					}
				} catch (GN_GClient_NotReadyException $e) {
					$this->debug($c['description'], 'not ready yet');
				} catch (Exception $e) {
					$this->debug($c['description'], 'an error occured - trying again');
					$c['exceptions'] []= $e;
					$c['error-num'] ++;
					usleep(pow(1.1, $c['error-num']));
				}
				$c['num'] ++;
				$this->clients[$id] = $c;
			}
			usleep(10000);
			$attempt ++;
			if ($attempt > $maxAttempts) {
				foreach ($this->clients as $id => $c) {
					if (!$c['running']) {
						continue;
					}
					$this->debug($c['description'], 'still running, but max attempts reach');
					if ($c['error-func'] != null) {
						$c['error-func']($c);
					}
				}
				return false;
			}
		}
		return true;
	}

	public function reset() {
		$this->clients = array();
	}

}
