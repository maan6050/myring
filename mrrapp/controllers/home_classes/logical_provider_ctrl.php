<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class LogicalProviderController
{
	private $ctrl = NULL;
	private $error_codes = array(
		'1000' => 'Failed transaction, please try again.',
		'1001' => 'Phone number is not rechargeable or does not belong to carrier.',
		'1004' => 'Timeout.',
		'1005' => 'Failed transaction',
		'1006' => 'Carrier is temporarily offline.',
		'1007' => 'Carrier is performing some maintenance, service is intermittent.',
		'3001' => 'Bad credentials',
		'3002' => 'You are not authorized to use the API',
		'3003' => 'Client is temporarily suspended.',
		'3004' => 'The request ID does not exist.',
		'3005' => 'The request ID is missing.',
		'3006' => 'Invalid parameters.',
		'3007' => 'Product not found.',
		'3008' => 'Bad phone number.',
		'3009' => 'A topup was recently performed on this number, please wait 10 minutes and try again.',
		'3010' => 'Failed request in API, please try again',
		'3011' => 'Insufficient funds',
		'3012' => 'Request was previously canceled.',
		'3013' => 'Request is in process or finished and cannot be canceled.',
		'3014' => 'The amount is missing or is invalid. Should be an integer between 1 and 100.',
		'3021' => 'Timeout.',
		'3022' => 'Request is waiting for execute instruction.',
		'3023' => 'Failed transaction in API.',
		'3024' => 'Action not found.',
		'3025' => 'Deleting primary account is not allowed.',
		'3026' => 'Maximum number of account holders reached',
		'3027' => 'Cannot assign ani that is already assigned',
		'3028' => 'Invalid Amount',
		'3030' => 'Unexpected Error.'
	);

	public function __construct($ctrl)
	{
		$this->ctrl = $ctrl;
		// Load libraries //
		$this->ctrl->load->library('encryption');
		$this->ctrl->load->library('twilio');
		// Load models //
		$this->ctrl->load->model('client');
		$this->ctrl->load->model('provider');
		$this->ctrl->load->model('transaction');
	}

	public function addFunds($client_phone, $phone, $offering_id, $product_id, $amount, $clientAmount, $profitPercent, $serviceCharge, $includeCharge)
	{
		$ctrl = $this->ctrl;
		$data = array(
			'phonenumber' => substr($phone, -10),
			'sku' => $offering_id,
			'amount' => $amount,
			'allow_consecutive_topups' => 0
		);

		$response = $this->callAPI('POST', 'topup_requests.json', $data);

		$response_code = getArrayValue($response, 'Response-Code');

		if($response_code == 201)
		{
			if ($request_id = getArrayValue($response, 'request_id'))
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
					'transId' => $request_id,
					'status' => 'Pending'
				));

				$data['error'] = 'Transaction pending. Check again soon to see if transaction succeeded.';
				$data['status'] = 'Pending';
				$data['transId'] = $request_id;
			}
		}
		else
		{
			$error_msg = getArrayValue($this->error_codes, strval($response_code));
			$data['error'] = $error_msg ?: 'Error communicating with the service.';
		}
		return $data;
	}

	public function callAPI($method, $complement_url, $data=array())
	{
		try
		{
			$ctrl = $this->ctrl;

			$provider = $ctrl->provider->getById('LOGICAL');
			$provider->password = $ctrl->encryption->decrypt($provider->password);

			$auth = base64_encode($provider->username . ':' . $provider->password);

			$options = array(
			'http' => array(
				'method'  => $method,
				'content' => $data ? json_encode($data) : '',
				'header'=>  "Content-Type: application/json\r\n" .
							"Accept: application/json\r\n" .
							"Authorization: Basic {$auth}"
				)
			);

			$url = ($provider->url.$complement_url);
			$context = stream_context_create($options);
			$result = @file_get_contents($url, FALSE, $context);
			$response = json_decode($result, TRUE);

			$response = is_array($response) ? $response : array();

			$response_headers = $this->parseHeaders($http_response_header);

			if($response_code = getArrayValue($response_headers, 'Response-Code'))
			{
				$response['Response-Code'] = $response_code;
			}

			return $response;
		}
		catch(Exception $e)
		{
			return FALSE;
		}
	}

	public function confirm($id, $request_id)
	{
		$ctrl = $this->ctrl;

		$transaction = $ctrl->transaction->getById($id);

		if('Success' === $transaction->status)
		{
			$data['status'] = 'AlreadyApproved';
			$data['message'] = 'This transaction has already been approved.';
		}
		else
		{
			$request_status = $this->getRequestStatus($request_id);

			if ('new' === $request_status)
			{
				$request_data = array(
					'request_action' => 'execute'
				);

				$response = $this->callAPI('PUT', ('topup_requests/' . $request_id . '.json'), $request_data);

				$response_code = getArrayValue($response, 'Response-Code');

				if (200 == $response_code || 202 == $response_code)
				{
					// Calculo lo que le queda a la empresa.
					$recargaReal = ($transaction->amount - $transaction->includeCharge);
					$due = ($recargaReal + ($transaction->serviceCharge + $transaction->includeCharge)) - $transaction->profit;
					// Actualizo el balance del cliente.
					$ctrl->client->updateBalance($_SESSION['userId'], $due);

					$data['status'] = 'Pending';

					// Envío los mensajes de texto.
					$ctrl->twilio->sendSMS($transaction->clientPhone);  // Pagador.
					$ctrl->twilio->sendSMS($transaction->phone);  // Receptor.
				}
				else
				{
					$error_msg = getArrayValue($this->error_codes, strval($response_code));
					$data['error'] = $error_msg ?: 'Error communicating with the service.';
				}
			}
			elseif ('finished' === $request_status)
			{
				$ctrl->transaction->update($id, array(
					'status' => 'Success'
				));

				$data['status'] = 'Success';

				$amount_calculated = $transaction->amount - (($transaction->includeCharge > 0) ? $transaction->includeCharge : 0);

				$data['message'] = ('$'
					. number_format($amount_calculated, 2)
					. ' added successfully to the phone number '
					. $transaction->phone
					. '.');
				$data['amount'] = $transaction->amount + $transaction->serviceCharge;
				$data['phone'] = $transaction->phone;
			}
			else
			{
				$ctrl->transaction->update($id, array(
					'status' => 'Failed'
				));

				$data['error'] = 'Error communicating with the service.';
			}
		}

		return $data;
	}

	private function getRequestStatus($request_id)
	{
		$response = $this->callAPI('GET', ('topup_requests/' . $request_id . '.json'));
		$response_code = getArrayValue($response, 'Response-Code');

		if (200 == $response_code)
		{
			return getArrayValue($response, 'request_status');
		}

		return FALSE;
	}

	private function parseHeaders($headers)
	{
		$head = array();
		foreach($headers as $k => $v)
		{
			$t = explode( ':', $v, 2 );
			if( isset( $t[1] ) )
				$head[ trim($t[0]) ] = trim( $t[1] );
			else
			{
				$head[] = $v;
				if(preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out))
					$head['Response-Code'] = intval($out[1]);
			}
		}
		return $head;
	}
}
?>