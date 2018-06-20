<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con los depósitos.
 * Creado: Enero 23, 2017
 * Modificaciones: CZapata
 */

class Deposit extends CI_Model
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
	 * make
	 * Inserta el registro del usuario usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function make($data)
	{
		if($this->db->insert('deposit', $data))
		{
			$this->db->set('balance', 'balance - '.$data['amount'], FALSE);  // FALSE evita que el CI escape automáticamente los nombres.
			$this->db->where('id', $data['clientId']);
			return $this->db->update('client');  // Devuelve TRUE en caso de éxito.
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * numDeposits
	 * Cuenta la cantidad de depósitos realizados este mes.
	 */
	public function numDeposits($type)
	{
		$this->db->where('YEAR(created) = YEAR(CURDATE()) AND MONTH(created) = MONTH(CURDATE())');
		// Cuento solo los depósitos de las tiendas del vendedor.
		if($type == SELLER)
		{
			$this->db->where('clientId IN (SELECT id FROM client WHERE userId='.$_SESSION['userId'].')');
		}
		$this->db->from('deposit');
		return $this->db->count_all_results();
	}

	/**
	 * sumClientDeposits
	 * Suma el valor de todos los depósitos de un cliente.
	 */
	public function sumClientDeposits($clientId)
	{
		$this->db->select_sum('amount');
		$this->db->where('clientId', $clientId);
		$query = $this->db->get('deposit');
		$row = $query->row();
		return is_numeric($row->amount) ? $row->amount : 0;
	}

	/**
	 * sumDeposits
	 * Suma el valor de los depósitos de este mes que hay en la base de datos.
	 */
	public function sumDeposits($type)
	{
		$this->db->select_sum('amount');
		$this->db->where('YEAR(created) = YEAR(CURDATE()) AND MONTH(created) = MONTH(CURDATE())');
		// Sumo solo los depósitos de las tiendas del vendedor.
		if($type == SELLER)
		{
			$this->db->where('clientId IN (SELECT id FROM client WHERE userId='.$_SESSION['userId'].')');
		}
		$query = $this->db->get('deposit');
		$row = $query->row();
		return is_numeric($row->amount) ? $row->amount : 0;
	}

	/**
	 * getByDates
	 * Obtiene los depósitos realizados en un periodo de tiempo.
	 */
	public function getByDates($initialDate = '', $finalDate = '', $store = '')
	{
		$this->db->select("d.created AS Date, c.name AS Client, u.name AS 'User', d.paymentMethod, d.reference, d.amount, d.comments");
		$this->db->from('deposit AS d, client AS c, user AS u');
		$this->db->where('d.clientId = c.id AND d.userId = u.id');
		if($initialDate != '' && $finalDate != '')
		{
			$this->db->where('d.created >=', $initialDate.' 00:00:00');
			$this->db->where('d.created <=', $finalDate.' 23:59:59');
		}
		else
		{
			if($initialDate != '')
			{
				$this->db->where('d.created >=', $initialDate.' 00:00:00');
			}
			if($finalDate != '')
			{
				$this->db->where('d.created <=', $finalDate.' 23:59:59');
			}
		}
		if($store != '')
		{
			$this->db->where('d.clientId', $store);
		}
		$this->db->order_by('d.created', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * getAll
	 * Método que retorna todos los depósitos en la base da datos con el nombre de usuario y del cliente.
	 */
	public function getAll()
	{
		$query = 'SELECT d.created AS Date, c.name AS Client, u.name AS User, d.paymentMethod, d.reference, d.amount, d.comments FROM deposit AS d, client AS c, user AS u WHERE d.clientId = c.id AND d.userId = u.id';
		return $this->db->query($query);
	}
}