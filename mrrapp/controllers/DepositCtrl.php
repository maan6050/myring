<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DepositCtrl extends MY_Controller
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
		if($_SESSION['userType'] != SELLER && $_SESSION['userType'] != ADMIN)
		{
			redirect(base_url('home'));
		}
	}

	public function index()
	{
		$this->load->model('client');
		$this->load->model('deposit');
		// Se envió el formulario de creación.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$items = $this->input->post(NULL, TRUE);
			// Valido que el depósito no exceda el límite.
			$client = $this->client->getById($items['clientId'], STORE);
			if($items['amount'] <= $client->balance)
			{
				$items['userId'] = $_SESSION['userId'];
				$this->deposit->make($items);
				$data['msg'] = 'The deposit was registered successfully.';
			}
			else
			{
				$data['msg'] = 'The amount to be deposited exceeds the limit.';
			}
		}
		$data['clients'] = $this->client->getAll(STORE);
		// Busco y elimino los clientes con balance en cero ya que a ellos no se les pueden hacer abonos.
		foreach($data['clients'] as $key => $c)
		{
			if($c->balance == 0.00)
			{
				unset($data['clients'][$key]);
			}
		}
		$data['title'] = 'New deposit';
		$this->load->view('header', $data);
		$this->load->view('deposit', $data);
		$this->load->view('footer');
	}
}
