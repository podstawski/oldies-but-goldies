<?php

require_once __DIR__.'/../../library/Millionaire-quiz/application/controllers/IndexController.php';

class IndexController extends MillionaireIndexController
{
	public function init()
	{
		parent::init();		
		if ($this->user == null) {
			unset($this->view->userName);
		}

		$this->view->actionName = $this->getRequest()->getActionName();		
		if($this->view->actionName != 'old-browser' && $this->view->actionName != 'java-script-disabled') {
				$browser = $this->getBrowser();
				if($browser['name'] == 'Internet Explorer' && $browser['version'] < 8) {
					$this->_redirect('index/old-browser');
				}
		}

	}

	public function oldBrowserAction() {

	}

	public function javaScriptDisabledAction() {

	}

	// sprawdza czy zalogowany user ma range Admina
	public function installGame() {
			// Typy kategorii
			$dbCategoryTypes = new Model_CategoryType;
			$data = array(
					array('Poziom trudności', 0),
					array('Rodzaj szkoły', 1),
					array('Program', 1)
			);
			foreach($data as $d) {
				$dbCategoryTypes->insert(array(
						'name' => $d[0],
						'multiple' => $d[1]
				));
			}
		
			// Kategorie
			$dbCategories = new Model_Category;
			$data = array(
					array(0, 'Łatwy', 1),
					array(0, 'Średni', 1),
					array(0, 'Trudny', 1),
					array(0, 'Liceum  i technikum', 2),
					array(0, 'Zasadnicza szkoła zawodowa', 2),
					array(0, 'Przedsiębiorczość', 3)
			);

			foreach($data as $d) {
				$dbCategories->insert(array(
						'parent_id' => $d[0],
						'name' => $d[1],
						'category_type_id' => $d[2]
				));
			}
		
			// Koła ratunkowe
			$dbLifeBuoy = new Model_LifebuoyType;
			$data = array(
					array(1, 'Ekspert'),
					array(2, 'Badanie opinii'),
					array(3, '50/50')
			);
			foreach($data as $d) {
				$lfb = $dbLifeBuoy->getById($d[0]);
				if(!isset($lfb->id)) {
					$dbLifeBuoy->insert(array(
							'id' => $d[0],
							'name' => $d[1]
					));
				}
			}
		
			// Rodzaje kont
			$dbUserRoles = new Model_UserRole;
			$data = array(
					array('Gość'),
					array('Uczeń'),
					array('Nauczyciel'),
					array('Moderator'),
					array('Administrator')
			);
			foreach($data as $d) {
				$dbUserRoles->insert(array(
						'name' => $d[0]
				));
			}
	}
	
}

