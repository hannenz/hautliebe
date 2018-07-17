<?php
namespace Hautliebe;

use Contentomat\Contentomat;

class Treatment extends Model {


	public function init () {
		$this->tableName = 'hl_treatments';
		$this->order (['treatment_pos' => 'asc']);
	}




	public function afterRead ($results) {
		foreach ($results as &$result) {
		}
		return $results;
	}
}
?>
