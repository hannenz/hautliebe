<?php
/**
 * appinc_tablebrowser_templates.inc
 * Skript zur Einstellung der Templates für eine Tabelle oder eine Anwendung.
 * 
 * @version 2012-05-10
 */
namespace Contentomat\Tablebrowser;

use Contentomat\DBCex;
use Contentomat\Parser;
use Contentomat\Contentomat;
use Contentomat\ApplicationHandler;

	// Übergebene Variablen holen
	$default_vars = array ('action' => '', 'id' => '', 'do' => '', 'dont_use_templates' => '', 'cmt_tablename' => '',
							'overview_frame' => '', 'overview_row' => '', 'edit_entry' => '');
	
	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include (PATHTOADMIN.'includes/func_get_vars.php');

	$parser = new Parser();
	$cmt = Contentomat::getContentomat();
	$userMessage = false;

	// Tabellenauswahlliste erzeugen
	$applicationHandler = new ApplicationHandler();
	$selectTable = $applicationHandler->makeApplicationSelect($id);
	$parser->setParserVar('selectTable', $selectTable);
	
	
	// Eintrag speichern
	if ($action == 'save') {
		
		$templates = array();
		
		$templates['dont_use_templates'] = $dont_use_templates;
		$templates['overview_frame'] = $overview_frame;
		$templates['overview_row'] = $overview_row;
		$templates['edit_entry'] = $edit_entry; 

		$query = "UPDATE cmt_tables SET cmt_templates = '".$cmt->safeSerialize($templates)."' WHERE id = '".$id."'";
		
		$db->Query($query);
		if ($db->last_errorNr()) {
			$parser->setParserVar('messageType', 'error');
			$parser->setParserVar('saveErrorText', $db->getLastError());

		} else {
			$parser->setParserVar('messageType', 'success');
		}
		$action = '';
	}

	// Tabellendaten auslesen
	if ($id) {
		
		$query = "SELECT * FROM cmt_tables WHERE id = '".intval($id)."'";
		
		$db->Query($query);
		$r = $db->Get(MYSQLI_ASSOC);
		$cmt_tablename = $r['cmt_showname'];

		$templates = (array)$cmt->safeUnserialize($r['cmt_templates']);
		$parser->setMultipleParserVars($templates);
		$parser->setParserVar('showTemplates', true);

	} else {
		$userMessage = true;
		$parser->setParserVar('messageType', 'warning');
	}
	
	// Ausgeben
	$parser->setParserVar('entryID', $id);
	$parser->setParserVar('entryName', $id);
	$parser->setParserVar('userMessage', $userMessage);
		
	$replace = $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_templates.tpl');
	
	
?>