<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include(realpath(dirname(__FILE__)).'/home_classes/ceretel_provider_ctrl.php');
include(realpath(dirname(__FILE__)).'/home_classes/dollar_phone_provider_ctrl.php');
include(realpath(dirname(__FILE__)).'/home_classes/logical_provider_ctrl.php');

class Home extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if(!isset($_SESSION['userId']))
		{
			redirect(base_url('login'));  // El usuario no ha iniciado sesión.
		}
		if(isset($_GET['cache']) && $_GET['cache'] == 1)
		{
			$this->output->delete_cache();
		}
		if($_SESSION['userType'] != STORE)
		{
			$this->session->set_userdata('lang', 'en');
		}
		$this->lang->load('header_lang', $this->getLanguage());
		$this->lang->load('home_lang', $this->getLanguage());
	}

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		$data['title'] = 'Home';
		switch($_SESSION['userType'])
		{
			case ADMIN:
				$this->load->model('client');
				$this->load->model('deposit');
				$this->load->model('transaction');
				$data['latest'] = $this->transaction->getLatest(ADMIN);
				$data['stores'] = $this->client->sumClients(ADMIN, STORE);
				$data['sumBalances'] = number_format($this->client->sumBalances(ADMIN, STORE), 2);
				$data['sumTransactions'] = number_format($this->transaction->sumTransactions(ADMIN), 2);
				$data['numTransactions'] = $this->transaction->numTransactions(ADMIN);
				$data['sumDeposits'] = number_format($this->deposit->sumDeposits(ADMIN), 2);
				$data['numDeposits'] = $this->deposit->numDeposits(ADMIN);
				$view = 'admin';
				break;
			case SELLER:
				$this->load->model('client');
				$this->load->model('deposit');
				$this->load->model('transaction');
				$data['latest'] = $this->transaction->getLatest(SELLER);
				$data['stores'] = $this->client->sumClients(SELLER, STORE);
				$data['sumBalances'] = number_format($this->client->sumBalances(SELLER, STORE), 2);
				$data['sumTransactions'] = number_format($this->transaction->sumTransactions(SELLER), 2);
				$data['numTransactions'] = $this->transaction->numTransactions(SELLER);
				$data['sumDeposits'] = number_format($this->deposit->sumDeposits(SELLER), 2);
				$data['numDeposits'] = $this->deposit->numDeposits(SELLER);
				$view = 'seller';
				break;
			case STORE:
				$this->load->model('client');
				$this->load->model('country');
				$this->load->model('product');
				$this->load->model('slide');
				$this->load->model('news');
				$client = $this->client->getById($_SESSION['userId'], STORE);
				$data['max'] = $client->maxBalance - $client->balance;
				// Traigo el listado de países activos.
				$countries = $this->country->getAll('a');
				$ids = $preferred = $defaultCountry = '';
				foreach($countries as $country)
				{
					$ids .= '"'.$country->id.'", ';
					if($country->preferred == 'Yes')
					{
						$preferred .= '"'.$country->id.'", ';
						if($defaultCountry == '')
						{
							$defaultCountry = $country->id;
						}
					}
				}
				// Traigo los productos activos del país por defecto.
				$data['products'] = $this->product->getAll($defaultCountry, TRUE);
				foreach($data['products'] as &$product)
				{
					$product->values = $product->type == 'f' ? $product->fixed : $product->rangeMin.','.$product->rangeMax;
				}
				$data['ids'] = substr($ids, 0, -2);  // Quito la última coma.
				$data['preferred'] = substr($preferred, 0, -2);
				$data['slides'] = $this->slide->getAll();
				$data['news'] = $this->news->getAll();
				$data['cart'] = $this->_getLatestTopups();
				$view = 'home';
		}
		$this->load->view('header', $data);
		$this->load->view($view, $data);
		$this->load->view('footer');
	}

	public function account()
	{
		$this->load->model('client');
		$this->load->model('user');
		$data['title'] = lang('title_my_account');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			if($items['password'] != '' && $items['password'] == $items['passwordConf'])
			{
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
				unset($items['passwordConf']);
			}
			else
			{
				unset($items['password'], $items['passwordConf']);
			}
			switch($_SESSION['userType'])
			{
				case ADMIN:
				case SELLER:
					$this->user->update($_SESSION['userId'], $items);
					break;
				case STORE:
					$this->client->update($_SESSION['userId'], $items);
					break;
			}
			if($_SESSION['userName'] != $items['name'])
			{
				$_SESSION['userName'] = $items['name'];
			}
			$data['msg'] = 'Your account was updated successfully.';
		}
		$this->load->view('header', $data);
		switch($_SESSION['userType'])
		{
			case ADMIN:
			case SELLER:
				$data['selected'] = $this->user->getById($_SESSION['userId'], $_SESSION['userType']);
				$this->load->view('userAccount', $data);
				break;
			case STORE:
				$data['selected'] = $this->client->getById($_SESSION['userId'], $_SESSION['userType']);
				$this->load->view('clientAccount', $data);
				break;
		}
		$this->load->view('footer');
	}

	public function addFunds()
	{
		$this->load->model('client');
		$this->load->model('country');
		$this->load->model('phonebook');
		$this->load->model('product');
		$this->load->model('transaction');
		$this->load->model('slide');
		$this->load->model('news');
		$client = $this->client->getById($_SESSION['userId'], STORE);
		$max = $client->maxBalance - $client->balance;
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productId']))  // Se envió el formulario de recarga.
		{
			$items = $this->input->post(NULL, TRUE);
			// Valido que no haya hecho una transacción del mismo cliente para el mismo número en los últimos cinco minutos.
			$existingTr = $this->transaction->transactionExists($items['productId'], $items['clientPhone'], $items['phone']);
			if(empty($existingTr))
			{
				if($amountFromList = getArrayValue($items, 'amountfromlist'))
				{
					$items['amount'] = $amountFromList;
				}
				// Valido que el depósito no exceda el límite.
				if($items['amount'] <= $max)
				{
					$product = $this->product->getById($items['productId']);
					// Determino si este producto muestra un valor diferente al valor a recargar.
					if($product->type == 'f' && strpos($product->fixed, '|') !== FALSE)
					{
						if($product->includeCharge > 0)
						{
							$clientAmount = $items['amount'];
						}
						else
						{
							// Este es el valor por defecto de la recarga.
							$clientAmount = $items['amount'];
							$fixed = explode(',', $product->fixed);
							foreach($fixed as $f)
							{
								$value = explode('|', $f);
								// Con el valor de la recarga determino cuál fue el valor mostrado.
								if($items['amount'] == $value[0])
								{
									// Voy a almacenar el valor que el cliente vio en pantalla.
									$clientAmount = $value[1];
								}
							}
						}
					}
					else
					{
						// El valor a recargar es el mismo que se muestra.
						$clientAmount = $items['amount'];
					}
					// Obtengo el porcentaje de ganancia.
					$profitPercent = $this->client->getProfitPercent($_SESSION['userId'], $items['productId']);
					switch($product->providerId)
					{
						case 'CERETEL':
							$ceretelCtrl = new CeretelProviderController($this);
							$data = $ceretelCtrl->addFunds(
								$items['clientPhone'],
								$items['phone'],
								$product->offeringId,
								$items['productId'],
								$items['amount'],
								$clientAmount,
								$profitPercent,
								$product->serviceCharge,
								$product->includeCharge
							);
							if(isset($data['status']))
							{
								$name = isset($items['name']) ? $items['name'] : NULL;
								// La transacción fue exitosa o está pendiente de aprobación, de cualquier forma inserto el teléfono.
								$this->phonebook->addPhone($items['clientPhone'], $items['productId'], $items['phone'], $name);
							}
							$data['provider'] = 'CERETEL';
							break;
						case 'DOLLARPHO':
							if($product->isPIN)
							{
								$dollarPhoneCtrl = new DollarPhoneProviderController($this);
								$data = $dollarPhoneCtrl->topUpRequest(
									$items['clientPhone'],
									$items['phone'],
									$product->offeringId,
									$items['productId'],
									$items['amount'],
									$clientAmount,
									$profitPercent,
									$product->serviceCharge,
									$product->includeCharge
								);
							}
							else
							{
								$data = $this->dollarPhone(
									$items['clientPhone'],
									$items['phone'],
									$product->offeringId,
									$items['productId'],
									$items['amount'],
									$clientAmount,
									$profitPercent,
									$product->serviceCharge,
									$product->includeCharge
								);
							}
							if(isset($data['status']))
							{
								$name = isset($items['name']) ? $items['name'] : NULL;
								// La transacción fue exitosa o está pendiente de aprobación, de cualquier forma inserto el teléfono.
								$this->phonebook->addPhone($items['clientPhone'], $items['productId'], $items['phone'], $name);
							}
							$data['provider'] = 'DOLLARPHO';
							break;
						case 'LOGICAL':
							$logicalCtrl = new LogicalProviderController($this);
							$data = $logicalCtrl->addFunds(
								$items['clientPhone'],
								$items['phone'],
								$product->offeringId,
								$items['productId'],
								$items['amount'],
								$clientAmount,
								$profitPercent,
								$product->serviceCharge,
								$product->includeCharge
							);
							if(isset($data['status']))
							{
								$name = isset($items['name']) ? $items['name'] : NULL;
								// La transacción fue exitosa o está pendiente de aprobación, de cualquier forma inserto el teléfono.
								$this->phonebook->addPhone($items['clientPhone'], $items['productId'], $items['phone'], $name);
							}
							$data['provider'] = 'LOGICAL';
							break;
						case 'PREPAYNAT':
							$data = $this->prepayNation(
								$items['clientPhone'],
								$items['phone'],
								$product->offeringId,
								$items['productId'],
								$items['amount'],
								$clientAmount,
								$profitPercent,
								$product->serviceCharge,
								$product->includeCharge
							);
							if(isset($data['status']))
							{
								if($data['status'] == 'Success' || $data['status'] == 'Pending')
								{
									$name = isset($items['name']) ? $items['name'] : NULL;
									// La transacción fue exitosa o está pendiente de aprobación, de cualquier forma inserto el teléfono.
									$this->phonebook->addPhone($items['clientPhone'], $items['productId'], $items['phone'], $name);
								}
							}
							break;
						case 'DPPINLESS':
							$data['provider'] = 'DPPINLESS';
							// Calculo la ganancia del cliente.
							$profit = (float)$product->defaultProfit * $clientAmount / 100;
							$profit = number_format($profit, 2);
							// Calculo lo que le queda a la empresa.
							$realTopup = ($clientAmount - $product->includeCharge);
							$due = ($realTopup + $product->serviceCharge + $product->includeCharge) - $profit;
							// Calculo la ganancia del vendedor y la empresa.
							$userProfit = (float)$product->defaultUserProfit * $realTopup / 100;
							$companyProfit = (float)$product->companyProfit * $realTopup / 100;
							// Registro la transacción
							$transaction = array(
								'clientId' => $_SESSION['userId'],
								'productId' => $items['productId'],
								'clientPhone' => $items['clientPhone'],
								'phone' => $items['phone'],
								'amount' => $items['amount'],
								'serviceCharge' => $product->serviceCharge,
								'includeCharge' => $product->includeCharge,
								'profit' => $profit,
								'userProfit' => $userProfit,
								'companyProfit' => $companyProfit,
								'transId' => '',
								'status' => 'Pending'
							);
							$data['id'] = $this->transaction->create($transaction);
							$account = $this->activateOrRechargeAccount($product, $items, $data['id']);
							if($account->TransId > 0)
							{
								$transInfo = $this->getWebTransactionInfo($account->TransId, $due);
								if(isset($transInfo['status']))
								{
									if($transInfo['status'] == 'Success' || $transInfo['status'] == 'Pending')
									{
										$name = isset($items['name']) ? $items['name'] : NULL;
										// La transacción fue exitosa o está pendiente de aprobación, de cualquier forma inserto el teléfono.
										$this->phonebook->addPhone($items['clientPhone'], $items['productId'], $items['phone'], $name);
										$data['transId'] = $account->TransId;
										$data['status'] = $transInfo['status'];
										$data['amount'] = $items['amount'];
										$data['phone'] = $items['phone'];
										$data['due'] = $due;
										$this->transaction->update($data['id'], array('status' => $transInfo['status'],
											'transId' => $account->TransId));
									}
									else
									{
										$data['status'] = $transInfo['status'];
										$data['error'] = 'The transaction was failed';
									}
								}
							}
							else
							{
								$data['error'] = $account->responseMessage;
								$this->transaction->update($data['id'], array('status' => 'Failed'));
							}
							break;
						default:
							$data['error'] = lang('error_provider_no_configurated').$product->name.'.';
					}
				}
				else
				{
					$data['error'] = lang('error_amount_exceeded');
				}
			}
			else
			{
				if($existingTr->status == 'Pending')
				{
					// Si la transacción aún está pendiente, intento verificar si fue exitosa.
					$response = $this->dollarPhoneConfirm($existingTr->id, $existingTr->transId, TRUE);
					// La transacción pasó de pendiente a aprobada o rechazada.
					if($response['status'] != 'Pending')
					{
						$existingTr->status = $response['status'];
					}
				}
				$data['error'] = lang('error_topup_already_exist1').'<strong>'.$items['clientPhone'].'</strong>'.lang('error_topup_already_exist2').'<strong>'.$items['phone'].'</strong>'.lang('error_topup_already_exist3').'<strong>'.$existingTr->status.'</strong>.';
				if($existingTr->status == 'Pending')
				{
					$data['error'] .= lang('error_go_to_recient_transaction1').'"<a href="'.base_url('clientCtrl/transactionsList').'">'.lang('recent_transactions').'</a>"'.lang('error_go_to_recient_transaction2');
				}
			}
		}
		$data['max'] = $max;
		$data['title'] = 'Home';
		$data['clientPhone'] = $items['clientPhone'];
		// Traigo el listado de países activos.
		$countries = $this->country->getAll('a');
		$ids = $preferred = $defaultCountry = '';
		foreach($countries as $country)
		{
			$ids .= '"'.$country->id.'", ';
			if($country->preferred == 'Yes')
			{
				$preferred .= '"'.$country->id.'", ';
				if($defaultCountry == '')
				{
					$defaultCountry = $country->id;
				}
			}
		}
		// Traigo los productos activos del país por defecto.
		$data['products'] = $this->product->getAll($defaultCountry, TRUE);
		foreach($data['products'] as &$product)
		{
			$product->values = $product->type == 'f' ? $product->fixed : $product->rangeMin.','.$product->rangeMax;
		}
		$data['ids'] = substr($ids, 0, -2);  // Quito la última coma.
		$data['preferred'] = substr($preferred, 0, -2);
		$data['slides'] = $this->slide->getAll();
		$data['news'] = $this->news->getAll();
		$data['cart'] = $this->_getLatestTopups();
		$this->load->view('header', $data);
		$this->load->view('home', $data);
		$this->load->view('footer');
	}

	/**
	 * arrayPushAfter
	 * Método que permite insertar nuevos parámetros a un array asociativo.
	 * Es usado especialmente para la creación del array de transacción que se va a insertar en BD.
	 */
	private function arrayPushAfter($src, $in, $pos)
	{
		if(is_int($pos))
		{
			$r = array_merge(array_slice($src, 0, $pos + 1), $in, array_slice($src, $pos + 1));
		}
		else
		{
			foreach($src as $k => $v)
			{
				$r[$k] = $v;
				if($k == $pos)
				{
					$r = array_merge($r, $in);
				}
			}
		}
		return $r;
	}

	public function dollarPhone($clientPhone, $phone, $offeringId, $productId, $realAmount, $clientAmount, $profitPercent, $serviceCharge, $includeCharge)
	{
		$data = array();
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DOLLARPHO');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
		}
		$params = array('TopUpReq' =>
					array('PhoneNumber' => $phone,
						'OfferingId' => $offeringId,
						'Amount' => $realAmount,
						'ProviderId' => 0,
						'Action' => 'AddFunds'
					)
				);
		$response = $client->TopUpRequest($params);
		if(is_object($response))
		{
			if($response->TopUpRequestResult->responseCode > 0 && $response->TopUpRequestResult->TransId > 0)
			{
				// El TransId es positivo, quiere decir que el servidor aceptó la petición, ahora determinamos el estado.
				$params = array('TransID' => $response->TopUpRequestResult->TransId);
				$status = $client->TopupConfirm($params);
				if($status->TopupConfirmResult->ErrorCode == 0)
				{
					switch($status->TopupConfirmResult->Status)
					{
						case 'Pending':
						case 'Success':
							// Calculo la ganancia del cliente.
							$profit = (float)$profitPercent['profit'] * $clientAmount / 100;
							$profit = number_format($profit, 2);
							// Calculo lo que le queda a la empresa.
							$realTopup = ($clientAmount - $includeCharge);
							$due = ($realTopup + $serviceCharge + $includeCharge) - $profit;
							// Calculo la ganancia del vendedor y la empresa.
							$userProfit = (float)$profitPercent['userProfit'] * $realTopup / 100;
							$companyProfit = (float)$profitPercent['companyProfit'] * $realTopup / 100;
							// Registro la transacción.
							$transaction = array(
								'clientId' => $_SESSION['userId'],
								'productId' => $productId,
								'clientPhone' => $clientPhone,
								'phone' => $phone,
								'amount' => $clientAmount,
								'serviceCharge' => $serviceCharge,
								'includeCharge' => $includeCharge,
								'profit' => $profit,
								'userProfit' => $userProfit,
								'companyProfit' => $companyProfit,
								'transId' => $response->TopUpRequestResult->TransId,
								'status' => $status->TopupConfirmResult->Status
							);
							$data['id'] = $this->transaction->create($transaction);
							$data['status'] = $status->TopupConfirmResult->Status;
							if($status->TopupConfirmResult->Status == 'Success')
							{
								// Actualizo el balance del cliente.
								$this->client->updateBalance($_SESSION['userId'], $due);
								// Envío los mensajes de texto.
								$this->load->library('twilio');
								$this->twilio->sendSMS($clientPhone);  // Pagador.
								$this->twilio->sendSMS($phone);  // Receptor.

								$data['phone'] = $phone;
								$data['amount'] = number_format($clientAmount, 2);
							}
							else
							{
								$data['transId'] = $response->TopUpRequestResult->TransId;
								$data['error'] = 'Transaction pending. Check again soon to see if transaction succeeded.';
							}
							break;
						case 'Failed':
							$data['error'] = $status->TopupConfirmResult->ErrorMsg;
							break;
					}
				}
				else
				{
					$data['error'] = $status->TopupConfirmResult->ErrorMsg;
				}
			}
			else
			{
				if(isset($response->TopUpRequestResult->responseMessage))
				{
					$msg = $response->TopUpRequestResult->responseMessage;
					$data['error'] = empty($msg) ? 'Error communicating with the service.' : $msg;
				}
				else
				{
					$data['error'] = 'Invalid product.';
				}
			}
		}
		else
		{
			$data['error'] = 'Error communicating with the service.';
		}
		return $data;
	}

	public function ceretelConfirm($id, $transId)
	{
		$ceretelCtrl = new CeretelProviderController($this);
		$response = $ceretelCtrl->confirm($id, $transId);
		echo json_encode($response);
	}

	public function deletePhonebook($clientPhone, $phone, $productId)
	{
		$this->load->model('phonebook');
		if($this->phonebook->delete($clientPhone, $phone, $productId))
		{
			echo json_encode(array('status' => 'ok'));
		}
		else
		{
			echo json_encode(array('status' => 'error'));
		}
	}

	public function dollarPhoneConfirm($id, $transId, $return = FALSE)
	{
		$data = array();

		$this->load->model('transaction');
		$this->load->model('product');

		$transaction = $this->transaction->getById($id);
		$product = $this->product->getById($transaction->productId);

		if($product->isPIN)
		{
			$dollarPhoneCtrl = new DollarPhoneProviderController($this);
			$data = $dollarPhoneCtrl->confirm($id, $transId);
		}
		else
		{
			$this->load->model('provider');
			$this->load->library('encryption');
			$provider = $this->provider->getById('DOLLARPHO');
			// Desencripto la contraseña.
			$provider->password = $this->encryption->decrypt($provider->password);
			try
			{
				$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));
			}
			catch(Exception $e)
			{
			}
			// Ahora determinamos el estado.
			$params = array('TransID' => $transId);
			$status = $client->TopupConfirm($params);
			if(is_object($status))
			{
				if($status->TopupConfirmResult->ErrorCode == 0)
				{
					$data['status'] = $status->TopupConfirmResult->Status;
					switch($status->TopupConfirmResult->Status)
					{
						case 'Pending':
							$data['error'] = 'Transaction pending. Check again soon to see if transaction succeeded.';
							break;
						case 'Success':
							$this->load->model('client');

							// Valido que la transacción no se haya aprobado previamente.
							if($transaction->status != 'Success')
							{
								// Calculo lo que le queda a la empresa.
								$realTopup = ($transaction->amount - $transaction->includeCharge);
								$due = ($realTopup + $transaction->serviceCharge + $transaction->includeCharge) - $transaction->profit;
								// Actualizo el balance del cliente.
								$this->client->updateBalance($_SESSION['userId'], $due);
								// Cambio el estado a la transacción.
								$this->transaction->update($id, array('status' => 'Success'));
								// Envío los mensajes de texto.
								$this->load->library('twilio');
								$this->twilio->sendSMS($transaction->clientPhone);  // Pagador.
								$this->twilio->sendSMS($transaction->phone);  // Receptor.

								$data['message'] = '$'.number_format($realTopup, 2).' added successfully to the phone number '.$transaction->phone.'.';
								$data['amount'] = $transaction->amount + $transaction->serviceCharge;
								$data['phone'] = $transaction->phone;
							}
							else
							{
								$data['status'] = 'AlreadyApproved';
								$data['message'] = 'This transaction has already been approved.';
							}
							break;
						case 'Failed':
							$this->transaction->update($id, array('status' => 'Failed'));
							$data['error'] = $status->TopupConfirmResult->ErrorMsg;
							break;
					}
				}
				else
				{
					if($status->TopupConfirmResult->Status == 'Failed')
					{
						$data['status'] = 'Failed';
						$this->transaction->update($id, array('status' => 'Failed'));
					}
					else
					{
						$data['status'] = 'Error';
					}
					$data['error'] = $status->TopupConfirmResult->ErrorMsg;
				}
			}
			else
			{
				$data['status'] = 'Error';
				$data['error'] = 'Error communicating with the service.';
			}
		}
		if($return)
		{
			return $data;
		}
		else
		{
			echo json_encode($data);
		}
	}

	public function getRecipients($clientPhone)
	{
		$p = array();
		$this->load->model('phonebook');
		$this->load->model('product');
		$phones = $this->phonebook->getAll($clientPhone);
		foreach($phones as $phone)
		{
			if($phone->image != '' && is_file(UPLOADS_DIR.$phone->image))
			{
				$image = '<img src="'.base_url(UPLOADS.$phone->image).'" width="70">';
			}
			else
			{
				$image = '';
			}
			$name = $phone->productName.' ('.strtoupper($phone->countryId).')';
			$values = $phone->type == 'f' ? $phone->fixed : $phone->rangeMin.','.$phone->rangeMax;
			$phone_data = array(
				'name' => $phone->name,
				'phone' => $phone->phone,
				'productId' => $phone->productId,
				'productName' => $name,
				'image' => $image,
				'type' => $phone->type,
				'values' => $values
			);

			$product = $this->product->getById($phone->productId);

			$phone_data['serviceCharge'] = $product ? (float) $product->serviceCharge : 0;
			$phone_data['includeCharge'] = $product ? (float) $product->includeCharge : 0;

			$p[] = $phone_data;
		}
		echo json_encode($p);
	}

	public function getProductByPhone($phone)
	{
		$this->load->library('twilio');
		$response = $this->twilio->getProductByPhone($phone);
		echo json_encode($response);
	}

	public function getProducts($countryId)
	{
		$p = array();
		$this->load->model('product');
		$products = $this->product->getAll($countryId, TRUE);
		foreach($products as $product)
		{
			$values = $product->type == 'f' ? $product->fixed : $product->rangeMin.','.$product->rangeMax;

			$charge = $product->serviceCharge + (($product->includeCharge > 0) ? $product->includeCharge : 0);

			$p[] = array(
				'id' => $product->id,
				'isPIN' => $product->isPIN,
				'isUnlimited' => $product->isUnlimited,
				'allowOpenAmount' => $product->allowOpenAmount,
				'showAsList' => $product->showAsList,
				'mnc' => $product->mnc,
				'name' => $product->name,
				'image' => $product->image,
				'type' => $product->type,
				'values' => $values,
				'charge' => $charge
			);
		}
		echo json_encode($p);
	}

	public function logicalConfirm($id, $transId)
	{
		$logicalCtrl = new LogicalProviderController($this);
		$response = $logicalCtrl->confirm($id, $transId);
		echo json_encode($response);
	}

	public function logout()
	{
		unset($_SESSION['userId']);
		unset($_SESSION['userName']);
		unset($_SESSION['userType']);
		redirect(base_url('login'));
	}

	public function prepayNation($clientPhone, $phone, $offeringId, $productId, $realAmount, $clientAmount, $profitPercent, $serviceCharge, $includeCharge)
	{
		ini_set('max_execution_time', 150);
		$data = array();
		$this->load->model('client');
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('PREPAYNAT');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		$data['provider'] = 'PREPAYNAT';
		try
		{
			$client = new SoapClient($provider->url, array());
			$headerbody = array('userId' => $provider->username, 'password' => $provider->password);
			$headers = new SoapHeader('http://www.pininteract.com', 'AuthenticationHeader', $headerbody, FALSE);
			$client->__setSoapHeaders(array($headers));
			$response = $client->PurchaseRtr2(array('version' => '1.0',
													'skuId' => $offeringId,
													'amount' => $realAmount,
													'mobile' => $phone,
													'corelationId' => $clientPhone.$phone,
													'senderMobile' => '',
													'storeId' => ''
													)
												);
		}
		catch(Exception $fault)
		{
			$response = new stdClass();
			$response->orderResponse = new stdClass();
			$response->orderResponse->responseCode = 'Pending';
		}

		if(is_object($response))
		{
			// Calculo la ganancia del cliente.
			$profit = (float)$profitPercent['profit'] * $clientAmount / 100;
			$profit = number_format($profit, 2);
			// Calculo lo que le queda a la empresa.
			$realTopup = ($clientAmount - $includeCharge);
			$due = ($realTopup + $serviceCharge + $includeCharge) - $profit;
			// Calculo la ganancia del vendedor y la empresa.
			$userProfit = (float)$profitPercent['userProfit'] * $realTopup / 100;
			$companyProfit = (float)$profitPercent['companyProfit'] * $realTopup / 100;
			$transaction = array(
					'clientId' => $_SESSION['userId'],
					'productId' => $productId,
					'clientPhone' => $clientPhone,
					'phone' => $phone,
					'amount' => $clientAmount,
					'serviceCharge' => $serviceCharge,
					'includeCharge' => $includeCharge,
					'profit' => $profit,
					'userProfit' => $userProfit,
					'companyProfit' => $companyProfit
				);
			$data['phone'] = $phone;
			$data['amount'] = number_format($clientAmount, 2);

			if($response->orderResponse->responseCode == '000')
			{
				// Registro la transacción.
				$trans = array('transId' => $response->orderResponse->invoice->invoiceNumber, 'status' => 'Success');
				$data['status'] = 'Success';
				// Actualizo el balance del cliente.
				$this->client->updateBalance($_SESSION['userId'], $due);
				// Envío los mensajes de texto.
				$this->load->library('twilio');
				$this->twilio->sendSMS($clientPhone);  // Pagador.
				$this->twilio->sendSMS($phone);  // Receptor.
			}
			elseif($response->orderResponse->responseCode == 'Pending')
			{
				$trans = array('transId' => '',	'status' => 'Pending');
				$data['status'] = 'Pending';
				$data['transId'] = $clientPhone.$phone;
			}
			elseif($response->orderResponse->responseCode != '000')
			{
				$trans = array('transId' => 0, 'status' => 'Failed');
				$data['status'] = 'Failed';
				if(isset($response->orderResponse->responseMessage))
				{
					$msg = $response->orderResponse->responseMessage;
					$data['error'] = empty($msg) ? lang('error_communication_service') : $msg;
				}
				else
				{
					$data['error'] = lang('error_invalid_product');
				}
			}
			$transaction = $this->arrayPushAfter($transaction, $trans, 'profit');
			$data['id'] = $this->transaction->create($transaction);
		}
		return $data;
	}

	public function prepayNationConfirm($id)
	{
		$this->load->library('encryption');
		$this->load->model('transaction');
		$this->load->model('client');
		$this->load->model('provider');
		$transaction = $this->transaction->getById($id);
		$provider = $this->provider->getById('PREPAYNAT');
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$soap = new SoapClient($provider->url, array());
			$headerbody = array('userId' => $provider->username, 'password' => $provider->password);
			$header = new SOAPHeader('http://www.pininteract.com', 'AuthenticationHeader', $headerbody);
			$soap->__setSoapHeaders($header);
			// correlationId: se define en el llamado a prepayNation, es único y es la concatenación entre los números del cliente y receptor.
			$parameter = array('correlationId' => $transaction->clientPhone.$transaction->phone);
			$result = $soap->GetInvoiceByCorrelationId($parameter);
		}
		catch(SoapFault $fault)
		{
			$result = new stdClass();
			$result->orderResponse = new stdClass();
			$result->orderResponse->responseCode = 'Pending';
			$result->orderResponse->responseMessage = $fault->faultstring;
		}
		$realTopup = ($transaction->amount - $transaction->includeCharge);
		$due = ($realTopup + $transaction->serviceCharge) - $transaction->profit;
		if(is_object($result))
		{
			if($result->orderResponse->responseCode == '000')
			{
				if($transaction->status != 'Success')
				{
					$this->transaction->update($id, array('transId' => $result->orderResponse->invoice->invoiceNumber,'status' => 'Success'));
					$this->client->updateBalance($_SESSION['userId'], $due);
					$data['status'] = 'Success';
					$data['message'] = '$'.number_format($realTopup, 2).' added successfully to the phone number '.$transaction->phone.'.';
					$data['amount'] = $transaction->amount + $transaction->serviceCharge;
					$data['phone'] = $transaction->phone;
				}
				else
				{
					$data['status'] = 'AlreadyApproved';
					$data['message'] = lang('msg_already_approved');
				}
			}
			if($result->orderResponse->responseCode == 'Pending' || $result->orderResponse->responseCode == '851' || $result->orderResponse->responseCode == '852')
			{
				$data['status'] = 'Pending';
			}
			elseif($result->orderResponse->responseCode != '000')
			{
				$data['status'] = 'Failed';
				$msg = $result->orderResponse->responseMessage;
				$data['error'] = empty($msg) ? lang('error_communication_service') : $msg;
				$this->transaction->update($id, array('transId' => 0, 'status' => 'Failed'));
			}
		}
		echo json_encode($data);
	}

	public function activateOrRechargeAccount($product, $item, $orderId)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById($product->providerId);
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
		}
		$params = array('reqActAccount' =>
			array('OrderId' => $orderId,
				'Account' => array(
					'OfferingId' => $product->offeringId,
					'Balance' => $item['amount'],
					'Ani' => isset($item['phoneInput']) ? $item['phoneInput'] : substr($item['phone'], 2),
					'Language' => 'English',
					'responseCode' => '',
					'TransId' => '',
					'Status' => 'active',
					'LotId' => ''
				),
				'Language' => 'English',
				'SignupType' => 'None',
				'PromoTransaction' => FALSE
			)
		);
		$response = $client->ActivateOrRechargeAccount($params);
		return $response->ActivateOrRechargeAccountResult;
	}

	public function getWebTransactionInfo($transId, $id = NULL, $due = NULL, $ajx = NULL)
	{
		if($transId == 0)
		{
			$array['NewDataSet']['webTransInfo']['status'] = $status = 'Failed';
			$this->transaction->update($id, array('status' => $status));
			if($ajx != NULL)
			{
				$data['status'] = $status;
				$data['error'] = 'Sorry, the transaction failed.';
				echo json_encode($data);
			}
			else
			{
				return $array['NewDataSet']['webTransInfo'];
			}
		}
		else
		{
			$this->load->model('provider');
			$this->load->model('transaction');
			$this->load->model('client');
			$this->load->library('encryption');
			$provider = $this->provider->getById('DPPINLESS');
			// Desencripto la contraseña.
			$provider->password = $this->encryption->decrypt($provider->password);
			try
			{
				$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));
				$params = array('WebTransId' => $transId);
				$response = $client->GetWebTransactionInfo($params)->GetWebTransactionInfoResult;
				$xml = simplexml_load_string($response->any, 'SimpleXMLElement', LIBXML_NOCDATA);
				$array = json_decode(json_encode($xml), TRUE);

				switch($array['NewDataSet']['webTransInfo']['status'])
				{
					case 'S':
						$array['NewDataSet']['webTransInfo']['status'] = $status = 'Success';
						$msg = 'The transaction was successful.';
						if($id != NULL)
						{
							$this->transaction->update($id, array('status' => 'Success'));
							$transaction = $this->transaction->getById($id);
							$realTopup = $transaction->amount - $transaction->includeCharge;
							$msg = '$' . number_format($realTopup, 2) . ' added successfully to the phone number ' . $transaction->phone . '.';
							$data['amount'] = $transaction->amount + $transaction->serviceCharge;
							$data['phone'] = $transaction->phone;
						}
						$this->client->updateBalance($_SESSION['userId'], (float) $due);
						break;
					case 'F':
						$array['NewDataSet']['webTransInfo']['status'] = $status = 'Failed';
						$msg = 'Sorry, the transaction failed.';
						break;
					case 'P':
						$array['NewDataSet']['webTransInfo']['status'] = $status = 'Pending';
				}

				if($ajx != NULL)
				{
					$data['status'] = $status;
					$data['message'] = $status != 'Pending' ? $msg : '';
					echo json_encode($data);
				}
				else
				{
					return $array['NewDataSet']['webTransInfo'];
				}
			}
			catch(Exception $e)
			{
				echo 'No address associated with hostname.';
				echo $e->getMessage();
			}
		}
	}

	private function _getLatestTopups()
	{
		$r = array();
		// Listado de transacciones recientes.
		$r['tr'] = array();
		$this->load->model('transaction');
		$topups = $this->transaction->getLatestTopups();
		if(count($topups) > 0)
		{
			// Número del cliente pagador, lo asigno en la primera iteración.
			$r['clientPhone'] = '';
			// Cantidad de transacciones hechas.
			$r['qty'] = 0;
			// Sumatoria de las transacciones realizadas.
			$r['amount'] = 0;
			foreach($topups as $t)
			{
				if($r['clientPhone'] == '')
				{
					$r['clientPhone'] = $t->clientPhone;
				}
				elseif($r['clientPhone'] != $t->clientPhone)
				{
					// El número del cliente es diferente, no sigo avanzando.
					break;
				}
				$r['qty']++;
				$amount = $t->status == 'Success' ? $t->amount : 0;
				$r['amount'] += $amount;
				$r['tr'][] = array($t->phone.' ('.$t->status.')', $amount);
			}
		}
		return $r;
	}
}
