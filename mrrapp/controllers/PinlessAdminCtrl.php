<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include(realpath(dirname(__FILE__)).'/home_classes/logical_provider_ctrl.php');

class PinlessAdminCtrl extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!isset($_SESSION['userId']))
		{
			redirect(base_url('login'));
		}
		$this->lang->load('header_lang', $this->getLanguage());
		$this->lang->load('pinless_admin', $this->getLanguage());
	}

	private function loadView($view, $data=array(), $header_data=array())
	{
		$header_data['title'] = lang('section_title');

		$this->load->view('header', $header_data);
		$this->load->view($view, $data);
		$this->load->view('footer');
	}

	public function createRegisteredNumber(/*$code, $ani, $new_ani*/)
	{
		$code = $this->input->get_default('code', FALSE);
		$ani = $this->input->get_default('ani', FALSE);
		$new_ani = $this->input->get_default('new_ani', FALSE);
		$api = new LogicalProviderController($this);
		$complement_url = ('product/' . $code . '/pinless_accounts/' . $ani . '/registered_numbers.json');

		$response = $api->callAPI('POST', $complement_url, array(
			'ani' => strval($new_ani)
		));

		$response_code = getArrayValue($response, 'Response-Code');

		$msg_code = (201 == $response_code) ? 'phone_created' : 'phone_created_error';
		$msg_type = (201 == $response_code) ? 'success' : 'danger';

		redirect('/pinlessAdminCtrl/info?code=' . $code . '&ani=' . $ani . '&tab=1&msg_code=' . $msg_code . '&msg_type=' . $msg_type); // set tab 1

	}

	public function deleteRegisteredNumber(/*$code, $ani, $ani_delete*/)
	{
		$code = $this->input->get_default('code', FALSE);
		$ani = $this->input->get_default('ani', FALSE);
		$ani_delete = $this->input->get_default('ani_delete', FALSE);

		$api = new LogicalProviderController($this);

		$complement_url = ('product/' . $code . '/pinless_accounts/' . $ani . '/registered_numbers/' . $ani_delete . '.json');

		$response = $api->callAPI('DELETE', $complement_url);
		$response_code = getArrayValue($response, 'Response-Code');

		$msg_code = (200 == $response_code) ? 'phone_deleted' : 'phone_deleted_error';
		$msg_type = (200 == $response_code) ? 'success' : 'danger';

		redirect('/pinlessAdminCtrl/info?code=' . $code . '&ani=' . $ani . '&tab=1&msg_code=' . $msg_code . '&msg_type=' . $msg_type); // set tab 1
	}

	public function index()
	{
		$this->loadView('pinlessAdmin/index');
	}

	public function info()
	{
		$params = $this->input->get_default('code', FALSE);
		$ani = $this->input->get_default('ani', FALSE);
		$tab = $this->input->get_default('tab', 1);
		$msg_code = $this->input->get_default('msg_code', FALSE);
		$msg_type = $this->input->get_default('msg_type', 'info');
		$params = explode('-', $params);
		$code = $params[0];
		if($code != 'DPPINLESS')
		{
			$api = new LogicalProviderController($this);

			$complement_url = ('product/' . $code . '/pinless_accounts/' . $ani . '.json');
			$data = array(
				'response' => $api->callAPI('GET', $complement_url)
			);

			$data['tab'] = $tab;
			$data['msg_code'] = lang($msg_code);
			$data['msg_type'] = in_array($msg_type, array(
				'danger',
				'info',
				'success',
				'warning'
			)) ? $msg_type : 'info';

			$this->loadView('pinlessAdmin/info', $data);
		}
		else
		{
			$exist = $this->aniExist($ani)->AniExistsResult;
			if($exist)
			{
				$account = $this->getAccountInfoByAni($ani)->GetAccountInfoByAniResult;
				if($account->responseCode >= 0)
				{
					$data['aniList'] = $this->getAniList($account->Pin)->getAniListResult;
					$data['speedDialList'] = $this->getSpeedDialList($account->Pin)->GetSpeedDialListResult;
					$data['callHistory'] = $this->getCalls($account->Pin)->getCallsResult;
					$data['responseCode'] = $account->responseCode;
					$data['balance'] = $account->Balance;
					$data['tab'] = $tab;
					$data['msg_code'] = lang($msg_code);
					$data['msg_type'] = in_array($msg_type, array(
								'danger',
								'info',
								'success',
								'warning'
							)) ? $msg_type : 'info';
					$data['account'] = $account;
					$data['code'] = lang('pinless_world');
					$this->loadView('pinlessAdmin/infoDPPinless', $data);
				}
				else
				{
					$offeringId = $params[1];
					$account = $this->getAccountInfoByAniOfferingId($ani, $offeringId)->GetAccountInfoByAniOfferingResult;
					if($account->responseCode >= 0)
					{
						$data['aniList'] = $this->getAniList($account->Pin)->getAniListResult;
						$data['speedDialList'] = $this->getSpeedDialList($account->Pin)->GetSpeedDialListResult;
						$data['callHistory'] = $this->getCalls($account->Pin)->getCallsResult;
						$data['responseCode'] = $account->responseCode;
						$data['balance'] = $account->Balance;
						$data['tab'] = $tab;
						$data['msg_code'] = lang($msg_code);
						$data['msg_type'] = in_array($msg_type, array(
									'danger',
									'info',
									'success',
									'warning'
								)) ? $msg_type : 'info';
						$account->OfferingId = $offeringId;
						$data['account'] = $account;
						$data['code'] = lang('pinless_world');
						$this->loadView('pinlessAdmin/infoDPPinless', $data);
					}
					else
					{
						$data['msg_code'] = lang($msg_code);
						$data['response'] = array(null);
						$data['msg_type'] = in_array($msg_type, array(
									'danger',
									'info',
									'success',
									'warning'
								)) ? $msg_type : 'info';
						$data['tab'] = $tab;
						$this->loadView('pinlessAdmin/info', $data);
					}
				}
			}
		}
	}

	public function switchLanguage()
	{
		$code = $this->input->get_default('code', FALSE);
		$ani = $this->input->get_default('ani', FALSE);
		$language = $this->input->get_default('language', FALSE);

		$api = new LogicalProviderController($this);

		$complement_url = ('product/' . $code . '/pinless_accounts/' . $ani . '/' . $language . '.json');

		$response = $api->callAPI('PUT', $complement_url);

		$response_code = getArrayValue($response, 'Response-Code');

		redirect('/pinlessAdminCtrl/info?code=' . $code . '&ani=' . $ani . '&tab=4'); // set tab 4
	}

	public function updateSpeedDeals()
	{
		$ani = $this->input->post_default('ani', FALSE);
		$code = $this->input->post_default('code', FALSE);
		$speed_dials = $this->input->post_default('speed_dials', FALSE);

		if ($ani && $code && $speed_dials)
		{
			$api = new LogicalProviderController($this);

			$complement_url = ('product/' . $code . '/pinless_accounts/' . $ani . '/speed_dials.json');

			$data = array();
			foreach ($speed_dials as $key => $item)
			{
				$pos = ($key + 1);

				if (isset($item['telephone']) && !empty($item['telephone']))
				{
					$data["{$pos}"]['phone_number'] = $item['telephone'];
				}

				if (isset($item['description']) && !empty($item['description']))
				{
					$data["{$pos}"]['description'] = $item['description'];
				}
			}

			$response = $api->callAPI('PUT', $complement_url, array(
				'speed_dials' => $data
			));

			$response_code = getArrayValue($response, 'Response-Code');

			$msg_code = (200 == $response_code) ? 'speed_dials_updated' : 'speed_dials_updated_error';
			$msg_type = (200 == $response_code) ? 'success' : 'danger';

			redirect('/pinlessAdminCtrl/info?code=' . $code . '&ani=' . $ani . '&tab=2&msg_code=' . $msg_code . '&msg_type=' . $msg_type); // set tab 2
		}
	}

	public function activateOrRechargeAccount($ani)
	{
		$data = array();
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('reqActAccount' =>
							array('OrderId' => 123,
								'Account' => array(
									'OfferingId' => 30175660,
									'Balance' => 0.01,
									'Ani' => $ani,
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
			return $response;
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	public function getPinInformation($pin)
	{
		$data = array();
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('GetPinInformation'=> array('pin' => $pin));
			$response = $client->GetPinInformation($params);
			print_r($response);
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	public function getAniList($pin)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('pin' => $pin);
			$response = $client->getAniList($params);
			return $response;
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	public function getWebTransactionInfo()
	{
		$data = array();
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('WebTransId' => 11);
			$response = $client->GetWebTransactionInfo($params);
			print_r($response);
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	public function getCalls($pin)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('pin' => $pin);
			return $client->getCalls($params);
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	public function getSpeedDialList($pin)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('pin' => $pin);
			//var_dump($client->GetSpeedDialList($params)); exit();
			return $client->GetSpeedDialList($params);
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	/**
	 * getAccountInfoByAni
	 * Retrieve information for account associated with specified phone number(Ani)
	 */
	public function getAccountInfoByAni($ani)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('Ani' => $ani);
			return $client->GetAccountInfoByAni($params);
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	/**
	 * aniExist
	 * Determina si el ANI que se ingresó ya existe en el sistema.
	 */
	public function aniExist($ani)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('Ani' => $ani);
			return $client->AniExists($params);
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	/**
	 * getAccountInfoByAniOfferingId
	 * Retrieve information for account associated with specified phone number(Ani) and product´s offeringId
	 */
	public function getAccountInfoByAniOfferingId($ani, $offeringId)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('Ani' => $ani, 'OfferingId' => $offeringId);
			return $client->GetAccountInfoByAniOffering($params);
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	/**
	 * addAni
	 * Allow add a new partner phone.
	 */
	public function addAni()
	{
		$data = $this->input->get(NULL, TRUE);
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('pin' => $data['pin'], 'anis' => $data['new_ani'], 'firstName' => '?', 'lastName' => '?');
			$response = $client->AddAni($params);
			if($response->AddAniResult > 0)
			{
				header('Location: ' . base_url('pinlessAdminCtrl/info?code=DPPINLESS-' . $data['offeringId'] . '&ani=' . $data['ani'] . '&msg_code=phone_created&msg_type=success'));
			}
			else
			{
				header('Location: ' . base_url('pinlessAdminCtrl/info?code=DPPINLESS-' . $data['offeringId'] . '&ani=' . $data['ani'] . '&msg_code=phone_created_error&msg_type=danger'));
			}
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}

	/**
	 * removeAni
	 * Allow delete a partner phone.
	 */
	public function removeAni()
	{
		$data = $this->input->get(NULL, TRUE);
		$this->load->model('provider');
		$this->load->library('encryption');
		$provider = $this->provider->getById('DPPINLESS');
		// Desencripto la contraseña.
		$provider->password = $this->encryption->decrypt($provider->password);
		try
		{
			$client = new SoapClient($provider->url, array('login' => $provider->username, 'password' => $provider->password));

			$params = array('pin' => $data['pin'], 'ani' => $data['ani_delete']);
			$response = $client->removeAni($params);
			if($response->removeAniResult > 0)
			{
				header('Location: ' . base_url('pinlessAdminCtrl/info?code=DPPINLESS-' . $data['offeringId'] . '&ani=' . $data['ani'] . '&msg_code=phone_deleted&msg_type=success'));
			}
			else
			{
				header('Location: ' . base_url('pinlessAdminCtrl/info?code=DPPINLESS-' . $data['offeringId'] . '&ani=' . $data['ani'] . '&msg_code=phone_deleted_error&msg_type=danger'));
			}
		}
		catch(Exception $e)
		{
			echo 'No address associated with hostname.';
			echo $e->getMessage();
		}
	}
} ?>