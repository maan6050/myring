<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con los productos.
 * Creado: Marzo 11, 2017
 * Modificaciones: CZapata
 */

class Product extends CI_Model
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
	 * Inserta el registro del producto usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('product', $data);
		return $this->db->insert_id();  // Devuelve el id del registro recién insertado.
	}

	/**
	 * delete
	 * Borra el registro del producto.
	 */
	public function delete($id)
	{
		$this->db->delete('client_product', array('productId' => $id));  // Borro los productos asociados con las tiendas.
		return $this->db->delete('product', array('id' => $id));
	}

	/**
	 * deleteClientProfit
	 * Borra el registro del producto asociado al cliente.
	 */
	public function deleteClientProfit($clientId, $productId)
	{
		return $this->db->delete('client_product', array('clientId' => $clientId, 'productId' => $productId));
	}

	/**
	 * deleteSellerProfit
	 * Borra el registro del producto asociado al vendedor.
	 */
	public function deleteSellerProfit($sellerId, $productId)
	{
		return $this->db->delete('user_product', array('userId' => $sellerId, 'productId' => $productId));
	}

	/**
	 * getAll
	 * Obtiene los datos de todos los productos en orden alfabético.
	 */
	public function getAll($countryId, $onlyActives = FALSE)
	{
		$this->db->select('*');
		$this->db->order_by('countryId', 'asc');
		$this->db->order_by('name', 'asc');
		if(!is_null($countryId))
		{
			$this->db->where('countryId', $countryId);
		}
		if($onlyActives)
		{
			$this->db->where('status', 'a');
		}
		$query = $this->db->get('product');
		$rows = $query->result();
		foreach($rows as &$row)
		{
			if($row->countryId != '')
			{
				$this->db->select('name');
				$queryCountry = $this->db->get_where('country', array('id' => $row->countryId));
				$rowCountry = $queryCountry->row();
				$row->countryName = $rowCountry->name;
			}
			else
			{
				$row->countryName = 'n/a';
			}
			if($row->providerId != '')
			{
				$this->db->select('name');
				$queryProvider = $this->db->get_where('provider', array('id' => $row->providerId));
				$rowProvider = $queryProvider->row();
				$row->providerName = $rowProvider->name;
			}
			else
			{
				$row->providerName = 'n/a';
			}
			if($row->fixed != '')
			{
				$row->denominations = strlen($row->fixed) <= 20 ? $row->fixed : substr($row->fixed, 0, 20).'...';
			}
			else
			{
				$row->denominations = 'From '.$row->rangeMin.' to '.$row->rangeMax;
			}
			if($row->image != '' && is_file(UPLOADS_DIR.$row->image))
			{
				$row->image = '<img src="'.base_url(UPLOADS.$row->image).'" width="80">';
			}
			else
			{
				$row->image = '';
			}
			$row->statusClass = $row->status == 'a' ? '' : 'red lineThrough';
		}
		return $rows;
	}

	/**
	 * getById
	 * Obtiene los datos del producto indicado por $id.
	 */
	public function getById($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('product', array('id' => $id));
		return $query->row();
	}

	/**
	 * getClientProfit
	 * Obtiene los porcentajes de ganancia personalizados para el cliente indicado por $id.
	 */
	public function getClientProfit($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('client_product', array('clientId' => $id));
		return $query->result();
	}

	/**
	 * getSellerProfit
	 * Obtiene los porcentajes de ganancia personalizados para el vendedor indicado por $id.
	 */
	public function getSellerProfit($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('user_product', array('userId' => $id));
		return $query->result();
	}

	/**
	 * replaceClientProfit
	 * Borra e inserta el registro del producto asociado al cliente.
	 */
	public function replaceClientProfit($clientId, $productId, $profit)
	{
		$data = array('clientId' => $clientId, 'productId' => $productId);
		$this->db->delete('client_product', $data);
		$data['profit'] = $profit;
		return $this->db->insert('client_product', $data);
	}

	/**
	 * replaceSellerProfit
	 * Borra e inserta el registro del producto asociado al vendedor.
	 */
	public function replaceSellerProfit($userId, $productId, $profit)
	{
		$data = array('userId' => $userId, 'productId' => $productId);
		$this->db->delete('user_product', $data);
		$data['profit'] = $profit;
		return $this->db->insert('user_product', $data);
	}

	/**
	 * update
	 * Actualiza el registro del producto usando los datos recibidos del formulario.
	 */
	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('product', $data);  // Devuelve TRUE en caso de éxito.
	}

	/**
	 * getStoresToinovoice
	 * Devuelve los productos que hayan realizado transacciones.
	 */
	public function getProductsToReport($store = '')
	{
		$this->db->select('id, name, countryId');
		$this->db->from('product');
		if($_SESSION['userType'] == SELLER)
		{
			// Muestro solo las tiendas asociadas al vendedor logueado.
			$this->db->where('userId', $_SESSION['userId']);
		}
		if($store == '')
		{
			$this->db->where('id IN (SELECT DISTINCT productId FROM transaction )');
		}
		else
		{
			$this->db->where('id IN (SELECT DISTINCT productId FROM transaction WHERE clientId = '.$store.')');
		}
		$this->db->order_by('name');
		$query = $this->db->get();
		return $query->result();
	}
}