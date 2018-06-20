<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PrintThermo extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index($id)
	{
		$this->load->model('product');
		$this->load->model('transaction');
		$data['transaction'] = $this->transaction->getById($id);
		$data['product'] = $this->product->getById($data['transaction']->productId);
		// Determino si el producto tiene cargo por servicio y lo sumo.
		if($data['transaction']->serviceCharge > 0)
		{
			$total = $data['transaction']->amount + $data['transaction']->serviceCharge;
			$data['transaction']->total = number_format($total, 2);
		}
		else
		{
			$data['transaction']->total = $data['transaction']->amount;
		}
		$this->load->view('printThermo', $data);
	}
}
