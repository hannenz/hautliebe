<?php   
/**
 * app_tablebrowser.php
 * Tablebrowser: Stellt diverse Anwendungen zur Bearbeitung von Datenbanktabellen zur Verf�gung
 * 
 * @version 2009-01-14
 * @author J.Hahn <info@content-o-mat.de>
 * 
 */

// TODO: app_tablebrowser.php: Wird neue Tabelle erstellt und in default_settings.ini steht ein Wert mit ' drin, dann wird beim Registrierieren in cmt_tables eine falsche QUery erzeugt und es wird abgebrochen!
namespace Contentomat\Tablebrowser;

use Contentomat\DBCex;
use Contentomat\Parser;
use Contentomat\Table;
use Contentomat\Form;
use Contentomat\Dataformat;
use Contentomat\Div;
use Contentomat\Contentomat;

// Objekte
$db = new DBCex();
$tab = new Table();
$form = new Form();
$dformat = new dataformat();
$parser = new Parser();
$div = new Div();
$cmt = Contentomat::getContentomat();

$templatePath = CMT_TEMPLATE.'app_tablebrowser/';
$includePath = 'applications/app_tablebrowser/';
$noTemplate = false;

if (defined("CMT_SLIDER")) {
	$cmt_slider = CMT_SLIDER;
	$cmt_sessionvars = $session->GetSessionVar("cmt_sessionvars");
	$cmt_sessionvars[CMT_APPID]["cmt_slider"] = CMT_SLIDER;
	$session->SetSessionVar("cmt_sessionvars", $cmt_sessionvars);
	$session->SaveSessionVars();
	
} else {
	$cmt_sessionvars = $session->GetSessionVar("cmt_sessionvars");
	$cmt_slider = $cmt_sessionvars[CMT_APPID]["cmt_slider"];
	if ($cmt_slider == "") {
		$cmt_slider = 1;
	}
}
	


// Reiter
$slider_cells[0] = "<a href=\"".SELFURL."&cmt_slider=1\">&Uuml;bersicht</a>";
$slider_cells[1] = "<a href=\"".SELFURL."&cmt_slider=2\">Felder bearbeiten</a>";
$slider_cells[2] = "<a href=\"".SELFURL."&cmt_slider=3\">Tabellenstruktur</a>";
$slider_cells[3] = "<a href=\"".SELFURL."&cmt_slider=4\">Templates</a>";
$slider_cells[4] = "<a href=\"".SELFURL."&cmt_slider=5\">Einstellungen</a>";

$parser->setParserVar('contentSliders', $div->makeSliders($slider_cells, $cmt_slider));

switch ($cmt_slider) {
	
	//////////////////////////////
	// Slider 1: Übersicht
		
	case "1":
		include ($includePath . 'appinc_tablebrowser_overview.inc');
	break;
	
	//////////////////////////////
	// Slider 2: Einstellungen
	case "2":
		include ($includePath . 'appinc_tablebrowser_fields2.inc');
	break;
	
	//////////////////////////////
	// Slider 3: Tabellenstruktur
	case "3":
		include ($includePath . 'appinc_tablebrowser_structure.inc');
	break;

	/////////////////////////////
	// Slider 5: Templates

	case "4":
		include ($includePath . 'appinc_tablebrowser_templates.inc');		
	break;
	
	/////////////////////////////
	// Slider 5: Einstellungen

	case "5":
		include ($includePath . '/appinc_tablebrowser_settings.inc');		
	break;
		
}

// Eingebundene Skripte können durch setzen von $noTemplae = true verhindern,
// dass das Rahmentemplate geparst wird.
if (!$noTemplate) {
	$parser->setParserVar('icon', $cmt->getVar('applicationIcon'));
	$parser->setParserVar('contentInclude', $replace);
	
	// Rahmentemplate ausgeben
	$content = $parser->parseTemplate($templatePath.'app_tablebrowser.tpl');
}
?>