<?php
/**
 * Tabellenmanager - Reiter "�bersicht"
 * 
 * Zeigt die Tabellen- und Gruppenstruktur innerhalb des CMS an.
 * 
 * @author J.Hahn <jhahn@content-o-mat.de>
 * @version 2006-9-18
 * 
 * @package app_tablebrowser.php
 * 
 * @todo: Fehlersicherheit: Eigentlich sollten eine neu erstellte Tabelle und auch die Eintr�ge in cmt_fields gel�scht werden, wenn
 * es zu einem Speicherfehler kommt: in Funktionen packen
 */
namespace Contentomat\Tablebrowser;

use Contentomat\DBCex;
use Contentomat\Parser;
use Contentomat\Form;
use Contentomat\Contentomat;
use Contentomat\error;
	
	function error ($text) {
		return '<div class="cmtMessage cmtMessageError">' . $text . '</div>';
	}
	
	function warning ($text) {
		return '<div class="cmtMessage cmtMessageWarning">' . $text . '</div>';
	}

	function success ($text) {
		return '<div class="cmtMessage cmtMessageSuccess">' . $text . '</div>';
	}

	// �bergebene Variablen holen
	$default_vars = array ("action" => "", "cmt_dbtable" => "", "cmt_groupname" => "", "cmt_newpos" => "", "cmt_type" => "",
							"id[]" => "", "cmt_groupid" => "", "cmt_tablename" => "", "cmt_include" => "", "cmt_showname" => "", "save" => 0,
							"cmt_entrytype" => "table", "cmt_itemid" => "", "cmt_visible" => "1", "cmt_tabman" => "");
	
	// Alle Variablennamen, die als Uservars gespeichert werden sollen
	$save_uservars = array ();
	// Alle Variablennamen, die als Sessionvars gespeichert werden sollen
	$save_sessionvars = array ();
	include (PATHTOADMIN.'includes/func_get_vars.php');

	// MySQL-Version ermitteln
	$mysql_supports_charsets = true;
	
	$cmt = Contentomat::getContentomat();

	switch ($action) {
		
		case 'change_visibility':
			switch ($cmt_type) {
				case 'group':
					
					$query = "SELECT cmt_group FROM cmt_tables WHERE id = '".intval($cmt_tabman)."'";
					$db->Query($query);
					$r = $db->Get(MYSQLI_ASSOC);
					
					if ($r['cmt_group'] == $id[0] && $cmt_visible) {
// 						$userMessage = warning ("Achtung! Sie haben die Gruppe 'unsichtbar' geschaltet, in der sich der Tabellenmanager befindet. " .
// 								"In diesem Zustand ist der Tabellenmanager nicht mehr �ber die Navigation erreichbar, so dass er nach dem Verlassen " .
// 								"dieser Seite nicht mehr auf 'sichtbar' geschaltet werden kann.", 0, 'div');
					}
					
					$query = "UPDATE cmt_tables_groups SET cmt_visible = ABS(cmt_visible - 1) WHERE id = '".$id[0]."'";
					$db->Query($query);
					//echo $query;
					if ($db->last_errorNr()) {
						$userMessage = error ("Konnte Anzeigestatuts der Gruppe nicht &auml;ndern: ".$db->last_error(), 0, 'div');
					}
					unset ($action);
					unset ($save);
					unset ($id); 
				break;
				
				case 'application':
				case 'table':
					if ($cmt_visible == '') {
						$cmt_visible = 0;
					}
					
					$query = "UPDATE cmt_tables SET cmt_itemvisible = ABS(cmt_itemvisible - 1) WHERE id = '".$id[0]."'";
					$db->Query($query);
					if ($db->last_errorNr()) {
						$userMessage .= error ("Konnte Anzeigestatuts des Elementes nicht &auml;ndern: ".$db->last_error(), 0, 'div');
					}
					
					if ($id[0] == CMT_APPID && $cmt_visible) {
						$userMessage .= warning ("Achtung! Sie haben den Tabellenmanager 'unsichtbar' geschaltet. " .
								"In diesem Zustand ist der Tabellenmanager nicht mehr &uuml;ber die Navigation erreichbar, so dass er nach dem Verlassen " .
								"dieser Seite nicht mehr auf 'sichtbar' geschaltet werden kann.", 0, 'div');
					}					
					unset ($action);
					unset ($save);
					unset ($id); 
				break;	
			}
		break;
		
		//
		// Gruppe oder Eintrag neu erstellen und bearbeiten
		//
		case 'edit':
		case 'duplicate':
		case 'new':

			if ($cmt_type == 'group') {

				$cmtGroupsFields = array();

				// Feldinformationen holen: Gruppentabelle
				$query = "SELECT cmt_fieldname, cmt_fieldtype FROM cmt_fields WHERE cmt_tablename='cmt_tables_groups'";
				$db->Query($query);
				while ($r = $db->Get(MYSQLI_ASSOC)) {
					$cmtGroupsFields[$r['cmt_fieldname']] = $r['cmt_fieldtype'];
				}

				// Gruppendaten holen
				$query = "SELECT * FROM cmt_tables_groups WHERE id='".intval($id[0])."'";
				$db->Query($query);
				$oldGroupData = $db->Get(MYSQLI_ASSOC);				
			} else {
				
				$cmtTablesFields = array();
				
				// Feldinformationen holen: Tabellentabelle
				$query = "SELECT cmt_fieldname, cmt_fieldtype FROM cmt_fields WHERE cmt_tablename='cmt_tables'";
				$db->Query($query);
				while ($r = $db->Get(MYSQLI_ASSOC)) {
					$cmtTablesFields[$r['cmt_fieldname']] = $r['cmt_fieldtype'];
				}
				
				// Tabellendaten holen
				$db->Query("SELECT * FROM cmt_tables WHERE id = '".intval($id[0])."'");
				$oldTableData = $db->Get(MYSQLI_ASSOC);
				$oldTableName = $oldTableData['cmt_tablename'];
				$oldTableGroup = $oldTableData['cmt_group'];
			}

			switch ($cmt_type) {
				
				case 'group':
					
					if ($save) {
						// POST-Variablen holen. Sonderfall Duplizieren: Falls hier eine Variable 
						// nicht per POST kommt, dann aus altem Datensatz �bernehmen.
						// In $groupData werden die aktuellen Daten aus alt und neu gespeichert
						foreach ($cmtGroupsFields as $field => $fieldType) {
							if ($fieldType == 'flag' && !isset($_POST[$field])) {
								$_POST[$field] = 0;
							}
							
							if (in_array($field, array_keys($_POST))) {
								$postVar = trim(urldecode($_POST[$field]));
								$querySet[$field] = $field." = '".$db->dbQuote($dformat->format('cmt_tables_groups', $field, $postVar, 'save', $id[0]))."'";									
								$tableData[$field] = $postVar;
							} else if ($action == 'duplicate') {
								$querySet[$field] = $field." = '".$db->dbQuote($dformat->format('cmt_tables_groups', $field, $oldGroupData[$field], 'save'))."'";
								$groupData[$field] = $oldGroupData[$field];
							} else {
								$groupData[$field] = $oldGroupData[$field];
							}
						}
						
						// Meldungen je nach Aktion
						$errorMessage = array('new' => 'Konnte neue Gruppe nicht in Tabelle \'cmt_tables_groups\' eintragen: ',
											'duplicate' => 'Konnte Gruppe '.$oldGroupData['cmt_groupname'].' nicht duplizieren: ',
											'edit' => 'Konnte Gruppe '.$oldGroupData['cmt_groupname'].' nicht speichern: ');

						$successMessage = array('new' => 'Neue Gruppe wurde erfolgreich eintragen. ',
											'duplicate' => 'Die Gruppe '.$oldGroupData['cmt_groupname'].' wurde erfolgreich dupliziert. ',
											'edit' => 'Die Gruppe '.$oldGroupData['cmt_groupname'].' wurde erfolgreich bearbeitet. ');

						// Speichern - Query je nach Aktion erstellen
						if ($action != 'edit') {
							unset ($querySet['id']);
							$query = "INSERT INTO cmt_tables_groups SET ".implode(', ',$querySet);
						} else {
							$query = "UPDATE cmt_tables_groups SET ".implode(', ',$querySet)." WHERE id='".$id[0]."'";
						}
//die($query);
						$db->Query($query);
						if ($db->last_errorNr()) {
							$userMessage = error ($errorMessage[$action].$db->last_error(), 0, 'div');
							$save = false;
							$fallback = true;
						} else {
							$userMessage = success ($successMessage[$action], 0, 'div');
							if ($action != 'edit') {
								$id[0] = $db->lastInsertedId();
							}
							
							// Noch Importgruppen-Flag �berpr�fen
							if ($tableData['cmt_isimportgroup'] && $tableData['cmt_isimportgroup'] != $oldTableData['cmt_isimportgroup']) {
								$query = "UPDATE cmt_tables_groups SET cmt_isimportgroup = '0' WHERE id != '".$id[0]."'";
								$db->Query($query);
							}
							$save = false;
							$action = '';
							break;
						}							
						
					}
					
					if (!$save) {
						$headline = array('new' => 'Neue Gruppe eintragen',
										'edit' => 'Gruppe '.$oldGroupData['cmt_groupname'].' bearbeiten',
										'duplicate' => 'Gruppe '.$oldGroupData['cmt_groupname'].' duplizieren');
						$parser->setParserVar('headline', $headline[$action]);
						$parser->setParserVar('action', $action);
						
						// Gibt es eine Meldung?
						if ($userMessage) {
							$parser->setParserVar('userMessage', $userMessage);;
						}
						
						// id gesondert registrieren, da sie in $oldGroupData gel�scht wird
						$parser->setParserVar('id', $id[0]);
						unset ($oldGroupData['id']);
						
						// Felder registrieren
						foreach ($cmtGroupsFields as $field => $fieldType) {
							$parser->db_values[$field] = $oldGroupData[$field];
							$parser->setParserVar($field, $dformat->format('cmt_tables_groups', $field, $oldGroupData[$field], $action));
						}						
						$replace = $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_edit_group.tpl');							
					}
				break;				
				
				case 'application':
					if ($save) {
						
						// POST-Variablen holen. Sonderfall Duplizieren: Falls hier eine Variable 
						// nicht per POST kommt, dann aus altem Datensatz �bernehmen.
						// In $tableData werden die aktuellen Daten aus alt und neu gespeichert
						foreach ($cmtTablesFields as $field => $fieldType) {
							if ($fieldType == 'flag' && !isset($_POST[$field])) {
								$_POST[$field] = 0;
							}
							
							if (in_array($field, array_keys($_POST))) {
								$postVar = trim(urldecode($_POST[$field]));
								$querySet[$field] = $field." = '".$db->dbQuote($dformat->format('cmt_tables', $field, $postVar, 'save', $id[0]))."'";									
								$tableData[$field] = $postVar;
							} else if ($action == 'duplicate') {
								$querySet[$field] = $field." = '".$db->dbQuote($dformat->format('cmt_tables', $field, $oldTableData[$field], 'save'))."'";
								$tableData[$field] = $oldTableData[$field];
							} else {
								$tableData[$field] = $oldTableData[$field];
							}
						}
						
						// Default-Einstellunge falls Applikation neu
						if ($action == 'new') {
							$fileName = preg_replace('/\..*/', '', basename($tableData['cmt_include']));
							if (file_exists(CMT_TEMPLATE.$fileName.'/default_settings.ini')) {
								$defaultSettings = parse_ini_file(CMT_TEMPLATE.$fileName.'/default_settings.ini');
								$querySet['cmt_tablesettings'] = "cmt_tablesettings = '". $cmt->safeSerialize($defaultSettings)."'";
							}
						}
													
						// Meldungen je nach Aktion
						$errorMessage = array('new' => 'Konnte neue Anwendung nicht in Tabelle \'cmt_tables\' eintragen: ',
											'duplicate' => 'Konnte Anwendung '.$oldTableData['cmt_showname'].' nicht duplizieren: ',
											'edit' => 'Konnte Anwendung '.$oldTableData['cmt_showname'].' nicht speichern: ');

						$successMessage = array('new' => 'Neue Anwendung wurde erfolgreich eintragen. ',
											'duplicate' => 'Die Anwendung '.$oldTableData['cmt_showname'].' wurde erfolgreich dupliziert. ',
											'edit' => 'Die Anwendung '.$oldTableData['cmt_showname'].' wurde erfolgreich bearbeitet. ');

						// Speichern - Query je nach Aktion erstellen
						if ($action != 'edit') {
							unset ($querySet['id']);
							$query = "INSERT INTO cmt_tables SET ".implode(', ',$querySet);
						} else {
							$query = "UPDATE cmt_tables SET ".implode(', ',$querySet)." WHERE id='".$id[0]."'";
						}
//die($query);
						$db->Query($query);
						if ($db->last_errorNr()) {
							$userMessage = error ($errorMessage[$action].$db->last_error(), 0, 'div');
							$save = false;
							$fallback = true;
						} else {
							$userMessage = success ($successMessage[$action], 0, 'div');
							if ($action != 'edit') {
								$id[0] = $db->lastInsertedId();
							}
							
							$save = false;
							$action = '';
							break;
						}					
					}
					
					if (!$save) {

						$headline = array('new' => 'Neue Anwendung eintragen',
										'edit' => 'Anwendung '.$oldTableData['cmt_showname'].' bearbeiten',
										'duplicate' => 'Anwendung '.$oldTableData['cmt_showname'].' duplizieren');
						$parser->setParserVar('headline', $headline[$action]);
						$parser->setParserVar('action', $action);
						
						// Gibt es eine Meldung?
						if ($userMessage) {
							$parser->setParserVar('userMessage', $userMessage);;
						}
						
						// id gesondert registrieren, da sie in $oldTableData gel�scht wird
						$parser->setParserVar('id', $id[0]);
						unset ($oldTableData['id']);
						
						// Felder registrieren
						foreach ($cmtTablesFields as $field => $fieldType) {
							if (!$fallback) {
								$parser->db_values[$field] = $oldTableData[$field];
								$parser->setParserVar($field, $dformat->format('cmt_tables', $field, $oldTableData[$field], $action));
							} else {
								$parser->db_values[$field] = $tableData[$field];
								$parser->setParserVar($field, $dformat->format('cmt_tables', $field, $tableData[$field], $action));
							}
						}						
						$replace = $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_edit_application.tpl');
					}
				break;
				
				case 'table':
					if ($save) {

						// POST-Variablen holen
						foreach ($cmtTablesFields as $field => $fieldType) {
							if ($fieldType == 'flag' && !isset($_POST[$field])) {
								$_POST[$field] = 0;
							}
							
							if (in_array($field, array_keys($_POST))) {
								$postVar = trim(urldecode($_POST[$field]));
								$querySet[$field] = $field." = '". $db->dbQuote($dformat->format('cmt_tables', $field, $postVar, 'save', $id[0]))."'";									
								$tableData[$field] = $postVar;
							} else {
								$tableData[$field] = $oldTableData[$field];
							}
						}

						// Passen Zeichensatz und Sortierreihenfolge?
						if ($mysql_supports_charsets && !preg_match('/^'.$tableData['cmt_charset'].'_/', $tableData['cmt_collation'])) {
							$userMessage = error ("Ung&uuml;ltige Sortierreihenfolge f&uuml;r den gew&auml;hlten Zeichensatz. Bitte w&auml;hlen Sie eine passende Reihenfolge.", 0);
							$save = false;
						}

						switch ($action) {
							case "new":
								// Neue Tabelle erzeugen falls Tabellenname angegeben
								$query = "CREATE TABLE ".$tableData['cmt_tablename']." (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)";
								if ($mysql_supports_charsets) {
								 	$query .= " DEFAULT CHARACTER SET ".$tableData['cmt_charset']." COLLATE ".$tableData['cmt_collation'];
								}
								$db->Query($query);
								if ($db->last_errorNr()) {
									$userMessage = error ("Konnte MySQL-Tabelle '".$tableData['cmt_tablename']."' nicht erstellen: ".$db->last_error(), 0, 'div');
									$save = false;
									$fallback = true;
								}
								
							break;
							
							case "edit":
								// Muss Tabelle umbenannt werden?
								if ($oldTableName != $tableData['cmt_tablename']) {
									//die ($oldTableName.' -> '.$tableData['cmt_tablename']);
									$query = "RENAME TABLE ".$oldTableName." TO ".$tableData['cmt_tablename'];
									$db->Query($query);
									if ($db->last_errorNr()) {
										$userMessage = error ("Konnte MySQL-Tabelle nicht umbenennen: ".$db->last_error(), 0, 'div');
										$save = false;
										$fallback = true;
										break;
									}
									
									// auch in cmt_fields umbenennen!
									$query = "UPDATE cmt_fields SET cmt_tablename = '".$tableData['cmt_tablename']."' WHERE cmt_tablename = '".$oldTableName."'";
									$db->Query($query);
									if ($db->last_errorNr()) {
										$userMessage = error ("Konnte Felder in Feldertabelle (cmt_fields) nicht umbenennen: ".$db->last_error(), 0);
										$save = false;
										$fallback = true;
										break;
									}
									
									// Und Schlussendlich in cmt_fields noch im Feld cmt_fieldquery
									$cmtFieldQuery = array();
									$cmtFieldQueryChanged = array();
									$query = "SELECT id, cmt_fieldquery FROM cmt_fields WHERE cmt_fieldquery LIKE '%$oldTableName%'";
									$db->Query($query);
									while ($r = $db->Get(MYSQLI_ASSOC)) {
										$cmtFieldQuery[$r['id']] = $cmt->safeUnserialize($r['cmt_fieldquery']);
									}
									foreach ($cmtFieldQuery as $fieldId => $fieldArray) {
										if (is_array($fieldArray)) {
											foreach ($fieldArray as $key=>$value) {
												if ($value == $oldTableName) {
													$fieldArray[$key] = $tableData['cmt_tablename'];
												}
											}
											// Wieder speichern und serialisieren
											$cmtFieldQueryChanged[$fieldId] = $cmt->safeSerialize($fieldArray);
										}
									}
									
									// Wieder in cmt_fields zur�ckschreiben
									if (is_array($cmtFieldQueryChanged)) {
										foreach ($cmtFieldQueryChanged as $fieldId => $value) {
											$query = "UPDATE cmt_fields SET cmt_fieldquery = '$value' WHERE id = '$fieldId'";
											echo $query.'<br>';
											$db->Query($query);
											if ($db->last_errorNr()) {
												$userMessage = error ("Konnte neuen Tabellennamen im Eigenschaftsfeld der Feldertabelle (cmt_fieldquery in cmt_fields) nicht umbenennen: ".$db->last_error(), 0, 'div');
												$save = false;
												break;
											}
										}
									} 
								}
								
								// Neuer Zeichensatz?
								if ($mysql_supports_charsets && ($oldTableData['cmt_charset'] != $tableData['cmt_charset'] || $oldTableData['cmt_collation'] != $tableData['cmt_collation'])) {
									$query = "ALTER TABLE ".$tableData['cmt_tablename']." DEFAULT CHARACTER SET ".$tableData['cmt_charset']." COLLATE ".$tableData['cmt_collation'];
									$db->Query($query);
									if ($db->last_errorNr()) {
										$userMessage .= error ("Konnte den Zeichensatz und / oder die Sortierreihenfolge der MySQL-Tabelle nicht �ndern: ".$db->last_error(), 0);
										$save = false; // -> kein kritischer Fehler?
										$fallback = true;
										break;
									}
								}								
								
								// Neue Gruppe?
								if ($oldTableGroup != $tableData['cmt_group']) {
									$query = "UPDATE cmt_tables SET cmt_itempos = cmt_itempos - 1 WHERE cmt_group = '$oldTableGroup' AND cmt_itempos > '".$oldTableData['cmt_itempos']."'";
									$db->Query($query);
									if ($db->last_errorNr()) {
										$userMessage .= error ("Konnte die Position der �brigen Elemente in der alten Gruppe '$oldTableGroup' nicht aktualisieren: ".$db->last_error(), 0);
										$save = false; // -> kein kritischer Fehler?
										$fallback = true;
										break;
									}
								}				
							break;
							
							case "duplicate":
								if ($tableData['cmt_tablename'] && ($oldTableName == $tableData['cmt_tablename'])) {
									$userMessage .= error ("Die duplizierte Tabelle kann nicht den gleichen Namen haben, wie die Ursprungstabelle.", 0);
									$save = false;
									break;
								} else if ($tableData['cmt_tablename']) {
									// Neue Tabelle duplizieren
									// 1. Felder holen
									$fields_temp = $db->getFieldinfo($oldTableName);
									$fields_temp = $fields_temp['name'];
									
									unset ($fields_temp['id']);
									
									// Tabelle ohne weitere Felder (au�er id)? Auch unterscheiden nach MySQL-Version
									$cmt_oldfields = implode(", ", $fields_temp);
									if ($mysql_supports_charsets) {
										if (!count($fields_temp)) {
											$query = "CREATE TABLE ".$tableData['cmt_tablename']." (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY) DEFAULT CHARACTER SET ".$tableData['cmt_charset']." COLLATE ".$tableData['cmt_collation'];
										} else {
											$query = "CREATE TABLE ".$tableData['cmt_tablename']." (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY) DEFAULT CHARACTER SET ".$tableData['cmt_charset']." COLLATE ".$tableData['cmt_collation']." SELECT ".$cmt_oldfields." FROM ".$oldTableName;
										}
									} else {
										$query = "CREATE TABLE ".$tableData['cmt_tablename']." (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY) SELECT ".$cmt_oldfields." FROM ".$oldTableName;
									}
									
									$db->Query($query);
									if ($db->last_errorNr()) {
										$userMessage .= error ("Konnte MySQL-Tabelle nicht duplizieren: ".$db->last_error(), 0, 'div');
										$tableData['cmt_tablename'] = $oldTableName;
										$save = false;
										$fallback = true;
										break;
									}
									
									// auch in cmt_fields duplizieren!
									$rows = array();
									$query = "SELECT * FROM cmt_fields WHERE cmt_tablename = '".$oldTableName."'";
									$db->Query($query);
									while ($r = $db-> Get(MYSQLI_ASSOC)) {
										unset ($r['id']);
										unset ($r['cmt_tablename']);
										foreach ($r as $fieldname => $fieldvalue) {
											$query_parts[] = $fieldname." = '".$db->dbQuote($fieldvalue)."'";
										}
										$query_parts[] = "cmt_tablename = '".$tableData['cmt_tablename']."'";
										$rows[] = implode (", ", $query_parts);
										unset ($query_parts);
									}
									
									foreach ($rows as $row) {
										$query = "INSERT INTO cmt_fields SET ".$row;
										$db->Query($query);
										if ($db->last_errorNr()) {
											$userMessage .= error ("Konnte duplizierte Tabelle nicht in Feldertabelle (cmt_fields) eintragen: ".$db->last_error(), 0, 'div');
											$save = false;
											$fallback = true;
											
											// Tabelle wieder l�schen
											$query = "DROP TABLE ".$tableData['cmt_tablename'];
											$db->Query($query);
										}
									}
								}		
							break;
						}	// Ende 'switch ($action)'
						
						// Kein Fehler, dann auch in cmt_tables speichern!
						if ($save) {

// ???????????????
							// H�chste Position innerhalb Gruppe ermitteln 
							if ($action != 'edit' || $tableData['cmt_group'] != $oldTableData['cmt_group']) {	
								$query = "SELECT MAX(cmt_itempos) AS maxpos FROM cmt_tables WHERE cmt_group = '".intval($tableData['cmt_group'])."'";
								$db->Query($query);
								$s = $db->Get(MYSQLI_ASSOC);
								$querySet['cmt_itempos'] = "cmt_itempos = '".(intval($s['maxpos']) + 1)."'";
								
								// wenn Gruppe beim Editieren gewechselt wurde, dann die Reihenfolge in der alten Gruppe wiederherstellen
								if ($action =='edit') {
									//$db->Query("UPDATE cmt_tables SET cmt_itempos = cmt_itempos-1 WHERE cmt_itempos > '".$oldTableData['cmt_itempos']."' AND cmt_group = '".$oldTableData['cmt_group']."'");
								}
							}
							
							// Default-Einstellungen holen, falls Tabelle neu
							if ($action == 'new') {
								$defaultSettings = parse_ini_file(CMT_TEMPLATE.'app_showtable/default_settings.ini');
								
								$querySet['cmt_tablesettings'] = "cmt_tablesettings = '".$db->dbQuote($cmt->safeSerialize($defaultSettings))."'";
							}

							// In cmt_tables schreiben
							if ($action == 'edit' && $id[0]) {
								$query = "UPDATE cmt_tables SET ".implode(", ", $querySet)." WHERE id = '".intval($id[0])."'";
							} else {
								$query = "INSERT INTO cmt_tables SET ".implode(", ", $querySet);
							}

							$db->Query($query);
							if ($db->last_errorNr()) {
								$userMessage .= error ("Konnte Tabelle / Applikation nicht registrieren (Fehler in interner Tabelle cmt_tables) : ".$db->last_error(), 0, 'div');
								$save = false;
								$fallback = true;
							} else {
								$show_action = array ("edit" => "Tabelle / Eintrag erfolgreich bearbeitet", 
													  "duplicate" => "Tabelle / Eintrag erfolgreich dupliziert",
													  "new" => "Neue Tabelle / neuer Eintrag erfolgreich erstellt");
								$userMessage .= success ($show_action[$action], '0', 'div');
								
								if ($action != 'edit') {
									$id[0] = $db->lastInsertedId();
								}

								$action = '';
								$save = false;
								break;
							}
						}						
					}	// Ende 'if ($save)'
					
					// Oder Eintrag anzeigen?
					if (!$save) {
						
						// �berschrift
						$headline = array ("new" => "neue Tabelle", "edit" => "Tabelle {TABLENAME} bearbeiten", "duplicate" => "Tabelle {TABLENAME} duplizieren");
						
						$parser->setParserVar('headline', str_replace('{TABLENAME}', $tableData['cmt_tablename'], $headline[$action]));
						
						// Gibt es eine Meldung?
						if ($userMessage) {
							$parser->setParserVar('userMessage', $userMessage);;
						}
						
						// Felder registrieren
						foreach ($cmtTablesFields as $field => $fieldType) {
							if ($fallback) {
								$parser->db_values[$field] = $tableData[$field];
								$parser->setParserVar($field, $dformat->format('cmt_tables', $field, $tableData[$field], $action));
							} else {
								$parser->db_values[$field] = $oldTableData[$field];
								$parser->setParserVar($field, $dformat->format('cmt_tables', $field, $oldTableData[$field], $action));
							}
						}
					
		
						// Ausnahme: Zeichensatz- und Kollationsselectfeld
						if ($mysql_supports_charsets) {
							// Zeichensatz
							//include_once(PATHTOADMIN.'includes/mysql_charsets.inc');
							
							// Fallback nach Fehler oder neu aufgerufen?
							if ($fallback) {
								$charset = $tableData['cmt_charset'];
								$collation = $tableData['cmt_collation'];
							} else {
								$charset = $oldTableData['cmt_charset'];
								$collation = $oldTableData['cmt_collation'];
							}
							
							if (!$charset) {
								$charset = strtolower(str_replace('-', '', CMT_DEFAULTCHARSET));
							}
							
							if (!$collation) {
								$collation = $db->getDefaultCollation($charset);
							}
							
							$cmt_charset = $db->getAvailableCharsets();

							$form = new Form;
							$selectCharset = $form->select(array(
								'values' => $cmt_charset,
								'optionsOnly' => true,
								'selected' => $charset
							));
														
							$parser->setParserVar('cmt_charset', $selectCharset);
							
							// Kollation
							$c = array();
							$collations = $db->getCollations();
							$selectCollation = $form->select(array(
									'values' => array_keys($collations),
									'optionsOnly' => true,
									'selected' => $collation
							));
							//$cmt_collation = file_get_contents(PATHTOADMIN.'includes/mysql_collation.inc');
							//$cmt_collation = str_replace('value="'.$collation.'"', 'selected value="'.$collation.'"', $cmt_collation);
							$parser->setParserVar('cmt_collation', $selectCollation);
							
							//echo 'Kollation und Zeichensatz erzeugt!';
						} else {
							$parser->setParserVar('cmt_collation', '');
							$parser->setParserVar('cmt_charset', '');
						}

						// Weitere Variablen f�r versteckte Felder etc.
						$parserVars = array ("id" => $id[0], "action" => $action, "cmt_slider" => $cmt_slider, "oldTablename" => $tableData['cmt_tablename']);

						$parser->setMultipleParserVars($parserVars);

						// Seite ausgeben
						$replace = $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_edit_table.tpl');
					}
				break;	// Ende 'case "table"'
			}
				
		break;
		
		//
		// Gruppe oder Eintrag l�schen
		//
		case "delete":
			
			switch ($cmt_type) {
				case "group":
					//Alte Daten holen zwecks Analyse und event Sicherung
					$query = "SELECT * FROM cmt_tables_groups WHERE id = '".$id[0]."'";
					$db->Query($query);
					$old_data = $db->Get(MYSQLI_ASSOC);

					// Letzte Gruppe? Kann nicht gel�scht werden!!
					$query = "SELECT COUNT(*) AS groups_count FROM cmt_tables_groups";
					$db->Query($query);
					$r = $db->Get(MYSQLI_ASSOC);
					if ($r['groups_count'] <= 1) {
						$text = "Die Gruppe ".$old_Data['cmt_groupname']." konnte nicht gel�scht werden, da sie die einzige bestehende Gruppe ist.";
						$userMessage .= error($text,  '0', 'div');
						$action = "";
						break;
					}

					// Dann l�schen
					$query = "DELETE FROM cmt_tables_groups WHERE id = '".$id[0]."'";
					$db->Query($query);
	
					if ($db->Last_ErrorNr()) {
						$text = "Konnte Gruppe ".$old_data['cmt_groupname']." nicht l�schen, da ein Fehler aufgetreten ist: ".$db->Last_Error()." - Fehler-Nr.: ".$db->Last_ErrorNr();
						$userMessage .= error($text,  '0', 'div');
					} else {
						$dformat->format("cmt_tables_groups", "cmt_grouppos", $old_data['cmt_grouppos'], "delete", $id[0]);
						
						// Noch die alten Tabellen umgruppieren
						if ($old_data['cmt_grouppos'] > 1) {
							$cmt_newgrouppos = $old_data['cmt_grouppos'] - 1;
						} else {
							$cmt_newgrouppos = 1;
						}
						
						// ID der neuen Gruppe ermitteln
						$query = "SELECT id FROM cmt_tables_groups WHERE cmt_grouppos = '".$cmt_newgrouppos."'";
						$db->Query($query);
						$r = $db->Get(MYSQLI_ASSOC);
						$newgroup = $r['id'];
						
						// H�chste Position der neuen Gruppe ermitteln
						$query = "SELECT cmt_itempos FROM cmt_tables WHERE cmt_group = '".$newgroup."' ORDER BY cmt_itempos DESC LIMIT 1";
						$db->Query($query);
						$r = $db->Get(MYSQLI_ASSOC);
						$newitempos = $r['cmt_itempos'] + 1;
						if (!$newitempos) {
							$newitempos = 1;
						}
						//echo "newitempos: ".$newitempos."<br>";
						$items_oldgroup = array();
						$query = "SELECT id FROM cmt_tables WHERE cmt_group = '".$old_data['id']."' ORDER BY cmt_itempos ASC";
						$db->Query($query);
						while ($r = $db->Get(MYSQLI_ASSOC)) {
							$items_oldgroup[] = $r['id'];
						}
						
						// Neue Positionen erstellen
						foreach ($items_oldgroup as $item) {
							$query = "UPDATE cmt_tables SET cmt_itempos = '".$newitempos++."' WHERE id = '".$item."'";
							$db->Query($query);
							if ($db->Last_ErrorNr()) {
								$text = "Konnte die Eintr&auml;ge der Gruppe ".$old_data['cmt_groupname']." nicht umsortieren, da ein Fehler aufgetreten ist: ".$db->Last_Error()." - Fehler-Nr.: ".$db->Last_ErrorNr();
								$userMessage .= error($text,  '0', 'div');
								break;
							}
						}
						
						// Eintr�ge in Gruppe eins dr�ber verschieben
						$query = "UPDATE cmt_tables SET cmt_group = '".$newgroup."' WHERE cmt_group = '".$old_data['id']."'";
						$db->Query($query);
						if ($db->Last_ErrorNr()) {
							$text = "Konnte die Eintr&auml;ge der Gruppe ".$old_data['cmt_groupname']." nicht umgruppieren, da ein Fehler aufgetreten ist: ".$db->Last_Error()." - Fehler-Nr.: ".$db->Last_ErrorNr();
							$userMessage .= error($text, '0', 'div');
						} else {
							$text = "Gruppe '".$old_data['cmt_groupname']."' wurde gel�scht.";
							$userMessage .= success ($text,  '0', 'div');
						}
					}
					$action = "";
				break;
				
				case 'table':
				case 'application':
					$query = "SELECT * FROM cmt_tables WHERE id = '".intval($id[0])."'";
					$db->Query($query);
					$r = $db->Get(MYSQLI_ASSOC);
					$cmt_tablename = $r['cmt_tablename'];
					$cmt_showname = $r['cmt_showname'];
					$cmtType = $r['cmt_type'];
					// Muss eine Tabelle gel�scht werden?
					if ($cmtType == 'table') {
						$query = "DROP TABLE ".$cmt_tablename;
						$db->Query($query);
						if ($db->last_errorNr()) {
							$userMessage = error ("Konnte Tabelle ".$cmt_showname." (".$cmt_tablename.") nicht l�schen: ".$db->last_error(), 0, 'div');
							$action = '';
						} else {
							$query = "DELETE FROM cmt_fields WHERE cmt_tablename = '".$cmt_tablename."'";
							$db->Query($query);
							if ($db->last_errorNr()) {
								$userMessage = error ("Konnte Feldinformationen (cmt_fields) f&uuml;r Tabelle ".$cmt_showname." (".$cmt_tablename.") nicht l�schen: ".$db->last_error(), 0, 'div');
								$action = '';
							} 
						}
					}
					
					if ($action) {
						$query = "DELETE FROM cmt_tables WHERE id = '".intval($id[0])."'";
						$db->Query($query);
						if ($db->last_errorNr()) {
							$userMessage .= error ("Konnte Tabelleninformationen (cmt_tables) f&uuml;r Eintrag ".$cmt_showname." (ID-Nr. ".$id[0].") nicht l�schen: ".$db->last_error(), 0, 'div');
							$action = '';
						} else {
							if ($cmtType == 'table') {
								$userMessage .= success("Tabelle ".$cmt_showname." (".$cmt_tablename.") erfolgreich gel&ouml;scht.", 0, 'div');
							} else {
								$userMessage .= success("Anwendung ".$cmt_showname." erfolgreich gel&ouml;scht.", 0, 'div');
							}
							
							// Positionen der anderen Eintr�ge korrigieren
							$query = "UPDATE cmt_tables SET cmt_itempos = cmt_itempos-1 WHERE cmt_itempos > '".$r['cmt_itempos']."' AND cmt_group = '".$r['cmt_group']."'";
							$db->Query($query);
							if ($db->last_errorNr()) {
								$userMessage .= error ("Der Eintrag ".$cmt_showname." (ID-Nr. ".$id[0].") wurde gel&ouml;scht, die �brigen Eintr&auml;ge in der Gruppe konnten allerdings nicht richtig sortiert werden: ".$db->last_error(), 0, 'div');
								$action = '';
							}
						}
					}
				break;
			}
		break;
		
		
		//
		// Gruppe oder Eintrag bewegen
		//
		case 'move':
		
			switch ($cmt_type) {
				case "group":
					$query = "SELECT cmt_grouppos FROM cmt_tables_groups WHERE id = '".intval($cmt_groupid)."' LIMIT 1";
					$db->Query($query);
					$r = $db->Get(MYSQLI_ASSOC);
					$cmt_oldpos = $r['cmt_grouppos'];
					$cmt_newpos = intval($cmt_newpos);
					$total =  $cmt_oldpos + $cmt_newpos;
					//$query = "UPDATE news SET news_pos = (".$summe." - news_pos) WHERE news_pos in (".$pos.",".$newpos.")";
					
					$query = "UPDATE cmt_tables_groups SET cmt_grouppos = (".$total." - cmt_grouppos) WHERE cmt_grouppos in (".$cmt_oldpos.",".$cmt_newpos.")";
					$db->Query($query);
					if ($db->last_errorNr()) {
						$userMessage .= error ("Konnte Gruppe wegen eines Datenbankfehlers nicht verschieben: ".$db->last_error(),  '0', 'div');
					}
					$action = "";
				break;
				
				case 'application':
				case 'table':
					$db->Query("SELECT cmt_itempos, cmt_group FROM cmt_tables WHERE id = '".intval($id[0])."'");
					$r = $db->Get(MYSQLI_ASSOC);
					$cmt_oldpos = $r['cmt_itempos'];
					$cmt_groupid = $r['cmt_group'];
					
					$cmt_newpos = intval($cmt_newpos);
					$total =  $cmt_oldpos + $cmt_newpos;
					
					$query = "UPDATE cmt_tables SET cmt_itempos = (".$total." - cmt_itempos) WHERE cmt_itempos in (".$cmt_oldpos.",".$cmt_newpos.") AND cmt_group = '".$cmt_groupid."'";
					$db->Query($query);
					if ($db->last_errorNr()) {
						$userMessage = error ("Konnte Tabelle / Eintrag wegen eines Datenbankfehlers nicht verschieben: ".$db->last_error(), 0, 'div');
					}
					$action = '';
					
				break;
				
				default:
					$action = '';
				break;
			}					
		break;


		//
		//  Eintr�ge in andere Gruppe bewegen
		//
		case "sort":

			// ID der neuen Gruppe ermitteln
			$db->Query("SELECT id FROM cmt_tables_groups WHERE cmt_grouppos = '".intval($cmt_newpos)."'");
			$r = $db->Get(MYSQLI_ASSOC);
			$new_groupid = $r['id'];
			
			// ... ans Ende der neuen Gruppe stellen
			$db->Query("SELECT cmt_itempos FROM cmt_tables WHERE cmt_group = '".$new_groupid."' ORDER BY cmt_itempos DESC LIMIT 1");
			$r = $db->Get(MYSQLI_ASSOC);
			$last_item = $r['cmt_itempos'];
			
			// verschieben
			$query = "UPDATE cmt_tables SET cmt_group = '".$new_groupid."', cmt_itempos = '".($last_item+1)."' WHERE id = '".intval($id[0])."'";
			$db->Query($query);

			// ... alte Gruppe neu ordnen
			$item_positions = array();
			$query = "SELECT id, cmt_itempos FROM cmt_tables WHERE cmt_group = '".intval($cmt_groupid)."' ORDER BY cmt_itempos ASC";
			$db->Query($query);
			while ($r = $db->Get(MYSQLI_ASSOC)) {
				$item_positions[] = $r['id'];
			}

			//print_r ($item_positions);
			//die;
			$item_counter = 1;
			foreach ($item_positions as $item_id) {
				$query = "UPDATE cmt_tables SET cmt_itempos = '".$item_counter."' WHERE id = '".$item_id."'";
				$db->Query($query);
				$item_counter++;
			}
			if ($db->last_errorNr()) {
				$userMessage = error ("Konnte Eintrag wegen eines Datenbankfehlers nicht umgruppieren: ".$db->last_error(), 0, 'div');
			}
			$action = '';
			
			// Ist Tabellenmanager in einer unsichtbaren Gruppe?
			if ($id[0] == CMT_APPID) {
				$query = "SELECT cmt_visible FROM cmt_tables_groups WHERE id = '".$new_groupid."'";
				$db->Query($query);
				$r = $db->Get(MYSQLI_ASSOC);

				if (!$r['cmt_visible']) {
					$userMessage = warning ("Achtung! Sie haben den Tabellenmanager in eine in der Navigation nicht sichtbare Gruppe " .
							"verschoben. " .
							"In diesem Zustand ist der Tabellenmanager nicht mehr �ber die Navigation erreichbar, so dass er nach dem Verlassen " .
							"dieser Seite nicht mehr auf 'sichtbar' geschaltet werden kann.", 0, 'div');
				}
			}				
		break;
		
	}
	
	//////////////////////////////////
	// Startscreen: �bersicht
	//////////////////////////////////
	$possible_actions = array ("edit", "save", "new", "duplicate", "move", "sort");
	
	if (!in_array($action, $possible_actions)) {


		//////////////////////
		// Gruppen anzeigen
		//////////////////////
		
		// Variablen
		$groups = array();
		$groupTemplate = file_get_contents(CMT_TEMPLATE.'app_tablebrowser/appinc_overview_group.tpl');
		$itemTemplate = file_get_contents(CMT_TEMPLATE.'app_tablebrowser/appinc_overview_item.tpl');
		$itemParser = new Parser();

		// alte ID f�r 'zuletzt bearbeitet'_Funktion �bergeben
		$itemParser->setParserVar('lastEditedId', $id[0]);
		
		$query = "SELECT * FROM cmt_tables_groups ORDER BY cmt_grouppos ASC";
		$db->Query($query);
		
		while ($r = $db->Get(MYSQLI_ASSOC)) {
			 $groups[] = $r;
		}

		// Anzahl der vorhandenen Gruppen speichern		
		$maxGroupPos = count($groups);
		$parser->setParserVar('maxGroupPos', $maxGroupPos);
		
		foreach ($groups as $group) {
			
			// Positionen f�r Verschiebepfeile speichern
			if ($group['cmt_grouppos'] > 1) {
				$parser->setParserVar('newLowerPos', intval($group['cmt_grouppos'])-1);
				$itemParser->setParserVar('newLowerGroup', intval($group['cmt_grouppos'])-1);
			} else {
				$parser->setParserVar('newLowerPos', false);
				$itemParser->setParserVar('newLowerGroup', false);
			}
			
			if ($group['cmt_grouppos'] < $maxGroupPos) {
				$parser->setParserVar('newUpperPos', intval($group['cmt_grouppos'])+1);
				$itemParser->setParserVar('newUpperGroup', intval($group['cmt_grouppos'])+1);
			} else {
				$parser->setParserVar('newUpperPos', false);
				$itemParser->setParserVar('newUpperGroup', false);
			}

			$parser->setMultipleParserVars($group);
			$query = "SELECT COUNT(*) AS itemsInGroup FROM cmt_tables WHERE cmt_group = '".$group['id']."'";
			$db->Query($query);
			$r = $db->Get(MYSQLI_ASSOC);
			
			$itemsInGroup = $r['itemsInGroup'];
			$parser->setParserVar('itemsInGroup', $itemsInGroup);
			$itemParser->setParserVar('itemsInGroup', $r['itemsInGroup']);

			$query = "SELECT * FROM cmt_tables WHERE cmt_group = '".$group['id']."' ORDER BY cmt_itempos ASC";
			$db->Query($query);
			while ($r = $db->Get(MYSQLI_ASSOC)) {
				
				// Positionen speichern
				if ($r['cmt_itempos'] > 1) {
					$itemParser->setParserVar('newLowerPos', intval($r['cmt_itempos'])-1);
				} else {
					$itemParser->setParserVar('newLowerPos', false);
				}
				
				if ($r['cmt_itempos'] < $itemsInGroup) {
					$itemParser->setParserVar('newUpperPos', intval($r['cmt_itempos'])+1);
				} else {
					$itemParser->setParserVar('newUpperPos', false);
				}
				
				// Restliche Variablen �bergeben und parsen
				$itemParser->setMultipleParserVars($r);
				$contentItems .= $itemParser->parse($itemTemplate);
			}
			
			//Gruppe ausgeben
			$parser->setParserVar('contentItems', $contentItems);
			$contentGroups .= $parser->parse($groupTemplate);
			
			unset ($contentItems);
			$itemParser->alternationFlag = 0;
		}
		
		// Seite ausgeben
		$parser->setParserVar('contentGroups', $contentGroups);
		$parser->setParserVar('userMessage', $userMessage);
		$replace .= $parser->parseTemplate(CMT_TEMPLATE.'app_tablebrowser/appinc_overview.tpl');
	}
?>