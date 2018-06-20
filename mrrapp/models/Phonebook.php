<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con el directorio.
 * Creado: Mayo 10, 2017
 * Modificaciones: CZapata
 */

class Phonebook extends CI_Model
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

	public function addPhone($clientPhone, $productId, $phone, $name = NULL)
	{
		$this->db->where('clientPhone', $clientPhone);
		$this->db->where('phone', $phone);
		$this->db->from('phonebook');
		// Si no encuentro resultados, inserto el número.
		if($this->db->count_all_results() == 0)
		{
			$data = array('clientPhone' => $clientPhone, 'productId' => $productId, 'phone' => $phone);
			$this->db->insert('phonebook', $data);
		}
		else
		{
			if(!is_null($name))
			{
				$where = array('clientPhone' => $clientPhone, 'phone' => $phone);
				$this->db->select('productId, name');
				$query = $this->db->get_where('phonebook', $where);
				$row = $query->row();
				if($row->name != $name)
				{
					$this->db->where($where);
					$this->db->update('phonebook', array('name' => $name));
				}
			}
		}
	}

	/**
	 * create
	 * Inserta el registro del directorio usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('phonebook', $data);
		return $this->db->insert_id();  // Devuelve el id del registro recién insertado.
	}

	/**
	 * getAll
	 * Obtiene los datos de todos los números en el directorio en orden alfabético.
	 */
	public function getAll($clientPhone)
	{
		$this->db->select('ph.phone, ph.productId, ph.name, countryId, pr.name AS productName, image, type, fixed, rangeMin, rangeMax');
		$this->db->from('phonebook AS ph');
		$this->db->join('product AS pr', 'ph.productId = pr.id', 'left');
		$this->db->where('clientPhone', $clientPhone);
		$this->db->order_by('ph.name', 'asc');
		$this->db->limit(20);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * deletePhone
	 * Método para eliminar un registro en la tabla phonebook.
	 * @param char $clientP varible con el valor de la llave clientPhone.
	 * @param char $phone varible con el valor de la llave phone.
	 * @param int $prodID varible con el valor de la llave productId.
	 */
	public function delete($clientP, $phone, $prodID)
	{
		return $this->db->delete('phonebook', array('clientPhone' => $clientP, 'phone' => $phone, 'productId' => $prodID));
	}

	/**
	 * deleteByClientP
	 * Método para eliminar los registros en la tabla phonebook de acuerdo a la llave.
	 * @param char $clientP
	 */
	public function deleteByClientP($clientP)
	{
		return $this->db->delete('phonebook', array('clientPhone' => $clientP));
	}
}