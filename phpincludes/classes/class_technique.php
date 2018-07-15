<?php
namespace Hautliebe;

use Contentomat\Contentomat;

class Technique extends Model {

	protected $cmt; 
	
	public function init () {
		$this->tableName = 'hl_techniques';
		$this->order (['techniques_pos' => 'asc']);
		$this->cmt = Contentomat::getContentomat ();
	}


	public function afterRead ($results) {
		return $results;
	}
}
?>
