<?php
namespace Hautliebe;

error_reporting (E_ALL & ~E_NOTICE & ~E_WARNING);

use Contentomat\Debug;
use Contentomat\Contentomat;
use Contentomat\PsrAutoloader;
use Contentomat\Controller;
use Hautliebe\Price;
use Hautliebe\Offer;
use Hautliebe\Treatment;
use \Exception;

class PricesController extends Controller {

	public function init () {
		$this->Offer = new Offer ();
		$this->Price = new Price ();
		$this->Price->order([
			'price_pos' => 'asc'
		]);
		$this->Treatment = new Treatment ();
		$this->Treatment->order([
			'treatment_pos' => 'asc'
		]);
		$this->templatesPath = $this->templatesPath . 'prices/';
	}



	public function actionDefault () {

		$offers = $this->Offer->findAllActive ();
		$categories = $this->Treatment->filter ([
			'treatment_is_active' => true
		])->findAll ();

		foreach ($categories as &$category) {
			$category['prices'] = $this->Price->findByCategory ($category['id']);
		}

		$this->parser->setParserVar ('offers', $offers);
		$this->parser->setParserVar ('categories', $categories);
		$this->content = $this->parser->parseTemplate ($this->templatesPath . 'index.tpl');
	}
}

$autoLoad = new PsrAutoloader ();
$autoLoad->addNamespace ('Hautliebe', PATHTOWEBROOT . 'phpincludes/classes');
$ctl = new PricesController ();
$content = $ctl->work ();
?>
