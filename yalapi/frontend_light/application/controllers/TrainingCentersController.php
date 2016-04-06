<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class TrainingCentersController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$this->view->pageNumber = max(intval($request->getParam('page')), 1);
		$request = $this->makeRequest('/training-centers', array('pager' => array('total_records' => 1)));
		$this->view->totalRecords = $request['outputJSON']['total_records'];
		$this->view->recordsPerPage = 15;
		$offset = $this->view->recordsPerPage * ($this->view->pageNumber - 1);
		$limit = $this->view->recordsPerPage;
		$this->view->records = $this->makeRequest('/training-centers', array('pager' => array('offset' => $offset, 'limit' => $limit)));
	}

	public function viewAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->info = $this->makeRequest('/training-centers', array('id' => $id));
		$this->view->rooms = $this->makeRequest('/rooms', array('training_center_id' => $id));
		$this->view->resources = $this->makeRequest('/resources', array('training_center_id' => $id));
		foreach ($this->view->resources['outputJSON'] as $key => $resource)
		{
			$request = $this->makeRequest('/resource-types', array('id' => $resource['resource_type_id']));
			$resource['name'] = $request['outputJSON']['name'];
			if (isset ($resource['amount']))
			{
				$resource['quantity'] = $resource['amount'];
				unset ($resource['amount']);
			}
			$this->view->resources['outputJSON'][$key] = $resource;
		}
	}

	public function createAction()
	{
		$request = $this->getRequest();
		if (empty($_POST))
		{
			$this->view->resourceTypes = $this->makeRequest('/resource-types');
		}
		else
		{
			$data = array
			(
				'training_center' => array
				(
					'name' => $request->getParam('name'),
					'code' => $request->getParam('code'),
					'street' => $request->getParam('street'),
					'zip_code' => $request->getParam('zip-code'),
					'city' => $request->getParam('city'),
					'manager' => $request->getParam('manager'),
					'phone_number' => $request->getParam('phone-number'),
					'url' => $request->getParam('url'),
					'description' => $request->getParam('description')
				),
				'rooms' => json_decode($request->getParam('room-data'), true),
				'resources' => json_decode($request->getParam('resource-data'), true)
			);
			//utwórz wszystkie resourcesy jeśli ich nie ma, w przeciwnym razie pobierz ich id
			foreach ($data['resources'] as $key => $resource)
			{
				$request = $this->makeRequest('/resource-types', array('name' => $resource['name']));
				if (empty($request['outputJSON']))
				{
					//dany zasób nie istnieje i trzeba go utworzyć
					$request = $this->makeRequest('/resource-types', array(), array('name' => $resource['name']));
					$id = $request['outputJSON']['id'];
				}
				else
				{
					$id = $request['outputJSON'][0]['id'];
				}
				$resource['type'] = $id;
				$data['resources'][$key] = $resource;
			}
			$this->view->response = $this->makeRequest('/training-centers', array(), array('data' => json_encode($data)));
		}
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		if (empty($_POST))
		{
			$this->view->info = $this->makeRequest('/training-centers', array('id' => $id));
			$this->view->rooms = $this->makeRequest('/rooms', array('training_center_id' => $id));
			$this->view->resources = $this->makeRequest('/resources', array('training_center_id' => $id));
			$this->view->resourceTypes = $this->makeRequest('/resource-types');
			foreach ($this->view->resources['outputJSON'] as $key => $resource)
			{
				$request = $this->makeRequest('/resource-types', array('id' => $resource['resource_type_id']));
				$resource['name'] = @$request['outputJSON']['name'];
				if (isset ($resource['amount']))
				{
					$resource['quantity'] = $resource['amount'];
					unset ($resource['amount']);
				}
				$this->view->resources['outputJSON'][$key] = $resource;
			}
		}
		else
		{
			$data = array
			(
				'training_center' => array
				(
					'name' => $request->getParam('name'),
					'code' => $request->getParam('code'),
					'street' => $request->getParam('street'),
					'zip_code' => $request->getParam('zip-code'),
					'city' => $request->getParam('city'),
					'manager' => $request->getParam('manager'),
					'phone_number' => $request->getParam('phone-number'),
					'url' => $request->getParam('url'),
					'description' => $request->getParam('description')
				),
				'rooms' => json_decode($request->getParam('room-data'), true),
				'resources' => json_decode($request->getParam('resource-data'), true)
			);

			//pobierz id wszystkich wysłanych zasobów.
			foreach ($data['resources'] as $key => $resource)
			{
				//zobacz czy taki resource się znajduje w systemie.
				$request = $this->makeRequest('/resource-types', array('name' => $resource['name']));
				if (empty($request['outputJSON']))
				{
					//dany zasób nie istnieje i trzeba go utworzyć
					$request = $this->makeRequest('/resource-types', array(), array('name' => $resource['name']));
					$resource_id = $request['outputJSON']['id'];
				}
				else
				{
					$resource_id = $request['outputJSON'][0]['id'];
				}
				$resource['resource_type_id'] = $resource_id;
				$resource['type'] = $resource_id;
				$resource['training_center_id'] = $id;
				if (isset ($resource['amount']))
				{
					$resource['quantity'] = $resource['amount'];
					unset ($resource['amount']);
				}
				unset ($resource['id']);
				$data['resources'][$key] = $resource;
			}

			//możliwe, że wystarczy to samo zrobić z salami co z zasobami?

			$this->view->responses = array();
			$this->view->responses []= $this->makeRequest('/training-centers', array('id' => $id), array('data' => json_encode($data)), 'PUT');

			//usuń lub edytuj istniejące sale
			$rooms = $this->makeRequest('/rooms/', array('training_center_id' => $id));
			foreach ($rooms['outputJSON'] as $room)
			{
				//zobacz, czy user wysłał dane pomieszczenia o takim id
				$roomUser = null;
				foreach ($data['rooms'] as $roomSent)
				{
					if ($room['id'] == $roomSent['id'])
					{
						$roomUser = $roomSent;
						$removed = false;
						break;
					}
				}
				if (!$roomUser)
				{
					//usuwamy
					$this->view->responses []= $this->makeRequest('/rooms', array('id' => $room['id']), array(), 'DELETE');
				}
				else
				{
					//edytujemy
					$this->view->responses []= $this->makeRequest('/rooms', array('id' => $room['id']), $roomUser);
				}
			}
			//wreszcie dodaj wszystkie z wysłanego formularza
			foreach ($data['rooms'] as $room)
			{
				//jeśli ma swój id, no to nie ma co dodawać, bo już jest dodany
				if (isset($room['id']))
				{
					continue;
				}
				$room['training_center_id'] = $id;
				$this->view->responses []= $this->makeRequest('/rooms', array(), $room);
			}
		}
	}

	public function deleteAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->response = $this->makeRequest('/training-centers', array('id' => $id), array(), 'DELETE');
	}
}
?>
