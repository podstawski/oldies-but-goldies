<?php
class GN_Pararell
{
	private $clients;
	private $goParallel;

	public function __construct($goParallel = true) {
		$this->goParallel = $goParallel;
		$this->clients = array();
		GN_Debug::debug('pararell: init');
	}

	public function __destruct() {
		GN_Debug::debug('pararell: destruct');
	}

	public function request(GN_GClient $g, $description, $requestFunc, $successFunc = null , $errorFunc = null) {
		//$ng = new GN_GClient($g->getUser(), $g->getMode(), $this->goParallel);
		$classname=get_class($g);
		//$ng= clone($g);
		//$ng->setNonBlocking($this->goParallel);
		$ng = new $classname($g->getUser(), $g->getMode(), $this->goParallel);
		
		$this->clients[] = array (
			'client' => $ng,
			'exceptions' => array(),
			'description' => $description,
			'request-func' => $requestFunc,
			'success-func' => $successFunc,
			'error-func' => $errorFunc,
			'success' => null,
			'running' => true,
			'num' => 1,
			'error-num' => 0);
	}

	public function work($maxAttempts = 1000, $maxErrors = 3) {
		$running = true;
		$attempt = 0;
		foreach ($this->clients as $id => $c) {
			GN_Debug::debug($c['description'] . ': started request');
		}
		while ($running) {
			$running = false;
			foreach ($this->clients as $id => $c) {
				if (!$c['running']) {
					continue;
				}
				$running = true;
				try {
					GN_Debug::debug($c['description'] . ': sending request');
					$c['request-func']($c);
					GN_Debug::debug($c['description'] . ': done');
					$c['running'] = false;
					$c['success'] = true;
					if ($c['success-func'] != null) {
						$c['success-func']($c);
					}
				} catch (GN_GClient_NotReadyException $e) {
					GN_Debug::debug($c['description'] . ': not ready yet' . $e->getMessage());
				} catch (Exception $e) {
					GN_Debug::debug($c['description'] . ': an error occured - trying again (exception: ' . $e->getMessage());
					$c['success'] = false;
					$c['exceptions'] []= $e;
					$c['error-num'] ++;
					usleep(pow(1.1, $c['error-num']));
					if ($c['error-num'] >= $maxErrors) {
						$c['running'] = false;
					}
					if ($c['error-func'] != null) {
						$c['error-func']($c);
					}
				}
				$c['num'] ++;
				$this->clients[$id] = $c;
			}
			usleep(100000);
			$attempt ++;
			if ($attempt > $maxAttempts) {
				#GN_Debug::debug($c['description'] . ': still running, but max attempts reach');
				return false;
			}
		}
		foreach ($this->clients as $id => $c) {
			if ($c['success'] === false) {
				return false;
			}
		}
		return true;
	}

	public function reset() {
		$this->clients = array();
	}

}
