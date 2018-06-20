<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con los clientes.
 * Creado: Enero 20, 2017
 * Modificaciones: CZapata
 */

class Client extends CI_Model
{
	/**
	 * __construct
	 * Método constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * changeBalance
	 * Actualiza la deuda del cliente, sumando lo que debe pagarle a la empresa, excluyendo su ganancia.
	 */
	public function changeBalance($id, $realBalance)
	{
		$this->db->set('balance', $realBalance);
		$this->db->where('id', $id);
		return $this->db->update('client');  // Devuelve TRUE en caso de éxito.
	}

	/**
	 * create
	 * Inserta el registro del cliente usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('client', $data);
		return $this->db->insert_id();  // Devuelve el id del registro recién insertado.
	}

	/**
	 * delete
	 * Inactiva el registro del cliente.
	 */
	public function delete($id, $type)
	{
		$this->db->set('status', 'i');
		$this->db->where(array('id' => $id, 'type' => $type));
		return $this->db->update('client');
	}

	/**
	 * getAll
	 * Obtiene los datos de todos los clientes en orden alfabético.
	 */
	public function getAll($type, $name = '', $orderBy = 'name')
	{
		$this->db->select('id, userId, name, balance, maxBalance, paymentMethod');
		if($name != '')
		{
			// Buscar cliente por nombre.
			$this->db->like('name', $name);
		}
		$asc = $orderBy == 'balance' ? 'desc' : 'asc';
		$this->db->order_by($orderBy, $asc);
		if($_SESSION['userType'] == SELLER)
		{
			$this->db->where('userId', $_SESSION['userId']);
		}
		$query = $this->db->get_where('client', array('type' => $type, 'status' => 'a'));
		$rows = $query->result();
		foreach($rows as &$row)
		{
			$this->db->select('name');
			$queryUser = $this->db->get_where('user', array('id' => $row->userId));
			$rowUser = $queryUser->row();
			$row->userName = $rowUser->name;
			switch($row->paymentMethod)
			{
				case 'c': $row->paymentMethodName = 'Credit';
					break;
				case 'a': $row->paymentMethodName = 'ACH';
					break;
			}
		}
		return $rows;
	}

	/**
	 * getById
	 * Obtiene los datos del cliente indicado por $id.
	 */
	public function getById($id, $type)
	{
		$this->db->select('*');
		$query = $this->db->get_where('client', array('id' => $id, 'type' => $type));
		return $query->row();
	}

	/**
	 * getByUsername
	 * Obtiene los datos del cliente indicado por $username.
	 */
	public function getByUsername($username)
	{
		$this->db->select('*');
		$query = $this->db->get_where('client', array('username' => $username));
		return $query->row();
	}

	/**
	 * getProfitPercent
	 * Obtiene el porcentaje de ganancia de una tienda, del vendedor y de la empresa con respecto a un producto.
	 * Primero verifica si tiene un porcentaje independiente, sino devuelve el de por defecto.
	 */
	public function getProfitPercent($clientId, $productId)
	{
		$profits = array();
		$this->db->select('defaultProfit, defaultUserProfit, companyProfit');
		$queryPro = $this->db->get_where('product', array('id' => $productId));
		$rowPro = $queryPro->row();
		$profits['companyProfit'] = $rowPro->companyProfit;
		// Busco el porcentaje de ganancia particular de la tienda.
		$this->db->select('profit');
		$query = $this->db->get_where('client_product', array('clientId' => $clientId, 'productId' => $productId));
		$row = $query->row();
		if($row != NULL)
		{
			// Se ha configurado un porcentaje diferente para este cliente de este producto.
			$profits['profit'] = $row->profit;
		}
		else
		{
			// No se ha configurado un porcentaje, así que tomo el que hay por defecto.
			$profits['profit'] = $rowPro->defaultProfit;
		}
		// Busco el porcentaje de ganancia particular del vendedor.
		$this->db->select('profit');
		$this->db->from('client');
		$this->db->join('user_product', 'client.userId = user_product.userId');
		$this->db->where(array('id' => $clientId, 'productId' => $productId));
		$query = $this->db->get();
		$row = $query->row();
		if($row != NULL)
		{
			// Se ha configurado un porcentaje diferente para este vendedor de este producto.
			$profits['userProfit'] = $row->profit;
		}
		else
		{
			// No se ha configurado un porcentaje, así que tomo el que hay por defecto.
			$profits['userProfit'] = $rowPro->defaultUserProfit;
		}
		return $profits;
	}

	/**
	 * sumBalances
	 * Suma la deuda de todos los clientes.
	 */
	public function sumBalances($userType, $clientType = NULL)
	{
		$this->db->select_sum('balance');
		if($userType == SELLER)
		{
			$this->db->where('userId', $_SESSION['userId']);
		}
		if($clientType != NULL)
		{
			$this->db->where('type', $clientType);
		}
		$this->db->where('status', 'a');
		$this->db->from('client');
		$query = $this->db->get();
		$row = $query->row();
		return $row->balance;
	}

	/**
	 * sumClients
	 * Cuenta la cantidad de clientes activos que hay en la base de datos.
	 */
	public function sumClients($userType, $clientType = NULL)
	{
		if($userType == SELLER)
		{
			$this->db->where('userId', $_SESSION['userId']);
		}
		if($clientType != NULL)
		{
			$this->db->where('type', $clientType);
		}
		$this->db->where('status', 'a');
		$this->db->from('client');
		return $this->db->count_all_results();
	}

	/**
	 * update
	 * Actualiza el registro del cliente usando los datos recibidos del formulario.
	 */
	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('client', $data);  // Devuelve TRUE en caso de éxito.
	}

	/**
	 * updateBalance
	 * Actualiza la deuda del cliente, sumando lo que debe pagarle a la empresa, excluyendo su ganancia.
	 */
	public function updateBalance($id, $amount)
	{
		$this->db->set('balance', 'balance + '.$amount, FALSE);  // FALSE evita que el CI escape automáticamente los nombres.
		$this->db->where('id', $id);
		return $this->db->update('client');  // Devuelve TRUE en caso de éxito.
	}

	/**
	 * getStores
	 * Devuelve las tiendas que contengan depósitos.
	 */
	public function getStores()
	{
		$this->db->select('id, name');
		$this->db->from('client');
		$this->db->where('id IN (SELECT DISTINCT clientId FROM deposit)');
		$this->db->order_by('name');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * getStoresToinovoice
	 * Devuelve las tiendas que hayan realizado transacciones.
	 */
	public function getStoresToInvoice()
	{
		$this->db->select('id, name');
		$this->db->from('client');
		if($_SESSION['userType'] == SELLER)
		{
			// Muestro solo las tiendas asociadas al vendedor logueado.
			$this->db->where('userId', $_SESSION['userId']);
		}
		$this->db->where('id IN (SELECT DISTINCT clientId FROM transaction)');
		$this->db->order_by('name');
		$query = $this->db->get();
		return $query->result();
	}
}