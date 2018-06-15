<?php
/*
 * Code-Manager-Menü
 * Letze Änderung 2014-08-28
 */
namespace Contentomat;

 	if (!defined("CMT_APPLAUNCHER")) {
 		exit();
 	}
 	
 	$cmt = Contentomat::getContentomat();
	$session = $cmt->getSession();
	
	$form = new Form();	
	$db = new DBCex();
	$parser = new Parser();
	
	$vars = array(
		'cmtShowTemplates' => ''
	);
	
	// Variablen holen
	foreach ($vars as $varname => $value) {
		if (isset($_POST[$varname])) {
			$vars[$varname] = $_POST[$varname];
			$session->setSessionVar($varname, $vars[$varname]);
		} else {
			$vars[$varname] = $session->getSessionVar($varname);
		}
	}
	extract ($vars);
	
	if ($cmtShowTemplates == '') {
		$session->deleteSessionVar('cmtShowTemplates');
		$cmtShowTemplates = '';
	}

	$parser->setParserVar('cmtShowTemplates', $cmtShowTemplates);
	$content = $parser->parseTemplate(CMT_TEMPLATE.'app_templates_objects/app_templates_objects_service.tpl');
	$query = '';

	switch ($cmtShowTemplates) {
		
		case 'used':
			$query = "WHERE id IN(select DISTINCT(t.id) FROM cmt_content_de c JOIN cmt_templates_objects t WHERE c.cmt_objecttemplate = t.id)";
			break;
			
		case 'unused':
			$query = "WHERE id NOT IN(select DISTINCT(t.id) FROM cmt_content_de c JOIN cmt_templates_objects t WHERE c.cmt_objecttemplate = t.id)";
			break;
	}
	
	$this->cmt->setVar('cmtAddQuery', $query);

	$session->saveSessionVars();
?>