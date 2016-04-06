<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class UsersController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function ajaxAction()
	{
		$request = $this->getRequest();
		$this->view->pageNumber = max(intval($request->getParam('page')), 1);
		$search = null;
		if ($request->getParam('search'))
		{
			$search = $request->getParam('search');
		}
		$request = $this->makeRequest('/users', array('pager' => array('total_records' => 1, 'search' => $search), 'full_name' => $search));
		$this->view->totalRecords = intval($request['outputJSON']['total_records']);
		$this->view->recordsPerPage = 15;
		$offset = $this->view->recordsPerPage * ($this->view->pageNumber - 1);
		$limit = $this->view->recordsPerPage;
		$this->view->users = $this->makeRequest('/users', array('pager' => array('offset' => $offset, 'limit' => $limit, 'search' => $search)));
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$this->view->pageNumber = max(intval($request->getParam('page')), 1);
		$request = $this->makeRequest('/users', array('pager' => array('total_records' => 1)));
		$this->view->totalRecords = $request['outputJSON']['total_records'];
		$this->view->recordsPerPage = 15;
		$offset = $this->view->recordsPerPage * ($this->view->pageNumber - 1);
		$limit = $this->view->recordsPerPage;
		$this->view->users = $this->makeRequest('/users', array('pager' => array('offset' => $offset, 'limit' => $limit)));
	}

	public function viewAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->recordBasic = $this->makeRequest('/users', array('id' => $id));
		$this->view->recordProfile = $this->makeRequest('/user-profile', array('id' => $id));
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		if (empty($_POST))
		{
			$this->view->recordBasic = $this->makeRequest('/users', array('id' => $id));
			$this->view->recordProfile = $this->makeRequest('/user-profile', array('id' => $id));
		}
		else
		{
			$this->view->records = array();
			$postData = array
			(
				'first_name' => $request->getParam('first-name'),
				'last_name' => $request->getParam('last-name')
			);
			//$this->view->records []= array();
			$postData = array
			(
				'sex' => $request->getParam('sex'),
				'national_identity' => $request->getParam('pesel'),
				'birth_date' => $request->getParam('birth-date'),
				'birth_city' => $request->getParam('birth-city'),
				'education' => $request->getParam('education'),
				'care_children_up_to_seven' => $request->getParam('care-children-up-to-seven') !== null,
				'care_dependant_person' => $request->getParam('care-dependant-person') !== null,
				'personal_status' => $request->getParam('personal-status'),
				'group_headmaster' => $request->getParam('group-headmaster'),
				'group_project_leader' => $request->getParam('group-project-leader'),
				'group_guardian' => $request->getParam('group-guardian'),
				'group_student' => $request->getParam('group_student'),
				'group_education_staff' => $request->getParam('group_education_staff'),
				'teacher_of' => $request->getParam('teacher-of'),
				'poland_id' => $request->getParam('poland-id'),
				'address_city' => $request->getParam('address-city'),
				'address_zip_code' => $request->getParam('address-zip-code'),
				'address_street' => $request->getParam('address-street'),
				'address_house_nr' => $request->getParam('address-house-nr'),
				'address_flar_nr' => $request->getParam('address-flar-nr'),
				'region' => $request->getParam('region'),
				'administration_region' => $request->getParam('administration-region'),
				'phone_number' => $request->getParam('phone-number'),
				'mobile_number' => $reques->tgetParam('mobile-number'),
				'fax_number' => $request->getParam('fax-number'),
				'work_name' => $request->getParam('work-name'),
				'work_poland_id' => $request->getParam('work-poland-id'),
				'work_city' => $request->getParam('work-city'),
				'work_zip_code' => $request->getParam('work-zip-code'),
				'work_street' => $request->getParam('work-street'),
				'work_tax_identification_number' => $request->getParam('work-nip'),
				'tax_identification_number' => $request->getParam('nip'),
				'tax_office' => $request->getParam('tax-office'),
				'tax_office_poland_id' => $request->getParam('tax-office-poland-id'),
				'tax_office_city' => $request->getParam('tax-office-city'),
				'tax_office_zip_code' => $request->getParam('tax-office-zip-code'),
				'tax_office_address' => $request->getParam('tax-office-address'),
				'tax_office_house_nr' => $request->getParam('tax-office-house-nr'),
				'tax_office_country' => $request->getParam('tax-office-country'),
				'tax_office_post_city' => $request->getParam('tax-office-post-city'),
				'identification_name' => $request->getParam('identification-name'),
				'identification_number' => $request->getParam('identification-number'),
				'identification_publisher' => $request->getParam('identification-publisher'),
				'father_name' => $request->getParam('father-name'),
				'mother_name' => $request->getParam('mother-name'),
				'nfz' => $request->getParam('health-care'),
				'bank' => $request->getParam('bank-account-number'),
				'zus' => $request->getParam('zus-contract-type'),
			);
			$disabledForms = array();
			if ($request->getParam('work-not-applicable') !== null)
			{
				$disabledForms []= 'work';
			}
			if ($request->getParam('tax-not-applicable') !== null)
			{
				$disabledForms []= 'tax';
			}
			if ($request->getParam('zus-not-applicable') !== null)
			{
				$disabledForms []= 'zus';
			}
			$postData['disabled_forms'] = join(',', $disabledForms);
			var_dump($postData);
			/*
			disabled_forms=work%23tax%23zus

			first_name=Marcin
			last_name=Kurczewski
			sex=M
			national_identity=90102207034
			birth_date=22-10-1990
			birth_place=Pozna%C5%84
			education=null
			care_children_up_to_seven=0
			care_dependant_person=0
			personal_status=1
			group_headmaster=0
			group_project_leader=0
			group_guardian=0
			group_student=0
			group_education_staff=0
			teacher_of=null
			poland_id=3258
			address_city=Pozna%C5%84
			address_zip_code=60-666
			address_street=Ulica
			address_house_nr=5
			address_flat_nr=null
			region=1
			administration_region=1
			phone_number=55-555
			mobile_number=555555555
			fax_number=null
			work_name=Firma
			work_poland_id=17
			work_city=Miasto
			work_zip_code=60-666
			work_street=Adres%20adres
			work_tax_identification_number=54128491
			tax_identification_number=null
			tax_office=Urz%C4%85d
			tax_office_poland_id=17
			tax_office_city=Miasto
			tax_office_zip_code=60-666
			tax_office_address=Ulica
			tax_office_house_nr=5
			tax_office_country=Kraj
			tax_office_post_city=Poczta
			identification_name=ARL
			identification_number=9999
			identification_publisher=Wydawca
			father_name=Ojciec
			mother_name=Matka
			nfz=NFZ
			bank=22102028920000580204695146
			zus=1
			disabled_forms=
			*/
		}
	}

	public function createAction()
	{
		if (empty($_POST))
		{
		}
		else
		{
		}
	}

	public function deleteAction()
	{
	}
}
?>
