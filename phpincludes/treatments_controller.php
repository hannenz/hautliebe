<?php
namespace Hautliebe;

error_reporting (E_ALL & ~E_NOTICE);

use Contentomat\Debug;
use Contentomat\Contentomat;
use Contentomat\PsrAutoloader;
use Contentomat\Controller;
use Hautliebe\Treatment;
use \Exception;

class TreatmentsController extends Controller {

	public function init () {
		$this->Treatment = new Treatment ();
		$this->Treatment->order([
			'treatment_pos' => 'asc'
		]);
		$this->templatesPath = $this->templatesPath . 'treatments/';
	}



	public function actionDefault () {
		$treatments = $this->Treatment->filter ([
			'treatment_is_active' => true
		])->findAll ();

		$this->parser->setParserVar ('treatments', $treatments);
		$this->content = $this->parser->parseTemplate ($this->templatesPath . 'index.tpl');
	}
}

$autoLoad = new PsrAutoloader ();
$autoLoad->addNamespace ('Hautliebe', PATHTOWEBROOT . 'phpincludes/classes');
$ctl = new TreatmentsController ();
$content = $ctl->work ();
?>
