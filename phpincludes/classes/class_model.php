<?php
namespace Hautliebe;

use Contentomat\DBCex;
use \Exception;

class Model {
	
	/**
	 * @var Object
	 */
	protected $db;

	/**
	 * @var String
	 */
	protected $tableName;

	/**
	 * @var Array
	 */
	protected $fields;

	/**
	 * @var Array
	 */
	protected $filter;

	/**
	 * @var Array
	 */
	protected $order;

	
	/**
	 * @var Int
	 */
	protected $limit;


	public function __construct () {
		$this->db = new DBCex ();
		$this->fields = [];
		$this->filter = [];
		$this->order = [];
		$this->limit = -1;
		$this->init ();
	}

	public function init () {
	}

	public function order ($order = []) {
		$this->order = $order;
		return $this;
	}

	public function filter ($filter = []) {
		$this->filter = $filter;
		return $this;
	}

	public function limit ($limit = -1) {
		$this->limit = $limit;
		return $this;
	}

	public function fields ($fields = []) {
		$this->fields = $fields;
		return $this;
	}

	public function findAll () {
		$query = sprintf ("SELECT %s FROM %s %s %s %s",
			empty ($this->fields) ? "*" : join (',', $this->fields),
			$this->tableName,
			empty ($this->filter) ? "" : "WHERE " . $this->getFilterString (),
			empty ($this->order) ? "" : "ORDER BY " . $this->getOrderString (),
			$this->limit == -1 ? "" : sprintf ("LIMIT %u", $this->limit)
		);
		if ($this->db->query ($query) != 0) {
			throw new Exception ("Query failed: " . $query);
		}
		$results = $this->db->getAll ();

		return $this->afterRead ($results);
	}

	public function findOne () {
		$query = sprintf ("SELECT %s FROM %s %s %s LIMIT 1",
			empty ($this->fields) ? "*" : join (',', $this->fields),
			$this->tableName,
			empty ($this->filter) ? "" : "WHERE " . $this->getFilterString (),
			empty ($this->order) ? "" : "ORDER BY " . $this->getOrderString ()
		);
		if ($this->db->query ($query) != 0) {
			throw new Exception ("Query failed: " . $query);
		}

		$results = $this->db->getAll ();
		if (count ($results) > 0)  {
			$results = $this->afterRead ($results);
			return array_shift($results);
		}
		else {
			return null;
		}
	}


	/**
	 * A filter is passed like this:
	 * [
	 *     'operand1' => $operand2,
	 * ]
	 */
	private function getFilterString () {

		$strings = [];

		foreach ($this->filter as $operand1 => $operand2) {
			$operator = '=';
			if (preg_match ('/^(.*)\s+([\=\!\>\<\%])\s+$/', $operand1, $matches)) {
				$operand1 = $matches[1];
				$operator = $matches[2];
			}
			if (is_string ($operand2)) {
				$operand2 = "'".$operand2."'";
			}
			$strings[] = sprintf ('%s %s %s', $operand1, $operator, $operand2);
		}

		return join (' AND ', $strings);
	}

	/**
	 * An order is passed like this:
	 * [
	 *     'field' => 'direction',
	 * ]
	 */
	private function getOrderString () {

		$strings = [];

		foreach ($this->order as $field => $direction) {
			switch (strtolower ($direction)) {
				case '<':
				case 'd':
				case 'desc':
					$direction = 'DESC';
					break;
				case '>':
				case 'a':
				case 'asc':
					$direction = 'ASC';
					break;
			}

			$strings[] = sprintf ('%s %s', $field, $direction);
		}

		return join (',', $strings);
	}

	protected function afterRead ($results) {
		return $results;
	}
}
?>
