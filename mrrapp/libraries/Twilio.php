<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$pre = $_SERVER['SERVER_NAME'] == 'localhost' ? '' : '../';
include $pre.'vendor/autoload.php';
// Use the REST API Client to make requests to the Twilio REST API.
use Twilio\Rest\Client;

class Twilio
{
	private $sid = 'AC65a19d0bf6368e0b93070099df39d5b4';
	private $token = '5073e779d830ec90dc9a321199430d60';
	private $phone = '+15095712691';

	public function __construct()
	{
	}

	public function getProductByPhone($phone)
	{
		//$response = array('mcc' => "732", 'mnc' => "123", 'name' => "Telefonica Moviles Colombia S.A. (Movistar)", 'type' => "mobile");
		//$response = array('mcc' => "732", 'mnc' => "101", 'name' => "Comcel S.A. (Claro)", 'type' => "mobile");
		//$response = array('mcc' => null, 'mnc' => null, 'name' => "Avantel Servicios Locales", 'type' => "landline");
		//$response = array('error' => 'Carrier not found, please verify phone number.');
		//return $response;
		$client = new Client($this->sid, $this->token);

		try
		{
			$number = $client->lookups->phoneNumbers($phone)->fetch(
							array('type' => 'carrier')
						);
		}
		catch(Exception $e)
		{
			// Captura la excepción si no encuentra el número.
			$number = FALSE;
		}

		if(is_object($number))
		{
			$response = array(
							'mcc' => $number->carrier['mobile_country_code'],
							'mnc' => $number->carrier['mobile_network_code'],
							'name' => $number->carrier['name'],
							'type' => $number->carrier['type']
						);
		}
		else
		{
			$response = array('error' => 'Carrier not found, please verify phone number.');
		}
		return $response;
	}

	public function sendSMS($phone, $message = NULL)
	{
		$client = new Client($this->sid, $this->token);

		try
		{
			$client->messages->create(
				// El número al que le enviaré el mensaje.
				$phone,
				array(
					// El número telefónico que se compró en Twilio.
					'from' => $this->phone,
					// El cuerpo del SMS que se enviará.
					'body' => !is_null($message) ? $message : "Su recarga ha sido completada, gracias/Your Top-up has been completed, thank you.\nPor/By\nPortal myringring.net"
				)
			);
		}
		catch(Exception $e)
		{
		}
	}
}
