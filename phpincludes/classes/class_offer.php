<?php
namespace Hautliebe;

use Contentomat\Contentomat;
use \Exception;

class Offer extends Model {

	
	public function init () {
		$this->tableName = 'hl_offers';
		$this->order (['offer_pos' => 'asc']);
	}


	public function findAllActive () {
		// $query = sprintf ("SELECT * FROM %s WHERE offer_is_active=1 AND (offer_begin='0000-00-000 00:00:00' OR offer_end='0000-00-00 00:00:00' OR NOW() BETWEEN offer_begin AND offer_end') ORDER BY offer_pos ASC", $this->tableName);
		$query = sprintf ("SELECT * FROM %s ORDER BY offer_pos ASC", $this->tableName);
		if ($this->db->query ($query) !== 0) {
			throw new Exception ("Query failed: " . $query);
		}
		return $this->db->getAll ();
	}


	public function afterRead ($results) {
		return $results;
	}
}
?>
