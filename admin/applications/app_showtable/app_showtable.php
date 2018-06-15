<?php
/**
 * app_showtable - Datenbanktabellenausgabe
 * Standard-Anwendung, die Datenbanktabellen ausgibt.
 * 
 * @version 2016-11-03
 * @author J.Hahn <info@contentomat.de>
 */
namespace Contentomat;

// Sicherheitsabfrage
if (! defined ( 'CMT_APPLAUNCHER' ))
	die ();
	
	// //////////////////////////////////
	//
	// Objekte
	//
	// //////////////////////////////////
	
//$d = new Debug();

$cmt = Contentomat::getContentomat ();
$session = $cmt->getSession ();
$db = new DBCex ();
$tab = new Table ();
$form = new Form ();
$dformat = new dataformat ();
$parser = new CMTParser ();
$template_parser = new Parser ();
$div = new Div ();
$user = new User ( $cmt->session->getSessionID () );
$gui = new Gui ();
$paging = new Paging ();
$fieldHandler = new FieldHandler ();
$applicationHandler = new ApplicationHandler ();
$evalCode = new EvalCode ();
$debug = new Debug();

$ownServiceTableData = array();

$cmt_dbtable = $cmt->getVar ( 'CMT_DBTABLE' );

// get all field informations
$cmt_fieldnames = array('id' => 'id') + $fieldHandler->getFieldNames ( $cmt_dbtable );
//  var_dump($cmt_fieldnames);
//  die();


$cmt_fields = $db->GetFieldInfo ( $cmt_dbtable );

$possibleEditActions = array (
	'new',
	'edit',
	'duplicate',
	'view',
	'view_next',
	'view_prev',
	'savenshow_next',
	'savenshow_prev',
	'deletenshow_next',
	'deletenshow_prev'
);

$possibleOverviewActions = array (
	'delete',
	'delete_multiple',
	'deleteMultiple',
	'duplicate_multiple',
	'duplicateMultiple',
	'abortEdit',
	'abortDuplicate',
	'abortView',
	'abortNew'
);

$possible_actions = array_merge ($possibleEditActions, $possibleOverviewActions);

// 	"new",
// 	"edit",
// 	"duplicate",
// 	"view",
// 	"view_next",
// 	"view_prev",
// 	"savenshow_next",
// 	"savenshow_prev",
// 	'delete',
// 	'deletenshow_next',
// 	'deletenshow_prev'
// );

$cmt_fieldaliases = array('id' => 'ID') + $fieldHandler->getFieldAliases ( $cmt_dbtable );

$cmt_fieldtypes = $fieldHandler->getFieldTypes ( $cmt_dbtable );
$cmt_fieldsformatted = array_merge ( $cmt_fieldnames, $cmt_fieldaliases );
//$cmt_fieldsformatted ['id'] = 'ID';

$appData = $cmt->getVar ( 'applicationData' ); // $applicationHandler->getApplication($cmt->getVar('CMT_APPID'));
$appID = $cmt->getVar ( 'applicationID' );

// $cmt_settings = $appData['cmt_tablesettings'];
$cmt_settings ['cmt_showname'] = $cmt->getVar ( 'applicationName' );
$cmt_templates = $appData ['cmt_templates'];
if (! is_array ( $cmt_templates )) {
	$cmt_templates = array ();
}

$cmt_executecode = $applicationHandler->getCodeToExecute ( $cmt->getVar ( 'CMT_APPID' ) );

/*
 * Pruefen, ob Tabelle ueberhaupt existiert
 */
$all_dbtables = $db->GetAllTables ();

if (! in_array ( $cmt_dbtable, $all_dbtables )) {
	$parser->setParserVar ( 'icon', $cmt_icon );
	$parser->setParserVar ( 'userMessage', $parser->parseTemplate ( 'app_showtable/cmt_table_error_not_found.tpl' ) );
	$content = $parser->parseTemplate ( 'app_showtable/cmt_table_not_found.tpl' );
	$parser->SetParserVar ( 'content', $content );
	// echo $parser->parseTemplate(CMT_TEMPLATE.'administration/cmt_applauncher.tpl');
	
	// ugly ugly workaround!
	return $parser->parseTemplate ( 'app_showtable/cmt_table_overview.tpl' );
	die ();
}

/*
 * Verfuegbare Funktionen definieren und ueberpruefen, ob der User die passenden Rechte hat. TODO: Muss hier noch ein array_merge mit den UserRights durchgefuehrt werden, damit auch selbst definierte Rechte durchegehen?
 */
$user_tablefunctions = array (
	'view' => true,
	'edit' => true,
	'duplicate' => true,
	'delete' => true 
);
foreach ( $user_tablefunctions as $func => $value ) {
	if (! $user->checkUserPermission ( $func ))
		unset ( $user_tablefunctions [$func] );
}

// //////////////////////////////////
//
// Variablen
//
// //////////////////////////////////

// Variablen holen
$default_vars = array (
		'action' => '',
		'id[]' => '',
		'cmt_pos' => 0,
		'cmt_ipp' => $cmt_settings ['show_ippnumber'],
		'launch' => '',
		'save' => '',
		'cmt_newpos' => '',
		'cmt_field' => '',
		'cmt_position' => '',
		'mysql_query' => '',
		'sort_by[]' => '',
		'search_field[]' => '',
		'search_criteria[]' => '',
		'search_link[]' => '',
		'sort_dir[]' => '',
		'search_value[]' => '',
		'cmt_returnto' => '',
		'cmt_returnto_params' => '',
		'entry_nr' => 0,
		'prev_id' => false,
		'prev_entry' => false,
		'next_id' => false,
		'next_entry' => false,
		'action_performed' => '',
		'edited_id' => '',
		'cmtDuplicatedID' => '',
		'cmt_slider' => 1,
		'suggestSearchField' => '',
		'suggestSearchValue' => '',
		'suggestSearchFieldID' => 0,
		'cmtDialog' => false 
);

// Alle Variablennamen, die als Uservars gespeichert werden sollen
$save_uservars = array (
		'sort_by[]',
		'sort_dir[]',
		'cmt_ipp' 
);

// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
$save_sessionvars = array (
		'cmt_pos',
		'cmt_ipp',
		'search_field[]',
		'search_criteria[]',
		'search_link[]',
		'sort_by[]',
		'sort_dir[]',
		'search_value[]',
		'cmt_returnto_params',
		'cmt_returnto' 
);

// Ziemlich doofes Konzept mit "scriptvars"!
$save_scriptvars = array (
	'cmt_returnto_params',
	'cmt_returnto'
);

include (PATHTOADMIN . 'includes/func_get_vars.php');

$cmt->setVar ( 'cmtEditedEntryID', $edited_id );
$cmt->setVar ( 'cmtActionPerformed', $action_performed );

// Array fuer geparste Inhalte
$parsed_content = '';
$cmt_elements = array ();

if (! $cmt_ipp) {
	$cmt_ipp = 10;
}
/*
 * $searchCriteriaWrapper = array('=' => '=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>=', 'enth&auml;lt' => 'LIKE', 'enth&auml;lt nicht' => 'NOT LIKE');
 */
// id der Tabelle gleich an Parser uebergeben
$parser->setParserVar ( 'launch', $launch );

// Flag markiert, ob Tabelle in einenm Dialogfenster aufgerufen wird
$parser->setParserVar ( 'cmtDialog', $cmtDialog );

// //////////////////////////////////
//
// Konstanten definieren
//
// //////////////////////////////////

// ??? sind diese Globalen noch sinnvoll??? $cmt_pos muss z.B. ggf. nochmal geaendert werden in der Tabellenuebersicht
define ( 'CMT_IPP', $cmt_ipp );
define ( 'CMT_POS', $cmt_pos );
define ( 'CMT_DBTABLE', $cmt_dbtable );
define ( 'PRIMARY_KEY', 'id' );

/**
 * Ajax-Aktionen: Start
 */
switch ($action) {
	
	// Standardsuchfelder in Seitenkopf
	case 'suggestSearchValue' :
		
		if (! $suggestSearchField || ! $suggestSearchValue) {
			exit ();
		}
		
		$suggestSearchField = $db->dbQuote ( $suggestSearchField );
		$suggestSearchValue = $db->dbQuote ( $suggestSearchValue );
		
		$query = "SELECT DISTINCT(" . $suggestSearchField . ") 
					  FROM " . $cmt_dbtable . " 
					  WHERE " . $suggestSearchField . " 
					  LIKE '%" . $suggestSearchValue . "%' 
					  ORDER BY " . $suggestSearchField . " ASC 
					  LIMIT 10";
		$db->query ( $query );
		
		// $response = $db->getAll();
		$response = array ();
		while ( $r = $db->get () ) {
			$response [] = substr ( $r [$suggestSearchField], 0, 64 );
		}

		echo json_encode ( $response );
		exit ();
		break;
	
	// Autocomplete für benutzerdefinierte Felder
	case 'getAutocompleteList' :
		
		// if (!$suggestSearchField || $suggestSearchFieldID || !$suggestSearchValue) {
		if (! $suggestSearchField || ! $suggestSearchValue) {
			exit ();
		}
		
		$fieldHandler = new FieldHandler ();
		$fieldData = $fieldHandler->getField ( array (
				'tableName' => CMT_DBTABLE,
				'fieldName' => $suggestSearchField 
		) );
		
		$suggestSearchTable = $db->dbQuote ( $fieldData ['cmt_option_string_from_table'] );
		$suggestSearchField = $db->dbQuote ( $fieldData ['cmt_option_string_from_table_value_field'] );
		$suggestSearchValue = $db->dbQuote ( $suggestSearchValue );
		
		$query = "SELECT DISTINCT(" . $suggestSearchField . ") 
					  FROM " . $suggestSearchTable . " 
					  WHERE " . $suggestSearchField . " 
					  LIKE '%" . $suggestSearchValue . "%' 
					  ORDER BY " . $suggestSearchField . " ASC 
					  LIMIT 10";
		$db->query ( $query );
		
		// $response = $db->getAll();
		$response = array ();
		while ( $r = $db->get () ) {
			$response [] = substr ( $r [$suggestSearchField], 0, 64 );
		}

		echo json_encode ( $response );
		exit ();
		break;
}

// //////////////////////////////////////////////////////
// Darstellungsformatierungen holen:
//
// $show_fields_query: fuer uebersicht
// $col_titles: fuer uebersicht und Bearbeitungsansicht!
// $edit_struct: fuer die Bearbeitungsansicht
//

$query = "SELECT cmt_showname, cmt_editstruct, cmt_showfields FROM cmt_tables WHERE id ='" . CMT_APPID . "'";
$db->Query ( $query );
$r = $db->Get ( MYSQLI_ASSOC );

// 1. Welche Felder in der uebersicht anzeigen?
if ($r ['cmt_showfields']) {
	$cmt_dummy1 = explode ( "\n", $r ['cmt_showfields'] );
	foreach ( $cmt_dummy1 as $fieldname ) {
		$show_fields [] = trim ( $fieldname );
	}
} else {
	// Keine Auswahl, welche Felder angezeigt werden sollen, dann alle Felder!
	$show_fields = $cmt_fieldnames;
}
unset ( $cmt_dummy1 );

// 2. Spaltentitel festlegen
$colsFormatted = array ();
$showFieldsCleaned = array ();
$colTitlesSortButtons = array ();

foreach ( $show_fields as $key => $value ) {
	
	$skipField = false;
	
	preg_match ( '/^(\{([^}]*)\})?(.*)/i', $value, $match );
	$params = explode ( ':', $match [2] );
	$makro = array_shift ( $params );
	$field = $match [3];
	
	switch ($makro) {
		// Problem: Auch hier werden im Tabellekopf Sortierauswahlpfeile angezeigt. Das ist aber Quatsch!!!
		case 'OWNCOLUMN' :
			if ($params [0]) {
				$col_titles [$field] = $params [0];
			} else {
				$col_titles [$field] = $field;
			}
			$colTitlesSortButtons [$field] = false;
			break;
		
		case 'EDITROW' :
			if ($params [0]) {
				$col_titles ['cmt__functions'] = $params [0];
			} else {
				$col_titles ['cmt__functions'] = '&nbsp;';
			}
			$colTitlesSortButtons [$field] = false;
			$skipField = true;
			break;
		
		case 'SHOWONLYADMIN' :
			if (CMT_USERTYPE == 'admin') {
				$col_titles [$field] = $cmt_fieldsformatted [$field];
				$colTitlesSortButtons [$field] = true;
			} else {
				$skipField = true;
			}
			break;
		
		case 'SHOWONLYGROUP' :
			// Admins sehen alles!
			if (CMT_USERTYPE == 'admin') {
				$col_titles [$field] = $cmt_fieldsformatted [$field];
				$colTitlesSortButtons [$field] = true;
				break;
			} else {
				$groups = explode ( ',', $params [0] );
				if (in_array ( CMT_GROUPID, $groups )) {
					$col_titles [$field] = $cmt_fieldsformatted [$field];
					$colTitlesSortButtons [$field] = true;
				} else {
					$skipField = true;
				}
			}
			
			break;
		
		case 'SHOWONLYUSER' :
			// Admins sehen alles!
			if (CMT_USERTYPE == 'admin') {
				$col_titles [$field] = $cmt_fieldsformatted [$field];
				$colTitlesSortButtons [$field] = true;
				break;
			} else {
				
				$users = explode ( ',', $params [0] );
				if (in_array ( CMT_USERID, $users )) {
					$col_titles [$field] = $cmt_fieldsformatted [$field];
					$colTitlesSortButtons [$field] = true;
				} else {
					$skipField = true;
				}
			}
			break;
		
		case 'DONTSHOWGROUP' :
			// Admins sehen alles!
			if (CMT_USERTYPE == 'admin') {
				$col_titles [$field] = $cmt_fieldsformatted [$field];
				$colTitlesSortButtons [$field] = true;
				break;
			} else {
				
				$groups = explode ( ',', $params [0] );
				if (! in_array ( CMT_GROUPID, $groups )) {
					$col_titles [$field] = $cmt_fieldsformatted [$field];
					$colTitlesSortButtons [$field] = true;
				} else {
					$skipField = true;
				}
			}
			break;
		
		case 'DONTSHOWUSER' :
			// Admins sehen alles!
			if (CMT_USERTYPE == 'admin') {
				$col_titles [$field] = $cmt_fieldsformatted [$field];
				$colTitlesSortButtons [$field] = true;
				break;
			} else {
				
				$users = explode ( ',', $params [0] );
				if (! in_array ( CMT_USERID, $users )) {
					$col_titles [$field] = $cmt_fieldsformatted [$field];
				} else {
					$skipField = true;
				}
			}
			break;
		
		/* Noch nicht implementiert! */
		case 'FORMAT' :
			$formats = explode ( ',', $params [0] );
			foreach ( $formats as $f ) {
				$fa = explode ( '=', $f );
				$colsFormatted [$field] [trim ( $fa [0] )] = trim ( $fa [1] );
			}
			$col_titles [$field] = $cmt_fieldsformatted [$field];
			$colTitlesSortButtons [$field] = true;
			break;
		
		case 'MAXCHARS' :
			$maxChars = intval ( trim ( $params [0] ) );
			if (! $maxChars)
				$maxChars = $cmt_settings ['max_chars'];
			$colsFormatted [$field] ['maxChars'] = $maxChars;
			$col_titles [$field] = $cmt_fieldsformatted [$field];
			$colTitlesSortButtons [$field] = true;
			break;
		
		case 'INCLUDE' :
			
			// very very ugly workaround to prevent double includes
			$dontIncludeActions = array (
				'edit',
				'duplicate',
				'new',
				'view',
				'savenshow_next',
				'savenshow_prev',
				'deletenshow_next',
				'deletenshow_prev' 
			);
			if (in_array ( $action, $dontIncludeActions )) {
				break;
			}
			
			eval ( '$includeFilePath = ' . $params [0] . ';' );
			$includeFile = file_get_contents ( $includeFilePath );
			$includeFile = preg_replace ( array (
					'/^\<\?(php)?/i',
					'/\?\>$/' 
			), array (
					'{EVAL}',
					'{ENDEVAL}' 
			), trim ( $includeFile ) );
			
			$replace .= $parser->parse ( $includeFile );
			
			$skipField = true;
			break;
		
		default :
			$col_titles [$field] = $cmt_fieldsformatted [$field];
			$colTitlesSortButtons [$field] = true;
			break;
	}
	
	// Letzendlich nur noch den Feldnamen zurueckschreiben
	if (! $skipField) {
		$showFieldsCleaned [$key] = $field;
	}
}

// Falls keine Spalte/Position für die Funktionsknöpfe definiert wurde, dann hier noch anhängen
if (! array_key_exists ( 'cmt__functions', $col_titles )) {
	$col_titles ['cmt__functions'] = '&nbsp;';
}

// ==> Spaltentitel fertig

// Zurueckschreiben
$show_fields = $showFieldsCleaned;

// 3. Editierreihenfolge erstellen
$r ['cmt_editstruct'] = trim ( $r ['cmt_editstruct'] );
if ($r ['cmt_editstruct']) {
	$field_names_diff = $cmt_fieldnames;
	$cmt_dummy1 = explode ( "\n", $r ['cmt_editstruct'] );
	
	foreach ( $cmt_dummy1 as $value ) {
		$value = trim ( $value );
		$edit_struct [] = $value;
		$value = preg_replace ( '/\{(.*)\}/', '', $value );
		
		if (in_array ( $value, $field_names_diff )) {
			unset ( $field_names_diff [$value] );
		}
	}
	
	if (count ( $field_names_diff ) > 0) {
		$edit_struct [] = '{HEAD}Weitere Variablen';
	}
	$edit_struct = array_merge ( $edit_struct, $field_names_diff );
} else {
	// Keine Editierstruktur, dann alle Felder nacheinander editieren!
	$edit_struct = $cmt_fieldnames;
}

