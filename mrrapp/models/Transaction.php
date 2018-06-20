<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con las transacciones.
 * Creado: Febrero 26, 2017
 * Modificaciones: CZapata
 */

class Transaction extends CI_Model
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
	 * create
	 * Inserta el registro de la transacción usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('transaction', $data);
		return $this->db->insert_id();  // Devuelve el id del registro recién insertado.
	}

	/**
	 * delete
	 * Borra el registro de la transacción.
	 */
	public function delete($id)
	{
		return $this->db->delete('transaction', array('id' => $id));
	}

	/**
	 * getAll
	 * Obtiene las últimas transacciones realizadas por las tiendas.
	 */
	public function getAll($from = '', $to = '', $status = '', $clientId = '')
	{
		$this->db->select('t.*, c.name');
		$this->db->from('transaction AS t');
		$this->db->join('client AS c', 't.clientId = c.id');
		if($from != '' || $to != '')
		{
			if($from != '')
			{
				$this->db->where('created >=', $from.' 00:00:00');
			}
			if($to != '')
			{
				$this->db->where('created <=', $to.' 23:59:59');
			}
		}
		else
		{
			$this->db->limit(100);
		}
		if($status != '')
		{
			$this->db->where('t.status', $status);
		}
		if($clientId != '')
		{
			$this->db->where('t.clientId', $clientId);
		}
		$this->db->order_by('t.id', 'desc');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * getByClientId
	 * Obtiene las transacciones realizadas por el cliente indicado por $id.
	 */
	public function getByClientId($id, $from = '', $to = '', $status = '')
	{
		$this->db->select('*');
		if($from != '' && $to != '')
		{
			$this->db->where('created >=', $from.' 00:00:00');
			$this->db->where('created <=', $to.' 23:59:59');
		}
		else
		{
			if($from != '')
			{
				$this->db->where('created >=', $from.' 00:00:00');
			}
			if($to != '')
			{
				$this->db->where('created <=', $to.' 23:59:59');
			}
			$this->db->limit(100);
		}
		if($status != '')
		{
			$this->db->where('status', $status);
		}
		$this->db->order_by('id', 'desc');
		$query = $this->db->get_where('transaction', array('clientId' => $id));
		return $query->result();
	}

	/**
	 * getById
	 * Obtiene los datos de la transacción indicada por $id.
	 */
	public function getById($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('transaction', array('id' => $id));
		return $query->row();
	}

	/**
	 * getByUserId
	 * Obtiene las últimas transacciones realizadas por las tiendas del vendedor $userId.
	 */
	public function getByUserId($userId, $from = '', $to = '', $status = '', $clientId = '')
	{
		$this->db->select('t.*, c.name');
		$this->db->from('transaction AS t');
		$this->db->join('client AS c', 't.clientId = c.id');
		$this->db->where('userId', $userId);
		if($from != '' || $to != '')
		{
			if($from != '')
			{
				$this->db->where('created >=', $from.' 00:00:00');
			}
			if($to != '')
			{
				$this->db->where('created <=', $to.' 23:59:59');
			}
		}
		else
		{
			$this->db->limit(100);
		}
		if($status != '')
		{
			$this->db->where('t.status', $status);
		}
		if($clientId != '')
		{
			$this->db->where('t.clientId', $clientId);
		}
		$this->db->order_by('t.id', 'desc');
		$this->db->limit(100);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * getEarnings
	 * Obtiene las ganancias del vendedor y la empresa, agrupadas por cliente o producto.
	 */
	public function getEarnings($from, $to, $groupedBy, $userId = '')
	{
		$this->db->select($groupedBy.', c.name, SUM(t.amount) AS total, SUM(t.includeCharge) AS included, SUM(t.profit) AS clientEarnings, SUM(t.userProfit) AS userEarnings, SUM(t.companyProfit) AS earnings');
		$this->db->from('transaction AS t');
		if($groupedBy == 'productId')
		{
			$this->db->join('product AS c', 't.productId = c.id');
			if($userId != '')
			{
				$this->db->join('client AS s', 't.clientId = s.id');
			}
		}
		else
		{
			$this->db->join('client AS c', 't.clientId = c.id');
		}
		$this->db->where('created >=', $from.' 00:00:00');
		$this->db->where('created <=', $to.' 23:59:59');
		$this->db->where('t.status', 'Success');
		if($userId != '')
		{
			$this->db->where('userId', $userId);
			$this->db->order_by('userEarnings', 'desc');
		}
		else
		{
			$this->db->order_by('earnings', 'desc');
		}
		$this->db->group_by($groupedBy);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * getInvoice
	 * Método que agrupa las transaciones para generar la factura.
	 * @param timestamp $from fecha de inicio
	 * @param timestamp $to fecha de cierre
	 */
	public function getInvoice($profile, $from = '', $to = '', $store = '')
	{
		$select = '';
		$where = 't.clientId = c.id AND t.productId = p.id ';
		$groupBy = 'c.name';
		if($profile == ADMIN)
		{
			if($from != '' && $to != '' && $store != '')
			{
				$select = 'COUNT(t.id) AS numTrans, DATE_FORMAT(t.created, "%Y-%m-%d") AS date, c.name AS store, SUM(t.amount + t.serviceCharge) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount + t.serviceCharge - t.profit) AS total, t.clientId';
				$where .= "AND t.created >= '".$from." 00:00:00' AND t.created <= '".$to." 23:59:59' AND t.clientId = ".$store." AND t.status = 'Success' ";
				$groupBy = 'date';
			}
			else
			{
				if($store != '' && $from == '' && $to == '')
				{
					$select = 't.created, c.name AS store, p.name AS product, t.clientPhone, t.phone, (t.amount + t.serviceCharge) AS amount, t.serviceCharge, t.includeCharge, t.profit, (t.amount + t.serviceCharge - t.profit) AS total';
					$where .= 'AND c.id = '.$store;
					$groupBy = 't.id';
				}
				else
				{
					if($store != '')
					{
						$select = "COUNT(t.id) AS numTrans, DATE_FORMAT(t.created, '%Y-%m-%d') AS 'date', c.name AS store, SUM(t.amount + t.serviceCharge) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount + t.serviceCharge - t.profit) AS total, t.clientId";
						$where .= 'AND t.clientId = '.$store;
						$groupBy = 'date';

						if($from != '')
						{
							$where .= " AND t.created >= '".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <= '".$to." 23:59:59'";
						}
					}
					else
					{
						$select = 'c.name AS store, SUM(t.amount + t.serviceCharge) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount + t.serviceCharge - t.profit) AS total';
						$this->db->limit(100);
						if($from != '')
						{
							$where .= " AND t.created >= '".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <= '".$to." 23:59:59'";
						}
					}
				}
			}
		}
		if($profile == STORE)
		{
			if($from != '' && $to != '' && $store != '')
			{
				$select = 'COUNT(t.id) AS numTrans, DATE_FORMAT(t.created, "%Y-%m-%d") AS date, c.name AS store, SUM(t.amount + t.serviceCharge) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount + t.serviceCharge - t.profit) AS total, t.clientId';
				$where .= " AND t.created >= '".$from." 00:00:00' AND t.created <= '".$to." 23:59:59' ";
				$where .= " AND t.clientId = ".$store." AND t.status = 'success'";
				$groupBy = 'date';
			}
			else
			{
				if($store != '' && $from == '' && $to == '')
				{
					$select = 't.created, c.name AS store, p.name AS product, t.clientPhone, t.phone, (t.amount + t.serviceCharge) AS amount, t.serviceCharge, t.includeCharge, t.profit, SUM(t.amount + t.serviceCharge - t.profit) AS total';
					$where .= 'AND c.id = '.$store;
					$groupBy = 't.id';
				}
				else
				{
					if($store != '')
					{
						$select = "COUNT(t.id) AS numTrans, DATE_FORMAT(t.created, '%Y-%m-%d') AS 'date', c.name AS store, SUM(t.amount + t.serviceCharge) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount + t.serviceCharge - t.profit) AS total, t.clientId";
						$where .= 'AND t.clientId = '.$store;
						$groupBy = 'date';
						if($from != '')
						{
							$where .= " AND t.created >='".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <='".$to." 23:59:59'";
						}
					}
				}
			}
		}
		$this->db->select($select);
		$this->db->from('transaction AS t, client AS c, product AS p');
		$this->db->where($where);
		$this->db->group_by($groupBy);
		$query = $this->db->get();
		return $query;
	}

	/**
	 * getLatest
	 * Obtiene la última transacción realizada por alguna de las tiendas del vendedor logueado.
	 */
	public function getLatest($type)
	{
		$this->db->select('created, transaction.phone, amount, name, serviceCharge, includeCharge');
		$this->db->order_by('transaction.id', 'desc');
		$this->db->limit(1);
		$this->db->from('transaction');
		$this->db->join('client', 'transaction.clientId = client.id');
		$this->db->where('transaction.status', 'Success');
		// Muestro solo las transacciones de las tiendas del vendedor.
		if($type == SELLER)
		{
			$this->db->where('userId', $_SESSION['userId']);
		}
		$query = $this->db->get();
		return $query->row();
	}

	/**
	 * getLatestTopups
	 * Obtiene las últimas transacciones realizadas por la tienda indicada por $clientId.
	 */
	public function getLatestTopups($limit = 20)
	{
		$this->db->select('productId, created, clientPhone, phone, (amount + serviceCharge) AS amount, status');
		$this->db->from('transaction');
		$this->db->where('clientId', $_SESSION['userId']);
		$this->db->order_by('transaction.id', 'desc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * getProductName
	 * Obtiene el nombre del producto especificado por $productId.
	 */
	public function getProductName($productId)
	{
		$this->db->select('name');
		$query = $this->db->get_where('product', array('id' => $productId));
		$row = $query->row();
		if($row)
		{
			return $row->name;
		}
		else
		{
			return 'Not found.';
		}
	}

	/**
	 * getProviderId
	 * Obtiene el providerId del producto especificado por $productId.
	 */
	public function getProviderId($productId)
	{
		$this->db->select('providerId');
		$query = $this->db->get_where('product', array('id' => $productId));
		$row = $query->row();
		if($row)
		{
			return $row->providerId;
		}
		else
		{
			return 'Not found.';
		}
	}

	/**
	 * getSpecialInvoice
	 * Este método consulta los datos para exportar la factura.
	 */
	public function getSpecialInvoice($d, $store)
	{
		$this->db->select('t.created, t.transId, c.name AS store, p.name AS product, t.clientPhone, t.phone, t.pin, t.status, (t.amount + t.serviceCharge) AS amount, t.serviceCharge, t.includeCharge, t.profit, (t.amount + t.serviceCharge - t.profit) AS total');
		$this->db->from('transaction AS t, client AS c, product AS p');
		$this->db->where("t.clientId = c.id AND t.productId = p.id AND t.created>='".$d." 00:00:00' AND t.created<='".$d." 23:59:59' AND t.clientId =".$store);
		$this->db->group_by('t.created');
		$query = $this->db->get();
		return $query;
	}

	/**
	 * getSumByClientId
	 * Obtiene el total de transacciones exitosas, el valor total pagado por el cliente final y la ganancia total de la tienda.
	 */
	public function getSumByClientId($clientId)
	{
		$this->db->select('COUNT(*) AS transactions, SUM(amount) AS amount, SUM(serviceCharge) AS serviceCharge, SUM(profit) AS profit');
		$this->db->where('clientId', $clientId);
		$this->db->where('status', 'Success');
		$query = $this->db->get('transaction');
		return $query->row();
	}

	/**
	 * numTransactions
	 * Cuenta la cantidad de transacciones realizadas este mes.
	 */
	public function numTransactions($type)
	{
		$this->db->where('YEAR(created) = YEAR(CURDATE()) AND MONTH(created) = MONTH(CURDATE())');
		$this->db->where('status', 'Success');
		// Cuento solo las transacciones de las tiendas del vendedor.
		if($type == SELLER)
		{
			$this->db->where('clientId IN (SELECT id FROM client WHERE userId='.$_SESSION['userId'].')');
		}
		$this->db->from('transaction');
		return $this->db->count_all_results();
	}

	/**
	 * sumTransactions
	 * Suma el valor de las transacciones de este mes que hay en la base de datos.
	 */
	public function sumTransactions($type)
	{
		$this->db->select_sum('(amount + serviceCharge)', 'amount');
		$this->db->where('YEAR(created) = YEAR(CURDATE()) AND MONTH(created) = MONTH(CURDATE())');
		$this->db->where('status', 'Success');
		// Sumo solo las transacciones de las tiendas del vendedor.
		if($type == SELLER)
		{
			$this->db->where('clientId IN (SELECT id FROM client WHERE userId='.$_SESSION['userId'].')');
		}
		$query = $this->db->get('transaction');
		$row = $query->row();
		return $row->amount ? $row->amount : 0;
	}

	/**
	 * transactionExists
	 * Determina si una transacción ya se realizó dentro de los últimos 90 minutos.
	 */
	public function transactionExists($productId, $clientPhone, $phone)
	{
		$this->db->select('id, transId, status');
		$this->db->where('created > DATE_SUB(NOW(), INTERVAL 90 MINUTE)');
		$this->db->where('productId', $productId);
		$this->db->where('clientPhone', $clientPhone);
		$this->db->where('phone', $phone);
		$this->db->limit(1);
		$query = $this->db->get('transaction');
		return $query->row();
	}

	/**
	 * update
	 * Actualiza el registro de la transacción usando los datos recibidos.
	 */
	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('transaction', $data);  // Devuelve TRUE en caso de éxito.
	}

	/**
	 * getSales
	 * Método que obtiene las ventas.
	 */
	public function getSales($profile, $from = '', $to = '', $store = '', $product = '')
	{
		$where = 't.clientId = c.id AND t.productId = p.id';
		if($profile == STORE)
		{
			$where .= ' AND c.id='.$store." AND t.status = 'Success'";
			if($from != '' && $to != '' && $product != '')  // Condición con todos los filtros.
			{
				$select = 't.created, t.transId, p.name AS product, p.countryId, t.clientPhone, t.phone, t.pin, t.status, t.amount, t.serviceCharge, t.includeCharge, t.profit, SUM(t.amount - t.profit) AS total';
				$where .= " AND t.created >= '".$from." 00:00:00' AND t.created <= '".$to." 23:59:59'";
				$where .= " AND p.id= ".$product;
				$groupBy = 't.created';
			}
			else
			{
				if($product != '' && $from == '' && $to == '')  // Condición solo con el producto.
				{
					$select = 't.created, p.name AS product, p.countryId, t.clientPhone, t.phone, t.amount, t.serviceCharge, t.includeCharge, t.profit, (t.amount - t.profit) AS total';
					$where .= ' AND c.id = '.$store. ' AND p.id='.$product;
					$groupBy = 't.created';
				}
				else
				{
					if($product != '')
					{
						$select = 't.created, t.transId, p.name AS product, p.countryId, t.clientPhone, t.phone, t.pin, t.status, t.amount, t.serviceCharge, t.includeCharge, t.profit, SUM(t.amount - t.profit) AS total';
						$where .= ' AND c.id = '.$store. ' AND p.id='.$product;
						$groupBy = 't.created';
						if($from != '')
						{
							$where .= " AND t.created >='".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <='".$to." 23:59:59'";
						}
					}
					else
					{
						$select = ' p.name AS product, p.countryId, COUNT(t.id) AS numTrans, SUM(t.amount) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount - t.profit) AS total';
						$groupBy = 'p.id';
						$this->db->limit(100);
						if($from != '')
						{
							$where .= " AND t.created >= '".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <= '".$to." 23:59:59'";
						}
					}
				}
			}
		}
		$this->db->select($select);
		$this->db->from('transaction AS t, client AS c, product AS p');
		$this->db->where($where);
		$this->db->group_by($groupBy);
		$this->db->order_by('p.name');
		$query = $this->db->get();
		return $query;
	}

	/**
	 * getSalesBySeller
	 * Método que permite obtener las ventas por vendedor.
	 */
	public function getSalesBySeller($from = '', $to = '', $seller = '', $product = '')
	{
		$where = "t.clientId = c.id AND t.productId = p.id AND c.userId = u.id AND t.status = 'Success'";
		if($product != '')
		{
			$where .= ' AND t.productId='.$product;
			if($seller != '' && $from != '' && $to != '')  // Con todos los filtros.
			{
				$select = 't.created, t.transId, u.name AS seller, p.name AS product, p.countryId, t.clientPhone, t.phone, t.pin, t.status, t.amount, t.serviceCharge, t.includeCharge, t.profit, SUM(t.amount - t.profit) AS total';
				$where .= ' AND u.id='.$seller;
				$where .= " AND t.created >= '".$from." 00:00:00' AND t.created <= '".$to." 23:59:59'";
				$groupBy = 't.created';
			}
			else
			{
				if($seller != '' && $from == '' && $to == '')  // Solo el vendedor.
				{
					$select = 't.created, u.name AS seller, p.name AS product, p.countryId, t.clientPhone, t.phone, t.amount, t.serviceCharge, t.includeCharge, t.profit, (t.amount - t.profit) AS total';
					$where .= ' AND u.id='.$seller;
					$groupBy = 't.created';
				}
				else
				{
					if($seller != '')  // Vendedor con alguna fecha.
					{
						$select = 't.created, t.transId, u.name AS seller, p.name AS product, p.countryId,  t.clientPhone, t.phone, t.pin, t.status, t.amount, t.serviceCharge, t.includeCharge, t.profit, SUM(t.amount - t.profit) AS total';
						$where .= ' AND u.id='.$seller;
						$groupBy = 't.created';

						if($from != '')
						{
							$where .= " AND t.created >= '".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <= '".$to." 23:59:59'";
						}
					}
					else
					{
						// Todos los vendedores con o sin fechas.
						$select = 'u.name AS seller, COUNT(t.id) AS numTrans, SUM(t.amount) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount - t.profit) AS total';
						$groupBy = 'u.id';
						if($from != '')
						{
							$where .= " AND t.created >= '".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <= '".$to." 23:59:59'";
						}
					}
				}
			}
		}
		else
		{
			if($seller != '' && $from != '' && $to != '')  // Con todos los filtros.
			{
				$select = 't.created, t.transId, u.name AS seller, p.name AS product, p.countryId, t.clientPhone, t.phone, t.pin, t.status, t.amount, t.serviceCharge, t.includeCharge, t.profit, SUM(t.amount - t.profit) AS total';
				$where .= ' AND u.id='.$seller;
				$where .= " AND t.created >= '".$from." 00:00:00' AND t.created <= '".$to." 23:59:59'";
				$groupBy = 't.created';
			}
			else
			{
				if($seller != '' && $from == '' && $to == '')  // Solo el vendedor.
				{
					$select = 't.created, u.name AS seller, p.name AS product, p.countryId, t.clientPhone, t.phone, t.amount, t.serviceCharge, t.includeCharge, t.profit, (t.amount - t.profit) AS total';
					$where .= ' AND u.id='.$seller;
					$groupBy = 't.created';
				}
				else
				{
					if($seller != '')  // Vendedor con alguna fecha.
					{
						$select = 't.created, t.transId, u.name AS seller,  p.name AS product, p.countryId, t.clientPhone, t.phone, t.pin, t.status, t.amount, t.serviceCharge, t.includeCharge, t.profit, SUM(t.amount - t.profit) AS total';
						$where .= ' AND u.id='.$seller;
						$groupBy = 't.created';

						if($from != '')
						{
							$where .= " AND t.created >= '".$from." 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <= '".$to." 23:59:59'";
						}
					}
					else
					{
						// Todos los vendedores con o sin fechas.
						$select = 'u.name AS seller, COUNT(t.id) AS numTrans, SUM(t.amount) AS amount, SUM(t.serviceCharge) AS serviceCharge, SUM(t.includeCharge) AS includeCharge, SUM(t.profit) AS profit, SUM(t.amount - t.profit) AS total';
						$groupBy = 'u.id';
						if($from != '')
						{
							$where .= " AND t.created >= '" . $from . " 00:00:00'";
						}
						if($to != '')
						{
							$where .= " AND t.created <= '" . $to . " 23:59:59'";
						}
					}
				}
			}
		}
		$this->db->select($select);
		$this->db->from('transaction AS t, client AS c, product AS p, user AS u');
		$this->db->where($where);
		$this->db->group_by($groupBy);
		$this->db->order_by('u.name');
		$query = $this->db->get();
		return $query;
	}
}
