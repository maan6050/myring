<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImportCtrl extends MY_Controller
{
	private $agent_id_list;

	public function __construct()
	{
		parent::__construct();

		$this->load->model('client');
		$this->load->model('user');

		$this->agent_id_list = array();
	}

	public function index()
	{
		die('Import disabled.');
		$line = 1;
		if (FALSE !== ($file = fopen(dirname(__FILE__) . '/stores.csv', 'r')))
		{
			while (FALSE !== ($data = fgetcsv($file, 1000, ',')))
			{
				if ($line > 1)
				{
					$this->insertData($data, $this->getAgentId($data[3]));
				}
				++$line;
			}
			fclose($file);
		}

		echo $line . ' lines imported.';
	}

	private function getAgentId($name)
	{
		$slug_name = $this->slugify($name, '.');

		if (isset($this->agent_id_list[$slug_name]))
		{
			return $this->agent_id_list[$slug_name];
		}
		else
		{
			$data = array(
				'type' => 's',
				'name' => $name,
				'email' => ($slug_name . '@gmail.com'),
				'password' => password_hash('12345', PASSWORD_DEFAULT)
			);

			$user_id = $this->user->create($data);
			$this->agent_id_list[$slug_name] = $user_id;
			return $user_id;
		}
	}

	private function insertData($data, $user_id)
	{
		$phone_list = array(
			str_replace('-', '', $data[9]),
			str_replace('-', '', $data[10])
		);

		$username = $this->slugify($data[0], '-', array('#'), array('n'));

		$values = array(
			'userId' => $user_id,
			'type' => STORE,
			'name' => $data[0],
			'username' => $username,
			'password' => password_hash('mexico2017', PASSWORD_DEFAULT),
			'balance' => (empty($data[1]) ? 0.00 : (float) $data[1]),
			'maxBalance' => 1000,
			'paymentMethod' => 'c',
			'contactName' => $data[8],
			'email' => $data[17],
			'phone' => implode(',', $phone_list),
			'address' => $data[11],
			'city' => $data[12],
			'zip' => $data[15],
			'state' => $data[13],
			'country' => $data[14],
			'timezone' => $data[4],
			'status' => 'a'
		);

		$this->client->create($values);
		/*
		0 - Name
		1 - Balance
		2 - Brand
		3 - Agent
		4 - Timezone
		5 - Billing Type
		6 - Payment Type
		7 - Credit Limit
		8 - Contact
		9 - Work Phone
		10 - Mobile Phone
		11 - Address
		12 - City
		13 - State
		14 - Country
		15 - Zip Code
		16 - UID
		17 - Email
		18 - Last Purchase
		*/
	}

	private function slugify($string, $delimiter = '-', $replace = array(), $replace_to = array())
	{
		$oldLocale = setlocale(LC_ALL, '0');
		setlocale(LC_ALL, 'en_US.UTF-8');
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		if(!empty($replace))
		{
			$clean = str_replace((array) $replace, $replace_to, $clean);
		}
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower($clean);
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		$clean = trim($clean, $delimiter);
		// Revert back to the old locale
		setlocale(LC_ALL, $oldLocale);
		return $clean;
	}
}
?>