// }
// -> Ende Darstellungsoptionen
// //////////////////////////////////

// //////////////////////////////////
//
// Los geht's mit dem Geswitche!
//
// //////////////////////////////////
// echo $action;

/*
 * Auf Aktionen im Editiermodus pruefen
 */
switch ($action) {
	// Speichern und naechster Eintrag
	case 'savenshow_next' :
		$show_next_id = $next_id;
		$show_next_entry = $next_entry;
		$action = 'edit';
		break;
	
	// Speichern und vorheriger Eintrag
	case 'savenshow_prev' :
		$show_next_id = $prev_id;
		$show_next_entry = $prev_entry;
		$action = 'edit';
		break;
	
	// L�schen und noechster Eintrag
	case 'deletenshow_next' :
		$show_next_id = $next_id;
		$show_next_entry = $entry_nr;
		$action = 'delete';
		break;
	
	// Loeschen und vorheriger Eintrag
	case 'deletenshow_prev' :
		$show_next_id = $prev_id;
		$show_next_entry = $prev_entry;
		$action = 'delete';
		break;
}

/*
 * Auf Aktionen allgemein proefen
 */

// Hier nach edit actions und overview actions unterscheiden!
/*
 * Include file for edit entry view?
 */
if (in_array ( $action, $possibleEditActions ) && $cmt_settings ['cmt_include_edit']) {

	if (! is_file ( $cmt_settings ['cmt_include_edit'] )) {
		$parser->setParserVar ( 'messageClass', 'warning' );
		$user_message = warning ( 'Die Datei "' . $cmt_settings ['cmt_include_edit'] . '" konnte nicht eingebunden werden.' );
	} else {
		$ownservice_code = file_get_contents ( $cmt_settings ['cmt_include_edit'] );
		
		// OUTDATED??? $code_vars not defined yet!
		//$evalCode->setVars ( $code_vars );
		
		// set vars for ussage in include file the right way:
		$cmt->setVar('cmtSave', $save);
		$cmt->setVar('cmtAction', $action);
		
		// get dataset
		$db->query ("SELECT * FROM " . CMT_DBTABLE . " WHERE id = '" . intval ( $id [0] ) . "'");
		$r = $db->get();
		$cmt->setVar('cmtTableData', $r);
		$parser->db_values = $r;
		$template_parser->db_values = $r;
		
		//$evalCode = new EvalCode();
		
		$ownservice_content = $evalCode->evalCode ( $ownservice_code );
		$parser->setMultipleUserVars ( $evalCode->getVars );
		$template_parser->setMultipleUserVars ( $evalCode->getVars );
		
		// Pfusch
		if (is_array($cmt->getVar('cmtTableData'))) {

			$parser->db_values = array_merge($ownServiceTableData, $cmt->getVar('cmtTableData'));
			$template_parser->db_values = array_merge($ownServiceTableData, $cmt->getVar('cmtTableData'));
			$ownServiceTableData = $cmt->getVar('cmtTableData');
		}

// 		$d->log($cmt->getVar('cmtTableData'));
		$replace .= $ownservice_content;
		
		// get vars from include script. t.b.c.
		$save = $cmt->getVar('cmtSave');
		$action = $cmt->getVar('cmtAction');

	}
}

/*
 * Include file for table overview?
 */
// eigene Include-Datei? Achtung: Rückwärtskompatibilität, früher hieß die Einstellung 'cmt_ownservice'
if ($cmt_settings ['cmt_ownservice']) {
	$cmt_settings ['cmt_include_overview'] = $cmt_settings ['cmt_ownservice'];
}
//$debug->info($cmt->getVars());

if ((!in_array($action, $possibleEditActions) && $cmt_settings ['cmt_include_overview']) || $cmt->getVar('cmtAddQuery')) { 

	$ownservice_content = '';

	// if there is an inlcude file given but it is not existing, show waring
	if ($cmt_settings['cmt_include_overview'] && !is_file($cmt_settings ['cmt_include_overview'])) {

		$parser->setParserVar ( 'messageClass', 'warning' );
		$parser->setParserVar ( 'cmtMessage', 'Die Datei "' . $cmt_settings ['cmt_include_overview'] . '" konnte nicht eingebunden werden.' );
	} else if ($cmt_settings['cmt_include_overview']) {
		$ownservice_code = file_get_contents ( $cmt_settings ['cmt_include_overview'] );

		// set vars for ussage in include file the right way:
		$cmt->setVar('cmtAction', $action);

		$ownservice_content = $evalCode->evalCode ( $ownservice_code );

		$parser->setMultipleUserVars ( $evalCode->getVars );
		$template_parser->setMultipleUserVars ( $evalCode->getVars );
	}
	
	// do following steps in all cases: wether there is an include file nor a file was included by macro in the edit structure
	
	// get vars and do funny things with them
	$add_newentrylink = $evalCode->getVar ( 'add_newentrylink' );
	if (!$add_newentrylink) {
		$evalCode->getVar('cmtAddNewEntryLink');
	}
	if (!$add_newentrylink) {
		$cmt->getVar('cmtAddNewEntryLink');
	}
		
	if ($add_newentrylink && substr ( $add_newentrylink, 0, 1 ) != "&") {
		$add_newentrylink = "&" . $add_newentrylink;
	}
	// 1. alt
	$include_add_query = $evalCode->getVar ( 'add_query' );
		
	// 2. neu
	$includeAddQuery = $evalCode->getVar ( 'addQuery' );
		
	// 3. new, object orientend
	if (! $includeAddQuery & ! $includeAddQuery) {
		$includeAddQuery = $cmt->getVar ( 'cmtAddQuery' );
	}

	// 1. alt: Include-Query verarbeiten
	if (isset ( $include_add_query )) {
		$include_add_query = trim ( preg_replace ( "/(.*)WHERE/i", "", $include_add_query ) );

		$match = preg_split ( "/LIMIT/i", $include_add_query, 2 );
		$include_limit_clause = trim ( $match [1] );
		$include_add_query = $match [0];
		// Das hier kann aber nett stimmen. Muss doch /ORDER\s*BY/ heioeemn!??
		// $match = preg_split ("/ORDER*\sBY/i", $include_add_query,2);
		$match = preg_split ( "/ORDER\s*BY/i", $include_add_query, 2 );
		$include_order_clause = trim ( $match [1] );
		$include_where_clause = trim ( $match [0] );
	}
		
	// 2. neu: Include-Query mit mehr Möglichkeiten, wird nicht nur angehängt
	if (isset ( $includeAddQuery )) {
		// Select-Teil
		preg_match ( '/^\s*SELECT(.*)FROM/is', $includeAddQuery, $match );
		$includeSelectAdd = trim ( $match [1] );

		// JOIN-Teil
		preg_match ( '/((LEFT JOIN|RIGHT JOIN|INNER JOIN|JOIN|UNION)(.*))\s*(WHERE|ORDER|LIMIT|$)/Uis', $includeAddQuery, $match );
		$includeJoinAdd = trim ( $match [1] );

		// WHERE-Teil
		preg_match ( '/WHERE(.*)(ORDER\s+BY|LIMIT|$)/Uis', $includeAddQuery, $match );
		$includeWhereAdd = trim ( $match [1] );

		// ORDER-Teil
		preg_match ( '/ORDER\s+BY\s+(.*)\s*(LIMIT|$)/Uis', $includeAddQuery, $match );
		$includeOrderAdd = trim ( $match [1] );
	}


	// get vars from include script. t.b.c.
	$action = $cmt->getVar('cmtAction');
}

