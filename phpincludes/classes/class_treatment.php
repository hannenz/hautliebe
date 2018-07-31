<?php
namespace Hautliebe;

use Contentomat\Contentomat;
use Hautliebe\Technique;

class Treatment extends Model {

	/**
	 * @var Object
	 */
	protected $Technique;


	public function init () {
		$this->tableName = 'hl_treatments';
		$this->order (['treatment_pos' => 'asc']);
		$this->Technique = new Technique;
	}




	public function afterRead ($results) {
		foreach ($results as &$result) {

			$techniques = explode (',', $result['treatment_techniques']);
			$result['techniques'] = [];
			$names = [];
			foreach ($techniques as $techniqueId) {
				$technique = $this->Technique->filter([
					'id' => $techniqueId,
					'technique_is_active' => true
				])->findOne ();
				
				$result['techniques'][] = $technique;
				$names[] = $technique['technique_name'];
			}

			$result['treatment_techniques'] = join (', ', $names);
		}

		return $results;
	}
}
?>
