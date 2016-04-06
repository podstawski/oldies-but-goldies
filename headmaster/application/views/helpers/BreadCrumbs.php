<?php

class Zend_View_Helper_BreadCrumbs extends Zend_View_Helper_Abstract
{ 
   
    public function BreadCrumbs($string = false, $user = false) 
    {
		if($user) {
			$email = ': '.$user->email;
			$params = array(
				'id' => $user->id
			);
		} else {
			$email = '';
			$params = array();
		}
		$container = new Zend_Navigation();
		$this->view->navigation($container);
		$container->addPage(
		    array(
		        'label'      => $this->view->translate('Headmaster'),
		        'controller' => 'index',
		        'action'     => 'index',
		        'pages'      =>
		        array(
				    array(
				        'label'      => $this->view->translate('Panel nauczyciela'),
				        'controller' => 'nauczyciel',
				        'action'     => 'index',
				        'pages'      =>
				        array(
						    array(
						        'label'      => $this->view->translate('Szczegóły testu'),
						        'controller' => 'nauczyciel',
						        'action'     => 'pokaz-test',
						    ),
						    array(
						        'label'      => $this->view->translate('Wyślij zaproszenie'),
						        'controller' => 'nauczyciel',
						        'action'     => 'wyslij-zaproszenie',
						    ),
						    array(
						        'label'      => $this->view->translate('Statystyki testu'),
						        'controller' => 'nauczyciel',
						        'action'     => 'statystyki',
						    ),
						    array(
						        'label'      => $this->view->translate('Stwórz nowy test'),
						        'controller' => 'nauczyciel',
						        'action'     => 'nowy-test',
						    ),
						    array(
						        'label'      => $this->view->translate('Moje pytania'),
						        'controller' => 'nauczyciel',
                                'action'     => 'my-questions',
                                'pages'      => array()
						    ),
						    array(
						        'label'      => $this->view->translate('Dodaj pytanie'),
						        'controller' => 'nauczyciel',
                                'action'     => 'add-question',
                                'pages'      => array()
						    ),
						    array(
						        'label'      => $this->view->translate('Edytuj pytanie'),
						        'controller' => 'nauczyciel',
                                'action'     => 'show-question',
                                'pages'      => array()
						    ),
						    array(
						        'label'      => $this->view->translate('Moderuj pytanie'),
						        'controller' => 'nauczyciel',
                                'action'     => 'check-question',
                                'pages'      => array()
						    ),
						    array(
						        'label'      => $this->view->translate('Moderuj pytania'),
						        'controller' => 'nauczyciel',
                                'action'     => 'check-questions',
                                'pages'      => array()
						    ),
				        )
				    ),
				    array(
				        'label'      => $this->view->translate('Panel administratora'),
				        'controller' => 'administrator',
				        'action'     => 'index',
				        'pages'      =>
				        array(
						    array(
						        'label'      => $this->view->translate('Import pytan z Google Docs'),
						        'controller' => 'administrator',
						        'action'     => 'import-pytan-link',
						    ),
						    array(
						        'label'      => $this->view->translate('Statystyki gry'),
						        'controller' => 'administrator',
						        'action'     => 'statystyki',
						    ),
						    array(
						        'label'      => $this->view->translate('Usuń puste kategorie'),
						        'controller' => 'administrator',
						        'action'     => 'delete-empty-categories',
						    ),
						    array(
						        'label'      => $this->view->translate('Lista pytań w bazie danych'),
						        'controller' => 'administrator',
						        'action'     => 'lista-pytan',
						    ),
						    array(
						        'label'      => $this->view->translate('Import pytań'),
						        'controller' => 'administrator',
						        'action'     => 'import-pytan',
						    ),
						    array(
						        'label'      => $this->view->translate('Lista użytkowników'),
						        'controller' => 'administrator',
						        'action'     => 'lista-userow',
						        'pages'      => array(
								    array(
								        'label'      => $this->view->translate('Testy użytkownika').$email,
								        'controller' => 'administrator',
										'action'     => 'pokaz-usera',
										'params'     => $params,
								        'pages'      => array(
											array(
												'label'      => $this->view->translate('Pokaż test'),
												'controller' => 'administrator',
												'action'     => 'pokaz-test',
												'pages'      => array(
												)
											)
										)
								    )
								)
						    )
				        )
				    )

		        )
		    )
		);
		
		// narysuj BreadCrumb
		echo '<div id="breadCrumbs">';
		echo $this->view->navigation()->breadcrumbs()->setSeparator(' &raquo; ')->setLinkLast(false)->setMinDepth(0)->render();
		if($string) echo ': ',$string;
		echo '</div>';
		
		// zmień tytuł podstrony	
        $rootPage = current($this->view->navigation()->getContainer()->getPages());
        $pages = $rootPage->getPages();
        foreach($pages as $page) { 
            if($page->isActive(true)) { 
				$subPages = $page->getPages();
				if(count($subPages)>0) {
					foreach($subPages as $subPage) {
						if($subPage->isActive(true)) {
							$this->view->headTitle($subPage);
						} 
					}
				}	
				$this->view->headTitle($page);
            }
        }	
			
    }

	

}
