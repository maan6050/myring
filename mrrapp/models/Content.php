<?php
/**
 * Modelo para la realización de operaciones sobre la tabla Content
 * Creado: Agosto 23, 2017
 * Modificaciones: Cristian
 */

class Content extends CI_Model
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
	 * update
	 * Método que modifica el contendo de un registro mediante el uso de su respectivo id.
	 * @param char(3) $id
	 */
	public function update($id, $content)
	{
		$this->db->set('content', $content);
		$this->db->where('id', $id);
		return $this->db->update('content');  // Devuelve TRUE en caso de éxito.
	}

	/**
	 * getAll
	 * Método que obtiene todos los registros de la tabla content.
	 */
	public function getAll()
	{
		$query = $this->db->get('content');
		return $query->result();
	}

	/**
	 * getContent
	 * Método que obtiene un registro de la tabla content de acuerdo a su id.
	 * @param char(3) $id
	 */
	public function getContent($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('content', array('id' => $id));
		return $query->row();
	}
}