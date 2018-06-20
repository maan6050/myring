<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con los administradores.
 * Creado: Enero 12, 2017
 * Modificaciones: CZapata
 */

class User extends CI_Model
{
	private $userId;  // Identificador del administrador.

	/**
	 * __construct
	 * Método constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : NULL;
	}

	/**
	 * create
	 * Inserta el registro del usuario usando los datos recibidos del formulario y retorna el ID de dicho registro.
	 */
	public function create($data)
	{
		$this->db->insert('user', $data);
		return $this->db->insert_id();  // Devuelve el id del registro recién insertado.
	}

	/**
	 * delete
	 * Borra el registro del usuario.
	 */
	public function delete($id, $type)
	{
		return $this->db->delete('user', array('id' => $id, 'type' => $type));
	}

	/**
	 * getAll
	 * Obtiene los datos de todos los administradores en orden alfabético.
	 */
	public function getAll($type)
	{
		$this->db->select('id, name, email');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get_where('user', array('id >' => 1, 'type' => $type));
		return $query->result();
	}

	/**
	 * getByEmail
	 * Obtiene los datos del administrador indicado por $email.
	 */
	public function getByEmail($email)
	{
		$this->db->select('*');
		$query = $this->db->get_where('user', array('email' => $email));
		return $query->row();
	}

	/**
	 * getById
	 * Obtiene los datos del administrador indicado por $id.
	 */
	public function getById($id, $type)
	{
		$this->db->select('*');
		$query = $this->db->get_where('user', array('id' => $id, 'type' => $type));
		return $query->row();
	}

	/**
	 * update
	 * Actualiza el registro del usuario usando los datos recibidos del formulario.
	 */
	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('user', $data);  // Devuelve TRUE en caso de éxito.
	}

	public function getSellersUsers()
	{
		$this->db->where('type', SELLER);
	}
}