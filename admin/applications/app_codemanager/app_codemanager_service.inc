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
	
	// Tabellen
	$query = "SELECT id, cmt_showname FROM cmt_tables WHERE cmt_type = 'table' ORDER BY cmt_showname";
	$db->Query($query);
	while ($r = $db->Get(MYSQLI_ASSOC)) {
		$table[] = $r['id'];
		$alias[] = $r['cmt_showname'];
	}
	
	$vars = array ("cmt_tablename" => "");
	
	// Variablen holen
	foreach ($vars as $varname => $value) {
		if (isset($_POST[$varname])) {
			$vars[$varname] = $_POST[$varname];
			$session->SetSessionVar($varname, $vars[$varname]);
		} else {
			$vars[$varname] = $session->GetSessionVar($varname);
		}
	}
	extract ($vars);

	// Formularfelder erstellen
	//$form_select = makeEntrySelection("cmt_tablename", $cmt_tablename, "tables", 'onChange="checkAndReload(this.value, \''.SELFURL.'\')"');
	$applicationHandler = new ApplicationHandler();
	$applicationSelect = $applicationHandler->makeApplicationSelect($cmt_tablename, 'cmt_tablename', 'table');
	
	$parser->setParserVar('selectTable', $applicationSelect);
	
	$content = $parser->parseTemplate(CMT_TEMPLATE.'app_codemanager/cmt_codemanager_select_table.tpl');
	
	// Userrechte checken: Nur Admins dürfen Systemcode ver�ndern
	if (CMT_USERTYPE != "admin") {
		$query_restriction = "AND cmt_isinternal = '0'";
	}
	
	$add_query = "WHERE cmt_tablename = '$cmt_tablename' $query_restriction";
	$add_newentrylink = 'cmt_tablename='.$cmt_tablename;
	$session->SaveSessionVars();
?>