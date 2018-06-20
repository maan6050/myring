<?php
/**
 * Controlador para reportes administrativos
 * Creado: Julio 05, 2017
 * Modificaciones: Jorge Mario Romero Arroyo
 * Version 1.0
 */

class ReportsCtrl extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!isset($_SESSION['userId']) || !isset($_SESSION['userType']))
		{
			redirect(base_url('login'));
		}
		$this->lang->load('invoices_lang', $this->getLanguage());
	}

	public function invoices()
	{
        $this->load->model('reportsModel');

        // por defecto el id del cliente o id de vendedor.
		$stores = $_SESSION['userId'];

		switch($_SESSION['userType'])
		{
			case ADMIN:
		        $stores = $this->reportsModel->getStores();
				break;
			case SELLER:
			    $stores = $this->reportsModel->getStores($stores);
			    break;
			default:
			    break;
		}
		
		$data['stores'] = $stores;
		$data['title'] = lang('reports_invoices');

		if('POST' == $_SERVER['REQUEST_METHOD'])
		{
			$this->invoicesPOSTAction($data);
		}
		else
		{
			$data['from'] = $data['to'] = $data['store'] = '';
			$data['items'] = array();
		}

		$this->load->view('header', $data);
		$this->load->view('reports/invoices', $data);
		$this->load->view('footer');
	}

	private function invoicesPOSTAction(&$data)
	{
		catchAlert($this, $data, function ($ctrl, &$data)
		{
			$from = $ctrl->input->post('from', TRUE);
			$to = $ctrl->input->post('to', TRUE);
			$store = $ctrl->input->post('store', TRUE);

			if (!validateDate($from))
			{
				throw new Exception(lang('error_init_date'));
			}

			$data['from'] = $from;

			if (!validateDate($to))
			{
				throw new Exception(lang('error_end_date'));
			}

			$data['to'] = $to;

			$from = empty($from) ? $from : ($from . ' 00:00:00');
			$to = empty($to) ? $to : ($to . ' 23:59:59');

			if (empty($store) || is_nan($store))
			{
				throw new Exception(lang('error_store'));
			}

			$data['store'] = $store;

			if ((new DateTime($from)) > (new DateTime($to)))
			{
				throw new Exception(lang('error_date_range'));
			}

			$items = $ctrl->reportsModel->getTransactionsInvoice($store, $from, $to);

			$data['items'] = $items;

			$commission_total = 0;
			$transactions_total = 0;
			$facevalue_total = 0;

			foreach ($items as $item) 
			{
				$commission_total += $item->profit_sum;
				$transactions_total += $item->product_count;
				$facevalue_total += $item->amount_sum;
			}

			$data['commission_total'] = $commission_total;
			$data['transactions_total'] = $transactions_total;
			$data['facevalue_total'] = $facevalue_total;
		});
	}

	/**
	 * pdf
	 * Método que genera el reporte en pdf.
	 */
	public function generateInvoicePDF()
	{
        $this->load->model('reportsModel');
		$this->load->library('mpdf');
		
		$from = $this->input->post('from', TRUE);
		$to = $this->input->post('to', TRUE);
		$store = $this->input->post('store', TRUE);

		$from = empty($from) ? $from : ($from . ' 00:00:00');
		$to = empty($to) ? $to : ($to . ' 23:59:59');

		$data = array();
		
		$cdata = $this->reportsModel->getClientData($store);

		$cdata->postal_code = (empty($cdata->city) ? '' : $cdata->city) 
		    . (', ' . (empty($cdata->state) ? '' : $cdata->state))
			. (', ' . (empty($cdata->country) ? '' : $cdata->country))
			. (', ' . (empty($cdata->zip) ? '' : $cdata->zip));

		$data['cdata'] = $cdata;
		$data['from'] = (new \DateTime($from))->format('d/M/y');
		$data['to'] = (new \DateTime($to))->format('d/M/y');
		$data['invoice_date'] = (new \DateTime('now'))->format('d/M/y');
		$items = $this->reportsModel->getTransactionsInvoice($store, $from, $to);

		$data['items'] = $items;

        $commission_total = 0;
		$transactions_total = 0;
		$facevalue_total = 0;

        foreach ($items as $item) 
		{
			$commission_total += $item->profit_sum;
			$transactions_total += $item->product_count;
			$facevalue_total += $item->amount_sum;
		}

		$data['commission_total'] = $commission_total;
		$data['transactions_total'] = $transactions_total;
		$data['facevalue_total'] = $facevalue_total;
        
        $mpdf = new mPDF(
		    'L', // mode - default ''
			'LETTER', // format - A4, for example, default ''
			0, // font size - default 0
			'', // default font family
			10, // margin_left
			10, // margin right
			16, // margin top
			16, // margin bottom
			9, // margin header
			9, // margin footer
			'L');

		ob_start();
		$mpdf->WriteHTML($this->load->view('reports/invoice_pdf', $data, TRUE));
		ob_clean();
		$mpdf->Output();		
	}	
}