switch ($action) {
	
	// ////////////////////////////////////////////////////////////////////
	// 1. Eintrag bearbeiten: bearbeiten, neu oder duplizieren
	// ////////////////////////////////////////////////////////////////////
	case 'duplicate' :
	case 'new' :
	case 'edit' :
		// Bearbeitungsrechte gesetzt?
		if (! $user->checkUserPermission ( $action )) {
			$user_message = error ( 'Sie sind nicht berechtigt, diese Aktion durchzuf&uuml;hren!' );
			unset ( $action );
			break;
		}
		
		$current_rows = $session->GetSessionVar ( 'current_rows' );
		
		// Feldinformationen holen
		$query = "SELECT cmt_fieldname, cmt_fieldtype, cmt_default FROM cmt_fields WHERE cmt_tablename = '" . CMT_DBTABLE . "'";
		$db->Query ( $query );
		while ( $f = $db->Get ( MYSQLI_ASSOC ) ) {
			$cmt_fieldtypes [$f ['cmt_fieldname']] = $f ['cmt_fieldtype'];
			$cmt_fielddefaults [$f ['cmt_fieldname']] = $f ['cmt_default'];
		}
		
		// Eintrag speichern
		if ($save == 1) {

			$cmt_save = true;
			$cmt_saving_successfull = false;
			
			$multiple_fields = "";
			$dontshow_fields = array ();
			$vars = array ();
			$r = array ();
			
			// Rohdaten holen und an den Parser oebergeben -> Formate wie Datum oder Zeit werden nicht richtig oebergeben, da diese
			// im Formular aus mehreren Feldern bestehen!
			// print_r($parser->vars);
			foreach ( $cmt_fieldtypes as $field => $fieldtype ) {
				$parser->db_values [$field] = $_POST [$field];
			}
			
			// Alte Daten holen und verarbeiten
			$query = 'SELECT * FROM ' . $cmt_dbtable . ' WHERE id = \'' . intval ( $id [0] ) . '\'';
			$db->Query ( $query );
			$old_data = $db->Get ( MYSQLI_ASSOC );
			
			// Checken, ob Feld angezeigt wird und daher auf jeden Fall gespeichert werden muss (z.B. Flags machen da Probleme)
			$fields_to_save = array ();
			foreach ( $edit_struct as $fieldname => $fieldmakros ) {
				// Makro und Parameter ermitteln
				preg_match ( "/^(\{(.*)\})?(.*)/", $fieldmakros, $match );
				$params_all = explode ( ":", $match [2] );
				$makro = trim ( $params_all [0] );
				$field = trim ( $match [3] );
				
				// Feld speichern, wenn es hinter einem dieser erlaubten Makros steht.
				$store_makros = array (
					'FORMAT',
					'OWNHIDDEN',
					'NOEDIT',
					'SHOWONLYADMIN',
					'SHOWONLYGROUP',
					'SHOWONLYUSER',
					'DONTSHOWGROUP',
					'DONTSHOWUSER'
				);
				
				if (in_array ( $makro, $store_makros ) || ! $makro) {
					$fields_to_save [] = $field;
				}
			}
			
			foreach ( $cmt_fieldnames as $field ) {
				switch ($cmt_fieldtypes [$field]) {
					case 'date' :
						if (isset ( $_POST [$field . '_year'] ) && isset ( $_POST [$field . '_month'] ) && isset ( $_POST [$field . '_day'] )) {
							$r [$field] = $_POST [$field . '_year'] . '-' . $_POST [$field . '_month'] . '-' . $_POST [$field . '_day'];
						} else {
							$r [$field] = $old_data [$field];
						}
						break;
					
					case 'time' :
						if (isset ( $_POST [$field . '_hour'] ) && isset ( $_POST [$field . '_minute'] ) && isset ( $_POST [$field . '_second'] )) {
							$r [$field] = $_POST [$field . '_hour'] . ':' . $_POST [$field . '_minute'] . ':' . $_POST [$field . '_second'];
						} else {
							$r [$field] = $old_data [$field];
						}
						break;
					
					case 'datetime' :
						if (isset ( $_POST [$field . '_year'] ) && isset ( $_POST [$field . '_month'] ) && isset ( $_POST [$field . '_day'] ) && isset ( $_POST [$field . '_hour'] ) && isset ( $_POST [$field . '_minute'] ) && isset ( $_POST [$field . '_second'] )) {
							$r [$field] = $_POST [$field . '_year'] . '-' . $_POST [$field . '_month'] . '-' . $_POST [$field . '_day'];
							$r [$field] .= ' ' . $_POST [$field . '_hour'] . ':' . $_POST [$field . '_minute'] . ':' . $_POST [$field . '_second'];
						} else {
							$r [$field] = $old_data [$field];
						}
						break;
					
					case 'flag' :
						if (in_array ( $field, $fields_to_save )) {
							$r [$field] = trim ( $_POST [$field] );
						} else {
							$r [$field] = $old_data [$field];
						}
						break;
					
					case 'upload' :
						
						// Dateidetails auslesen
						$file_details = $_FILES [$field . '_newfile'];
						$file_source = $file_details ['tmp_name'];
						$file_name = $file_details ['name'];
						$file_size = $file_details ['size'];
						$file_type = $file_details ['type'];
						
						$f_ext = explode ( '.', $file_name );
						if (is_array ( $f_ext )) {
							$file_ext = array_pop ( $f_ext );
						}
						
						// Zur Sicherheit Feldetails erst hier ermitteln, nicht in Formular einbetten
						// Feldeinstellungen holen
						$query = "SELECT cmt_fieldquery, cmt_options FROM cmt_fields WHERE cmt_fieldname = '" . $field . "' AND cmt_tablename = '" . $cmt_dbtable . "'";
						$db->Query ( $query );
						$fr = $db->Get ( MYSQLI_ASSOC );
						$upload_vars = Contentomat::safeUnserialize ( $fr ['cmt_options'] );

						if (!is_array($upload_vars)) {
							$upload_vars = array();
						}
						
						// Anweisungen zur Dateibehandlung
						$cmt_deleteoldfile = $upload_vars ['deleteoldfile']; // alte Datei ($cmt_value) im Zielordner nach Upload loeschen
						$cmt_copynewfile = true; // neue Datei in Zielordner kopieren
						$cmt_abortupload = false; // damit der Upload vom geparsten Skript abgebrochen werden kann
						
						/*
						 * Haupt- Upload-Pfad errechnen: Falls es sich um einen absoluten Pfad handelt wird der belassen: /srv/www/website/ -> /srv/www/website/ Ansonsten wird der Pfad als relativer Pfad angesehen und der Pfad zur ROOT-Ebene vorangestellt downloads/fotos/ -> ../downloads/fotos/
						 */
						if (preg_match ( '/^\//', $upload_vars ['dir'] )) {
							$cmt_uploaddirectory = '/' . Contentomat::formatPath ( $upload_vars ['dir'] . '/' );
						} else {
							$cmt_uploaddirectory = Contentomat::formatPath ( ROOT . $upload_vars ['dir'] . '/' );
						}
						
						// event. gibt es einen Pfad, der aus einer anderen Tabelle geholt werden muss
						if ($fr ['cmt_fieldquery']) {
							
							$query_parts = Contentomat::safeUnserialize ( $fr ['cmt_fieldquery'] );
							$query = "SELECT " . $query_parts ['query_value'];
							$query .= " FROM " . $query_parts ['query_table'];
							$query_value = $parser->parse ( $query_parts ['query_link_target'] );
							$query .= " WHERE " . $query_parts ['query_table'] . "." . $query_parts ['query_link_source'] . " = '" . $query_value . "'";
							unset ( $possible_values );
							unset ( $qr );
							
							$db->Query ( $query );
							$qr = $db->Get ( MYSQLI_ASSOC );
							
							// Sofern vorher schon ein Standard-Rootverzeichnis festgelegt wurde, wird das User-Verzeichnis angehoengt.
							$cmt_uploaddirectory = Contentomat::formatPath ( $cmt_uploaddirectory . $qr [$query_parts ['query_value']] );
							
							unset ( $qr );
						}
						unset ( $fr );
						
						// Leerzeichen im Dateinamen entfernen
						// $file_name = str_replace (" ", "_", $file_name);
						$fileNameParts = explode ( ".", $file_name );
						$fileExtension = array_pop ( $fileNameParts );
						$fileNameBody = $cmt->makeNameWebSave ( join ( ".", $fileNameParts ) );
						$file_name = $fileNameBody . '.' . $fileExtension;
						
						// Wurde oeberhaupt etwas hochgeladen?
						$posted_filename = trim ( urldecode ( $_POST [$field] ) );
						if (! $file_source) {
							$r [$field] = $posted_filename;
							if (! $posted_filename && $cmt_deleteoldfile && $old_data [$field]) {
								// Feld leer und Einstellung auf loeschen => alte Datei loeschen
								@unlink ( $cmt_uploaddirectory . $old_data [$field] );
							}
							break;
						} else {
							$r [$field] = $file_name;
						}
						
						// ////////////////////////////////////////
						//
						// Code ausfoehren: upload_onupload
						//
						// ////////////////////////////////////////
						if ($cmt_executecode ['upload_onupload'] && $file_source) {
							$parser->evalvars ['cmt_oldfile'] = $old_data [$field];
							$parser->evalvars ['cmt_filename'] = $file_name;
							$parser->evalvars ['cmt_fieldname'] = $field;
							$parser->evalvars ['cmt_fileextension'] = $file_ext;
							$parser->evalvars ['cmt_filetype'] = $file_type;
							$parser->evalvars ['cmt_uploadpath'] = $cmt_uploaddirectory . $file_name;
							$parser->evalvars ['cmt_sourcepath'] = $file_source;
							$parser->evalvars ['cmt_filesize'] = $file_size;
							$parser->evalvars ['cmt_uploaddirectory'] = $cmt_uploaddirectory;
							$parser->evalvars ['cmt_deleteoldfile'] = true;
							$parser->evalvars ['cmt_copynewfile'] = true;
							
							if (is_array ( $old_data )) {
								$parser->db_values = array_merge ( $r, $old_data );
							} else {
								$parser->db_values = $r;
							}
							
							$replace .= $parser->parse ( $cmt_executecode ['upload_onupload'] );
							
							$file_name = $parser->evalvars ['cmt_filename'];
							$cmt_uploaddirectory = $parser->evalvars ['cmt_uploaddirectory'];
							$cmt_abortupload = $parser->evalvars ['cmt_abortupload'];
							$cmt_abortsave = $parser->evalvars ['cmt_abortsave'];
							$cmt_deleteoldfile = $parser->evalvars ['cmt_deleteoldfile'];
							$cmt_copynewfile = $parser->evalvars ['cmt_copynewfile'];
							$r = $parser->db_values;
							
							$cmt_usermessage = $parser->evalvars ['cmt_usermessage'];
						}
						
						if ($cmt_abortsave) {
							// ??????
							if ($cmt_usermessage) {
								$save_errortext .= error ( $cmt_usermessage, 0, "div" );
							}
							$cmt_save = false;
							$save = '';
							$action = 'edit';
							$cmt_executecode ['entry_onsave'] = '';
							break;
						}
						
						if ($cmt_abortupload) {
							$r [$field] = $old_data [$field];
							if ($cmt_deleteoldfile && basename ( $file_name ) != $old_data [$field]) {
								$cmt_oldfile = $cmt_uploaddirectory . $old_data [$field];
								@unlink ( $cmt_oldfile );
							}
							break;
						}
						
						// 2016-03-11: Workaround to implement new method to pass variables from codemanager include scripts
						if ($cmt->getVar('cmtCopyNewFile') !== null) {
							$cmt_copynewfile = $cmt->getVar('cmtCopyNewFile');
						}
						
						if ($cmt->getVar('cmtDeleteOldFile') !== null) {
							$cmt_deleteoldfile = $cmt->getVar('cmtDeleteOldFile');
						}
						
						if ($cmt_copynewfile && $file_source) {
							// $file_check = @copy($file_source, Contentomat::formatPath($cmt_uploaddirectory).$file_name);
							$file_check = @ copy ( $file_source, $cmt_uploaddirectory . $file_name );
							if (! $file_check) {
								/* PARSER! */
								$upload_error = "'" . $file_name . "' konnte nicht hochgeladen/gespeichert werden. Bitte &uuml;berpr&uuml;fen Sie, ob Ihre Zugriffsrechte ausreichend gesetzt sind.";
								//$upload_error .= " --> " . $cmt_uploaddirectory . $file_name;
								
								$save_errortext .= sprintf("<div class=\"cmtMessage cmtMessageError\">%s</div>", $upload_error); //error ( $upload_error, 0, "div" );
								$cmt_save = false;
								$save = '';
								$action = 'edit';
								$cmt_executecode ['entry_onsave'] = '';
								$r [$field] = $old_data [$field];
								break;
							} else {
								if ($cmt_deleteoldfile && basename ( $file_name ) != $old_data [$field]) {
									$cmt_oldfile = $cmt_uploaddirectory . $old_data [$field];
									@unlink ( $cmt_oldfile );
								}
								$r [$field] = $file_name;
							}
						} else if ($cmt_deleteoldfile && basename ( $file_name ) != $old_data [$field]) {
							$cmt_oldfile = $cmt_uploaddirectory . $old_data [$field];
							@unlink ( $cmt_oldfile );
							$r [$field] = $file_name;
						}
						unset ( $upload_vars );

						break;
					
					default :
						if (! isset ( $r [$field] )) {
							if (! isset ( $_POST [$field] ) && ! isset ( $r [$field] )) {
								$r [$field] = $old_data [$field];
							} else {
								$r [$field] = $_POST [$field];
							}
						}
						
						break;
				}
				
				// Kann man das mit der Schleife oben zusammenfassen?
				// Wenn nicht ID-Feld, dann auch in Query-String aufnehemn!
				// if ($field != "id" && isset($r[$field])) {
				if ($field != 'id') {
					switch ($cmt_fieldtypes [$field]) {
						case 'position' :
							if (isset ( $_POST [$_POST [$field . '_parent']] )) {
								$add_funcvars [$field . '_parent_value'] = $_POST [$_POST [$field . '_parent']];
							}
							$vars [$field] = $dformat->format ( CMT_DBTABLE, $field, $r [$field], 'save', $id [0], $add_funcvars );
							break;
						
						case 'integer' :
						case 'float' :
							if ($r [$field] == '') {
								$vars [$field] = 'NULL';
							} else {
								$vars [$field] = $dformat->format ( CMT_DBTABLE, $field, $r [$field], 'save', $id [0] );
							}
							break;
						
						default :
							$vars [$field] = $dformat->format ( CMT_DBTABLE, $field, $r [$field], 'save', $id [0] );
							break;
					}
				}
			}
			
			if ($action == 'edit') {
				$vars ['id'] = $id [0];
			}
			// print_r ($vars);
			// --> Daten auslesen fertig
			
			// //////////////////////////////////////////////////////
			// Code ausfoehren:
			// entry_onsave
			
			if ($cmt_executecode ['entry_onsave']) {
				$parser->evalvars ['cmt_action'] = $action;
				$parser->evalvars ['cmt_save'] = $cmt_save;
				$parser->evalvars ['cmt_editedid'] = $id [0];
				// $parser->evalvars['cmt_saving_successfull'] = $cmt_saving_successfull;
				// Hier werden formatierte Variablen in das Array foer unformatierte Variablen geschrieben . Ist das okay?
				
				// Diese Form der Variablenübergabe ist veraltet und schlecht! Übergabe mittels Contentomat-Singleton besser. nur funktioniert das derzeit nur im Parser-methode eval_user_script();
				$parser->db_values = $vars;
				// $cmt->setVars($vars);
				
				$replace .= $parser->parse ( $cmt_executecode ['entry_onsave'] );
				$cmt_save = $parser->evalvars ['cmt_save'];
				$cmt_abort = $parser->evalvars ['cmt_abort'];
				$user_message .= $parser->evalvars ['cmt_usermessage'];
				$vars = $parser->db_values;
			}
			
			// -> Ende Code ausfoehren:
			// entry_onsave
			// //////////////////////////////////////////////////////
			
			// Variablen foer Query in Form bringen
			foreach ( $vars as $key => $var ) {
				$var_string .= " " . $key . " = '" . $var . "',";
			}
			// die ($var_string);
			/* Besser: $cmt_abort? */
			// if (!$cmt_abort) {
			if ($cmt_save == true) {
				// Query bauen
				if ($action == "edit") {
					$query = "UPDATE " . CMT_DBTABLE . " SET";
					$query .= $var_string;
					$query = preg_replace ( "/,$/", " WHERE id = '" . $id [0] . "'", trim ( $query ) );
				}
				if ($action == "duplicate" || $action == "new") {
					$query = "INSERT " . CMT_DBTABLE . " SET";
					$query .= $var_string;
					$query = preg_replace ( '/,$/', '', trim ( $query ) );
				}
				
				$db->Query ( $query );
				$cmt_lastinsertedid = $db->lastInsertedId ();
				
				if ($db->Last_ErrorNr ()) {
					/* PARSER! */
					$errortext = "Konnte Eintrag nicht speichern, da ein Fehler aufgetreten ist: " . $db->Last_Error () . " - Fehler-Nr.: " . $db->Last_ErrorNr ();
					$save_errortext .= $div->DivMakeDiv ( $errortext, "editentry_error" );
					$save_error = true;
					unset ( $save );
					$session->DeleteSessionVar ( "save" );
					$session->SaveSessionVars ();
					
					$cmt_saving_successfull = false;
					
					// //////////////////////////////////////////////////////
					// Code ausfoehren:
					// entry_aftersave
					
					if ($cmt_executecode ['entry_aftersave']) {
						$parser->evalvars ['cmt_action'] = $action;
						$parser->evalvars ['cmt_editedid'] = $id [0];
						$parser->evalvars ['cmt_lastinsertedid'] = false;
						$parser->evalvars ['cmt_save'] = $cmt_save;
						$parser->evalvars ['cmt_saving_successfull'] = $cmt_saving_successfull;
						
						$parser->db_values = $vars;
						$replace .= $parser->parse ( $cmt_executecode ['entry_aftersave'] );
						// $cmt_save = $parser->evalvars['cmt_save'];
						$save_errortext .= $parser->evalvars ['cmt_usermessage'];
					}
					
					// -> Ende Code ausfoehren:
					// entry_aftersave
					// //////////////////////////////////////////////////////
				} else {
					$cmt_abort = false;
					
					unset ( $save );
					$session->DeleteSessionVar ( 'save' );
					$session->SaveSessionVars ();
					
					$cmt_saving_successfull = true;
					if ($action != 'edit' && $action != 'view') {
						$cmt_lastinsertedid = $db->lastInsertedId ();
					} else {
						$cmt_lastinsertedid = false;
					}
					// //////////////////////////////////////////////////////
					// Code ausfoehren:
					// entry_aftersave
					
					if ($cmt_executecode ['entry_aftersave']) {
						$parser->evalvars ['cmt_action'] = $action;
						$parser->evalvars ['cmt_editedid'] = $id [0];
						$parser->evalvars ['cmt_lastinsertedid'] = $cmt_lastinsertedid;
						$parser->evalvars ['cmt_save'] = $cmt_save;
						$parser->evalvars ['cmt_saving_successfull'] = $cmt_saving_successfull;
						
						if (! $vars ['id'] && $cmt_lastinsertedid) {
							$vars ['id'] = $cmt_lastinsertedid;
						}
						$parser->db_values = $vars;
						$replace .= $parser->parse ( $cmt_executecode ['entry_aftersave'] );
						$cmt_save = $parser->evalvars ['cmt_save'];
						$user_message .= $parser->evalvars ['cmt_usermessage'];
					}
					
					// -> Ende Code ausfoehren:
					// entry_aftersave
					// //////////////////////////////////////////////////////
					
					// abbrechen ermoeglichen?
					if ($cmt_abort) {
						unset ( $save );
						$save_errortext .= $user_message;
						$save_error = true;
						$r = $vars;
					} else {
						
						// ID zuroeckgeben
						if ($id [0]) {
							$editedId = $id [0];
						} else {
							$editedId = $cmt_lastinsertedid;
						}
						
						if ($cmt_returnto) {
							// $url = $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'.SELF."?sid=".SID."&launch=$cmt_returnto&cmt_pos=".CMT_POS."&ipp=".CMT_IPP;
							// $url = SELF."?sid=".SID."&launch=$cmt_returnto&cmt_pos=".CMT_POS."&ipp=".CMT_IPP.'&action_performed='.$action.'&edited_id='.$editedId.'&cmt_slider='.$cmt_slider;
							
							$url = SELF . '?sid=' . SID . '&launch=' . $cmt_returnto . '&';
							
							if ($cmt_returnto_params) {
								$url .= $cmt_returnto_params . '&';
							}
							
							$url .= 'cmt_pos=' . CMT_POS . '&ipp=' . CMT_IPP . '&action_performed=' . $action . '&edited_id=' . $editedId . '&cmtDuplicatedID=' . $cmtDuplicatedID . '&cmt_slider=' . $cmt_slider;
							
							// aus Session löschen
							$session->deleteSessionVar ( 'cmt_returnto' );
							$session->deleteSessionVar ( 'cmt_returnto_params' );
							$session->saveSessionVars ();
						} else if (isset ( $show_next_entry )) {
							// $url = $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'.SELFURL.'&launch='.$launch.'&action=edit&entry_nr='.$show_next_entry.'&id[]='.$show_next_id.'&cmt_pos='.CMT_POS.'&ipp='.CMT_IPP;
							$url = SELFURL . '&launch=' . $launch . '&action=edit&entry_nr=' . $show_next_entry . '&id[]=' . $show_next_id . '&cmt_pos=' . CMT_POS . '&ipp=' . CMT_IPP . '&edited_id=' . $editedId . '&cmt_slider=' . $cmt_slider;
						} else {
							// $url = $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'.SELFURL."&launch=".$launch."&cmt_pos=".CMT_POS."&ipp=".CMT_IPP;
							$url = SELFURL . "&launch=" . $launch . "&cmt_pos=" . CMT_POS . "&ipp=" . CMT_IPP . '&action_performed=' . $action . '&edited_id=' . $editedId . '&cmt_slider=' . $cmt_slider;
						}
						
						// header("Location: http://$url"); //."&dbtable=".urlencode($dbtable)
						header ( "Location: $url" );
						exit ();
					}
				}
			} else {
				unset ( $save );
				$save_errortext .= $user_message;
				$save_error = true;
				$r = $vars;
			}
		}
	// Eintrag nur anzeigen
	case 'view' :
		
		// Eintrag anzeigen
		if (! $save) {
			
			// ID des duplizierten Eintrags erhalten
			if ($action == 'duplicate') {
				$cmtDuplicatedID = $id [0];
				$parser->setParserVar ( 'cmtDuplicatedID', $cmtDuplicatedID );
			}
			
			// Headlines bestimmen
			$headline = array (
					'edit' => 'Eintrag bearbeiten',
					'duplicate' => 'Eintrag duplizieren',
					'new' => 'Neuer Eintrag' 
			);
			
			// Daten holen: entweder Datenbank oder Get/Post-Variablen
			if ($action != 'new' && ! $save_error) {
				$query = "SELECT * FROM " . CMT_DBTABLE . " WHERE id = '" . intval ( $id [0] ) . "'";
				$db->Query ( $query );
				$r = $db->Get ( MYSQLI_ASSOC );
			} else if (! $save_error) {
				
				// Eventuell per GET-Variable oebergebene Variablen einlesen falls neuer Eintrag
				unset ( $r );
				$r = array ();
				
				$all_fields = $db->GetFieldInfo ( CMT_DBTABLE );
				$add_fieldnames = array_flip ( array_keys ( $all_fields ['name'] ) );
				foreach ( $add_fieldnames as $key => $value ) {
					if ($_GET [$key]) {
						// Konflikt?
						if (in_array ( $key, array_keys ( $default_vars ) )) {
							$cmt_fieldname_conflict [] = $key;
						} else if (! is_array ( $_GET [$key] )) {
							$r [$key] = trim ( $_GET [$key] );
						}
					}
				}
				
				if (is_array ( $cmt_fieldname_conflict )) {
					$user_message .= error ( 'Namenskonflikt: Per GET &uuml;bergebene/s' . ' Feld/er ' . implode ( ', ', $cmt_fieldname_conflict ) . ' ist/sind ' . 'genauso benannt wie interne Variablen. Dies kann zu Fehlfunktionen f&uuml;hren!' . ' <br />Bitte benennen Sie das/die Feld/er um!' );
				}
				
				// get table data from 'own_service' include
				$r = array_merge($r, $ownServiceTableData);
			}
			
			// Aktion an Parser oebergeben
			$parser->evalvars ['cmt_action'] = $action;
			
			// Knoepfgroeoeen an Parser oebergeben
			if ($cmt_settings ['big_buttons']) {
				$parser->SetParserVar ( 'iconSize', '32' );
				$iconSize = '32';
			} else {
				$parser->SetParserVar ( 'iconSize', '24' );
				$iconSize = '24';
			}
			
			// Weitere VAriablen an Parser oebergeben
			$parser->setParserVar ( 'cmt_action', $action );
			
			// //////////////////////////////////////////////////////
			// Code ausfoehren:
			// entry_onload
			
			if ($cmt_executecode ['entry_onload']) {
				$parser->evalvars ['cmt_action'] = $action;
				$parser->db_values = $r;
				$replace .= $parser->parse ( $cmt_executecode ['entry_onload'] );
				$r = $parser->db_values;
				$cmt_abort = $parser->evalvars ['cmt_abort'];
			}
			
			// -> Ende Code ausfoehren:
			// overview_onsshow_entry
			// //////////////////////////////////////////////////////
			
			// Ausgabe abbrechen, falls $cmt_abort gesetzt
			if ($cmt_abort) {
				break;
			}
			
			if ($action != 'edit') {
				if (is_array ( $id )) {
					unset ( $id [0] );
				} else {
					unset ( $id );
				}
			}
			
			// Formulartyp: Ist Uploadfeld dabei oder normales Formular?
			if (in_array ( 'upload', $cmt_fieldtypes )) {
				$parser->setParserVar ( 'formEnctype', 'multipart/form-data' );
			} else {
				$parser->setParserVar ( 'formEnctype', 'application/x-www-form-urlencoded' );
			}
			
			// Ist ein Relation Feld auf der Seite? Dann JavascriptWindows-Skripte einbinden!
			if (in_array ( 'relation', $cmt_fieldtypes )) {
				$cmtAddCode .= $parser->parseTemplate ( 'general/appinc_relationselector/cmt_relationselector_add_html.tpl' );
			}
			// Zusoetzliche Codes an Parser oebergeben
			$parser->setParserVar ( 'cmtAddCode', $cmtAddCode );
			
			// Titel
			if ($cmt_settings ['cmt_showname']) {
				$parser->setParserVar ( 'tableTitle', $cmt_settings ['cmt_showname'] . '<span class="tableHeadlineAddText"> - ' . $headline [$action] . '</span>' );
			} else {
				$parser->setParserVar ( 'tableTitle', 'Datenmanager - ' . $headline [$action] . ' (' . CMT_DBTABLE . ')' );
			}
			// $cmt_elements['table_title'] = $app_vars['cmt_showname'];
			
			// Meldung
			$user_message .= $save_errortext; // Das hier prophylaktisch -> oendern
			$sm = trim ( $session->getSessionVar ( 'userMessage' ) );
			if ($sm != '') {
				$user_message .= $sm;
				$session->deleteSessionVar ( 'userMessage' );
				$session->saveSessionVars ();
			}
			$parser->setParserVar ( 'userMessage', $user_message );
			// $cmt_elements['user_message'] = $save_errortext;
			
			// Variable 'entry_nr' auf jeden Fall integer wegen Datenbanksicherheit
			$entry_nr = intval ( $entry_nr );
			
			// /////////////////////////////////
			// Service-Knoepfe erstellen
			
			$parser->setParserVar('entryID', (int)$id[0]);
			
			// Zuroeck-Knopf
			$button_back_link = SELF . "?sid=" . SID;
			if ($cmt_returnto && defined ( 'CMT_RETURNTO' )) {
				$button_back_link .= "&launch=" . CMT_RETURNTO;
				if ($cmt_returnto_params) {
					$button_back_link .= '&' . htmlentities ( $cmt_returnto_params );
				}
			} else {
				$button_back_link .= "&launch=" . CMT_APPID;
			}
			
			if (defined ( "CMT_IPP" ) && CMT_IPP != 0) {
				$qs_cmt_pos = floor ( $entry_nr / CMT_IPP );
			}
			$button_back_link .= '&action=abort' . ucfirst ( $action ) . '&edited_id=' . $id [0] . '&cmt_pos=' . $qs_cmt_pos . '&ipp=' . CMT_IPP . '&cmt_slider=' . $cmt_slider;
			$parser->setParserVar ( 'backURL', $button_back_link );
			$button_back = '<a href="' . $button_back_link . '"><img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_back_' . $iconSize . 'px.png" class="imageLinked"></a>';
			$parser->setParserVar ( 'backButton', $button_back );
			
			// Speicher-Knopf
			if ($action != 'view') {
				
				$parser->setParserVar ( 'saveItem', true );

				// 2014-10-11: OUTDATED?
				$button_save = $form->FormSubmit ( 'save', '', CMT_TEMPLATE . 'app_showtable/img/icon_save_' . $iconSize . 'px.png', 'class="imageLinked"' );
				$parser->setParserVar ( 'saveButton', $button_save );

				// Loesch-Knopf-URL
				$buttonDeleteLink = SELFURL . '&action=delete&id[]=' . $id [0] . '&cmt_pos=' . $cmt_pos . '&ipp=' . $cmt_ipp;
				$parser->setParserVar ( 'deletionURL', $buttonDeleteLink );
				$parser->setParserVar ( 'deleteURL', $buttonDeleteLink ); // Dies zur Sicherheit, bzw. wegen der Stringenz
				$buttonDelete = '<a href="' . $buttonDeleteLink . '"><img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_delete_' . $iconSize . 'px.png" class="imageLinked"></a>';
				$parser->setParserVar ( 'deleteButton', $buttonDelete );
			}
			
			// alt: Knopf-Teiler
			// $button_separator = '&nbsp;&nbsp;<img src="'.CMT_TEMPLATE.'img/icon_separator.gif">&nbsp;&nbsp;';
			
			// alt: Knopf-Spacer
			// $button_spacer = "&nbsp;&nbsp;";
			
			// Erste zwei Knoepfe zusammenfoegen
			// $service_field = $button_back.$button_spacer.$button_save;
			
			// Vorwoerts-/Roeckwoerts-Knoepfe und Loeschknopf nur wenn Aktion "edit" ist und Edit-Modus nicht von anderer Applikation aufgerufen wird.
			// Sinnvoll? Funktioniert noemlich auch aus anderer Applikation heraus! => CMT_RETURNTO proefen!!!

			//if ($action == 'edit' && ! defined ( 'CMT_RETURNTO' ) && $user->checkUserPermission ( 'edit' )) {
			if ($action == 'edit' && !(int)CMT_RETURNTO && $user->checkUserPermission ( 'edit' )) {
				$nav_query = $session->GetSessionVar ( 'nav_query' );
				$countQuery = $session->GetSessionVar ( 'countQuery' );
				
				// TODO: Irgendwo oeberproefen, ob der USer die Aktion oeberhaupt durchfoehren darf!
				$parser->setParserVar ( 'viewItem', true );
				$parser->setParserVar ( 'editItem', true );
				$parser->setParserVar ( 'deleteItem', true );
				
				// Gesamtanzahl der ausgesuchten Eintroege ermitteln
				$db->Query ( $countQuery );
				$totalEntriesArray = $db->Get ( MYSQLI_ASSOC );
				$totalEntries = $totalEntriesArray ['totalEntries'];
				
				// Geht's noch vorwoerts?
				$db->Query ( $nav_query . ' LIMIT ' . ($entry_nr + 1) . ', 1' );
				// echo "Vorwoerts-Query: ".$nav_query.' LIMIT '.($entry_nr + 1).', 1';
				$e = $db->Get ();
				if ($e ['id']) {
					$next_entry = $entry_nr + 1;
					$next_id = $e ['id'];
				}
				unset ( $e );
				
				// Wird das richtig berechnet?
				// Geht's noch roeckwoerts
				if ($entry_nr - 1 >= 0) {
					$db->Query ( $nav_query . ' LIMIT ' . ($entry_nr - 1) . ', 1' );
					$e = $db->Get ();
					$prev_entry = $entry_nr - 1;
					$prev_id = $e ['id'];
				}
				
				// Vor-/Zuroeck-Knoepfe erstellen
				if ($prev_id) {
					$button_prev_link = SELFURL . '&action=edit&entry_nr=' . $prev_entry . '&id[]=' . $prev_id . '&cmt_pos=' . $cmt_pos . '&ipp=' . $cmt_ipp;
					$button_prev = '<a class="cmtButton" href="' . $button_prev_link . '"><img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_showprev_' . $iconSize . 'px.png" class="imageLinked"></a>';
					
					$button_delete_prev_link = SELFURL . '&action=deletenshow_prev&prev_id=' . $prev_id . '&prev_entry=' . $prev_entry . '&entry_nr=' . $entry_nr . '&id[]=' . $id [0] . '&cmt_pos=' . CMT_POS . '&ipp=' . CMT_IPP;
					$button_delete_prev = '<a href="' . $button_delete_prev_link . '" onClick="return confirmDeletion(\'Wollen Sie diesen Eintrag wirklich l&ouml;schen?\')">' . '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_deletenprev_' . $iconSize . 'px.png" class="imageLinked"></a>';
					
					$button_save_prev = $form->FormSubmit ( 'save', '', CMT_TEMPLATE . 'app_showtable/img/icon_savenprev_' . $iconSize . 'px.png', ' onClick="setAction(this.form.name, \'savenshow_prev\')"' );
					
					$parser->setParserVar ( 'savePrevItem', true );
				} else {
					$button_prev = '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_showprev_disabled_' . $iconSize . 'px.png">';
					$button_save_prev = '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_savenprev_disabled_' . $iconSize . 'px.png">';
					$button_delete_prev = '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_deletenprev_disabled_' . $iconSize . 'px.png">';
					$parser->setParserVar ( 'savePrevItem', false );
				}
				
				// Variablen an Parser oebergeben
				$parser->setParserVar ( 'showPrevURL', $button_prev_link );
				$parser->setParserVar ( 'showPrevButton', $button_prev );
				$parser->setParserVar ( 'savePrevButton', $button_save_prev );
				$parser->setParserVar ( 'deletePrevURL', $button_delete_prev_link );
				$parser->setParserVar ( 'deletePrevButton', $button_delete_prev );
				
				if ($next_id) {
					$button_next_link = SELFURL . '&action=edit&entry_nr=' . $next_entry . '&id[]=' . $next_id . '&cmt_pos=' . CMT_POS . '&ipp=' . CMT_IPP;
					$button_next = '<a class="cmtButton" href="' . $button_next_link . '"><img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_shownext_' . $iconSize . 'px.png" class="imageLinked"></a>';
					
					$button_delete_next_link = SELFURL . '&action=deletenshow_next&next_id=' . $next_id . '&next_entry=' . $next_entry . '&entry_nr=' . $entry_nr . '&id[]=' . $id [0] . '&cmt_pos=' . CMT_POS . '&ipp=' . CMT_IPP;
					$button_delete_next = '<a href="' . $button_delete_next_link . '" onClick="return confirmDeletion(\'Wollen Sie diesen Eintrag wirklich l&ouml;schen?\')">' . '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_deletennext_' . $iconSize . 'px.png" class="imageLinked"></a>';
					
					$button_save_next = $form->FormSubmit ( 'save', '', CMT_TEMPLATE . 'app_showtable/img/icon_savennext_' . $iconSize . 'px.png', 'onClick="set_action(this.form.name, \'savenshow_next\')"' );
					
					$parser->setParserVar ( 'saveNextItem', true );
					// $cmt_elements['button_next_link'] = $button_next_link;
				} else {
					$button_next = '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_shownext_disabled_' . $iconSize . 'px.png">';
					$button_save_next = '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_savennext_disabled_' . $iconSize . 'px.png">';
					$button_delete_next = '<img src="' . CMT_TEMPLATE . 'app_showtable/img/icon_deletennext_disabled_' . $iconSize . 'px.png">';
					$parser->setParserVar ( 'saveNextItem', false );
				}
				// Variablen an Parser oebergeben
				$parser->setParserVar ( 'showNextURL', $button_next_link );
				$parser->setParserVar ( 'showNextButton', $button_next );
				$parser->setParserVar ( 'saveNextButton', $button_save_next );
				$parser->setParserVar ( 'deleteNextURL', $button_delete_next_link );
				$parser->setParserVar ( 'deleteNextButton', $button_delete_next );
			}
			
			// Anzahl der Eintroege in Variable speichern (nur im Edit Modus!)
			if ($action == 'edit') {
				$parser->setParserVar ( 'currentEntry', intval ( $entry_nr ) + 1 );
				$parser->setParserVar ( 'totalEntries', intval ( $totalEntries ) );
			}
			
			/*
			 * Service-Knopfleiste in Variable parsen
			 */
			$serviceRow = $parser->parseTemplate ( 'app_showtable/cmt_table_editentry_service.tpl' );
			
			// Ende: Service-Zeile erstellen
			// /////////////////////////////////
			
			/*
			 * Template foer Datenreihe einlesen
			 */
/* --- Hier geht's mit den Templates los! ------------------------------------------- */			 
			if ($cmt_templates ['edit_entry_row']) {
				$dataRow = file_get_contents ( $cmt_templates ['edit_entry_row'] );
			} else {
				$dataRow = $parser->getTemplate ( 'app_showtable/cmt_table_editentry_row.tpl' );
			}
			
			// Detailansicht: Hilfsvariable für Tab-IDs
			$tabCounter = 0;
			
			// ////////////////////////////////////////////
			// Felder in der Editierreihenfolge ausgeben
			
			// Parser-Variablen, die nach dem Anzeigen eines Feldes wieder zuroeckgesetzt werden moessen
			$parserVarsToUnset = array (
				'rowHead',
				'rowField',
				'rowDescription',
				'fielName'
			);
			$contentDepth = 0;
			$fieldContent = array ();
			
			foreach ( $edit_struct as $field ) {
				
				if ($cmt_abort) {
					break;
				}
				// Makro und Parameter ermitteln
				preg_match ( '/^(\{([^}]*)\})?(.*)/i', $field, $match );
				
				// Wurde style="trallala" verwendet?
				preg_match ( '/(style)\s?=\s?"([^"]*)"/i', $match [2], $styleMatch );
				if ($styleMatch [0]) {
					$params [$styleMatch [1]] = $styleMatch [2];
					$match [2] = str_replace ( $styleMatch [0], '', $match [2] );
				}
				
				// $makroParams -> 'MAKRO:parameter1, parameter2,...'
				$makroParams = explode ( ':', $match [2] );
				
				// $makro -> MAKRO
				$makro = array_shift ( $makroParams );
				
				// $params_temp -> array('parameter1=100', 'parameter2=blue',...)
				$params_temp = explode ( ',', $makroParams [0] );
				
				// $field -> 'myField'
				$field = $match [3];
				
				// Falls ein Feld nicht angezeigt werden soll, dann $skipField auf true!
				$skipField = false;
				
				// $params -> array('width' => '100%', 'style' => 'background-color: blue', ...)
				foreach ( $params_temp as $parameter ) {
					$parameter_parts = explode ( '=', trim ( $parameter ) );
					if (trim ( $parameter_parts [1] ) == '') {
						$parameter_parts [1] = $parameter_parts [0];
					}
					$params [$parameter_parts [0]] = $parameter_parts [1];
				}
				
				// ??? Wird nur noch bei 'OWNHIDDEN' zwecks Abwoertskompatibilitoet genutzt
				$params_each = $params_temp;
				
				unset ( $params_temp );
				unset ( $formfield );
				
				/*
				 * Noch zu implementieren: STARTSHOWONLYADMIN, STARTSHOWONLYGROUP, ..., ENDSHOWONLYADMIN, ENDSHOWONLYGROUP,... -> damit koennen Felderbereiche (oehnlich den Layern) definiert werden und die Felder in den Bereichen koennen mit anderen Makros formatiert werden! 'STARTSHOWONLYADMIN': if (CMT_USERTYPE != 'admin') { $skipNextFields = true; $skipField = true;} 'ENDSHOWONLYADMIN': $skipNextFields = false; $skipField = true;
				 */
				
				switch ($makro) {
					
					case 'TABSET' :
						// $replace .= '<div class="cmtTabs"><ul>{TABSETTABS}</ul>';
						$contentDepth ++;
						$skipField = true;
						break;
					
					case 'ENDTABSET' :
						// $replace = str_replace('{TABSETTABS}', $tabsetTabs, $replace);
						// $replace .= '</div>';
						
						// $tabsetTabs = '';
						$contentDepth --;
						$fieldContent [$contentDepth] .= $gui->makeTabSet ( array (
								'vars' => array (
										'tabsContent' => $tabsContent,
										'panelsContent' => $panelsContent 
								) 
						) );
						$tabsContent = '';
						$panelsContent = '';
						$skipField = true;
						
						break;
					
					case 'TAB' :
						$tabsContent .= $gui->makeTab ( array (
							'vars' => array (
									'tabTitle' => $field 
							) 
						) );
						
						$parser->alternationFlag = 0;
						$skipField = true;
						
						break;
					
					case 'ENDTAB' :
						$panelsContent .= $gui->makeTabPanel ( array (
							'vars' => array (
									'panelContent' => $fieldContent [$contentDepth] 
							) 
						) );
						
						$fieldContent [$contentDepth] = '';
						$skipField = true;
						break;
					
					case 'FORMAT' :
						// wird in der KLasse "dataformat" erledigt.
						break;
					
					case 'SHOWONLYADMIN' :
						if (CMT_USERTYPE != 'admin') {
							unset ( $params );
							$skipField = true;
						}
						break;
					
					case 'SHOWONLYGROUP' :
						// Admins sehen alles!
						if (CMT_USERTYPE == 'admin')
							break;
						
						$groups = explode ( ',', $makroParams [0] );
						if (! in_array ( CMT_GROUPID, $groups ))
							$skipField = true;
						break;
					
					case 'SHOWONLYUSER' :
						// Admins sehen alles!
						if (CMT_USERTYPE == 'admin')
							break;
						
						$users = explode ( ',', $makroParams [0] );
						if (! in_array ( CMT_USERID, $users ))
							$skipField = true;
						break;
					
					case 'DONTSHOWGROUP' :
						// Admins sehen alles!
						if (CMT_USERTYPE == 'admin')
							break;
						
						$groups = explode ( ',', $makroParams [0] );
						if (in_array ( CMT_GROUPID, $groups ))
							$skipField = true;
						break;
					
					case 'DONTSHOWUSER' :
						// Admins sehen alles!
						if (CMT_USERTYPE == 'admin')
							break;
						
						$users = explode ( ',', $makroParams [0] );
						if (in_array ( CMT_USERID, $users ))
							$skipField = true;
						break;
					
					// Layer
					case 'LAYER' :
						$contentDepth ++;
						$skipField = true;
						
						$layerStatus = $params [1];
						$layerTitle = $field;

						break;
					
					// Layer: Ende
					case 'ENDLAYER' :

						$skipField = true;
						$contentDepth --;
						$fieldContent [$contentDepth] .= $gui->makeLayer ( array (
								'vars' => array (
										'layerTitle' => $layerTitle,
										'layerContent' => $fieldContent [$contentDepth + 1],
										'layerStatus' => $layerStatus 
								) 
						) );
						$fieldContent [$contentDepth + 1] = '';
						break;
					
					// eigene Include-Datei
					case 'INCLUDE' :
						eval ( '$includeFilePath = ' . $makroParams [0] . ';' );
						$includeFile = file_get_contents ( $includeFilePath );
						$includeFile = preg_replace ( array (
								'/^\<\?(php)?/i',
								'/\?\>$/' 
						), array (
								'{EVAL}',
								'{ENDEVAL}' 
						), trim ( $includeFile ) );
						
						$fieldContent [$contentDepth] .= $parser->parse ( $includeFile );
						$skipField = true;
						break;
					
					case 'USERVAR' :
						$fieldContent [$contentDepth] .= $cmt->getVar ( $makroParams [0] );
						$skipField = true;
						break;
					
					// Headline
					case 'HEAD' :
						$fieldContent [$contentDepth] .= $div->DivMakeDiv ( $field, 'tableSubheadline' );
						unset ( $params );
						
						$skipField = true;
						break;
					
					// Eintrag nicht anzeigen
					case 'DONTSHOW' :
						if ($field == 'id') {
							$fieldContent [$contentDepth] .= $form->FormHidden ( $field . '[0]', $id [0] );
						}
						unset ( $params );
						
						$skipField = true;
						break;
					
					// Eintrag als Hidden-Feld mitliefern
					case 'HIDDEN' :
						$data = $r [$field];
						$fieldContent [$contentDepth] .= $form->FormHidden ( $field, $data );
						unset ( $params );
						
						$skipField = true;
						break;
					
					// Eintrag als Hidden-Feld mitliefern
					case 'OWNHIDDEN' :
						// Das wegen der Abwoertskompatibilitoet:
						// veraltet {OWNHIDDEN:myVar=1}
						// geoendert in {OWNHIDDEN}myVar=1
						// 2006-08-17
						if (trim ( $params_each [0] ) != '') {
							$data_temp = explode ( '=', $params_each [0] );
						} else {
							$data_temp = explode ( '=', $field );
						}
						$field = trim ( $data_temp [0] );
						
						if ($data_temp [1]) {
							$data = trim ( $data_temp [1] );
						} else {
							// $data = $parser->evalvars[trim($data_temp[0])];
							$data = $cmt->getVar ( $data_temp [0] );
						}
						$fieldContent [$contentDepth] .= $form->FormHidden ( $field, $data );
						unset ( $params );
						
						$skipField = true;
						break;
					
					// Servicezeile anzeigen
					case 'SERVICE' :
						$fieldContent [$contentDepth] .= $serviceRow;
						unset ( $params );
						$skipField = true;
						break;
					
					// Eintrag nicht bearbeitbar anzeigen
					case 'NOEDIT' :
						$data = $r [$field];
						$params ['readonly'] = 'readonly';
						break;
						
					case 'DISABLED' :
						$data = $r [$field];
						$params ['disabled'] = 'disabled';
						break;
				}
				
				if (! $skipField) {
					
					// ID-Feld?
					if ($field == 'id') {

						if ($action != 'view') {

							$addHTML = '';
							if ($params['disabled']) {
								$addHTML = ' disabled="disabled"';
							}
							
							if ($params['readonly']) {
								$addHTML = ' readonly="readonly"';
							}
							$formfield = $form->formInput ( "id[]", $id [0], 10, 0, $addHTML);
							
						} else {
							$formfield = $id[0];
						}
						$data = $id [0];
						$formhead_raw = "ID";
					} else {
						$data = $r [$field];
						$formhead_raw = $cmt_fieldsformatted [$field];
						// $formfield = $dformat->format(CMT_DBTABLE, $field, $data, $action, $id[0], $params);
						$formfield = $dformat->format ( CMT_DBTABLE, $field, $parser->protectMakros ( $data ), $action, $id [0], $params );
						
						$fielddesc_raw = $dformat->fieldDescription ();
					}
					
					// //////////////////////////////////////////////////////
					// Code ausfoehren:
					// entry_onshow_field
					
					$cmt_dontshow = false;
					$cmt_abort = false;
					
					if ($cmt_executecode ['entry_onshow_field']) {
						
						$parser->evalvars ['cmt_action'] = $action;
						
						// Variablen an Parser oebergeben
						$parser->evalvars ["cmt_fieldname"] = $field;
						$parser->evalvars ["cmt_fieldhead"] = $formhead_raw;
						$parser->evalvars ["cmt_fielddata"] = $r [$field];
						$parser->evalvars ["cmt_fielddata_formatted"] = $formfield;
						$parser->evalvars ["cmt_fielddescription"] = $fielddesc_raw;
						
						// Parsen
						$fieldContent [$contentDepth] .= $parser->parse ( $cmt_executecode ['entry_onshow_field'] );
						
						// Geparste Daten zuroeckschreiben
						$formfield = $parser->evalvars ['cmt_fielddata_formatted'];
						$r [$field] = $parser->evalvars ['cmt_fielddata'];
						$formhead = $parser->evalvars ['cmt_fieldhead'];
						$fielddesc_raw = $parser->evalvars ['cmt_fielddescription'];
						$cmt_dontshow = $parser->evalvars ['cmt_dontshow'];
						$cmt_abort = $parser->evalvars ['cmt_abort'];
					}
					
					// -> Ende Code ausfoehren:
					// entry_onshow
					// //////////////////////////////////////////////////////
					
					if ($cmt_abort) {
						break;
					}
					
					// Feldtitel erzeugen
					if ($formhead_raw) {
						$parser->setParserVar ( 'rowHead', $formhead_raw );
					}
					
					// Beschreibung erzeugen
					
					// Zeile erzeugen
					if (! $cmt_dontshow) {
						$parser->setParserVar ( 'rowHead', $formhead_raw );
						$parser->setParserVar ( 'rowField', $formfield );
						$parser->setParserVar ( 'rowDescription', $fielddesc_raw );
						$parser->setParserVar ( 'fieldName', $field );

						if (!is_array($r [$field])) {
							$r [$field] = stripslashes($r [$field]);
						}
						
						$parser->setParserVar ( $field, $parser->protectMakros ( $formfield ) );
						$parser->setParserVar ( $field . '_raw', $parser->protectMakros ( $r [$field] ) );
						$fieldContent [$contentDepth] .= $parser->protectMakros ( $parser->parse ( $dataRow ) );
					}
					
					// Felddaten foer event. Template erzeugen
					$template_parser->db_values_formatted [$field] = $formfield;
					$template_parser->db_values [$field] = $r [$field];
					$template_parser->cmt_dbtable = CMT_DBTABLE;
					
					// //////////////////////////////////////////////////////
					// Code ausfoehren:
					// entry_aftershow_field
					
					if ($cmt_executecode ['entry_aftershow_field']) {
						
						// Variabeln an Parser oebergeben
						$parser->evalvars ['cmt_action'] = $action;
						$parser->evalvars ['cmt_fieldname'] = $field;
						$parser->evalvars ['cmt_fieldhead'] = $formhead_raw;
						$parser->evalvars ['cmt_fielddata'] = $r [$field];
						$parser->evalvars ['cmt_fielddata_formatted'] = $formfield;
						$parser->evalvars ['cmt_fielddescription'] = $fielddesc_raw;
						
						// Parsen
						$fieldContent [$contentDepth] .= $parser->parse ( $cmt_executecode ['entry_aftershow_field'] );
						$cmt_abort = $parser->evalvars ['cmt_abort'];
					}
					
					// -> Ende Code ausfoehren:
					// entry_aftershow_field
					// //////////////////////////////////////////////////////
					
					// auch formatierte Felder speichern -> z.B. foer ein Rahmen-Template, das kein Reihen-Template benutzt
					$rFormatted [$field] = $formfield;
					
					// Variablen zuroecksetzen
					unset ( $params );
					unset ( $formfield );
					unset ( $fielddesc_raw );
					unset ( $fielddescription );
					unset ( $formhead );
					unset ( $formhead_raw );
					unset ( $skipField );
					
					// Parservariablen zuroecksetzen
					foreach ( $parserVarsToUnset as $v )
						$parser->unsetParserVar ( $v );
				}
				
				// Feld sofort anzeigen, wenn nicht verschachtelt
				if (! $contentDepth) {
					$replace .= $fieldContent [$contentDepth]; // $parser->parse($dataRow);
					$fieldContent [$contentDepth] = '';
				}
			}
			
			$hidden_fields = array (
				"action" => $action,
				"save" => 1,
				"cmt_dbtable" => $cmt_dbtable,
				"cmt_pos" => $cmt_pos,
				"cmt_ipp" => $cmt_ipp,
				"launch" => $launch,
				"prev_entry" => $prev_entry,
				"prev_id" => $prev_id,
				"next_entry" => $next_entry,
				"next_id" => $next_id,
				"entry_nr" => $entry_nr,
				'cmt_slider' => $cmt_slider,
				'entryID' => $id [0] 
			);
			
			// Nach Speichern zuroeck zu anderer Applikation?
			if (defined ( 'CMT_RETURNTO' )) {
				$hidden_fields ['cmt_returnto'] = CMT_RETURNTO;
				$hidden_fields ['cmt_dbtable'] = CMT_DBTABLE;
				$hidden_fields ['cmt_returnto_params'] = htmlentities ( $cmt_returnto_params );
			}
			
			$parser->setMultipleParserVars ( $hidden_fields );
			
			// //////////////////////////////////////////////////////
			// Code ausfoehren:
			// entry_oncomplete
			
			if ($cmt_executecode ['entry_oncomplete']) {
				$parser->evalvars ['cmt_action'] = $action;
				
				$parser->db_values = $r;
				$parser->db_values_formatted = $rFormatted;
				$replace .= $parser->parse ( $cmt_executecode ['entry_oncomplete'] );
			}
			
			// -> Ende Code ausfoehren:
			// entry_on_complete
			// //////////////////////////////////////////////////////
			
			/*
			 * Variablen parsern
			 */
			
			// Icon
			$parser->SetParserVar ( 'icon', $cmt_icon );
			
			// Meldung?
			$parser->SetParserVar ( 'userMessage', $user_message );
			
			// Service-Zeile
			$parser->SetParserVar ( 'serviceRow', $serviceRow );
			
			// Rahmen parsen
			if ($cmt_templates ['edit_entry']) {
				$parser->setMultipleParserVars ( $cmt_elements );
				$parser->setParserVar ( 'cmt_action', $action );
				// Hier die Pfadangabe oeberdenken: Es sollte kein fester Pfad vorgegeben sein.
				// $replace = $parser->parseTemplate(PATHTOWEBROOT.$cmt_settings['external_templates'].$cmt_templates['edit_entry']);
				// if (preg_match('/^\//', $cmt_templates['edit_entry'])) {
				// $replace = $parser->parseTemplate(PATHTOWEBROOT . $cmt_templates['edit_entry']);
				// } else {
				$replace = $parser->parseTemplate ( PATHTOWEBROOT . $cmt_templates ['edit_entry'], false );
				// }
			} else {
				$parser->setParserVar ( 'content', $replace );
				$replace = $parser->parseTemplate ( 'app_showtable/cmt_table_editentry.tpl' );
			}
		}
		break;
	
	// ////////////////////////////////////////////////////////////////////
	// 2. Eintrag loeschen
	// ////////////////////////////////////////////////////////////////////
	case 'delete' :

		// Bearbeitungsrechte gesetzt?
		if (! $user->checkUserPermission ( $action )) {
			$user_message = error ( 'Sie sind nicht berechtigt, diese Aktion durchzuf&uuml;hren!' );
			unset ( $action );
			break;
		}
		
		// Alte Daten holen zwecks Analyse und event Sicherung
		$cmt_delete = true;
		$cmt_deletion_successfull = false;
		$query = "SELECT * FROM " . CMT_DBTABLE . " WHERE id = '" . $id [0] . "'";
		$db->Query ( $query );
		$old_data = $db->Get ( MYSQLI_ASSOC );
		
		// //////////////////////////////////////////////////////
		// Code ausfoehren:
		// entry_ondelete
		
		if ($cmt_executecode ['entry_ondelete']) {
			$parser->evalvars ['cmt_action'] = $action;
			$parser->evalvars ['cmt_delete'] = $cmt_delete;
			$parser->db_values = $old_data;
			$replace .= $parser->parse ( $cmt_executecode ['entry_ondelete'] );
			$cmt_delete = $parser->evalvars ['cmt_delete'];
			$user_message = $parser->evalvars ['cmt_usermessage'];
		}
		
		// -> Ende Code ausfoehren:
		// entry_ondelete
		// //////////////////////////////////////////////////////
		
		$errorMessage = '';
		$successMessage = '';
		
		// Dann loeschen
		if ($cmt_delete == true) {
			$query = "DELETE FROM " . CMT_DBTABLE . " WHERE id = '" . $id [0] . "'";
			$db->Query ( $query );
			// echo $query;
			if ($db->Last_ErrorNr ()) {
				$errorMessage .= 'Konnte Eintrag Nr. ' . $id [0] . ' nicht l&ouml;schen, da ein Fehler aufgetreten ist: ' . $db->Last_Error () . ' - Fehler-Nr.: ' . $db->Last_ErrorNr () . '<br>';
				$cmt_deletion_successfull = false;
			} else {
				$successMessage = 'Eintrag Nr. ' . $id [0] . ' wurde gel&ouml;scht.<br>';
				$cmt_deletion_successfull = true;
				
				// Sonderfall Position.
				// 1. Ermitteln, ob es ein Positionsfeld gibt
				$query = "SELECT cmt_fieldname, cmt_options FROM cmt_fields WHERE cmt_tablename = '" . CMT_DBTABLE . "' AND cmt_fieldtype = 'position'";
				$db->Query ( $query );
				
				while ( $r = $db->Get () ) {
					// Parentfeld?
					$makros = Contentomat::safeUnserialize ( $r ['cmt_options'] );
					$parent_field = $makros ['parent'];
					$parent_value = $old_data [trim ( $parent_field )];
					
					$dformat->format_position ( $old_data [$r ['cmt_fieldname']], 'delete', $r ['cmt_fieldname'], CMT_DBTABLE, $id [0], $parent_field, $parent_value );
				}
			}
			
			// Meldungen ausgeben
			if ($errorMessage) {
				$parser->setParserVar ( 'cmtErrorMessage', $errorMessage );
				// $user_message = error($errorMessage);
			}
			if ($successMessage) {
				$parser->setParserVar ( 'cmtSuccessMessage', $successMessage );
				// $user_message = success($successMessage);
			}
		}
		unset ( $fields );
		$fields = $db->GetFieldInfo ( CMT_DBTABLE );
		$action = '';
		
		// //////////////////////////////////////////////////////
		// Code ausfoehren:
		// entry_afterdelete
		
		if ($cmt_executecode ['entry_afterdelete']) {
			$parser->evalvars ['cmt_action'] = $action;
			$parser->evalvars ['cmt_deletion_successfull'] = $cmt_deletion_successfull;
			$parser->db_values = $old_data;
			$replace .= $parser->parse ( $cmt_executecode ['entry_afterdelete'] );
			$user_message .= $parser->evalvars ['cmt_usermessage'];
		}
		
		// -> Ende Code ausfoehren:
		// entry_afterdelete
		// //////////////////////////////////////////////////////
		
		// Wurde die Editieransicht aus einer anderen Anwendung heraus gestartet? Dann dahin zuroeck.
		if ($cmt_returnto) {
			// Meldung als Sessionvariable speichern
			$session->setSessionVar ( 'userMessage', $user_message );
			$session->saveSessionVars ();
			
			$url = SELF . "?sid=" . SID . "&launch=$cmt_returnto&cmt_pos=" . CMT_POS . "&ipp=" . CMT_IPP . '&action_performed=delete&edited_id=' . $id [0];
			header ( "Location: $url" ); // ."&dbtable=".urlencode($dbtable)
			exit ();
		}
		
		// Wurde der Knopf "Speichern & noechster/vorheriger Eintrag" geklickt, dann im Editiermodus beleiben
		// die ("NExt_entry ist:".$show_next_entry);
		if (isset ( $show_next_entry )) {
			// Meldung als Sessionvariable speichern
			$session->setSessionVar ( 'userMessage', $user_message );
			$session->saveSessionVars ();
			
			// $url = formatDirectory($_SERVER['SERVER_NAME'].'/'.ADMINPATH.'/').SELF.'?sid='.SID.'&launch='.$launch.'&action=edit&entry_nr='.$show_next_entry.'&id[]='.$show_next_id.'&cmt_pos='.CMT_POS.'&ipp='.CMT_IPP;
			$url = SELF . '?sid=' . SID . '&launch=' . $launch . '&action=edit&entry_nr=' . $show_next_entry . '&id[]=' . $show_next_id . '&cmt_pos=' . CMT_POS . '&ipp=' . CMT_IPP;
			
			header ( 'Location: ' . $url );
			exit ();
		}
		
		$cmt->setVar ( 'cmtEditedEntryID', $id [0] );
		$cmt->setVar ( 'cmtActionPerformed', 'delete' );
		
		break;
	
	// ////////////////////////////////////////////////////////////////////
	// 3. Mehrere Eintroege loeschen
	// ////////////////////////////////////////////////////////////////////
	case 'deleteMultiple' :
	case 'delete_multiple' :
		// Bearbeitungsrechte gesetzt?
		if (! $user->checkUserPermission ( 'delete' )) {
			$user_message = error ( 'Sie sind nicht berechtigt, diese Aktion durchzuf&uuml;hren!' );
			unset ( $action );
			break;
		}
		
		// Gibt's oeberhaupt was zu loeschen?
		$ids_counted = array_values ( $id );
		if (! $ids_counted [0]) {
			break;
		}
		
		// Variablen foer die Meldungen
		$user_message = '';
		$deletedIDs = array ();
		$notDeletedIDs = array ();
		
		// Alte Daten holen zwecks Analyse und event Sicherung
		foreach ( $id as $single_id ) {
			$cmt_delete = true;
			$cmt_deletion_successfull = false;
			
			$query = "SELECT * FROM " . CMT_DBTABLE . " WHERE id = '" . $single_id . "'";
			$db->Query ( $query );
			$old_data = $db->Get ( MYSQLI_ASSOC );
			
			// //////////////////////////////////////////////////////
			// Code ausfoehren:
			// entry_ondelete
			
			if ($cmt_executecode ['entry_ondelete']) {
				$parser->evalvars ['cmt_action'] = $action;
				$parser->evalvars ['cmt_delete'] = $cmt_delete;
				
				$parser->db_values = $old_data;
				$replace .= $parser->parse ( $cmt_executecode ['entry_ondelete'] );
				$cmt_delete = $parser->evalvars ['cmt_delete'];
				$user_message .= $parser->evalvars ['cmt_usermessage'];
			}
			
			// -> Ende Code ausfoehren:
			// entry_ondelete
			// //////////////////////////////////////////////////////
			
			// Dann loeschen
			if ($cmt_delete == true) {
				
				$query = "DELETE FROM " . CMT_DBTABLE . " WHERE id = '$single_id'";
				$db->Query ( $query );
				
				if ($db->Last_ErrorNr ()) {
					$notDeletedIDs [] = $single_id;
					// $errorMessage .= "Konnte Eintrag Nr. $single_id nicht l&ouml;schen, da ein Fehler aufgetreten ist: ".$db->Last_Error()." - Fehler-Nr.: ".$db->Last_ErrorNr()."<br>";
				} else {
					$deletedIDs [] = $single_id;
					
					// Sonderfall Position.
					// 1. Ermitteln, ob es ein Positionsfeld gibt
					
					$query = "SELECT cmt_fieldname, cmt_options FROM cmt_fields WHERE cmt_tablename = '" . CMT_DBTABLE . "' AND cmt_fieldtype = 'position'";
					$db->Query ( $query );
					while ( $r = $db->Get () ) {
						/*
						 * BUG!!!! Das ist noch nach der alten Methode: Fehler!!!!
						 */
						preg_match ( "/\{PARENT\}([^\{]*)/", $r ['cmt_options'], $match );
						$parent_field = $match [1];
						$parent_value = $old_data [trim ( $parent_field )];
						
						$dformat->format_position ( $old_data [$r ['cmt_fieldname']], "delete", $r ['cmt_fieldname'], CMT_DBTABLE, $single_id, $parent_field, $parent_value );
					}
				}
			}
			
			// //////////////////////////////////////////////////////
			// Code ausfoehren:
			// entry_afterdelete
			
			if ($cmt_executecode ['entry_afterdelete']) {
				$parser->evalvars ['cmt_action'] = $action;
				$parser->evalvars ['cmt_deletion_successfull'] = $cmt_deletion_successfull;
				$parser->db_values = $old_data;
				$replace .= $parser->parse ( $cmt_executecode ['entry_afterdelete'] );
				$user_message .= $parser->evalvars ['cmt_usermessage'];
			}
			
			// -> Ende Code ausfoehren:
			// entry_afterdelete
			// //////////////////////////////////////////////////////
		}
		
		// Meldungen ausgeben
		$notDeletedNr = count ( $notDeletedIDs );
		$deletedNr = count ( $deletedIDs );
		
		if ($notDeletedNr >= 1) {
			$parser->setParserVar ( 'cmtErrorMessage', 'Folgende Eintr&auml;ge konnten nicht gel&ouml;scht werden: ' . implode ( ', ', $notDeletedIDs ) . '<br />' );
		}
		
		if ($deletedNr >= 1) {
			$parser->setParserVar ( 'cmtSuccessMessage', 'Folgende Eintr&auml;ge wurden gel&ouml;scht: ' . implode ( ', ', $deletedIDs ) . '<br />' );
		}
		
		unset ( $fields );
		$fields = $db->GetFieldInfo ( CMT_DBTABLE );
		$action = "";
		break;
	
	// ////////////////////////////////////////////////////////////////////
	// 4. Mehrere Eintroege duplizieren
	// ////////////////////////////////////////////////////////////////////
	case 'duplicate_multiple' :
	case 'duplicateMultiple' :
		// Bearbeitungsrechte gesetzt?
		if (! $user->checkUserPermission ( 'duplicate' )) {
			$user_message = error ( "Sie sind nicht berechtigt, diese Aktion durchzuf&uuml;hren!", 0, "div" );
			unset ( $action );
			break;
		}
		
		// Gibt's oeberhaupt was zu duplizieren?
		$ids_counted = array_values ( $id );
		if (! $ids_counted [0]) {
			break;
		}
		
		// Variablen foer Meldungen
		$errorMessage = '';
		$successMessage = '';
		$duplicatedIDs = array ();
		$notDuplicatedIDs = array ();
		
		// Alte Daten holen zwecks Analyse und event Sicherung
		foreach ( $id as $single_id ) {
			
			$query = "SELECT * FROM " . CMT_DBTABLE . " WHERE id = '" . $single_id . "'";
			$db->Query ( $query );
			$r = $db->Get ( MYSQLI_ASSOC );
			// Sonderfall Position.
			// 1. Ermitteln, ob es ein Positionsfeld gibt
			$query = "SELECT cmt_fieldname, cmt_options FROM cmt_fields WHERE cmt_tablename = '" . CMT_DBTABLE . "' AND cmt_fieldtype = 'position'";
			$db->Query ( $query );
			while ( $c = $db->Get () ) {
				/*
				 * BUG!!!! Das ist noch nach der alten Methode: Fehler!!!!
				 */
				preg_match ( "/\{PARENT\}([^\{]*)/", $c ['cmt_options'], $match );
				$parent_field = $match [1];
				$parent_value = $r [trim ( $parent_field )];
				// echo "Parentfield: ".$parent_field.", value: ".$parent_value." ".$r[$c['cmt_fieldname']];
				$dformat->format_position ( $r [$c ['cmt_fieldname']], "save", $c ['cmt_fieldname'], CMT_DBTABLE, $single_id, $parent_field, $parent_value );
			}
			unset ( $r [PRIMARY_KEY] );
			// Dann loeschen
			$query = "INSERT INTO " . CMT_DBTABLE . " SET ";
			foreach ( $r as $field => $value ) {
				$e [] = $field . " = '" . $value . "'";
			}
			$db->Query ( $query . implode ( ", ", $e ) );
			unset ( $e );
			if ($db->Last_ErrorNr ()) {
				$notDuplicatedIDs [] = $single_id;
				// $errorMessage .= "Konnte Eintrag Nr. $single_id nicht duplizieren, da ein Fehler aufgetreten ist: ".$db->Last_Error()." - Fehler-Nr.: ".$db->Last_ErrorNr()."<br>";
			} else {
				$duplicatedIDs [] = $single_id;
				// $successMessage .= "Eintrag Nr. $single_id wurde erfolgreich dupliziert.<br>";
			}
		}
		
		// Meldungen ausgeben
		$notDuplicatedNr = count ( $notDuplicatedIDs );
		$duplicatedNr = count ( $duplicatedIDs );
		$user_message = '';
		
		if ($notDuplicatedNr >= 1) {
			$parser->setParserVar ( 'cmtErrorMessage', 'Folgende Eintr&auml;ge konnten nicht dupliziert werden: ' . implode ( ', ', $notDuplicatedIDs ) . '<br />' );
		}
		
		if ($duplicatedNr >= 1) {
			$parser->setParserVar ( 'cmtSuccessMessage', 'Folgende Eintr&auml;ge wurden dupliziert: ' . implode ( ', ', $duplicatedIDs ) . '<br />' );
		}
		
		$action = '';
		break;
	
	// ////////////////////////////////////////////////////////////////////
	// 5. Position oendern
	// ////////////////////////////////////////////////////////////////////
	case "move" :
		// Bearbeitungsrechte gesetzt?
		if (! $user->checkUserPermission ( 'edit' )) {
			$parser->setParserVar ( 'cmtErrorMessage', "Sie sind nicht berechtigt, diese Aktion durchzuf&uuml;hren!" );
			unset ( $action );
			break;
		}
		
		$vars = " " . $cmt_field . " = '" . $dformat->format ( CMT_DBTABLE, $cmt_field, $cmt_newpos, "save", $id [0] ) . "', ";
		
		$query = "UPDATE " . CMT_DBTABLE . " SET";
		$query .= $vars;
		$query = preg_replace ( "/,$/", " WHERE id = '" . $id [0] . "'", trim ( $query ) );
		
		$db->Query ( $query );
		if ($db->Last_ErrorNr ()) {
			$errortext = "Konnte Eintrag nicht speichern, da ein Fehler aufgetreten ist: " . $db->Last_Error () . " - Fehler-Nr.: " . $db->Last_ErrorNr ();
			// $save_error = $tab->TableMakeRow($errortext, 0, "", "table_error");
			$parser->setParserVar ( 'cmtErrorMessage', $errortext );
			unset ( $save );
		}
		break;
}
// --> Switch Ende

