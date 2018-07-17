<?php
namespace Hautliebe;

use Contentomat\Contentomat;

class Price extends Model {

	
	public function init () {
		$this->tableName = 'hl_prices';
		$this->order (['price_pos' => 'asc']);
	}


	public function findByCategory ($categoryId) {
		return $this->filter([
			'price_is_active' => true,
			'price_treatment_category' => $categoryId
		])->findAll ();
	}


	public function afterRead ($results) {
		setlocale (LC_ALL, 'de_DE.UTF-8');
		foreach ($results as &$result) {
			$result['price_single_fmt'] = sprintf('%.0f', $result['price_single']);
			$result['price_bundle_fmt'] = sprintf('%.0f', $result['price_bundle']);
			switch ($result['price_gender']) {
				case 'male' :
					$result['price_gender_fmt'] = 'Männer';
					break;
				case 'female' :
					$result['price_gender_fmt'] = 'Frauen';
					break;
				default:
					$result['price_gender_fmt'] = 'all';
					break;
			}
		}
		return $results;
	}
}
?>
