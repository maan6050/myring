<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con los proveedores.
 * Creado: Marzo 10, 2017
 * Modificaciones: CZapata
 */

class Provider extends CI_Model
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
	 * Inserta el registro del proveedor usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('provider', $data);
		return $this->db->insert_id();  // Devuelve el id del registro recién insertado.
	}

	/**
	 * delete
	 * Borra el registro del proveedor y los productos asociados.
	 */
	public function delete($id)
	{
		$where = 'productId IN (SELECT id FROM product WHERE providerId="'.$id.'")';
		$this->db->where($where);
		$this->db->delete('client_product');
		$this->db->delete('product', array('providerId' => $id));
		return $this->db->delete('provider', array('id' => $id));
	}

	/**
	 * getAll
	 * Obtiene los datos de todos los proveedores en orden alfabético.
	 */
	public function getAll()
	{
		$this->db->select('*');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('provider');
		return $query->result();
	}

	/**
	 * getById
	 * Obtiene los datos del proveedor indicado por $id.
	 */
	public function getById($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('provider', array('id' => $id));
		return $query->row();
	}

	/**
	 * update
	 * Actualiza el registro del usuario usando los datos recibidos del formulario.
	 */
	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('provider', $data);  // Devuelve TRUE en caso de éxito.
	}
}