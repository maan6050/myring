<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con las noticias.
 * Creado: Junio 16, 2017
 * Modificaciones: CZapata
 */

class News extends CI_Model
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
	 * Inserta el registro de la noticia usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('news', $data);
		// Devuelve el id del registro recién insertado.
		return $this->db->insert_id();
	}

	/**
	 * delete
	 * Borra el registro de la noticia.
	 */
	public function delete($id)
	{
		return $this->db->delete('news', array('id' => $id));
	}

	/**
	 * getAll
	 * Obtiene los datos de todas las noticias en orden cronológico inverso.
	 */
	public function getAll($search = '')
	{
		$this->db->select('*');
		if($search != '')
		{
			// Buscar noticia por título o contenido.
			$this->db->like('title', $search);
			$this->db->or_like('content', $search);
		}
		$this->db->order_by('id', 'desc');
		$this->db->limit(30);
		$query = $this->db->get('news');
		return $query->result();
	}

	/**
	 * getById
	 * Obtiene los datos de la noticia indicada por $id.
	 */
	public function getById($id)
	{
		$this->db->select('*');
		$query = $this->db->get_where('news', array('id' => $id));
		return $query->row();
	}

	/**
	 * update
	 * Actualiza el registro de la noticia usando los datos recibidos del formulario.
	 */
	public function update($id, $data)
	{
		$this->db->where('id', $id);
		// Devuelve TRUE en caso de éxito.
		return $this->db->update('news', $data);
	}
}