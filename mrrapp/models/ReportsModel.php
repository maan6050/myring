<?php
/**
 * Modelo para la realización de operaciones sobre las tablas de la BD relacionadas con reportes administrativos.
 * Creado: Julio 05, 2017
 * Modificaciones: Jorge Mario Romero Arroyo
 */

class ReportsModel extends CI_Model
{
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	public function getClientData($client_id)
	{
		$sql = <<<EOF
SELECT c.name, c.address, c.city, c.state, c.country, c.zip, c.phone, c.contactName
FROM client c
WHERE c.id = ?;
EOF;
		$query = $this->db->query($sql, array(
			$client_id
		));
		$result = $query->result();
		return isset($result[0]) ? $result[0] : FALSE;
	}

	public function getTransactionsInvoice($client_id, $from, $to)
	{
		$sql = <<<EOF
SELECT t3.amount_sum, c.name AS country, t3.product_name, t3.product_count, t3.profit_sum
FROM (
	SELECT t2.amount_sum, p.countryId, p.name AS product_name, t2.product_count, t2.profit_sum
	FROM (
		SELECT SUM(t1.amount) AS amount_sum, t1.productId, COUNT(t1.productId) AS product_count, SUM(t1.profit) AS profit_sum
		FROM (
			SELECT (t.amount + t.serviceCharge) AS amount, t.productId, t.profit
			FROM transaction t
			WHERE t.clientID = ?
			AND t.status = 'Success'
			AND t.created BETWEEN ? AND ?
		) AS t1
		GROUP BY t1.productId
	) AS t2
	LEFT JOIN product p ON (p.id = t2.productId)
) AS t3
LEFT JOIN country c ON (c.id = t3.countryId);
EOF;
		$query = $this->db->query($sql, array(
			$client_id,
			$from,
			$to
		));
		return $query->result();
	}

	public function getStores($seller_id=FALSE)
	{
		$seller_sql = '';
		$data = FALSE;
        if ($seller_id)
		{
            $seller_sql = 'WHERE c.userId = ?';
			$data = array(
				$seller_id
			);
		}

		$sql = <<<EOF
SELECT c.id, c.name
FROM client c
{$seller_sql}
ORDER BY c.name;
EOF;
		$query = $this->db->query($sql, $data);
		$result = $query->result();
		return isset($result[0]) ? $result : FALSE;
	}
}
