<?php
/**
 * appinc_tablebrowser_fields2.inc
 * Kurzkommentar
 *
 * Langkommentar
 * 
 * @version 2013-05-22
 * @author J.Hahn <info@buero-hahn.de>
 */
namespace Contentomat\Tablebrowser;

use Contentomat\DBCex;
use Contentomat\Parser;
use Contentomat\DatabaseTable;
use Contentomat\Form;
use Contentomat\Fieldhandler;
use Contentomat\Paging;
	//$cmt = Contentomat::getContentomat();

	// Übergebene Variablen holen
	$default_vars = array (
		// Anwendungsvariablen
		'action' => '',
		'id[]' => '',
		'cmtPage' => 1,
		'cmtIpp' => 10,
		'launch' => '',
		'cmt_tablename' => '',
		'cmt_fieldtype' => '',
		'cmtIndexName' => '',
		'cmtIndexOldName' => '',
		'cmtIndexType' => '',
		'cmtIndexOldType' => '',
		'cmtIndexField[]' => array(),
		'cmtIndexFieldLength[]' => array(),
		'save' => '',

		// Ajax-Variablen
		'ajaxTableName' => '',		// Tabellenname bei Ajax-Aufruf
		'ajaxFieldName' => '',		// Feldname bei Ajax-Aufruf
		'ajaxFieldValue' => '',		// Feldwert bei Ajax-Aufruf
		'ajaxNewRelationNr' => '',	// Nummer der neu zu erzeugenden Relation/ Tabellenverknüpfung
		'ajaxEmptySelection' => ''	// Dieser text wird für eine Leerauswahl eines Selectfeldes genutzt, sofern vorhanden
	);

	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ('cmtIpp');
	
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ('cmtPage', 'cmtIpp', 'cmt_tablename');

	// Variablen holen und extract durchführen
	include (PATHTOADMIN.'includes/func_get_vars.php');
	
	// maybe the table has changed. The start again with page 1
// 	if ($_REQUEST['cmtCurrentTable'] != $cmt_tablename) {
		
// 		$cmtPage = 1;
		
