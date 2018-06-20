<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seller extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		// El usuario no está logueado.
		if(!isset($_SESSION['userId']) || !isset($_SESSION['userType']))
		{
			redirect(base_url('login'));
		}
		// El usuario no tiene permiso de ver este contenido.
		if($_SESSION['userType'] != SELLER)
		{
			redirect(base_url('home'));
		}
	}

	/**
	 * storeCreateForm
	 * Método que despliega el formulario para crear un negocio.
	 */
	public function storeCreateForm()
	{
		$this->load->model('user');
		$data['agents'] = $this->user->getAll(SELLER);
		$data['title'] = 'New store';
		$data['labels'] = array('action' => 'seller/storeCreate', 'pw' => 'Password:', 'req' => 'required', 'btn' => 'Create', 'maxBalance' => 'required');
		$this->load->view('header', $data);
		$this->load->view('createStore', $data);
		$this->load->view('footer');
	}

	public function storeCreate()
	{
		$this->load->model('client');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de creación.
		{
			$items = $this->input->post(NULL, TRUE);
			if(!$this->client->getByUsername($items['username']))
			{
				$items['type'] = STORE;
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
				$this->client->create($items);
				$data['msg'] = 'The store was created successfully.';
			}
			else
			{
				$data['msg'] = 'A client with username '.$items['email'].' already exists.';
			}
		}
		$data['clients'] = $this->client->getAll(STORE);
		$data['title'] = 'Stores list';
		$data['clients'] = storesBalance($data['clients']);
		$data['controller'] = 'seller';
		$this->load->view('header', $data);
		$this->load->view('stores', $data);
		$this->load->view('footer');
	}

	public function storeEditForm($id)
	{
		$id = (int)$id;
		$this->load->model('client');
		$this->load->model('user');
		$data['agents'] = $this->user->getAll(SELLER);
		$data['title'] = 'Edit store';
		$data['labels'] = array('action' => 'seller/storeEdit', 'pw' => 'New password: <em>optional</em>', 'req' => '', 'btn' => 'Update', 'maxBalance' => 'disabled');
		$data['selClient'] = $this->client->getById($id, STORE);
		$this->load->view('header', $data);
		$this->load->view('createStore', $data);
		$this->load->view('footer');
	}

	public function storeEdit()
	{
		$this->load->model('client');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			$id = $items['id'];
			unset($items['id']);  // Quito el Id ya que no se va a actualizar.
			if($items['password'] != '')
			{
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
			}
			else
			{
				unset($items['password']);  // No va a cambiar la contraseña.
			}
			$this->client->update($id, $items);  // Actualiza el cliente.
			$data['msg'] = 'The store was updated successfully.';
		}
		$data['clients'] = $this->client->getAll(STORE);
		$data['title'] = 'Stores list';
		$data['clients'] = storesBalance($data['clients']);
		$data['controller'] = 'seller';
		$this->load->view('header', $data);
		$this->load->view('stores', $data);
		$this->load->view('footer');
	}

	public function storesList($orderBy = 'name')
	{
		$this->load->model('client');
		// Se envió el formulario de actualización.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$name = $this->input->post('name', TRUE);
			$data['name'] = $name;
			$data['clients'] = $this->client->getAll(STORE, $name, $orderBy);
		}
		else
		{
			$data['clients'] = $this->client->getAll(STORE, '', $orderBy);
		}
		$data['title'] = 'Stores list';
		$data['clients'] = storesBalance($data['clients']);
		$data['controller'] = 'seller';
		$this->load->view('header', $data);
		$this->load->view('stores', $data);
		$this->load->view('footer');
	}

	public function transactionsList()
	{
		$this->load->model('client');
		$this->load->model('transaction');
		$this->lang->load('recent_transactions_lang', $this->getLanguage());
		$data['title'] = 'Recent transactions';
		$data['controller'] = 'seller';
		$data['totalAmount'] = 0;
		$data['totalDue'] = 0;
		$data['totalFee'] = 0;
		$data['clients'] = $this->client->getStoresToInvoice();
		// Se envió el formulario de búsqueda.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$from = $this->input->post('from', TRUE);
			$to = $this->input->post('to', TRUE);
			$status = $this->input->post('status', TRUE);
			$client = $this->input->post('client', TRUE);
			$data['from'] = $from;
			$data['to'] = $to;
			$data['status'] = $status;
			$data['client'] = $client;
			$data['transactions'] = $this->transaction->getByUserId($_SESSION['userId'], $from, $to, $status, $client);
		}
		else
		{
			$data['from'] = $data['to'] = $data['client'] = '';
			$data['status'] = 'Success';
			$data['transactions'] = $this->transaction->getByUserId($_SESSION['userId'], '', '', $data['status'], '');
		}
		foreach($data['transactions'] as &$t)
		{
			$realTopup = ($t->amount - $t->includeCharge);
			$t->amount = $realTopup + ($t->serviceCharge + $t->includeCharge);

			$t->balance = number_format($t->amount - $t->profit, 2);
			$t->product = $this->transaction->getProductName($t->productId);
			$t->providerId = $this->transaction->getProviderId($t->productId);
			$data['totalAmount'] += $t->amount;
			$data['totalDue'] += $t->balance;
			$data['totalFee'] += $t->profit;
		}
		$data['totalAmount'] = number_format($data['totalAmount'], 2);
		$data['totalDue'] = number_format($data['totalDue'], 2);
		$data['totalFee'] = number_format($data['totalFee'], 2);
		$this->load->view('header', $data);
		$this->load->view('clientTransactions', $data);
		$this->load->view('footer');
	}
}
