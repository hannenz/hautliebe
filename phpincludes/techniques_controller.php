<?php
namespace Hautliebe;

error_reporting (E_ALL & ~E_NOTICE);

use Contentomat\Debug;
use Contentomat\Contentomat;
use Contentomat\PsrAutoloader;
use Contentomat\Controller;
use Hautliebe\Technique;
use \Exception;

class TechniquesController extends Controller {

	public function init () {
		$this->Technique = new Technique ();
		$this->Technique->order([
			'technique_pos' => 'asc'
		]);
		$this->templatesPath = $this->templatesPath . 'techniques/';
	}



	public function actionDefault () {
		$techniques = $this->Technique->filter ([
			'technique_is_active' => true
		])->findAll ();

		$this->parser->setParserVar ('techniques', $techniques);
		$this->content = $this->parser->parseTemplate ($this->templatesPath . 'index.tpl');
	}
}

$autoLoad = new PsrAutoloader ();
$autoLoad->addNamespace ('Hautliebe', PATHTOWEBROOT . 'phpincludes/classes');
$ctl = new TechniquesController ();
$content = $ctl->work ();
?>
