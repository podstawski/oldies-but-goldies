<?php
require_once 'AbstractController.php';

class UsersController extends AbstractController {


	public function emailsAction()
	{
		$action=$this->_getParam('observerAction')?:'reminder';


		$trial = $this->getInvokeArg('bootstrap')->getOption('trial');
		$paypal = $this->getInvokeArg('bootstrap')->getOption('paypal');

		$users=new Model_Users();

		$ludziki = Model_Users::getAll($this->_getParam('emailSearch'));


		foreach($ludziki AS $ludzik)
		{
			$this->observer = null;
			$this->user = $ludzik;
			$this->initObserver();

			$ludzik=$ludzik->toArray();

			if ($ludzik['disabled']) continue;

			//sprawdzanie, czy gość ma dobry token - tutaj, i tylko tutaj robimy cokolwiek z db
			//(poza sytuacją kiedy sobie wyresetuje token przyciskiem, jeśli nie jest z marketu)
			if ($this->user->getDomain()->marketplace or $this->user->token) {
				try {
					$cclient = CRM_Core::getContactsClient($this->user);
					$cclient->getGroups();
				} catch (GN_GClient_EmptyTokenException $e) {
					$this->user->resetAccessToken();
					$msg = explode("\n", $e->getMessage());
					$msg = reset($msg);
					echo "resetAccessToken:GN_GClient_EmptyTokenException [".$ludzik['email']."] ... ".$msg."\n";
					continue;
				} catch (CRM_EmptyTokenException $e) {
					$this->user->resetAccessToken();
					$msg = explode("\n", $e->getMessage());
					$msg = reset($msg);
					echo "resetAccessToken:CRM_EmptyTokenException [".$ludzik['email']."] ... ".$msg."\n";
					continue;
				} catch (Exception $e) {
					if (strstr($e->getMessage(), '401')) {
						$msg = explode("\n", $e->getMessage());
						$msg = reset($msg);
						echo "resetAccessToken:401 [".$ludzik['email']."] ... ".$msg."\n";
						$this->user->resetAccessToken();
						continue;
					}
				}
				$this->user->confirmAccessToken();
			}

			$days=round((strtotime($ludzik['expire'])-time())/(3600*24))+0;

			if (strstr($ludzik['referer'],'@')) continue;


			$info['days']=$days;
			$info['name']=$ludzik['name'];
			$info['user-name']=$ludzik['name'];
			$info['currency']=$paypal['currency'];
			$info['expired']=($days<=0)+0;
			$info['user-mp']=$ludzik['marketplace'];
			$info['user-domain']=$ludzik['domain_name'];

			if (!$ludzik['language']) $ludzik['language']='en';

			$info['expire']=date($this->view->translate('common_date_format',$ludzik['language']), strtotime($ludzik['expire']));

			$wynik=$this->observer->observe($action,($days>0)+0, $info, $ludzik['language']);

			if (is_object($wynik)) $wynik=$wynik->mail;
			echo "$action-".$ludzik['language'].": ".$ludzik['email'].", days=$days .... $wynik\n";

			flush();
			@ob_flush();
		}

		$domeny = array();
		foreach ($ludziki as $ludzik) {
			$domain = $ludzik->getDomain();
			$domeny[$domain->id] = $domain;
		}
		foreach ($domeny as $domain) {
			if ($domain->allUsersDisabled()) {
				$domain->resetAccessToken();
			}
		}

		die();
	}


	public function manageDisabledAction()
	{
		$u=new Model_Users();
		$users=$u->getDisabled();
		

		$tab=array();
		$lasttime=0;
		foreach ($users AS $user)
		{
			$disabled=strtotime($user->disabled);
			if ($disabled-$lasttime > 60*15) {
				$lasttime=$disabled;
			}
			
			$tab[$lasttime][]=$user;
			
		}
		
		foreach($tab AS $time=>$users)
		{
			if (count($users)>4)
			{
				foreach ($users AS $user) {
					$user->disabled=null;
					$user->save();
				}
			}
		}
		
		
		
		
		die();
	}
}
