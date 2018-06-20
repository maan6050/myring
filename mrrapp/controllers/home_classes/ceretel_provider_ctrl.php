<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class CeretelProviderController
{
	private $ctrl = NULL;

	public function __construct($ctrl)
	{
		$this->ctrl = $ctrl;

		$this->ctrl->load->library('encryption');
		$this->ctrl->load->library('twilio');

		$this->ctrl->load->model('client');
		$this->ctrl->load->model('provider');
		$this->ctrl->load->model('transaction');
	}

	private function callSOAP($method, $params)
	{
		$ctrl = $this->ctrl;

		$provider = $ctrl->provider->getById('CERETEL');
		$provider->password = $ctrl->encryption->decrypt($provider->password);

		try
		{
			$soap_client = new SoapClient($provider->url, array(
					'login' => $provider->username,
					'password' => $provider->password,
					'trace' => TRUE
				)
			);

			$soap_client->$method($params);
			$response = @xmlrpc_decode($soap_client->__getLastResponse());
			return (NULL !== $response) ? $response : FALSE;
		}
		catch (\SoapFault $fault)
		{
			return FALSE;
		}
	}

	public function addFunds($client_phone, $phone, $offering_id, $product_id, $amount, $clientAmount, $profitPercent, $serviceCharge, $includeCharge)
	{
		$ctrl = $this->ctrl;
		$data = array();

		$response = $this->callSOAP('requestUnlimited', array(
			'request' => array(
				'txRef' => intval(microtime(true)),
				'phoneNumber' => substr($phone, 2),
				'offeringId' => $offering_id
			)
		));

		if($response === FALSE)
		{
			$data['error'] = 'Error communicating with the service.';
		}
		else
		{
			if($tx_id = getArrayValue($response, 'txId'))
			{
				if($status = getArrayValue($response, 'status'))
				{
					if('COMPLETED' === $status || 'PROCESSING' === $status)
					{
						$user_id = $_SESSION['userId'];
						// Calculo la ganancia del cliente.
						$profit = (float)$profitPercent['profit'] * $amount / 100;
						$profit = number_format($profit, 2);
						// Calculo la ganancia del vendedor y la empresa.
						$userProfit = (float)$profitPercent['userProfit'] * $amount / 100;
						$companyProfit = (float)$profitPercent['companyProfit'] * $amount / 100;
						// Registro la transacción.
						$data['id'] = $ctrl->transaction->create(array(
							'clientId' => $user_id,
							'productId' => $product_id,
							'clientPhone' => $client_phone,
							'phone' => $phone,
							'amount' => $amount,
							'serviceCharge' => $serviceCharge,
							'includeCharge' => $includeCharge,
							'profit' => $profit,
							'userProfit' => $userProfit,
							'companyProfit' => $companyProfit,
							'transId' => $tx_id,
							'status' => 'Pending'
						));

						$data['error'] = 'Transaction pending. Check again soon to see if transaction succeeded.';
						$data['status'] = 'Pending';
						$data['transId'] = $tx_id;
					}
					else
					{
						$data['error'] = $status.' - Request unlimited response.';
					}
				}
				else
				{
					$data['error'] = 'Invalid response.';
				}
			}
			else
			{
				$data['error'] = 'Invalid product.';
			}
		}
		return $data;
	}

	public function confirm($id, $transId)
	{
		$ctrl = $this->ctrl;
		$data = array();

		$response = $this->callSOAP('statusUnlimitedById', array(
			'request' => $transId
		));

		if (FALSE === $response)
		{
			$data['error'] = 'Error communicating with the service.';
		}
		else
		{
			if ($status = getArrayValue($response, 'status'))
			{
				switch($status)
				{
					case 'PROCESSING':
						$data['error'] = 'Transaction pending. Check again soon to see if transaction succeeded.';
						break;

					case 'COMPLETED':

						$transaction = $ctrl->transaction->getById($id);

						// Valido que la transacción no se haya aprobado previamente.
						if('Success' === $transaction->status)
						{
							$data['status'] = 'AlreadyApproved';
							$data['message'] = 'This transaction has already been approved.';
						}
						else
						{
							// Calculo lo que le queda a la empresa.
							$recargaReal = ($transaction->amount - $transaction->includeCharge);
							$due = ($recargaReal + ($transaction->serviceCharge + $transaction->includeCharge)) - $transaction->profit;
							// Actualizo el balance del cliente.
							$ctrl->client->updateBalance($_SESSION['userId'], $due);
							// Cambio el estado a la transacción.
							$ctrl->transaction->update($id, array(
								'status' => 'Success'
							));

							$data['status'] = 'Success';

							// Envío los mensajes de texto.
							$ctrl->twilio->sendSMS($transaction->clientPhone);  // Pagador.
							$ctrl->twilio->sendSMS($transaction->phone);  // Receptor.

							$amount_calculated = $transaction->amount - (($transaction->includeCharge > 0) ? $transaction->includeCharge : 0);

							$data['message'] = ('$'
								. number_format($amount_calculated, 2)
								. ' added successfully to the phone number '
								. $transaction->phone
								. ', the access number is '
								. getArrayValue($response, 'accessNumber')
								. ' and expiration date is '
								. getArrayValue($response, 'expirationDate')
								. '.');
							$data['amount'] = $transaction->amount + $transaction->serviceCharge;
							$data['phone'] = $transaction->phone;
						}

						break;

					default:

						$ctrl->transaction->update($id, array(
							'status' => 'Failed'
						));

						$data['error'] = $status . ' - Request unlimited response.';

						break;
				}
			}
			else
			{
				$data['error'] = 'Error communicating with the service.';
			}
		}

		return $data;
	}
}
?>