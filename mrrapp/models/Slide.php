<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con las imágenes del carrusel.
 * Creado: Junio 20, 2017
 * Modificaciones: CZapata
 */

class Slide extends CI_Model
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
	 * Inserta el registro de la imagen usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('slide', $data);
		// Devuelve el id del registro recién insertado.
		return $this->db->insert_id();
	}

	/**
	 * delete
	 * Borra el registro de la imagen.
	 */
	public function delete($id)
	{
		return $this->db->delete('slide', array('id' => $id));
	}

	/**
	 * getAll
	 * Obtiene los datos de todas las imágenes.
	 */
	public function getAll()
	{
		$this->db->select('*');
		$this->db->order_by('id');
		$query = $this->db->get('slide');
		return $query->result();
	}

	/**
	 * getById
	 * Obtiene los datos de la imagen indicada por $id.
	 */
	public function getById($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('slide', array('id' => $id));
		return $query->row();
	}

	/**
	 * update
	 * Actualiza el registro de la imagen usando los datos recibidos del formulario.
	 */
	public function update($id, $data)
	{
		$this->db->where('id', $id);
		// Devuelve TRUE en caso de éxito.
		return $this->db->update('slide', $data);
	}
}