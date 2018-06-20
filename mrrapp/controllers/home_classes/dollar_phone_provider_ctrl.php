<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class DollarPhoneProviderController
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

		$provider = $ctrl->provider->getById('DOLLARPHO');
		$provider->password = $ctrl->encryption->decrypt($provider->password);

		try
		{
			$soap_client = new SoapClient($provider->url, array(
					'login' => $provider->username,
					'password' => $provider->password,
					'trace' => TRUE
				)
			);
			return $soap_client->$method($params);
		}
		catch (SoapFault $fault)
		{
			return FALSE;
		}
	}

	public function topUpRequest($client_phone, $phone, $offering_id, $product_id, $amount, $clientAmount, $profitPercent, $serviceCharge, $includeCharge)
	{
		$ctrl = $this->ctrl;
		$data = array();

		$request_params = array(
			'Action' => 'PurchasePin',
			'Amount' => $amount,
			'OfferingId' => $offering_id,
			'ProviderId' => 0
		);

		$response = $this->callSOAP('TopUpRequest', array(
			'TopUpReq' => $request_params
		));

		if (!is_object($response))
		{
			$data['error'] = 'Error communicating with the service.';
		}
		else
		{
			$response_code = $response->TopUpRequestResult->responseCode;
			$tx_id = $response->TopUpRequestResult->TransId;

			if ($response_code > 0 && $tx_id > 0)
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
				$data['status'] = 'Pending';
				$data['transId'] = $tx_id;
			}
			else
			{
				if(isset($response->TopUpRequestResult->responseMessage))
				{
					$data['error'] = $response->TopUpRequestResult->responseMessage;
				}
				else
				{
					$data['error'] = 'Invalid product.';
				}
			}
		}
		return $data;
	}

	public function confirm($id, $transId)
	{
		$ctrl = $this->ctrl;
		$data = array();

		$response = $this->callSOAP('TopupConfirm', array(
			'TransID' => $transId
		));

		if (!is_object($response))
		{
			$data['status'] = 'Error';
			$data['error'] = 'Error communicating with the service.';
		}
		else
		{
			if ($response->TopupConfirmResult->ErrorCode == 0)
			{
				$data['status'] = $response->TopupConfirmResult->Status;

				switch($response->TopupConfirmResult->Status)
				{
					case 'Pending':
						$data['error'] = 'Transaction pending. Check again soon to see if transaction succeeded.';
						break;

					case 'Success':

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
								'pin' => $response->TopupConfirmResult->PIN,
								'status' => 'Success'
							));

							$data['status'] = 'Success';

							// Envío los mensajes de texto.

							$pin = $response->TopupConfirmResult->PIN;

							$amount_text = ('$' . number_format($transaction->amount, 2));
							$product_name = $ctrl->transaction->getProductName($transaction->productId);

							$msm_text = <<<EOF
Ticket Sale. Electronic Recharge {$product_name} {$amount_text} USD
PIN: {$pin}
Su recarga ha sido completada, gracias/Your Top-up has been completed, thank you.
Por/By
Portal myringring.net
EOF;

							$ctrl->twilio->sendSMS($transaction->clientPhone, $msm_text);  // Pagador.
							$ctrl->twilio->sendSMS($transaction->phone, $msm_text);  // Receptor.

							$amount_calculated = $transaction->amount - (($transaction->includeCharge > 0) ? $transaction->includeCharge : 0);

							$data['message'] = ('$'
								. number_format($amount_calculated, 2)
								. ' added successfully to the phone number '
								. $transaction->phone
								. ', the PIN number is '
								. $pin
								. '.');
						}

						break;

					default:

						$ctrl->transaction->update($id, array(
							'status' => 'Failed'
						));

						$data['error'] = $status . ' - Request unlimited response.';
						$data['status'] = 'Failed';

						break;
				}
			}
			else
			{
				if ($response->TopupConfirmResult->Status == 'Failed')
				{
					$data['status'] = 'Failed';

					$transaction = $ctrl->transaction->getById($id);
					$ctrl->transaction->update($id, array('status' => 'Failed'));
				}
				else
				{
					$data['status'] = 'Error';
				}

				$data['error'] = $response->TopupConfirmResult->ErrorMsg;
			}
		}

		return $data;
	}
}
?>