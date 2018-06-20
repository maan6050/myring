<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include(realpath(dirname(__FILE__)).'/home_classes/logical_provider_ctrl.php');

class ClientCtrl extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		if(!isset($_SESSION['userId']) || !isset($_SESSION['userType']))  // El usuario no está logueado.
		{
			redirect(base_url('login'));
		}
		if($_SESSION['userType'] != STORE)  // El usuario no tiene permiso de ver este contenido.
		{
			redirect(base_url('home'));
		}
		$this->lang->load('header_lang', $this->getLanguage());
		$this->lang->load('recent_transactions_lang', $this->getLanguage());
		$this->lang->load('textEdit_lang', $this->getLanguage());
	}

	public function feeList()
	{
		$this->load->model('client');
		$this->load->model('product');
		$data['client'] = $this->client->getById($_SESSION['userId'], STORE);
		$data['products'] = $this->product->getAll(NULL, TRUE);
		// Traigo el listado de porcentajes particulares para el cliente.
		$clientProfit = $this->product->getClientProfit($_SESSION['userId']);
		$countryName = '';
		foreach($data['products'] as $i => &$product)
		{
			foreach($clientProfit as $cp)
			{
				// Si el cliente tiene un porcentaje particular, aquí reemplazo el que viene por defecto.
				if($product->id == $cp->productId)
				{
					$product->defaultProfit = $cp->profit;
					// No sigo buscando, avanzo al siguiente producto.
					break;
				}
			}
			// Determino si es el primer o último producto de un país.
			if($product->countryName != $countryName)
			{
				$product->isFirst = TRUE;
				$countryName = $product->countryName;
				if($i > 0)
				{
					$data['products'][$i - 1]->isLast = TRUE;
				}
				$data['products'][$i]->isLast = FALSE;
			}
			else
			{
				$product->isFirst = $product->isLast = FALSE;
			}
		}
		// En el último ciclo no configura esta variable, por eso se hace aparte.
		$data['products'][$i]->isLast = TRUE;

		$data['title'] = lang('fee_list');
		$this->load->view('header', $data);
		$this->load->view('feeList', $data);
		$this->load->view('footer');
	}

	public function pinlessAccess()
	{
		$this->load->model('content');
		$data['title'] = lang('pinless_access');
		$data['tab'] = $this->input->get_default('tab', 1);
		$contents = $this->content->getAll();
		foreach($contents as $value)
		{
			$data[$value->id.'Title'] = lang($value->id.'Title');
			$data[$value->id.'Value'] = $value->content;
		}
		$this->load->view('header', $data);
		$this->load->view('pinlessAccessNumbers', $data);
		$this->load->view('footer');
	}

	public function ratesList()
	{
		$this->load->model('content');
		$data['title'] = lang('rates_list');
		$data['tab'] = $this->input->get_default('tab', 1);
		$contents = $this->content->getAll();
		foreach($contents as $value)
		{
			$data[$value->id.'Title'] = lang($value->id.'Title');
			$data[$value->id.'Value'] = $value->content;
		}
		$this->load->view('header', $data);
		$this->load->view('ratesList', $data);
		$this->load->view('footer');
	}

	public function refundPinless($transaction_id)
	{
		$this->load->model('client');
		$this->load->model('transaction');
		$transaction = $this->transaction->getById($transaction_id);
		$api = new LogicalProviderController($this);
		$complement_url = 'pinless_void/'.$transaction->transId.'.json';
		$response = $api->callAPI('POST', $complement_url);
		$response_code = getArrayValue($response, 'Response-Code');
		if($response_code == 200)
		{
			$value = floatval($transaction->amount - $transaction->profit) * -1;
			// Actualizo el balance del cliente.
			$this->client->updateBalance($_SESSION['userId'], $value);
			$this->transaction->update($transaction_id, array('status' => 'Refunded'));
		}
		redirect('/clientCtrl/transactionsList');
	}

	public function transactionsList()
	{
		$this->load->model('transaction');
		$data['title'] = lang('title');
		$data['controller'] = 'clientCtrl';
		$data['totalAmount'] = 0;
		$data['totalDue'] = 0;
		$data['totalFee'] = 0;
		// No hay clientes ya que solo es uno.
		$data['clients'] = array();
		// Se envió el formulario de búsqueda.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$from = $this->input->post('from', TRUE);
			$to = $this->input->post('to', TRUE);
			$status = $this->input->post('status', TRUE);
			$data['from'] = $from;
			$data['to'] = $to;
			$data['status'] = $status;
			$data['transactions'] = $this->transaction->getByClientId($_SESSION['userId'], $from, $to, $status);
		}
		else
		{
			$data['from'] = $data['to'] = $data['status'] = '';
			$data['transactions'] = $this->transaction->getByClientId($_SESSION['userId']);
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

	/**
	 * cancelOrder
	 * Cancel the transaction already made.
	 */
	public function cancelOrder($orderId)
	{
		$this->load->model('provider');
		$this->load->model('transaction');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('OrderId' => $orderId);
			$response = $client->CancelOrder($params)->CancelOrderResult;
			switch($response->Status)
			{
				case 'Success':
					$this->transaction->update($orderId, array('status' => 'Canceled'));
					$data['status'] = 'Success';
					$data['msg'] = lang('cancelOrderSuccess');
					echo json_encode($data);
					break;
				case 'Verify':
				case 'Pending':
					$data = $this->getWebTransactionInfo($orderId);
					echo json_encode($data);
					break;
				default:
					$data['status'] = 'Error';
					$data['msg'] = lang('cancelOrderFailed');
					echo json_encode($data);
					break;
			}
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	public function getWebTransactionInfo($orderId, $ajx = NULL)
	{
		$this->load->model('provider');
		$this->load->model('transaction');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		$trans = $this->transaction->getById($orderId);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));
			$params = array('WebTransId' => $trans->transId);
			$attemps = 0;
			$data = array();
			while($attemps <= 12)
			{
				$response = $client->GetWebTransactionInfo($params)->GetWebTransactionInfoResult;
				$xml = simplexml_load_string($response->any, 'SimpleXMLElement', LIBXML_NOCDATA);
				$array = json_decode(json_encode($xml), TRUE);

				if($array['NewDataSet']['webTransInfo']['status'] == 'S')
				{
					$this->transaction->update($orderId, array('status' => 'Canceled'));
					$data['status'] = 'Success';
					$data['msg'] = lang('cancelOrderSuccess');
				}
				$attemps++;
			}
			if(count($data) == 0)
			{
				$this->transaction->update($orderId, array('status' => 'PendingCO'));
				$data['status'] = 'Failed';
				$data['msg'] = lang('cancelOrderSuccess');
			}
			if($ajx != NULL)
			{
				echo json_encode($data);
				return;
			}
			else
			{
				return $data;
			}

		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
}