// 		$applicationVars = $session->getSessionVar('cmtApplicationVars');
// 		$applicationVars[CMT_APPID]['cmtPage'] = $cmtPage;
// 		$session->setSessionvar('cmtApplicationsVars', $applicationVars);
// 		$session->saveSessionVars();
// 	}

	// Parameter gültig für alle Feldtypen
	$fieldGeneralProperties = array (
		'cmt_fieldname' => 'fieldName',
		'cmt_fieldalias' => 'fieldAlias',
		'cmt_fieldtype' => 'fieldType',
		'cmt_fielddesc' => 'fieldDescription',
		'cmt_tablename' => 'tableName'
	);

	// Parameter, die es zwar für jedes Feld gibt, die aber feldspezifisch sind
	$fieldGeneralFieldTypePropertiesPrefix = array (
		'cmt_default_' => 'fieldDefaultValue'
	);

	// Prefix(e) für feldspezifische Variablen (z.B. cmt_options): Schlüssel: Formulareld-Prefix, Wert: Variablenname für Feldklasse
	$fieldSpecialPropertiesPrefix = array (
		'cmt_option_' => 'fieldOptions'
	);

	// FieldHandler einbinden und instanzieren
	require_once(PATHTOADMIN.'classes/class_fieldhandler.inc');
	$fieldHandler = new FieldHandler();
	$databaseTable = new DatabaseTable();

	// Tabellenansicht: Paging-Variablen
	$paging = new Paging(); 
	$cmtPage = intval($cmtPage);
	if (!$cmtPage) {
		$cmtPage = 1;
	}
	
	$cmtIpp = intval($cmtIpp);
	if (!$cmtIpp) {
		$cmtIpp = 10;
	}
	
	// Index möglich bei folgenden CMT-Feldtypen
	$indexNotPossible['index'] = array();
	$indexNotPossible['fulltext'] = array(
										'integer',
										'float',
										'flag',
										'date',
										'time',
										'datetime',
										'position'
									);
	$indexTypesPossible = array (
									'index' => 'INDEX',
									'fulltext' => 'FULLTEXT'
	);

	// Alle Tabellennamen ermitteln und Systemtabelle 'cmt_fields' entfernen
	$allTables = $db->getAllTables();
	array_flip($allTables);
	unset($allTables['cmt_fields']);
	array_flip($allTables);

	// Keine Tabelle ausgewählt?
	if (!$cmt_tablename) {
		$cmt_tablename = $allTables[0];
	}
	$indexByFields = $fieldHandler->getIndexByFields(array('tableName' => $cmt_tablename, 'except' => array('id')));
	$indexByNames = $fieldHandler->getIndexByNames(array('tableName' => $cmt_tablename, 'except' => array('id')));

	// Aktion immer an Parser übergeben
	$parser->setParserVar('action', $action);
	$parser->setParserVar('cmt_tablename', $cmt_tablename);

	/*
	 * Aktionen
	 */
	switch ($action) {

		/*
		 * Felderliste einer Tabelle ausgeben
		 */
		case 'ajaxUpdateField':
			$fieldDataCMT = $fieldHandler->getAllFields(array('tableName' => $ajaxTableName, 'getAll' => true));
			$fieldDataDBRaw = $db->getFieldInfo($ajaxTableName);

			$fieldDataAliases = array();
			$fieldDataValues = array();
			
			$fieldDataCMTFieldNames = array_keys($fieldDataCMT);
			
			if (is_array($fieldDataDBRaw['name'])) {
				foreach ($fieldDataDBRaw['name'] as $fieldName) {
					
					$fieldDataExt = $fieldName;
					if (in_array($fieldName, $fieldDataCMTFieldNames)) {
						$fieldDataExt .= '     => '.$fieldDataCMT[$fieldName]['cmt_fieldalias'].' ('.$fieldDataCMT[$fieldName]['cmt_fieldtype'].')';
					}
					$fieldDataAliases[] = $fieldDataExt;
					$fieldDataValues[] = $fieldName;
				}
			}
			
			// Leerauswahl
			if ($ajaxEmptySelection) {
				array_unshift($fieldDataValues, '');
				array_unshift($fieldDataAliases, $ajaxEmptySelection);
			}
			
			// Feld erzeugen
			$formField = $form->select(array(
				'values' => $fieldDataValues,
				'aliases' => $fieldDataAliases,
				'name' => $ajaxFieldName,
				'id' => str_replace(array('[', ']') , array('-', ''), $ajaxFieldName),
				'selected' => $ajaxFieldValue
			));
			echo utf8_encode($formField);
			exit();
			break;
		
		/*
		 * Feldtyp 'Relation': Zusätzliche Quelltabelle hinzufügen
		 */
		case 'ajaxAddRelation':

			$parser->setParserVar('relationNr', $ajaxNewRelationNr);
			$parser->setParserVar('rowNr', ($ajaxNewRelationNr+1) % 2);
			
			$contentRelation .= $parser->parseTemplate($templatePath.'appinc_fieldbrowser_edit_relation.tpl');

			$parser->setParserVar('contentRelation', $contentRelation);			
			header("Content-Type: text/javascript");
			echo utf8_encode($contentRelation);
			exit();
			break;			
			
			
		/*
		 * Eintrag bearbeiten
		 */
		case 'edit':
		case 'duplicate':
		case 'new':
			/*
			 * Feld speichern
			 */
			if ($save) {
//var_dump($_POST);
				// 1. Allgemeine Parameter übernehmen
				$gP = array();
				foreach ($fieldGeneralProperties as $propertyFieldName => $propertyName) {
					$gP[$propertyName] = trim(urldecode($_POST[$propertyFieldName]));
				}

				// 2. Allgemeine Parameter, aber feldtypisch
				$gsP = array();
				foreach ($fieldGeneralFieldTypePropertiesPrefix as $propertyPrefix => $propertyName) {
					$fieldPropertyPrefix = $propertyPrefix.$cmt_fieldtype;

					foreach ($_POST as $varName => $varValue) {
						if (preg_match('/^'.$fieldPropertyPrefix.'$/', $varName, $match)) {

							if ($match[0]) {
								$gsP[$propertyName] = trim(urldecode($varValue));
							}
						}
					}
				}

				// 3. Spezielle Variablen holen
				$sP = array();
				foreach ($fieldSpecialPropertiesPrefix as $propertyPrefix => $propertyName) {
					$fieldPropertyPrefix = $propertyPrefix.$cmt_fieldtype;

					foreach ($_POST as $varName => $varValue) {
						if (preg_match('/^'.$fieldPropertyPrefix.'(_(.*))?/', $varName, $match)) {

							if ($match[2]) {
								// Optionsfelder: z.B. cmt_options_link_path => 'path'
								$sPName = $match[2];

								// value kann ein String oder ein array sein
								if (is_array($varValue)){
									if ($cmt_fieldtype == 'relation') {
										foreach($varValue as $table){
											if($table['name']){
												$sP[$propertyName][$sPName][] = $table;
											}
										}
									} else {
										$sP[$propertyName][$sPName] = $varValue;
									}
								} else {
									// 2012-08-13: urldecode wird nicht benötigt??
									//$sP[$propertyName][$sPName] = trim(urldecode($varValue));
									$sP[$propertyName][$sPName] = trim($varValue);
								}
							} else {
								// Defaultfeld: z.B. cmt_default_string => 'path'
								$sPName = $match[0];
								
								// 2012-08-13: urldecode wird nicht benötigt?
								//$sP[$propertyName] = $parser->unprotectMakros(trim(urldecode($varValue)));
								$sP[$propertyName] = $parser->unprotectMakros(trim($varValue));
							}
						}
					}
				}
				$gP = array_merge($gP, $gsP, $sP);
				$gP['id'] = $id[0];
				$gP['action'] = $action;

				$check = $fieldHandler->editField($gP);

				if (!$check) {
					$parser->setParserVar('userMessageType', 'error');
					$parser->setParserVar('userMessage', $fieldHandler->getError());
					
					$postVars = $parser->flattenArray($_POST);

					$parser->setMultipleParserVars($postVars);

					unset ($save);
				} else {
					unset($action);
				}
			}

			/* 
			 * Feld anzeigen
			 */
			if (!$save) {

				$fieldData = $parser->flattenArray($fieldHandler->getField(array('id' => intval($id[0]))));
				foreach($fieldData as $fieldName => $fieldValue) {
					$fieldData[$fieldName] = $parser->protectMakros(htmlentities(stripslashes($fieldValue), ENT_COMPAT, CHARSET));
				}
				$parser->setMultipleParserVars($fieldData);
			

				/*
				 *  Einige Felder erfordern Sonderbehandlung
				 */
				switch ($fieldData['cmt_fieldtype']) {
					
					// Content für Relationen erzeugen
					case 'relation':
					// var_dump($fieldData);

						// ein Relationenfeldsatz muss immer erzeugt werden, auch wenn das Feld
						$relationPrefix = 'cmt_option_relation_from_table_';

						for($relationNr=1;$relationNr<=$fieldData[$relationPrefix."count"];$relationNr++){
							// if no table selected don't parse
							if(!$fieldData[$relationPrefix.$relationNr.'_name']) continue;
							$parser->setParserVar('relationNr', $relationNr);
							$parser->setParserVar('rowNr', ($relationNr + 1) % 2);
							
							//parse firest table (1)
							if($relationNr==1){
								$firstTable = array(
								'relationTableName' => $fieldData[$relationPrefix.$relationNr.'_name'],
								'relationValueField' => $fieldData[$relationPrefix.$relationNr.'_value_field'],
								'relationAliasField' => $parser->protectMakros($fieldData[$relationPrefix.$relationNr.'_alias_field']),
								'relationAddSQL' => $fieldData[$relationPrefix.$relationNr.'_add_sql']
								);
								continue;
							}
							// parse optional tables (2, 3 ....)
							$parser->setMultipleParserVars(array(
								'relationTableName' => $fieldData[$relationPrefix.$relationNr.'_name'],
								'relationValueField' => $fieldData[$relationPrefix.$relationNr.'_value_field'],
								'relationAliasField' => $parser->protectMakros($fieldData[$relationPrefix.$relationNr.'_alias_field']),
								'relationAddSQL' => $fieldData[$relationPrefix.$relationNr.'_add_sql']
							));
							
							// parse releation table row
							$contentRelation .= $parser->parseTemplate($templatePath.'appinc_fieldbrowser_edit_relation.tpl');
						}
						$parser->setParserVar('contentRelation', $contentRelation);
						$parser->setParserVar('id', intval($id[0]));
						break;
						
				}

				/* interne Variablen übergeben */
				$parser->setParserVar('cmtPage', $cmtPage);
				$parser->setParserVar('id', intval($id[0]));

				$parser->setMultipleParserVars($firstTable);
				
				$content = $parser->parseTemplate($templatePath.'appinc_fieldbrowser_edit.tpl');
				$noTemplate = true;
			}
		break;

		/*
		 * Eintrag löschen
		 */
		case 'delete':
			if (is_array($id)) {
				$successIds = array();
				$errorIds = array();
				foreach ($id as $singleId) {

					$check = $fieldHandler->deleteField(array('id' => $singleId));

					if ($check) {
						$successIds[] = $singleId;
					} else {
						$errorIds[] = $singleId;
					}
				}

				// Meldungen ausgeben
				$parser->setParserVar('message', 'deleteField');

				// Erfolgreich verarbeitete IDs
				if (!empty($successIds)) {
					$parser->setParserVar('successId', implode(', ', $successIds));
				}

				// Fehlgeschlagene IDs
				if (!empty($errorIds)) {
					$parser->setParserVar('errorId', implode(', ', $errorIds));
				}

				$userMessage = $parser->parseTemplate($templatePath.'appinc_fieldbrowser_messages.tpl');
				$parser->setParserVar('userMessage', $userMessage);

			}

			unset($action);
			break;

		/*
		 * Indizes
		 */
		case 'newIndex':
		case 'editIndex':

			/*
			 * Index speichern
			 */
			if ($save) {
				
				$error = false;
				
				// Fehler-Template einlesen
				$templateErrors = file_get_contents($templatePath.'appinc_fieldbrowser_index_errors.tpl');
				$templateMessages = file_get_contents($templatePath.'appinc_fieldbrowser_index_messages.tpl');
				
				
				// Existiert der Indexname bereits?
				if ($save && $action == 'newIndex' && in_array($cmtIndexName, array_keys($indexByNames))) {
					$parser->setParserVar('userMessageType', 'error');
					$parser->setParserVar('error', 'indexNameAlreadyExists');
					$parser->setParserVar('userMessage', $parser->parse($templateErrors));
					unset($save);
					$error = true;
				}
				
				// Index speichern
				if ($save) {
					
					$dbIndexType = $indexTypesPossible[$cmtIndexType];
					
					// Bereits vorhandener Index?
					if ($cmtIndexOldName) {
						
						$check = $fieldHandler->updateIndex(array(	
																'tableName' => $cmt_tablename,
																'indexField' => $cmtIndexField,
																'indexFieldLength' => $cmtIndexFieldLength,
																'indexType' => $dbIndexType,
																'indexName' => $cmtIndexOldName,
																'indexNewName' => $cmtIndexName
															));						
					} else {
						$check = $fieldHandler->addIndex(array(	
																'tableName' => $cmt_tablename,
																'indexField' => $cmtIndexField,
																'indexFieldLength' => $cmtIndexFieldLength,
																'indexType' => $dbIndexType,
																'indexName' => $cmtIndexName
															));
					}
					
					if (!$check) {
						$parser->setParserVar('userMessageType', 'error');
						$parser->setParserVar('error', 'cantSaveIndex');
						$parser->setParserVar('dbMessage', $fieldHandler->getError());
						$parser->setParserVar('dbErrorNr', $fieldHandler->getErrorNr());
						$parser->setParserVar('userMessage', $parser->parse($templateErrors));

						unset($save);
						$error = true;
					} else {
						$parser->setParserVar('userMessageType', 'success');
						$parser->setParserVar('message', 'indexEdited');
						$parser->setParserVar('userMessage', $parser->parse($templateMessages));
						
						// Index gespeichert, Index-Arrays neu auslesen
						$indexByFields = $fieldHandler->getIndexByFields(array('tableName' => $cmt_tablename, 'except' => array('id')));
						$indexByNames = $fieldHandler->getIndexByNames(array('tableName' => $cmt_tablename, 'except' => array('id')));
						unset($action);
						break;
					}
				}
			}
			
			/*
			 * Index anzeigen
			 */
			if (!$save) {
				
				// Zur Sicherheit bei weiterer Verarbeitung
				if (!is_array($indexByFields)) $indexByFields = array();
				if (!is_array($indexByName)) $indexByName = array();
				
				// Alle Felder holen
				$fieldData = $fieldHandler->getAllFields(array('tableName' => $cmt_tablename, 'getAll' => true));

				// Wird Index-Type übergeben oder muss er ermittelt werden?
				if (!$cmtIndexType) {
					if ($fieldHandler->isFulltext(array('tableName' => $cmt_tablename, 'indexName' => $cmtIndexName))) {
						$cmtIndexType = 'fulltext';
					} else {
						$cmtIndexType = 'index';
					}
				}
				$parser->setParserVar('cmtIndexType', $cmtIndexType);
	
				if (is_array($fieldData)) {
	
					$contentIndexFields = '';
					$contentTableFields = '';
					
					$templateField = file_get_contents($templatePath.'appinc_fieldbrowser_index_field.tpl');
					
					$fieldsInIndex = explode(',', $indexByNames[$cmtIndexName]['indexField']);
					
					$deleteParserVars = array('indexLength' => '', 'indexName' => '', 'indexType' => '');
				
					// Felder die nicht im Index sind
					foreach ($fieldData as $fdA) {
						if (!in_array($fdA['cmt_fieldtype'], $indexNotPossible[$cmtIndexType]) && (!$error && !in_array($fdA['cmt_fieldname'], $fieldsInIndex)) || ($error && !in_array($fdA['cmt_fieldname'], $cmtIndexField))) {
							$parser->setMultipleParserVars($fdA);
							$contentTableFields .= $parser->parse($templateField);
						}
					}
					
					// Felder aus dem Index
					if (!empty($fieldsInIndex) && $fieldsInIndex[0]) {

						foreach ($fieldsInIndex as $key => $fieldName) {
							$parser->setMultipleParserVars($deleteParserVars);
							$parser->setMultipleParserVars($fieldData[$fieldName]);
							$parser->setMultipleParserVars($indexByFields[$fieldName][$cmtIndexName]);
							$contentIndexFields .= $parser->parse($templateField);
						}
					}					
				}
				
				$parser->setParserVar('action', $action);
				$parser->setParserVar('cmtIndexType', $cmtIndexType);
				$parser->setParserVar('cmtIndexName', $cmtIndexName);
				$parser->setParserVar('contentIndexFields', $contentIndexFields);
				$parser->setParserVar('contentTableFields', $contentTableFields);
				$parser->setParserVar('contentIndex', $contentIndex);
				$content = $parser->parseTemplate($templatePath.'appinc_fieldbrowser_index_edit.tpl');
				
				$noTemplate = true;
			}	
			break;

		default:
			unset($action);
			unset($save);
			break;	
	}

	/*
	 * Felderübersicht für ausgewählte Tabelle anzeigen
	 */
	if (!$action) {
		
		unset ($save);

		// Auswahlliste Tabellen
		$parser->setParserVar('selectTable',
			$form->select(array(
								'name' => 'cmt_tablename',
								'addHtml' => '',
								'optionsOnly' => true,
								'values' => $allTables,
								'selected' => $cmt_tablename
	 							))
	 	);
	 	$parser->setParserVar('cmtIpp', $cmtIpp);

		// Anzahl der Einträge
		$fieldHandler->setTable('cmt_fields');
		$fieldsInTable = $databaseTable->countEntries(array(
															'tableName' => 'cmt_fields',
															'condWhere' => 'cmt_tablename = "'.$cmt_tablename.'"'
													));
	 	$parser->setParserVar('fieldsInTable', $fieldsInTable);

		// Paging
/*		$pT = file_get_contents(CMT_TEMPLATE.'app_tablebrowser/paging_tablebrowser_fields.tpl');
		$pagingTemplates = explode('{SPLITDATAHERE}', $pT);
		
		$paging = $fieldHandler->makePaging(array(	
													'tableName' => 'cmt_fields',
													'condWhere' => 'cmt_tablename = "'.$cmt_tablename.'"',
													'totalEntries' => $fieldsInTable,
													'currentPage' => $cmtPage,
													'itemsPerPage' => $cmtIpp,
													'templatePageLink' => $pagingTemplates[0],
													'templateLinkSeparator' => $pagingTemplates[1],
													'templatePrevLink' => $pagingTemplates[2],
													'templateNextLink' => $pagingTemplates[3],
													'showPages' => 0
												));
		$parser->setMultipleParserVars($paging);
*/
		$pagingLinks = $paging->makePaging(array(
			'totalEntries' => $fieldsInTable,
			'pagingLinks' => 10,
			'entriesPerPage' => $cmtIpp,
			'currentPage' => $cmtPage
		));	
		
		foreach($pagingLinks['prev'] as $pageDiff) {
			$page_select .= '<a href="'.SELFURL.'&cmtPage=' . ($pagingLinks['currentPage'] - $pageDiff) . '&cmt_ipp='.$cmtIpp.'" class="selectPage">-'. $pageDiff .'</a> ';
		}

		// zum Anfang
		if ($pagingLinks['totalPages'] > 1 && $pagingLinks['currentPage'] != 1) {
			$pagingSelectFirstPage = '<a href="'.SELFURL.'&cmtPage=0&cmt_ipp='.$cmtIpp.'" class="selectPage selectPageIcon selectFirstPage"></a> ';
		} else {
			$pagingSelectFirstPage .= '<span class="selectPage selectPageDisabled selectPageIcon selectFirstPage"></span> ';
		}
		$parser->setParserVar('pagingSelectFirstPage', $pagingSelectFirstPage);
		$page_select .= $pagingSelectFirstPage;
		
		// eine Seite zurück
		if ($pagingLinks['prevPage']) {
			$pagingSelectPrevPage = '<a href="'.SELFURL.'&cmtPage=' . ($pagingLinks['prevPage']) . '&cmt_ipp='.$cmtIpp.'" class="selectPage selectPageIcon selectPrevPage"></a> ';
		} else {
			$pagingSelectPrevPage = '<span class="selectPage selectPageDisabled selectPageIcon selectPrevPage"></span> ';
		}
		$parser->setParserVar('pagingSelectPrevPage', $pagingSelectPrevPage);
		$page_select .= $pagingSelectPrevPage;
		
		// Zahlenstrahl
		foreach($pagingLinks['pages'] as $pageNr) {
			if ($pageNr == $pagingLinks['currentPage']) {
				$page_select .= '<span class="selectCurrentPage">'. $pageNr .'</span> ';
			} else {
				$page_select .= '<a href="'.SELFURL.'&cmtPage=' . ($pageNr - 1) . '&cmt_ipp='.$cmtIpp.'" class="selectPage">'. $pageNr .'</a> ';
			}			
		}
		
		// eine Seite vor
		if ($pagingLinks['nextPage']) {
			$pagingSelectNextPage = '<a href="'.SELFURL.'&cmtPage=' . ($pagingLinks['nextPage']) . '&cmt_ipp='.$cmtIpp.'" class="selectPage selectPageIcon selectNextPage"></a> ';
		} else {
			$pagingSelectNextPage = '<span class="selectPage selectPageDisabled selectPageIcon selectNextPage"></span> ';
		}
		$parser->setParserVar('pagingSelectNextPage', $pagingSelectNextPage);
		$page_select .= $pagingSelectNextPage;
		
		// zum Ende
		if ($pagingLinks['totalPages'] > 1 && $pagingLinks['totalPages'] != $pagingLinks['currentPage']) {
			$pagingSelectLastPage = '<a href="'.SELFURL.'&cmtPage=' . ($pagingLinks['totalPages']) . '&cmt_ipp='.$cmtIpp.'" class="selectPage selectPageIcon selectLastPage"></a> ';
		} else {
			$pagingSelectLastPage = '<span class="selectPage selectPageDisabled selectPageIcon selectLastPage"></span> ';
		}
		$parser->setParserVar('pagingSelectLastPage', $pagingSelectLastPage);
		$page_select .= $pagingSelectLastPage;
		
		// mehrere Seiten vor
		foreach($pagingLinks['next'] as $pageDiff) {
			$page_select .= '<a href="'.SELFURL.'&cmtPage=' . ($pagingLinks['currentPage'] + $pageDiff) . '&cmt_ipp='.$cmtIpp.'" class="selectPage">+'. $pageDiff .'</a> ';
		}
		
		// alle Seiten als Dropdown
		$pagingSelectValues = array();
		foreach ($pagingLinks['allPages'] as $pageNr) {
			$pagingSelectValues[] = $pageNr - 1;
		}

		$pagingSelectAll = $form->select(array(
			'name' => 'cmtPage',
			'values' => $pagingSelectValues,
			'aliases' => $pagingLinks['allPages'],
			'selected' => $pagingLinks['currentPage'] - 1,
			'addHtml' => 'class="cmtSelectPageList" data-url="'. SELFURL .'"'
		));
		
		$parser->setParserVar('pagingSelectAll', $pagingSelectAll);
		$page_select .= $pagingSelectAll;
		
		$parser->setParserVar('pagingTotalPages', $pagingLinks['totalPages']);
		$parser->setParserVar('pagingCurrentPage', $pagingLinks['currentPage']);
		// Paging => Ende

		
	 	// Auswahlliste Indizes
	 	$indexSummary = array();
	 	foreach ((array)$indexByNames as $indexName => $iA) {
	 		$indexSummary[$indexName] = $indexName.' ('.$iA['indexType'].': '.$iA['indexField'].')';
	 	}
	 	
		if (!empty($indexSummary)) {
			$parser->setParserVar('selectIndex',
				$form->select(array(
									'name' => 'cmtIndexName',
									'addHtml' => '',
									'optionsOnly' => false,
									'values' => array_keys($indexSummary),
									'aliases' => array_values($indexSummary),
									'selected' => $cmtIndexName
		 							))
		 	);	 	
		}
		
	 	// Tabellenfelder anzeigen
	 	$fieldDataTemplate = file_get_contents($templatePath.'appinc_fieldbrowser_overview_row.tpl');
	 	$rowCounter = 0;

	 	$fieldData = $fieldHandler->getAllFields(array('tableName' => $cmt_tablename, 'itemsPerPage' => $cmtIpp, 'currentPage' => $cmtPage));

	 	foreach ($fieldData as $fieldDataRow) {
	 		
	 		// Zeilenflag
	 		$parser->setParserVar('alternationFlag', $rowCounter++%2);
	 		
	 		// Daten
	 		$parser->setMultipleParserVars($fieldDataRow);
	 		
	 		// Indizes und Typen
	 		$isIndex = false;
	 		$isFulltext = false;
	 		
	 		if ($fieldHandler->isIndex(array('fieldName' => $fieldDataRow['cmt_fieldname'], 'tableName' => $cmt_tablename))) {
	 			$isIndex = true;
	 		}
	 		if ($fieldHandler->isFulltext(array('fieldName' => $fieldDataRow['cmt_fieldname'], 'tableName' => $cmt_tablename))) {
	 			$isFulltext = true;
	 		}
	 		$parser->setParserVar('isFulltext', $isFulltext);
	 		$parser->setParserVar('isIndex', $isIndex);
	 		
	 		$contentTable .= $parser->parse($fieldDataTemplate);
	 	}
	 	
	 	// additional vars
	 	$parser->setParserVar('cmtPage', $cmtPage);

		// Rahmentemplate parsen
		$parser->setParserVar('contentTable', $contentTable);
		$content = $parser->parseTemplate($templatePath.'appinc_fieldbrowser.tpl');
	}
	unset($fieldHandler);

	$replace = $content;
?>