// ////////////////////////////////////////////////////////////////////
//
// Tabelle anzeigen, wenn nicht edit, new oder duplicate
//
// ////////////////////////////////////////////////////////////////////

if (! in_array ( $action, $possible_actions ) || in_array($action, $possibleOverviewActions)) {
	
	// //////////////////////////////////////////////////////
	// Code ausfoehren:
	// overview_onload
	
	if ($cmt_executecode ['overview_onload']) {
		$parser->evalvars ['cmt_action'] = $action;

		$onLoadCode .= $parser->parse ( $cmt_executecode ['overview_onload'] );
		$parser->setParserVar ( 'onLoadCode', $onLoadCode );
	}
	
	// -> Ende Code ausfoehren:
	// overview_onload
	// //////////////////////////////////////////////////////
	
// 	/*
// 	 * Hier Schnittstelle: Falls keine $add_query vorhanden, dann wird eine aus den gegebenen Daten erstellt. Foer eigene Includes gilt: $add_query muss wie folgt aussehen: WHERE bedingung ORDER BY einfeld ASC, nocheinfeld DESC ALLE variablen, die in dem eingefoegten Code definiert werden, finden sich im Array $ownservice_results wieder!
// 	 */
	
// 	// eigene Include-Datei? Achtung: Rückwärtskompatibilität, früher hieß die Einstellung 'cmt_ownservice'
// 	if ($cmt_settings ['cmt_ownservice']) {
// 		$cmt_settings ['cmt_include_overview'] = $cmt_settings ['cmt_ownservice'];
// 	}
	
// 	if ($cmt_settings ['cmt_include_overview']) {
// 		if (! is_file ( $cmt_settings ['cmt_include_overview'] )) {
// 			$parser->setParserVar ( 'messageClass', 'warning' );
// 			$parser->setParserVar ( 'cmtMessage', 'Die Datei "' . $cmt_settings ['cmt_include_overview'] . '" konnte nicht eingebunden werden.' );
// 		} else {
// 			$ownservice_code = file_get_contents ( $cmt_settings ['cmt_include_overview'] );
			
// 			$ownservice_html = $evalCode->evalCode ( $ownservice_code );
// 			$add_newentrylink = $evalCode->getVar ( 'add_newentrylink' );
			
// 			if ($add_newentrylink && substr ( $add_newentrylink, 0, 1 ) != "&") {
// 				$add_newentrylink = "&" . $add_newentrylink;
// 			}
// 			// 1. alt
// 			$include_add_query = $evalCode->getVar ( 'add_query' );
			
// 			// 2. neu
// 			$includeAddQuery = $evalCode->getVar ( 'addQuery' );
			
// 			// 3. new, object orientend
// 			if (! $includeAddQuery & ! $includeAddQuery) {
// 				$includeAddQuery = $cmt->getVar ( 'cmtAddQuery' );
// 			}
			
// 			// 1. alt: Include-Query verarbeiten
// 			if (isset ( $include_add_query )) {
// 				$include_add_query = trim ( preg_replace ( "/(.*)WHERE/i", "", $include_add_query ) );
				
// 				$match = preg_split ( "/LIMIT/i", $include_add_query, 2 );
// 				$include_limit_clause = trim ( $match [1] );
// 				$include_add_query = $match [0];
// 				// Das hier kann aber nett stimmen. Muss doch /ORDER\s*BY/ heioeemn!??
// 				// $match = preg_split ("/ORDER*\sBY/i", $include_add_query,2);
// 				$match = preg_split ( "/ORDER\s*BY/i", $include_add_query, 2 );
// 				$include_order_clause = trim ( $match [1] );
// 				$include_where_clause = trim ( $match [0] );
// 			}
			
// 			// 2. neu: Include-Query mit mehr Möglichkeiten, wird nicht nur angehängt
// 			if (isset ( $includeAddQuery )) {
// 				// Select-Teil
// 				preg_match ( '/^\s*SELECT(.*)FROM/is', $includeAddQuery, $match );
// 				$includeSelectAdd = trim ( $match [1] );
				
// 				// JOIN-Teil
// 				preg_match ( '/((LEFT JOIN|RIGHT JOIN|INNER JOIN|JOIN|UNION)(.*))\s*(WHERE|ORDER|LIMIT|$)/Uis', $includeAddQuery, $match );
// 				$includeJoinAdd = trim ( $match [1] );
				
// 				// WHERE-Teil
// 				preg_match ( '/WHERE(.*)(ORDER|LIMIT|$)/Uis', $includeAddQuery, $match );
// 				$includeWhereAdd = trim ( $match [1] );
				
// 				// ORDER-Teil
// 				preg_match ( '/ORDER\s+BY\s+(.*)\s*(LIMIT|$)/Uis', $includeAddQuery, $match );
// 				$includeOrderAdd = trim ( $match [1] );
// 			}
// 		}
// 	}
	
	// -> Schnittstelle fertig
	// ///////////////////////////
	
	// ///////////////////////////
	// Query zusammenbauen
	// 0. Search- und Sort-Fields soeubern -> na ja
	$use_search_field = array();
	if (! empty ( $search_field )) {
		foreach ( $search_field as $key => $value ) {
			if ($value) {
				$use_search_field [] = $value;
				// $use_search_criteria[] = $searchCriteriaWrapper[$search_criteria[$key]];
				$use_search_criteria [] = $search_criteria [$key];
				$use_search_value [] = $search_value [$key];
				$use_search_link [] = $search_link [$key];
			}
		}
	}
	
	$use_sort_by = array();
	if (! empty ( $sort_by )) {
		foreach ( $sort_by as $key => $value ) {
			if ($value) {
				$use_sort_by [] = $value;
				$use_sort_dir [] = $sort_dir [$key];
			}
		}
	}
	
	// /////////////////////
	// 1. WHERE-Bedingung
	
	$c = count ( $use_search_field );
	for($i = 0; $i < $c; $i ++) {
		$add_where_clause .= $use_search_field [$i] . " " . $use_search_criteria [$i] . " ";
		
		if ($use_search_criteria [$i] == "LIKE" || $use_search_criteria [$i] == "NOT LIKE") {
			$add_where_clause .= "'%" . addslashes ( $use_search_value [$i] ) . "%'";
		} else if ($use_search_criteria [$i] != "IS NULL") {
			
			$add_where_clause .= "'" . addslashes ( $use_search_value [$i] ) . "'";
		}
		
		if ($i < $c - 1) {
			$add_where_clause .= " " . $use_search_link [$i] . " ";
		}
	}
	
	$add_where_clause = trim ( $add_where_clause );
	
	// Die WHERE-Bedingungen der Include-Datei hinzufoegen/ vorneanstellen
	// 1. alt
	if ($include_where_clause) {
		if ($add_where_clause) {
			$add_where_clause = $include_where_clause . " AND " . $add_where_clause;
		} else {
			$add_where_clause = $include_where_clause;
		}
	}
	
	// 2. neu
	if ($includeWhereAdd) {
		if ($add_where_clause) {
			$add_where_clause = $includeWhereAdd . " AND " . $add_where_clause;
		} else {
			$add_where_clause = $includeWhereAdd;
		}
	}
	if ($add_where_clause) {
		$add_where_clause = "WHERE " . $add_where_clause;
	}
	// -> WHERE-Bedingung fertig!
	// ///////////////////////////
	
	// ///////////////////////////
	// 2. Reihenfolge, ORDER BY
	$c = count ( $use_sort_by );
	
	if ($c) {
		foreach ( $use_sort_by as $key => $value ) {
			$add_order_clause_array [] .= trim ( $value . " " . $use_sort_dir [$key] );
		}
		$add_order_clause = trim ( implode ( ", ", $add_order_clause_array ) );
	}
	
	// ORDER BY aus Include-Datei
	// 1. alt
	if ($include_order_clause) {
		if ($add_order_clause) {
			$add_order_clause = $include_order_clause . ", " . $add_order_clause;
		} else {
			$add_order_clause = $include_order_clause;
		}
	}
	
	// 2. neu
	if ($includeOrderAdd) {
		if ($add_order_clause) {
			$add_order_clause = $includeOrderAdd . ", " . $add_order_clause;
		} else {
			$add_order_clause = $includeOrderAdd;
		}
	}
	if ($add_order_clause) {
		$add_order_clause = "ORDER BY " . $add_order_clause;
	}
	// -> Sortierreihenfolge fertig
	// ///////////////////////////////
	
	// Queryteile WHERE-Bedingung und ORDER BY-Sortierung zusammenbauen
	$add_query = trim ( $add_where_clause . " " . $add_order_clause );
	// -> Queryzusoetze fertig
	
	// Zahl der ausgesuchten Eintroege ermitteln
	$query = 'SELECT COUNT(*) FROM ' . CMT_DBTABLE . ' ' . $add_where_clause;
	$db->Query ( $query );
	$r = $db->Get ( MYSQLI_NUM );
	$current_rows = $r [0];
	
	// Foer Navi speichern
	$session->SetSessionVar ( "current_rows", $current_rows );
	$nav_query = 'SELECT id FROM ' . CMT_DBTABLE . ' ' . $add_query;
	$countQuery = 'SELECT COUNT(id) AS totalEntries FROM ' . CMT_DBTABLE . ' ' . $add_query;
	$session->SetSessionVar ( "countQuery", $countQuery );
	$session->SetSessionVar ( "nav_query", $nav_query );
	$session->SaveSessionVars ();
	
	// Gesamteintroege ermitteln
	$query = 'SELECT COUNT(*) FROM ' . CMT_DBTABLE;
	$db->Query ( $query );
	$r = $db->Get ( MYSQLI_NUM );
	$all_rows = $r [0];
	
	$pages_all = ceil ( $current_rows / $cmt_ipp );
	
	if ($cmt_pos >= $pages_all) {
		$cmt_pos = 0;
	}
	
	$show_page = $cmt_pos * $cmt_ipp;
	if ($show_page < 0) {
		$show_page = 0;
	}
	
	if ($pages_all > 1) {
		// Falls $cmt_pos noch nicht definiert ist:
		$pagingLinks = $paging->makePaging ( array (
				'totalEntries' => $current_rows,
				'pagingLinks' => 10,
				'entriesPerPage' => CMT_IPP,
				'currentPage' => $cmt_pos + 1 
		) );
		
		// Paging erzeugen
		// mehrere Seiten zurück
		foreach ( $pagingLinks ['prev'] as $pageDiff ) {
			$page_select .= '<a href="' . SELFURL . '&cmt_dbtable=' . CMT_DBTABLE . '&cmt_pos=' . ($pagingLinks ['currentPage'] - $pageDiff - 1) . '&cmt_ipp=' . CMT_IPP . '" class="selectPage">-' . $pageDiff . '</a> ';
		}
		
		// zum Anfang
		if ($pagingLinks ['totalPages'] > 1 && $pagingLinks ['currentPage'] != 1) {
			$pagingSelectFirstPage = '<a href="' . SELFURL . '&cmt_dbtable=' . CMT_DBTABLE . '&cmt_pos=0&cmt_ipp=' . CMT_IPP . '" class="selectPage selectPageIcon selectFirstPage"></a> ';
		} else {
			$pagingSelectFirstPage .= '<span class="selectPage selectPageDisabled selectPageIcon selectFirstPage"></span> ';
		}
		$parser->setParserVar ( 'pagingSelectFirstPage', $pagingSelectFirstPage );
		$page_select .= $pagingSelectFirstPage;
		
		// eine Seite zurück
		if ($pagingLinks ['prevPage']) {
			$pagingSelectPrevPage = '<a href="' . SELFURL . '&cmt_dbtable=' . CMT_DBTABLE . '&cmt_pos=' . ($pagingLinks ['prevPage'] - 1) . '&cmt_ipp=' . CMT_IPP . '" class="selectPage selectPageIcon selectPrevPage"></a> ';
		} else {
			$pagingSelectPrevPage = '<span class="selectPage selectPageDisabled selectPageIcon selectPrevPage"></span> ';
		}
		$parser->setParserVar ( 'pagingSelectPrevPage', $pagingSelectPrevPage );
		$page_select .= $pagingSelectPrevPage;
		
		// Zahlenstrahl
		foreach ( $pagingLinks ['pages'] as $pageNr ) {
			if ($pageNr == $pagingLinks ['currentPage']) {
				$page_select .= '<span class="selectCurrentPage">' . $pageNr . '</span> ';
			} else {
				$page_select .= '<a href="' . SELFURL . '&cmt_dbtable=' . CMT_DBTABLE . '&cmt_pos=' . ($pageNr - 1) . '&cmt_ipp=' . CMT_IPP . '" class="selectPage">' . $pageNr . '</a> ';
			}
		}
		
		// eine Seite vor
		if ($pagingLinks ['nextPage']) {
			$pagingSelectNextPage = '<a href="' . SELFURL . '&cmt_dbtable=' . CMT_DBTABLE . '&cmt_pos=' . ($pagingLinks ['nextPage'] - 1) . '&cmt_ipp=' . CMT_IPP . '" class="selectPage selectPageIcon selectNextPage"></a> ';
		} else {
			$pagingSelectNextPage = '<span class="selectPage selectPageDisabled selectPageIcon selectNextPage"></span> ';
		}
		$parser->setParserVar ( 'pagingSelectNextPage', $pagingSelectNextPage );
		$page_select .= $pagingSelectNextPage;
		
		// zum Ende
		if ($pagingLinks ['totalPages'] > 1 && $pagingLinks ['totalPages'] != $pagingLinks ['currentPage']) {
			$pagingSelectLastPage = '<a href="' . SELFURL . '&cmt_dbtable=' . CMT_DBTABLE . '&cmt_pos=' . ($pagingLinks ['totalPages'] - 1) . '&cmt_ipp=' . CMT_IPP . '" class="selectPage selectPageIcon selectLastPage"></a> ';
		} else {
			$pagingSelectLastPage = '<span class="selectPage selectPageDisabled selectPageIcon selectLastPage"></span> ';
		}
		$parser->setParserVar ( 'pagingSelectLastPage', $pagingSelectLastPage );
		$page_select .= $pagingSelectLastPage;
		
		// mehrere Seiten vor
		foreach ( $pagingLinks ['next'] as $pageDiff ) {
			$page_select .= '<a href="' . SELFURL . '&cmt_dbtable=' . CMT_DBTABLE . '&cmt_pos=' . ($pagingLinks ['currentPage'] + $pageDiff) . '&cmt_ipp=' . CMT_IPP . '" class="selectPage">+' . $pageDiff . '</a> ';
		}
		
		// alle Seiten als Dropdown
		$pagingSelectValues = array ();
		foreach ( $pagingLinks ['allPages'] as $pageNr ) {
			$pagingSelectValues [] = $pageNr - 1;
		}
		
		$pagingSelectAll = $form->select ( array (
				'name' => 'cmt_pos',
				'values' => $pagingSelectValues,
				'aliases' => $pagingLinks ['allPages'],
				'selected' => $pagingLinks ['currentPage'] - 1,
				'addHtml' => 'class="cmtSelectPageList" data-url="' . SELFURL . '"' 
		) );
		
		$parser->setParserVar ( 'pagingSelectAll', $pagingSelectAll );
		$page_select .= $pagingSelectAll;
		
		$parser->setParserVar ( 'pagingTotalPages', $pagingLinks ['totalPages'] );
		$parser->setParserVar ( 'pagingCurrentPage', $pagingLinks ['currentPage'] );
	}
	
	// ///////////////////
	// Service ausgeben
	// ///////////////////
	
	$cells = count ( $cmt_fields ['name'] ) + 1;
	
	// ??
	// $cmt_elements['service_start_form'] = $form->FormStart("cmt_sortform", SELFURL, "POST");
	$replace .= $cmt_elements ['service_start_form'];
	
	// Titel
	if ($cmt_settings ['cmt_showname']) {
		// $cmt_elements['table_title'] = $cmt_settings['cmt_showname']; // loeschen
		$tableTitle = $cmt_settings ['cmt_showname'];
	} else {
		// $cmt_elements['table_title'] = "&Uuml;bersicht: ".CMT_DBTABLE; // loeschen
		$tableTitle = "&Uuml;bersicht: " . CMT_DBTABLE;
	}
	$parser->SetParserVar ( 'tableTitle', $tableTitle );
	
	// Icon registrieren
	$parser->SetParserVar ( 'icon', $cmt_icon );
	
	// Meldung registrieren
	$parser->SetParserVar ( 'userMessage', $user_message );
	
	// Eigene Service-Includedatei registrieren
	$parser->SetParserVar ( 'addService', $ownservice_content );
	
	// Die Servicezeile mit Such- und Sortieroptionen
	// Sortierfelder
	if ($cmt_settings ['sort_fields']) {
		
		$sortfieldsOrdered = array_merge ( $cmt_fieldnames, $cmt_fieldaliases );
		natcasesort ( $sortfieldsOrdered );
		
		// Sortierfelder
		$sortfield_values = array_keys ( $sortfieldsOrdered ); // $cmt_fieldnames;
		$sortfield_aliases = $sortfield_values; // $cmt_fieldnames;
		
		if ($cmt_settings ['sort_aliases']) {
			$sortfield_aliases = array_values ( $sortfieldsOrdered ); // $cmt_fieldaliases;
		}
		array_unshift ( $sortfield_values, '' );
		array_unshift ( $sortfield_aliases, 'kein Feld' );
		
		for($i = 0; $i < intval ( $cmt_settings ['sort_fields'] ); $i ++) {
			$n = $i + 1;
			$sortFieldsHtml .= '<div class="sortField">';
			$sortFieldsHtml .= '<span class="serviceText">' . $n . '. Sortierfeld</span><br />';
			// $sortFieldsHtmlField = $form->FormSelect("sort_by[$n]", $sortfield_values, $sortfield_aliases, $sort_by[$n]);
			$sortFieldsHtmlField = $form->select ( array (
					'name' => 'sort_by[' . $n . ']',
					'values' => $sortfield_values,
					'aliases' => $sortfield_aliases,
					'selected' => $sort_by [$n],
					'addHtml' => 'class="cmtSelectSortField"' 
			) );
			
			$sortFieldsHtml .= $sortFieldsHtmlField;
			
			// Nur das Formularfeld ebenfalls an den Parser
			$parser->SetParserVar ( 'sortField' . $n, $sortFieldsHtmlField );
			
			$sortFieldsHtml .= '</div>';
		}
		$parser->SetParserVar ( 'sortFields', $sortFieldsHtml );
		
		// Sortierrichtung
		$cell = 0;
		// $sort_checked["asc"] = array(1,0);
		// $sort_checked["desc"] = array(0,1);
		
		$sortDirValues = array (
				'',
				'asc',
				'desc' 
		);
		
		$sortDirAliases = array (
				'keine',
				'aufsteigend',
				'absteigend' 
		);
		
		for($i = 0; $i < intval ( $cmt_settings ['sort_fields'] ); $i ++) {
			
			// TODO: Ist es sinnvoll, den kompletten Suchlayer als aktiv zu markieren, wenn nur sortiert wird?
			// if (!empty($sort_dir[$n])) {
			// $parser->setParserVar('searchActive', true);
			// }
			
			$cell = $i % 2;
			$n = $i + 1;
			
			$sortDirHtml .= '<div class="sortDirection">';
			$sortDirHtml .= '<span class="serviceText">' . $n . '. Sortierreihenfolge</span><br />';
			
			// Formularfeld "aufsteigend sortieren" als Feld und als HTML an Parser
			$sortDirHtml .= $form->select ( array (
					'values' => $sortDirValues,
					'aliases' => $sortDirAliases,
					'selected' => $sort_dir [$n],
					'name' => 'sort_dir[' . $n . ']',
					'addHtml' => 'class="cmtSelectSortDirection"' 
			) );
			
			$sortDirHtml .= '</div>';
			
			if ($cell == 1) {
				$sortDirHtml .= '<div class="clear"></div>';
			}
		}
		$parser->SetParserVar ( 'sortDirections', $sortDirHtml );
		
		// Sortierfeld und -richtung in Spaltenoeberschrift
		if (is_array ( $col_titles )) {
			foreach ( $col_titles as $field => $col ) {
				if ($colTitlesSortButtons [$field]) {
					$col_titles [$field] = '<div class="tableHeadText">' . $col . '<div class="tableHeadSortDirSelect"><a href="' . SELFURL . '&amp;sort_by[1]=' . $field . '&amp;sort_dir[1]=desc" title="absteigend sortieren"><img src="' . CMT_TEMPLATE . 'general/img/icon_desc.gif" class="imageLinked" alt="absteigend sortieren" /></a>&nbsp;<a href="' . SELFURL . '&amp;sort_by[1]=' . $field . '&amp;sort_dir[1]=asc" title="aufsteigend sortieren"><img src="' . CMT_TEMPLATE . 'general/img/icon_asc.gif" class="imageLinked" alt="aufsteigend sortieren"></a></div></div>';
				}
			}
		}
	}
	
	// Suchfelder
	if ($cmt_settings ['search_fields']) {
		
		$s = intval ( $cmt_settings ['search_fields'] );
		
		// $criteria_select = array ('=' => '=', '!=' => '!=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>=', 'LIKE' => 'enth&auml;lt', 'NOT LIKE' => 'enth&auml;lt nicht');
		$criteriaSelect = array (
				'LIKE',
				'NOT LIKE',
				'=',
				'!=',
				'<',
				'>',
				'<=',
				'>=',
				'IS NULL' 
		);
		$criteriaSelectAliases = array (
				'enth&auml;lt',
				'enth&auml;lt nicht',
				'=',
				'!=',
				'&lt;',
				'&gt;',
				'&lt;=',
				'&gt;=',
				'ist NULL' 
		);
		
		$searchlink_checked ['and'] = array (
				1,
				0 
		);
		$searchlink_checked ['or'] = array (
				0,
				1 
		);
		
		$searchfieldsOrdered = array_merge ( $cmt_fieldnames, $cmt_fieldaliases );
		natcasesort ( $searchfieldsOrdered );
		
		$searchfield_values = array_keys ( $sortfieldsOrdered ); // $cmt_fieldnames;
		$searchfield_aliases = $searchfield_values;
		
		if ($cmt_settings ['search_aliases']) {
			$searchfield_aliases = array_values ( $searchfieldsOrdered ); // $cmt_fieldaliases;
		}
		array_unshift ( $searchfield_values, '' );
		array_unshift ( $searchfield_aliases, 'kein Feld' );
		
		// oeberschrift
		$searchfieldsHtml = '<span class="serviceText">Suchriterien:</span><br>';
		
		for($i = 0; $i < $s; $i ++) {
			
			$n = $i + 1;
			
			if (! empty ( $search_field [$n] )) {
				$parser->setParserVar ( 'searchActive', true );
			}
			
			if (! $search_link [$n]) {
				$search_link [$n] = "and";
			}
			$searchfieldsHtml .= '<div class="searchField">';
			// $searchfieldsHtml .= $form->FormSelect("search_field[$n]", $searchfield_values, $searchfield_aliases, $search_field[$n], 1, 'class="cmtSelectSearchField"').'&nbsp;';
			$searchfieldsHtml .= $form->select ( array (
					'values' => $searchfield_values,
					'aliases' => $searchfield_aliases,
					'selected' => $search_field [$n],
					'name' => 'search_field[' . $n . ']',
					'id' => 'searchField_' . $n,
					'addHtml' => 'class="cmtSelectSearchField"' 
			) );
			$searchfieldsHtml .= '&nbsp;';
			
			// $searchfieldsHtml .= $form->FormSelect("search_criteria[$n]", $criteriaSelect, $criteriaSelectAliases, $search_criteria[$n], 1, 'class="cmtSelectSearchCriteria"')."&nbsp;";
			$searchfieldsHtml .= $form->select ( array (
					'values' => $criteriaSelect,
					'aliases' => $criteriaSelectAliases,
					'selected' => $search_criteria [$n],
					'name' => 'search_criteria[' . $n . ']',
					'addHtml' => 'class="cmtSelectSearchCriteria"' 
			) );
			$searchfieldsHtml .= '&nbsp;';
			
			$searchfieldsHtml .= $form->FormInput ( "search_value[$n]", $search_value [$n], 18, 0, 'class="cmtSearchValue cmtAutocomplete" data-url="' . SELFURL . '" data-field-id="searchField_' . $n . '" data-action="suggestSearchValue"' );
			$searchfieldsHtml .= '</div>';
			
			if ($s > 1 && $i < ($s - 1)) {
				$searchfieldsHtml .= '<div class="searchFieldLink">';
				$searchfieldsHtml .= '<span class="serviceText">Verkn&uuml;pfung:</span>&nbsp;' . $form->FormRadio ( "search_link[$n]", 'and', $searchlink_checked [$search_link [$n]] [0], ' id="searchFieldLinkAnd' . $n . '" ' ) . '<label for="searchFieldLinkAnd' . $n . '">UND</label>';
				$searchfieldsHtml .= $form->FormRadio ( "search_link[$n]", 'or', $searchlink_checked [$search_link [$n]] [1], ' id="searchFieldLinkOr' . $n . '" ' ) . '<label for="searchFieldLinkOr' . $n . '">ODER</label>';
				$searchfieldsHtml .= '</div>';
			}
		}
		$parser->SetParserVar ( 'searchFields', $searchfieldsHtml );
		// $cmt_elements['service_search'] = $searchfieldsHtml;
	}
	// -> Standard-Such-/Sortierfelderauswahl Ende
	
	// Knopf "Neuer Eintrag"?
	if ($cmt_settings ['add_item'] && $user->checkUserPermission ( 'new' )) {
		$newEntryImageAdd = '_24px';
		if ($cmt_settings ['big_buttons']) {
			$newEntryImageAdd = '_32px';
		}
		$newEntryLink = SELFURL . '&action=new&cmt_ipp=' . CMT_IPP . '&cmt_pos=' . CMT_POS . $add_newentrylink;
		$parser->SetParserVar ( 'newEntryLink', $newEntryLink );
		$parser->SetParserVar ( 'newEntryImageAdd', $newEntryImageAdd );
		$cmt_elements ['button_new'] = '<a href="' . $new_entry_link . '"><img src="' . CMT_TEMPLATE . 'img/icon_new.gif" border="0">&nbsp;Neuer&nbsp;Eintrag</a>';
		$cmt_elements ['button_new_link'] = $new_entry_link;
	}
	
	// Eintroege pro Seite anzeigen
	if ($cmt_settings ['show_ipp']) {
		$showIpp = $form->FormInput ( 'cmt_ipp', $cmt_ipp, 2 );
		$parser->setParserVar ( 'showIpp', $showIpp );
		$cmt_elements ['service_ipp'] = '<span class="serviceText">Eintr&auml;ge pro Seite</span>:&nbsp;' . $showIpp;
		// $function_cells[] = $cmt_elements['service_ipp'];
		// $function_cells_add[] = 'style="padding-right: 48px; vertical-align: bottom;"';
	}
	
	// Knopf "neu anzeigen"
	if ($function_cells) {
		$cmt_elements ['button_refresh'] = $form->FormSubmit ( 'neu anzeigen' );
		$function_cells [] = $cmt_elements ['button_refresh'];
		$function_cells_add [] = 'style="vertical-align: bottom;"';
		
		$function_table = $tab->TableStart ();
		$function_table .= $tab->TableMakeRow ( $function_cells, 0, $function_cells_add );
		$function_table .= $tab->TableEnd ();
		
		$replace .= $div->DivMakeDiv ( $function_table, "tableoverview_service" );
		
		unset ( $function_cells );
		unset ( $function_cells_add );
	}
	
	// Informationen zu angezeigten Eintroegen
	if ($cmt_settings ['show_pageselect']) {
		$parser->setParserVar ( 'currentRows', $current_rows );
		$parser->setParserVar ( 'allRows', $all_rows );
		$parser->setParserVar ( 'selectPage', $page_select );
		
		$cmt_elements ['service_currentrows'] = $current_rows;
		$cmt_elements ['service_allrows'] = $all_rows;
		$cmt_elements ['service_selectpage'] = $page_select;
	}
	
	if ($cmt_settings['show_iteminfos']) {
		$parser->setParserVar('showSelectedRowsInfo', true);
	}
	
	// Hover-Effekt eingeschaltet? Das ist Quatsch und sollte raus!
	if ($cmt_settings ['hover_row']) {
		$parser->setParserVar ( 'hoverRow', true );
	}
	
	// Query zusammenbauen und speichern
	if ($includeSelectAdd) {
		$querySelectAdd = $includeSelectAdd;
	} else {
		$querySelectAdd = '*';
	}
	$query = 'SELECT * FROM ' . CMT_DBTABLE . ' ' . $add_query;
	
	if ($include_limit_clause) {
		$query .= ' LIMIT ' . $include_limit_clause;
	} else {
		$query .= '  LIMIT ' . $show_page . ', ' . CMT_IPP;
	}
	
	// Soll Query angezeigt werden?
	if ($cmt_settings ['show_query']) {
		// $service_text = 'Ausgef&uuml;hrte Query:';
		unset ( $html );
		$parser->SetParserVar ( 'actQuery', $query );
		$cmt_elements ['service_showquery'] = $query;
	}
	
//	$service_form_end = 'hallo' . $form->FormHidden ( 'cmt_dbtable', CMT_DBTABLE ) . $form->FormHidden ( 'sid', SID ) . $form->FormEnd ();
//	$replace .= $service_form_end;
//	$cmt_elements ['service_end_form'] = $service_form_end;
	
	// ///////////////////
	// Query ausfoehren
	// ///////////////////
	
	// echo "<p>".$query;
	$db->Query ( $query );
	
	// ///////////////////
	// Daten ausgeben
	// ///////////////////
	
	// Falls keine Spalte für die Bearbeitungsknöpfe definiert wurde, Köpfe automatisch hinten anhängen
	if (! array_key_exists ( 'cmt__functions', $col_titles )) {
		$col_titles ['cmt__functions'] = '&nbsp;';
	}
	
	// Zellenbreiten definieren
	if (is_array ( $col_titles )) {
		$width = array_keys ( $col_titles );
		$width = array_flip ( $width );
	} else {
		$width = array ();
	}
	
	// Falls "id"-Feld oder Funktionen-Spalte vorhanden, dann diese von der Gesamtanzahl der Spalten abziehen,
	// da das nicht gleich breit sein muss, wie andere Spalten
	$cells = count ( $col_titles );
	
	if (isset ( $width [PRIMARY_KEY] )) {
		$cells -= 1;
	}
	if (isset ( $width ['cmt__functions'] )) {
		$cells -= 1;
	}
	
	if ($cells <= 0) {
		$cells = 1;
	}
	
	$cell_width = floor ( 100 / $cells );
	$cell_width = $cell_width . '%';
	
	foreach ( $width as $key => $value ) {
		if ($key != PRIMARY_KEY && $key != 'cmt__functions') {
			$width [$key] = $cell_width;
		}
	}
	
	// Ggf. eine oeberschriftenzelle foer die Mehrfachauswahl einbauen
	if (isset ( $user_tablefunctions ['delete'] ) || isset ( $user_tablefunctions ['duplicate'] )) {
		// $col_titles = array_merge(array('cmt__select' => '', 'cmt__functions' => 'Funktionen'), $col_titles);
	}
	
	// Wenn kein Rahmentemplate, dann Tabelle mit Hilfe der Table-Klasse erstellen
	// $htmlTableId = 'table'.CMT_DBTABLE;
	$htmlTableId = CMT_DBTABLE;
	$parser->setParserVar ( 'tableId', $htmlTableId );
	
	$rowCounter = 0;
	$cmtRowsContent = '';
	
	// weitere Variablen definieren
	$tableRowNr = 1;

	// Daten ausgeben
	while ( $r = $db->Get () ) {
		
		// Rohdaten an Parser oebergeben
		$parser->db_values = $r;
		// $template_parser->db_values = $r;
		
		// 2. Rohdaten formatieren und foer Ausgabe vorbereiten
		// foreach ($show_fields as $field) {
// var_dump($col_titles);
// die();
		foreach ( $col_titles as $field => $title ) {

			if (in_array ( $field, array_keys ( $r ) )) {
			
				// ID-Feld?
				switch ($field) {
					
					case 'id' :
						$output_fields [$field] = $r [$field];
						break;
					
					case 'cmt__functions' :
						$output_fields ['cmt__functions'] = '&nbsp;';
						break;
					
					default :
						$output_fields [$field] = $dformat->format ( CMT_DBTABLE, $field, $r [$field], 'formatted', $r ['id'] );
						
						break;
				}
				
				// Feld-Zeichenzahl beschroenken?
				if (isset ( $colsFormatted [$field] ['maxChars'] ) && strlen ( $output_fields [$field] ) > $colsFormatted [$field] ['maxChars']) {
					mb_internal_encoding ( CMT_DEFAULTCHARSET );
					$output_fields [$field] = mb_substr ( $output_fields [$field], 0, $colsFormatted [$field] ['maxChars'] ) . $cmt_settings ['max_chars_appendix'];
				}
			} else {
				$output_fields [$field] = '';
			}
					
			// Makros schützen
			$output_fields [$field] = $parser->protectMacros ( $output_fields [$field] );

		}
		
		$entryNr = intval ( ($rowCounter ++) + $cmt_pos * $cmt_ipp );
		
		$funcButtons = array (
			'edit' => 'icon_edit_24px.png',
			'duplicate' => 'icon_duplicate_24px.png',
			'delete' => 'icon_delete_24px.png' 
		);
		
		foreach ( $funcButtons as $k => $b ) {
			$b = CMT_TEMPLATE . 'app_showtable/img/' . $b;
			if ($cmt_settings ['big_buttons']) {
				$b = str_replace ( '_24px', '_32px', $b );
			}
			$funcButtons [$k] = $b;
		}
		
		$cmt_functions = array ();
		$cmtFunctionLinks = array ();
		
		foreach ( $user_tablefunctions as $functionName => $value ) {
			
			if ($value) {
				
				if ($functionName == 'delete') {
					
					$uri = SELFURL . '&amp;cmt_dbtable=' . CMT_DBTABLE . '&amp;cmt_ipp=' . $cmt_ipp . '&amp;cmt_pos=' . $cmt_pos . '&amp;entry_nr=' . $entryNr . '&amp;id[]=' . $r ['id'] . '&amp;action=' . $functionName;
					
					$cmt_functions [$functionName] = '<a href="Javascript:void(0);"' . ' class="cmtIcon cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" ' . 'data-dialog-content-id="cmtDialogConfirmDeletion" ' . 'data-dialog-var="' . $r ['id'] . '" ' . 'data-dialog-confirm-url="' . $uri . '" ' . 'data-dialog-cancel-url="" >';
				} else {
					$uri = SELFURL . '&amp;cmt_dbtable=' . CMT_DBTABLE . '&amp;cmt_ipp=' . $cmt_ipp . '&amp;cmt_pos=' . $cmt_pos . '&amp;entry_nr=' . $entryNr . '&amp;id[]=' . $r ['id'] . '&amp;action=' . $functionName;
					$cmt_functions [$functionName] = '<a href="' . $uri . '"';
					$cmt_functions [$functionName] .= ' class="cmtIcon cmtButton' . ucfirst ( $functionName ) . 'Entry">';
				}
				
				// Icon hinzufoegen
				// $cmt_functions[$functionName] .= '><img src="'.$funcButtons[$functionName].'" class="tableActionCellButton" alt="'.$functionName.'"></a>';
				$cmt_functions [$functionName] .= '</a>';
				
				$cmtFunctionLinks [$functionName] = $uri;
				
				// $add_query = '&key='.PRIMARY_KEY.'&id[]='.$id.'&cmt_dbtable='.CMT_DBTABLE.'&cmt_ipp='.CMT_IPP.'&cmt_pos='.CMT_POS.'&entry_nr='.$entry_nr;
			}
		}
		
		// Auswahlselectfeld (versteckt) hinzufügen
		if (in_array ( 'delete', $user_tablefunctions ) || in_array ( 'duplicate', $user_tablefunctions )) {
			$cmt_functions ['select'] = $form->FormCheckbox ( 'id[' . $rowCounter . ']', 0, $r ['id'], ' class="cmtSelectableStatus cmtIgnore"' );
		}
		
		// //////////////////////////////////////////////////////
		// Code ausfoehren:
		// overview_onshow_entry
		
		$cmt_dontshow = false;
		$cmt_abort = false;
		
		if ($cmt_executecode ['overview_onshow_entry']) {
			unset ( $parser->evalvars ['cmt_cellclass'] );
			unset ( $parser->evalvars ['cmt_cellhtml'] );
			$parser->evalvars ['cmt_action'] = $action;
			$parser->evalvars ['cmt_dontshow'] = $cmt_dontshow;
			$parser->evalvars ['cmt_abort'] = $cmt_abort;
			$parser->evalvars ['cmt_functions'] = $cmt_functions;
			$parser->evalvars ['tableRowNr'] = $tableRowNr;
			$parser->evalvars ['tableTotalRows'] = $tableTotalrows;
			$parser->db_values_formatted = $output_fields;
			
			// Code parsen

			$cmtRowsContent .= $parser->parse ( $cmt_executecode ['overview_onshow_entry'] );

			$output_fields = $parser->db_values_formatted;

			$cmt_dontshow = $parser->evalvars ['cmt_dontshow'];
			$cmt_abort = $parser->evalvars ['cmt_abort'];
			$cmt_functions = $parser->evalvars ['cmt_functions'];
			$cmt_cellclass = $parser->evalvars ['cmt_cellclass'];
			$cmt_cellhtml = $parser->evalvars ['cmt_cellhtml'];
		}
		// -> Ende Code ausfoehren:
		// overview_onsshow_entry
		// //////////////////////////////////////////////////////
		
		if ($cmt_abort) {
			unset ( $output_fields );
			break;
		}
		
		if (! $cmt_dontshow) {
		
			// Allgemeine Variablen übergeben
			$parser->setParserVar ( 'tableRowNr', $tableRowNr ++ );
			$parser->setParserVar ( 'tableTotalRows', $cmt_ipp );
			
			// Falls Reihentemplate vorhanden, dann parsen
			if ($cmt_templates ['overview_row']) {

				$parser->setMultipleParserVars ( $output_fields );
				
				foreach ( $cmt_functions as $key => $value ) {

					$parser->setParserVar ( 'cmtButton' . ucfirst ( $key ), $value );
					$parser->vars ['cmtButton' . ucfirst ( $key ) . 'Link'] = $cmtFunctionLinks [$key];
				}

				$cmtRowsContent .= $parser->parseTemplate ( PATHTOWEBROOT . $cmt_templates ['overview_row'], false );

				
				// Aktionen wieder loeschen
				foreach ( $cmt_functions as $key => $value ) {
					$template_parser->unsetParserVar ( 'cmtButton' . ucfirst ( $key ) );
					$template_parser->unsetParserVar ( 'cmtButton' . ucfirst ( $key ) . 'Link' );
				}
			} else {
				// Sonst Tabellenreihe in normale Variable schreiben

				$cmtRowsContent .= $tab->makeDataRow ( array (
						'content' => $output_fields,
						'formatted' => 'alternate',
						'functionButton' => $cmt_functions,
						'cellClass' => $cmt_cellclass,
						'cellHtml' => $cmt_cellhtml,
						'rowClass' => 'cmtSelectable' . ((int)$_REQUEST['edited_id'] == (int)$r['id'] ? ' cmtMarked' : '')
				) );
			}
			

		}
//die();		
		// //////////////////////////////////////////////////////
		// Code ausfoehren:
		// overview_aftershow_entry
		
		if ($cmt_executecode ['overview_aftershow_entry']) {
			$parser->evalvars ['cmt_action'] = $action;
			$parser->db_values_formatted = $output_fields;
			$cmtRowsContent .= $parser->parse ( $cmt_executecode ['overview_aftershow_entry'] );
			$cmt_abort = $parser->evalvars ['cmt_abort'];
		}
		
		// -> Ende Code ausfoehren:
		// overview_aftershow_entry
		// //////////////////////////////////////////////////////
		unset ( $output_fields );
		if ($cmt_abort) {
			break;
		}
		
		unset ( $cmt_functions );
		// unset($cmt_dontshow);
	}
	
	$cmtTableContent = $tab->TableStart ( '100%', '', '', '', 'id="' . $htmlTableId . '" class="cmtTable"' );
	$cmtTableContent .= $tab->headStart ();
	$cmtTableContent .= $tab->makeHead ( array (
			'cols' => $col_titles,
			'width' => $width 
	) );
	$cmtTableContent .= $tab->headEnd ();
	$cmtTableContent .= $tab->bodyStart ();
	
	$cmtTableContent .= $cmtRowsContent;
	
	$cmtTableContent .= $tab->bodyEnd ();
	$cmtTableContent .= $tab->TableEnd ();
	
	$parser->SetParserVar ( 'cmtTableContent', $parser->protectMakros ( $cmtTableContent ) );
	$parser->SetParserVar ( 'cmtRowsContent', $cmtRowsContent );
	
	// Knoepfe, Ausgewoehlte Eintroege: loeschen, verschieben
	if (isset ( $user_tablefunctions ['delete'] ) || isset ( $user_tablefunctions ['duplicate'] )) {

		if ($user_tablefunctions ['delete']) {
			$parser->setParserVar ( 'deleteMultiple', true );
		}
		
		if ($user_tablefunctions ['duplicate']) {
			$parser->setParserVar ( 'duplicateMultiple', true );
		}
	}
	
	$parser->SetParserVar ( 'cmt_dbtable', CMT_DBTABLE );
	$parser->SetParserVar ( 'sid', SID );
	$parser->SetParserVar ( 'action', '' );
	
	$cmt_elements ['end_form'] = $form->FormHidden ( 'cmt_dbtable', CMT_DBTABLE );
	$cmt_elements ['end_form'] .= $form->FormHidden ( 'sid', SID );
	$cmt_elements ['end_form'] .= $form->FormHidden ( 'action', "" );
	$cmt_elements ['end_form'] .= $form->FormEnd ();
	
	// //////////////////////////////////////////////////////
	// Code ausfoehren:
	// overview_oncomplete
	
	if ($cmt_executecode ['overview_oncomplete']) {
		$parser->evalvars ['cmt_action'] = $action;
		$onCompleteCode = $parser->parse ( $cmt_executecode ['overview_oncomplete'] );
		$parser->setParserVar ( 'onCompleteCode', $onCompleteCode );
	}
	
	// -> Ende Code ausfoehren:
	// overview_complete
	// //////////////////////////////////////////////////////
	
	// Rahmen parsen
	if ($cmt_templates ['overview_frame']) {
		
		/* ???? Loeschbar? */
		$parser->setMultipleParserVars ( $cmt_elements );
		
		$parser->setParserVar ( 'content', $replace );
		
		$replace .= $parser->parseTemplate ( PATHTOWEBROOT . $cmt_templates ['overview_frame'], false );

	} else {
		$replace .= $parser->parseTemplate ( 'app_showtable/cmt_table_overview.tpl' );
	}
	
}
?>