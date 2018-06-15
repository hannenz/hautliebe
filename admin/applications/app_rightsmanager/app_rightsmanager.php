<?php   
/**
 * app_rightsmanager.php
 * Bindet die Skripte zur Nutzerverwaltung ein.
 * 
 * @version 2012-11-27
 */

namespace Contentomat\RightsManager;
use \Contentomat\CMTParser;
use \Contentomat\Contentomat;

	$cmt = Contentomat::getContentomat();
	
	// Übergebene Variablen holen
	$default_vars = array (
		'action' => '', 
		'cmt_groupname' => '', 
		'id[]' => '',
//		'cmt_slider' => '1',
		'userID' => 0,
		'userGroupID' => 0,
		'cmtSave' => 0,
		'cmtRights' => ''
	);

	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include ('includes/func_get_vars.php');

	$parser = new CMTParser();
	$tplPath = 'app_rightsmanager/';

	switch ($cmt_slider) {
		
		/*
		 * Übersicht
		 */
		default:
		case '1':
			//include ($cmt->PATHTOADMIN(). 'applications/app_rightsmanager/appinc_rightsmanager_overview.inc');
			include (PATHTOADMIN . 'applications/app_rightsmanager/appinc_rightsmanager_overview.inc');
			break;

		/*
		 * Benutzerrechte
		 */
		case '2':
			include (PATHTOADMIN . 'applications/app_rightsmanager/appinc_rightsmanager_access.inc');
			break;
	}
	
	// Die Include-Datei MUSS die Inhalte in der Variablen $contentInclude übergeben
	$parser->setParserVar('contentInclude', $contentInclude);
	$replace .= $parser->parseTemplate($tplPath . 'cmt_rightsmanager_frame.tpl');

?>