<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pinless extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if(!isset($_SESSION['userId']))
		{
			// El usuario no ha iniciado sesión.
			redirect(base_url('login'));
		}
	}

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		try
		{
			$ch = curl_init('https://api.latinotopup.com/api/topup_requests.json');
			curl_setopt($ch, CURLOPT_USERPWD, 'directreloadllc:nTUZhQ4HdZGwSvwzxab');
			//curl_setopt($ch, CURLOPT_HEADER, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'phonenumber=5551112222&sku=foo-100-mxn-tel&amount=100&allow_consecutive_topups=0');

			$result = curl_exec($ch);
			curl_close($ch);
			//var_dump($result);
		}
		catch(Exception $e)
		{
			echo 'Error: '.$e;
		}
		/*try
		{
			$client = new SoapClient('https://api.latinotopup.com/api/sku_list.json', array('username' => 'directreloadllc', 'password' => 'nTUZhQ4HdZGwSvwzxab'));
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
		$response = $client->TopUpRequest($params);*/
	}
}
