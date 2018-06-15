<?php
/**
 * class_dataformat
 * 
 * Klasse, die Daten formatiert. Zwischenschicht zwischen Datenbank und CMS.
 * 
 * Diese Klasse stellt neue, CMS interne Datentypen zur Verfügung und übersetzt sie je nach Aktion.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2017-11-07
 */

namespace Contentomat;

class dataformat {

	////////////////////////////
	//
	// Objekt-Variablen
	//
	////////////////////////////

	private $form;
	private $db;
	private $table;
	private $parser;
	public $field;
	public $action;
	public $position_fields;
	public $htmlEditorCounter;
	public $fielddesc;
	public $fielddescription;
	public $fieldAlias;
	public $fieldtype;
	private $session;
	private $fieldHandler;
	private $gui;


	////////////////////////////////////////////
	//
	// Funktionen, Konstruktor: los geht's
	//
	////////////////////////////////////////////

	// Konstruktor
	public function __construct() {
		
		$this->form = new Form();
		$this->db = new DBCex();
		$this->fieldHandler = new FieldHandler();
		$this->gui = new Gui();
		//$this->session = new Session();

		// Parser-Kontruktor kann eine Referenz auf ein anderes Objekt übergeben werden! 
		$this->parser = new CMTParser($this);
		//$this->parser = new Parser();
		
		$this->htmlEditorCounter = 0;
	}


	/**
	 * Formatiert Daten je nach Aktion
	 * 
	 * @param $table string Name der Tabelle des Feldes, welches formatiert werden soll
	 * @param $field string Name des Feldes in $table, welches formatiert werden soll
	 * @param $data mixed Wert des zu formatierenden Feldes
	 * @param $action string Aktion (davon abhängig ist die Formatierung). Möglich sind "edit", "save", "duplicate", "view"
	 * @param $id integer Id des zu formatierenden Feldes in $table (meist für Positionen notwendig)
	 * @param $add_vars array Array, das verschiedene weitere Formatierungsoptionen (hauptsächlich für "edit"-Aktionen) bietet.
	 * Der Schlüssel bestimmt dabei die Verwendung des Wertes("style"->Wert wird als Style-Formatierung angehängt, "html"->Wert wird als zus�tzliches HTML angeh�ngt (z.B. f�r Javascript-Aktionen))
	 * 
	 * @return string R�ckgabe abh�ngig von Aktion (z.B. liefert "edit" ein Formularfeld mit dem formatierten Wert)
	 */
	  
	public function format($table, $field, $data, $action = 'view', $id=0, $add_vars=array()) {
		
		$this->field = $field;
		$this->table = $table;
		$this->action = $action;
		
		if (!is_array($add_vars)) {
			$add_vars = array();
		}

		// Felddaten ermitteln
		$r = $this->fieldHandler->getField(array(
			'tableName' => $table,
			'fieldName' => $field
		));
	
		$this->fieldtype = $r['cmt_fieldtype'];
		$this->fielddesc = $r['cmt_fielddesc'];
		$this->fieldAlias = $r['cmt_fieldalias'];

		// Default Variable wenn neuer Eintrag und keine Daten mitgeschickt
		if ($action == "new" && (trim($data) == "" && $data !== 0)) {
			$data = $r["cmt_default"];
		}

		// Typ-Änderungen wenn Feld auf "readonly"
		if ($add_vars['readonly'] == 'readonly') {
// 			$readonly_newtype = array ('select' => 'string', 'link' => 'string',  'position' => 'string', 'flag'  => 'string');
// 			if ($readonly_newtype[$this->fieldtype]) {
// 				$this->fieldtype = $readonly_newtype[$this->fieldtype];
// 			}
			
			$action = 'view';
		}
		
		// Aktion 'view' Synonym f�r 'formatted';
		if ($action == 'view') {
			$action = 'formatted';
		}
		
		//Mögliche Makros holen
		if (trim($r['cmt_options']) != '') { 
			$makros = Contentomat::safeUnserialize($r['cmt_options']);

			if (!is_array($makros)) {
				$makros = array();
			}
		} else {
			$makros = array();
		}

		switch ($this->fieldtype) {

			////////////////////////////////////////////
			// 1. String
			////////////////////////////////////////////
			case 'string' :
				
				if ($action == 'save') {
					
				}
				
				$addHtmlParts = array();
				$hasAutocomplete = false;

				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						
						// Vorschläge aus Liste
						case 'from_list':
							
							$autocompleteList = trim($makros['from_list']);
							if ($autocompleteList) {
								
								$autocompleteList = str_replace(array("\n", "\r"), array(',', ''), $autocompleteList);
								$addHtmlParts[] = 'data-list="' . htmlspecialchars($autocompleteList) .'"';
								
								$hasAutocomplete = true;
							}
							
							break;
							
						// Vorschläge aus DB-Tabelle (Daten kommen aus der jeweiligen Anwendung)
						case 'from_table':
							
							$tableName = trim($makros['from_table']);
							if ($tableName) {
								$addHtmlParts[] = 'data-url="' . SELFURL .'"';
						
								if (!$makros['action']) {
									$addHtmlParts[] = 'data-action="getAutocompleteList"';
								} else {
									$addHtmlParts[] = 'data-action="' . trim($makros['action']) . '"';
								}
								
								$hasAutocomplete = true;
							}
							break;

						// Mehrfachauswahl möglich
						case 'multiple':
							
							if ($makros['multiple']) {
								
								$addHtmlParts[] = 'data-multiple="1"';
							}
							
							break;

						// Mehrfachauswahl Trennzeichen
						case 'multiple_separator':
							
							if ($makros['multiple_separator']) {
								
								$addHtmlParts[] = 'data-separator="' . htmlspecialchars($makros['multiple_separator']) .'"';
							}
							
							break;					
					}

				}
				
				if ($hasAutocomplete) {
					if ($add_vars['class']) {
						$add_vars['class'] .= ' cmtAutocomplete';
					} else {
						$addHtmlParts[] = 'class="cmtAutocomplete"';
					}
				}
				
				$addHtml = implode(' ', $addHtmlParts);

				return $this->format_string($data, $action, $field, $add_vars, $addHtml);
			break;

			////////////////////////////////////////////
			// 2. Text
			////////////////////////////////////////////
			case 'text' :
				
				if ($makros['show_editor']) {
					
					$addHtml = $this->gui->makeTexteditor(array(
						'vars' => array(
							'cmtField' => $field,
							// 'cmtFieldData' => htmlspecialchars(trim(stripslashes($data))),
							'cmtFieldData' => trim($data),
							'editorLanguage' => $makros['editor_language'],
							'editorTheme' => $makros['editor_theme']
						)
					));

					$add_vars['html'] = ' id="' . $field .'" ';
				}

				return $this->format_text($data, $action, $field, $add_vars, $addHtml);
			break;

			////////////////////////////////////////////
			// 3. Html
			////////////////////////////////////////////
			case 'html' :
				//echo 'Makros sind: '.$makros;
				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						case 'tinymce':		// TODO: outdated!!!
						case 'editor':
							$html_editor = true;
						break;
						
					}
					if (stristr($makro, 'tinymce')) {
						$add_vars[$makro] = $params;
					}
				}

				return $this->format_html($data, $action, $field, $html_editor, $add_vars);
			break;

			////////////////////////////////////////////
			// 4. Integer / Ganzzahl
			////////////////////////////////////////////
			case 'integer' :
				return $this->format_integer($data, $action, $field, $add_vars);
			break;

			////////////////////////////////////////////
			// 5. Zahl
			////////////////////////////////////////////
			case 'float' :
				$round = false; 

				// Eigenschaften parsen
				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						case 'round' :
							$round = $params;
						break;
					}
				}			
				return $this->format_float($data, $action, $field, $round, $add_vars);
				break;

			////////////////////////////////////////////
			// 6. Datum
			////////////////////////////////////////////
			case 'date' :
				
				$addCalendarHtml = '';
//$makros['show_calendar'] = true;
				// Eigenschaften parsen
				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						case 'allways_current' :
							if ($action == 'save') {
								$data = date('Y-m-d', time());
							}
						break;

						case 'current':
							if ($action == 'new') {
								$data = date('Y-m-d', time());
							}
						break;
						
						case 'show_calendar':
							if ($params && ($action == 'edit' || $action == 'new' || $action == 'duplicate')) {
								//$addCalendarHtml = $this->createCalendarHtml($field, $data);
								$addCalendarHtml = $this->gui->makeCalendarButton();
							}
						break;
					}
				}
				return $this->format_date($data, $action, $field, $add_vars).$addCalendarHtml;
			break;

			////////////////////////////////////////////
			// 7. Zeit
			////////////////////////////////////////////
			case 'time' :

				// Eigenschaften parsen
				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						case 'allways_current' :
							if ($action == 'save') {
								$data = date('H:i:s', time());
							}
						break;

						case 'current' :
							if ($action == 'new') {
								$data = date('H:i:s', time());
							}
						break;
					}
				}

				return $this->format_time($data, $action, $field, $add_vars);
			break;

			////////////////////////////////////////////
			// 8. Datum und Uhrzeit
			////////////////////////////////////////////
			case 'datetime' :
//$makros['show_calendar'] = true;
				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						case 'allways_current' :
							if ($action == 'save') {
								$data = date("Y-m-d H:i:s", time());
							}
						break;

						case 'current' :
							if ($action == 'new') {
								$data = date('Y-m-d H:i:s', time());
							}
						break;
						
						case 'show_calendar':
							if ($params && ($action == 'edit' || $action == 'new' || $action == 'duplicate')) {
								$addCalendarHtml = $this->gui->makeCalendarButton();
							}
						break;
					}
				}
				return $this->format_datetime($data, $action, $field, $add_vars).$addCalendarHtml;
			break;

			////////////////////////////////////////////
			// 9. Relation
			////////////////////////////////////////////
			case 'relation' :
				$noselection = false;
				// Eigenschaften parsen
//				foreach ($makros as $optionName => $optionValue) {
//					switch ($optionName) {
//						case 'values' :
//							$possible_values['values'] = explode("\n", str_replace("\r", '', trim($optionValue)));
//						break;
//						
//						case 'aliases' :
//							if (!empty($optionValue)) {
//								$possible_values['aliases'] = explode("\n", str_replace("\r", '', trim($optionValue)));
//							} else {
//								$possible_values['aliases'] = explode("\n", str_replace("\r", '', trim($makros['values'])));
//							}
//						break;
//						
//						case 'noselection' :
//							$noselection = $optionValue;
//						break;
//					}
//				}

// TODO: Hier den Parser entfernen und zusätzliche Formatierungen in Class GUI!
				
// Geändert, JH, 2011-05-02
				$possible_values = array();
				$possible_aliases = array();
//var_dump($makros);							
				if (is_array($makros['from_table'])) {
					
					foreach ($makros['from_table'] as $relationData) {
						
						//escape when no table name given
						if(!$relationData['name']) {
							continue;
						}
						
						// Tabellendaten holen
						$query = "SELECT * FROM cmt_tables WHERE cmt_tablename='" . $this->db->dbQuote($relationData['name']) . "' " .
								 "AND cmt_type='table' " . 
								 "LIMIT 1";
						$this->db->query($query);
						$tableData = $this->db->get();
						
						$this->parser->setParserVar('cmtTableName', $relationData['name']);
						$this->parser->setParserVar('cmtTableID', $tableData['id']);
						$this->parser->setMultipleParserVars($tableData);
						
						// Inhaltsdaten holen
						$query = 'SELECT * FROM ' . $this->db->dbQuote($relationData['name']);

						if ($relationData['add_sql']) {
							$query .= ' '.str_replace("'", '"', stripslashes($relationData['from_table_add_sql']));
						}

						$this->db->query($query);
	
						while ($rp = $this->db->get()) {
							$possible_values['values'][] = $tableData['id'] . '_' . trim($rp[$relationData['value_field']]);
	
							// Ist ein Alias-Feld angegeben?
							if ($relationData['alias_field']) {
						
								// Datensatz an Parser übergeben: {VAR:myField}
								$this->parser->setMultipleParserVars($rp);
	
								//$query_parser->db_values = $rp;
	
								// Alias parsen
								$possible_values['aliases'][] = trim($this->parser->parse($relationData['alias_field']));
							}
						}
					}
				}

				$addLinkHtml = $this->createRelatedLinksSelectorHtml($this->table, $field, '/');
				
				// Workaround: "Unprotect" { and } ind data string
				$data = str_replace(array('&#123;', '&#125;'), array('{', '}'), $data);
				
				return $this->format_relation($data, $action, $field, $possible_values, $noselection, $add_vars, $makros,$addLinkHtml,$id);
				break;
			
			////////////////////////////////////////////
			// 9. Auswahlliste / Selectfeld
			////////////////////////////////////////////
			case 'select' :
				$noselection = false;

				// Eigenschaften parsen
				foreach ($makros as $optionName => $optionValue) {

					switch ($optionName) {
						case 'values' :
							// TODO: 2011-04-19: Workaround!
							if (is_array($optionValue)) {
								$possible_values['values'] = $optionValue;
							} else {
								$possible_values['values'] = explode("\n", str_replace("\r", '', trim($optionValue)));
							}
						break;
						
						case 'aliases' :
							if (!empty($optionValue)) {
								// TODO: 2011-04-19: Workaround!
								if (is_array($optionValue)) {
									$possible_values['aliases'] = $optionValue;
								} else {
									$possible_values['aliases'] = explode("\n", str_replace("\r", '', trim($optionValue)));
								}
							} else {
								$possible_values['aliases'] = explode("\n", str_replace("\r", '', trim($makros['values'])));
							}
						break;
						
						case 'noselection' :
							$noselection = $optionValue;
						break;
					}
				}

				//Auswahlliste aus anderer Tabelle erstellen?
				if ($makros['from_table']) {
					$possible_values = array();

					$query = 'SELECT * FROM '.$makros['from_table'];

					if ($makros['from_table_add_sql']) {
						$query .= ' '.str_replace("'", '"', stripslashes($makros['from_table_add_sql']));
					}

					$this->db->query($query);

					while ($rp = $this->db->get(MYSQLI_ASSOC)) {
						$possible_values['values'][] = trim($rp[$makros['from_table_value_field']]);

						// Ist ein Alias-Feld angegeben?
						if ($makros['from_table_alias_field']) {
							// Datensatz an Parser übergeben: {VAR:myField}
							$this->parser->vars = $rp;

							//$query_parser->db_values = $rp;

							// Alias parsen
							$possible_values['aliases'][] = trim($this->parser->parse($makros['from_table_alias_field']));
						}
					}

				}

				return $this->format_select($data, $action, $field, $possible_values, $noselection, $add_vars, $makros);
				break;
				
			////////////////////////////////////////////
			// 10. rekursive Auswahlliste / Selectfeld
			////////////////////////////////////////////
			case 'select_recursive':
				$noselection = false;

				// Eigenschaften parsen
				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						case 'from_table' :
							$table = $params;
						break;
						case 'parent' :
							$parent = $params;
						break;
						
						case 'parent_value_field' :
							$parent_field = $params;
						break;
// TODO_ startvalue schon implementiert in V2?
						case 'startvalue' :
							$parent_value = $params;
						break;
						case 'parent_alias_field' :
							$alias_field = $params;
						break;
						case 'add_sql' :
							$add_query = $params;
						break;

						case 'noselection' :
							$noselection = $params;
						break;
						
						case 'multiple' :
							$multiple = $params;
							break;						
					}
				}

				if (!$alias_field) {
					$alias_field = $value_field;
				}
				
				if (!$parent_value) {
					$parent_value = 'root';
				}

				$level = 0;
				$pages = '';

				// rekursive Funktion aufrufen
				$recursiveData = $this->get_tabledata_recursive($table, $parent, $parent_field, $parent_value, $alias_field, $level, $add_query, $pages, '', $multiple);
				
				// Array wird erwartet
				if (!is_array($recursiveData)) $recursiveData = array('values' => array(), 'aliases' => array());
				
				if ($parent_value == 'root') {
					array_unshift($recursiveData['values'], 'root');
					array_unshift($recursiveData['aliases'], 'Root');
				}
				
				$add_vars['class'] = 'select_link';
				$makros['replaceEntities'] = false;

				return $this->format_select($data, $action, $field, $recursiveData, $noselection, $add_vars, $makros);

			break;
			
			////////////////////////////////////////////
			// 11. Auswahlliste: Link
			////////////////////////////////////////////
			case 'link' :

				$absolute = 0; // Pfade absolut oder relativ angeben
				$noselection = '';
				$depth = ''; // Alle Unterverzeichnisse anzeigen;
				$addLinkHtml = '';
				$linkMode = 'dropdownmenu';

$makros['show_fileselector'] = true;				
				foreach ($makros as $makro=>$params) {
					switch ($makro) {

						case 'show_fileselector':
							if ($action == 'edit' || $action == 'new' || $action == 'duplicate') {
								$addLinkHtml = $this->gui->makeFileSelectorButton(array(
									'vars' => array(
										'cmtField' => $field,
										'action' => $action
									)
								));
							}
							$linkMode = 'fileSelector';
						break;

						case 'dir' :
						case 'path' :
							$rootpath = $params;
						break;

						case 'show' :
							$show = explode('\r\n', $params);
						break;

						case 'dontshow' :
							$dont_show = explode('\r\n', $params);
						break;

						case 'absolute' :
							$absolute = 1;
						break;

						case 'noselection' :
							$noselection = $params;
						break;
							
						case 'onlydir' :
							if (trim($params) == '1') {
								$dir = 'onlydir';
							}
						break;

						case 'depth' :
							$depth = $params;
						break;

					}
				}
				
				// Falls Eintrag neu -> Default-Wert anpassen
				if (!$absolute && $action == 'new') {
					$data = preg_replace ('/^'.preg_quote($rootpath, '/').'\//', '', $data);
				}
				
				// Pfad zum Web-Root hinzuf�gen/berechnen
				$rootpath = Contentomat::formatPath(PATHTOWEBROOT."/".$rootpath."/");				
				
				return $this->format_link($data, $action, $field, $dir, $rootpath, $show, $dont_show, $absolute, $noselection, $depth, $add_vars, $linkMode).$addLinkHtml;
			break;

			////////////////////////////////////////////
			// 12. Flag
			////////////////////////////////////////////
			case 'flag' :
			
				if ($action == 'new' && $r['cmt_default']) {
					$data = 1;
				}
				
				foreach ($makros as $makro=>$params) {
					$params = $values[$key];

					switch ($makro) {
						case 'value' :
							$value = $params;
						break;
					}
				}

				return $this->format_flag($data, $action, $field, $value, $add_vars);
			break;

			////////////////////////////////////////////
			// 9. Position
			////////////////////////////////////////////
			case 'position' :
				foreach ($makros as $makro=>$params) {

					switch ($makro) {
						case 'parent' :
							$parent = trim($params);
							if (!$parent) break;
							
							// Erster Versuch: $parent_value wird in den Advars mitgegeben:
							if (isset($add_vars[$field.'_parent_value'])) {
								$parent_value = $add_vars[$field.'_parent_value'];
							} else {
								// Zweiter Versuch: Parent und Wert per GET oder POST holen
								$query = "SELECT cmt_fieldtype FROM cmt_fields WHERE cmt_tablename = '".$table."' AND cmt_fieldname = '".$parent."'";
								$this->db->Query($query);
								$r = $this->db->Get(MYSQLI_ASSOC);
								
								$parent_fieldtype = $r['cmt_fieldtype'];
								
								global $_POST;
								global $_GET;
			
								// Zuerst auf Sonderformatierungen date, time, datetime achten!
						 		if ($parent_fieldtype == 'date') {
						 			if ($_POST[$parent.'_year'] || $_POST[$parent.'_month'] || $_POST[$parent.'_day']) {
						 				$parent_value = $this->format_date($_POST[$parent.'_year'].'-'.$_POST[$parent.'_month'].'-'.$_POST[$parent.'_day'], 'save');
						 			} else if ($_GET[$field.'_year'] || $_GET[$field.'_month'] || $_GET[$field.'_day']) {
						 				$parent_tmp = $this->format_date($_GET[$parent.'_year'].'-'.$_GET[$parent.'_month'].'-'.$_GET[$parent.'_day'], 'save');
						 			}
						 		} else 
		
						 		if ($parenttype == 'time') {
						 			if ($_POST[$parent.'_hour'] || $_POST[$parent.'_minute'] || $_POST[$parent.'_second']) {
						 				$parent_value = $this->format_time($_POST[$parent.'_hour'].':'.$_POST[$parent.'_minute'].':'.$_POST[$parent.'_second'], 'save');
						 			} else if ($_GET[$parent.'_hour'] || $_GET[$parent.'_minute'] || $_GET[$parent.'_second']) {
						 				$parent_value = $this->format_time($_GET[$parent.'_hour'].':'.$_GET[$parent.'_minute'].':'.$_GET[$parent.'_second'], 'save');
						 			}
						 		} else
								
						 		if ($parenttype == 'datetime') {
						 			if ($_POST[$parent.'_year'] || $_POST[$parent.'_month'] || $_POST[$parent.'_day'] || $_POST[$parent.'_hour'] || $_POST[$parent.'_minute'] || $_POST[$parent.'_second']) {
						 				$parent_value = $this->format_date($_POST[$parent.'_year'].'-'.$_POST[$parent.'_month'].'-'.$_POST[$parent.'_day'], 'save').' '.$this->format_time($_POST[$parent.'_hour'].':'.$_POST[$parent.'_minute'].':'.$_POST[$parent.'_second'], 'save');
						 			} else if ($_GET[$field.'_year'] || $_GET[$field.'_month'] || $_GET[$field.'_day'] || $_GET[$parent.'_hour'] || $_GET[$parent.'_minute'] || $_GET[$parent.'_second']) {
						 				$parent_value = $this->format_date($_GET[$parent.'_year'].'-'.$_GET[$parent.'_month'].'-'.$_GET[$parent.'_day'], 'save').' '.$this->format_time($_GET[$parent.'_hour'].':'.$_GET[$parent.'_minute'].':'.$_GET[$parent.'_second'], 'save');
						 			}
						 		} else	{
						 			// Keine SOnderformatierungen, dann Feld normal per POST versuchen
							 		$parent_value = $_POST[$parent];
							 	}
							 	
							 	// Per GET?	
							 	if (!isset($parent_value)) {
							 		$parent_value = $_GET[$parent];
							 	}
								
								// Dritter Versuch: $parent_value aus DB holen
								if (!isset($parent_value)) {
									$query = "SELECT ".$parent." FROM ".$table." WHERE id = '".$id."'";
									$this->db->Query($query);
									$r = $this->db->Get(MYSQLI_ASSOC);
									$parent_value = $r[$parent];
								}
							}
							// -> .. parent fertig
							break;
					}
				}
				return $this->format_position($data, $action, $field, $table, $id, $parent, $parent_value, $add_vars);
			break;


			////////////////////////////////////////////
			// 14. Systemvariable / -konstante
			////////////////////////////////////////////
			case 'system_var' :
				foreach ($makros as $makro=>$params) {

					switch ($makro) {
						case 'type':
							switch ($params) {
								case 'CMT_USERID':
									$sysdata = CMT_USERID;
								break;

								case 'CMT_USERNAME':
									$sysdata = CMT_USERNAME;
								break;

								case 'CMT_USERALIAS':
									$sysdata = CMT_USERALIAS;
								break;

								case 'CMT_GROUPID':
									$sysdata = CMT_USERGROUPID;
								break;

								case 'sys_timestamp':
									$sysdata = time();
								break;
								
								case 'ref_ip':
									$sysdata = getenv ('REMOTE_ADDR');
								break;

								case 'ref_browser':
									$sysdata = getenv ('HTTP_USER_AGENT');
								break;								
	
							}
						break;

						// Outdated: Option ob Wert angezeigt werden soll oder nicht:
						// -> sollte in der Tabellenstruktur geregelt werden
						/*
						case "show":
							$user_show = true;
						break;
						*/
						
						// Nicht implementiert
						case 'editable':
							$user_edit = true;
						break;
					}
				}

				return $this->format_systemvar($data, $action, $field, $sysdata, $user_edit, $add_vars);
			break;

			////////////////////////////////////////////
			// 15. Upload-Feld
			////////////////////////////////////////////
			case 'upload' :
//$makros['show_fileselector'] = true;
				$dir = '';
				$adLinkHtml = '';
				
				foreach ($makros as $makro=>$params) {
					switch ($makro) {
						case 'show_fileselector':
							if ($action == 'edit' || $action == 'new' || $action == 'duplicate') {
								$addLinkHtml = $this->gui->makeFileSelectorButton(array(
									'vars' => array(
										'cmtField' => $field,
										'action' => $action
									)
								));
							}
							$linkMode = 'fileSelector';
						break;

						case 'dir' :
							$dir .= '/'.$params;
							break;
					}
				}

				//$addLinkHtml .= ' class="cmtFileInput" ';
				// Auswahlliste aus anderer Tabelle erstellen? Das hier nochmal �berdenken -> Funktionsweise!
				/*				
				if ($r['cmt_fieldquery']) {
					$regex = '/\{([^}]*)\}([^{]*)/is';
					preg_match_all ($regex, $r['cmt_fieldquery'], $match);
					
					$query_makros = $match[1];
					$query_values = $match[2];			
					foreach ($query_makros as $key=>$makro) {
						$query_parts[trim(strtolower($makro))] = trim($query_values[$key]);
						unset ($makro);
					}
					
					$query = 'SELECT '.$query_parts['query_value'];
					$query .= ' FROM '.$query_parts['query_table'];
					$query .= ' JOIN '.$this->table;
					//$query .= ' WHERE '.$query_parts['query_table'].'.'.$query_parts['query_link_source'].' = '.$this->table.'.'.$query_parts['query_link_target'];
					eval ('$query_constant = '.$query_parts['query_link_target'].';');
					$query .= ' WHERE '.$query_parts['query_table'].'.'.$query_parts['query_link_source'].' = ''.$query_constant.''';
					unset ($possible_values);
					unset ($r);

					$this->db->Query($query);
					$r = $this->db->Get(MYSQLI_ASSOC);
					
					// Sofern vorher schon ein Standard-Rootverzeichnis festgelegt wurde, wird das User-Verzeichnis angeh�ngt.
					$dir .= '/'.$r[$query_parts['query_value']];
				}
				*/
				
				return $this->format_upload($data, $action, $field, $dir, $add_vars, $addLinkHtml);
			break;

		}		
	}

	////////////////////////////////////////////
	//
	// Formatierfunktionen
	//
	////////////////////////////////////////////

	////////////////////////////////////////////
	// string
	////////////////////////////////////////////
	public function format_string($data, $action="formatted", $field="", $add_vars="", $addHtml='') {
		if (!$field) {
			$field = $this->field;
		}
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
				$size = 40;
				$maxlength = 0;
				
				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));

				// 2018-01-26: Removed stripslashes here
				//$data = stripslashes($data);
				
				// 2018-01-26: Alternative to 'stripslashes' => no use for it
				//$data = preg_replace('/\/{2}/', '\\', $data);
				
				$formatted_data = $this->form->FormInput($field, $data, $size, $maxlength, $addHtml);

				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'save':
				// 2018-01-26: removed stripslashes here
				//$formatted_data = $this->db->dbQuote(stripslashes(trim($data)));
				$formatted_data = $this->db->dbQuote(trim($data));

			break;
			
			case "view":
			case 'formatted':
				$formatted_data = $data;
			break;
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// text
	////////////////////////////////////////////
	public function format_text($data, $action="edit", $field="", $add_vars="", $addHtml='') {
		if (!$field) {
			$field = $this->field;
		}
		
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
				$cols = 60;
				$rows = 8;
				
				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));

				//$data = stripslashes($data);
				//$formatted_data["html"] = $this->form->FormTextarea($field, $data);
				$formatted_data = $this->form->FormTextarea($field, $data, $cols, $rows, $add_html) . $addHtml;

			break;
			
			case 'save':
				$formatted_data = addslashes($data);
			break;
		
			case "view":
			case 'formatted':
				//formatiert
				//$formatted_data = nl2br($this->cmt_htmlentities(stripslashes($data)));
				$formatted_data = nl2br($this->cmt_htmlentities($data));
			break;
			
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// date
	////////////////////////////////////////////
	public function format_date($data, $action="edit", $field="", $add_vars="") {
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':

				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));

				$parts = explode("-", $data);

				$addClass = ' class="formFieldDay cmtDateDay"';
				$formatted_data = $this->form->FormInput($field."_day", $parts[2], 2, 2, $add_html.$addClass);
				$formatted_data .= "&nbsp;-&nbsp;";
				$addClass = ' class="formFieldMonth cmtDateMonth"';
				$formatted_data .= $this->form->FormInput($field."_month", $parts[1], 2, 2, $add_html.$addClass);
				$formatted_data .= "&nbsp;-&nbsp;";
				$addClass = ' class="formFieldYear cmtDateYear"';
				
				// Für spätere Datumsformatierungen
				//$addClass .= ' data-dateformat="yyyy-mm-dd" ';
				
				$formatted_data .= $this->form->FormInput($field."_year", $parts[0], 4, 4, $add_html.$addClass);

/*
				preg_match_all("/[0-9]{2,4}/", $data, $match);
				$formatted_data = $this->form->FormInput($field."_day", $match[0][2], 2, 2, $add_html);
				$formatted_data .= "&nbsp;-&nbsp;";
				$formatted_data .= $this->form->FormInput($field."_month", $match[0][1], 2, 2, $add_html);
				$formatted_data .= "&nbsp;-&nbsp;";
				$formatted_data .= $this->form->FormInput($field."_year", $match[0][0], 4, 4, $add_html);
*/					
				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;

			case 'save':
				// Jahr formatieren
				$parts = explode("-", $data);
				
				if (!$parts[0]) {
					$parts[0] = "0000";
				}
				// Monat formatieren
				if (!$parts[1] || $parts[1] > 12 || $parts[1] < 1) {
					$parts[1] = "00";
				}
				
				// Tag formatieren
				if (!$parts[2] || $parts[2] > 31 || $parts[2] < 1) {
					$parts[2] = "00";
				}

				$formatted_data = sprintf ("%04d-%02d-%02d", $parts[0], $parts[1], $parts[2]);
			break;
			
			case 'formatted':
				if ($data) {
					$parts = explode('-', $data);
					foreach ($parts as $p) {
						if (intval($p)) {
							$parts_formatted[] = $p;
						}
					}
					if (is_array ($parts_formatted)) {
						$parts_formatted = array_reverse($parts_formatted);
						$formatted_data = implode ('.', $parts_formatted);
					} else {
						//$formatted_data = '0000-00-00';
					} 
				}
			break;			
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// time (Uhrzeit)
	////////////////////////////////////////////
	public function format_time($data, $action="edit", $field="", $add_vars="") {
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':

				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));

				$parts = explode(":", $data);
				$addClass = ' class="formFieldHour"';
				$formatted_data = $this->form->FormInput($field."_hour", $parts[0], 2, 2, $add_html.$addClass);
				$formatted_data .= "&nbsp;:&nbsp;";
				$addClass = ' class="formFieldMinute"';
				$formatted_data .= $this->form->FormInput($field."_minute", $parts[1], 2, 2, $add_html.$addClass);
				$formatted_data .= "&nbsp;:&nbsp;";
				$addClass = ' class="formFieldSecond"';
				$formatted_data .= $this->form->FormInput($field."_second", $parts[2], 2, 2, $add_html.$addClass);

				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'save':
				// Zeit formatieren
				$parts = explode(":", $data);
				foreach ($parts as $key=>$part) {
					if (!$part) {
						$parts[$key] = "00"; // Ist es sinnvoll 00 einzusetzen statt NULL?
					}
				}
					
				$formatted_data = sprintf ("%02d:%02d:%02d", $parts[0], $parts[1], $parts[2]);
			break;
			
			case 'formatted':
				// formatiert
				if ($data) {
					$parts = explode(":", $data);
					$formatted_data = $parts[0].":".$parts[1];
				}
			break;
		}

		return $formatted_data;
	}

	////////////////////////////////////////////
	// datetime (Datum+Uhrzeit)
	////////////////////////////////////////////
	public function format_datetime($data, $action="edit", $field="", $add_vars="") {
	
		$parts = explode(" ", $data);
		$date = $parts[0];
		$time = $parts[1];
						
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
			
				//Sonderformatierungen holen
//?? warum auskommentiert?
				//extract ($this->get_special_formats($add_vars));
							
				$formatted_date = $this->format_date($date, "edit", $field, $add_vars);
				$formatted_time = $this->format_time($time, "edit", $field, $add_vars);
				$formatted_data = $formatted_date."&nbsp;&nbsp;&nbsp;".$formatted_time;

				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;

			case 'save':
				$formatted_date = $this->format_date($date, "save", $field);
				$formatted_time = $this->format_time($time, "save", $field);						
				$formatted_data = $formatted_date." ".$formatted_time;
			break;
			
			case 'formatted':
				if ($data) {
					$formatted_date = $this->format_date($date, "formatted", $field);
					$formatted_time = $this->format_time($time, "formatted", $field);						
					if (!$formatted_date) {
						if ($time != '00:00:00') {
							$formatted_data = $formatted_time;
						} else {	
							$formatted_data = '';
						}
					} else {
						$formatted_data = $formatted_date.", ".$formatted_time;
					}
				}
			break;
		}
		return $formatted_data;
	}
	
	////////////////////////////////////////////
	// Relations 
	////////////////////////////////////////////
	public function format_relation($data='', $action='edit', $field='', $possible_values=array(), $noselection='', $add_vars='', $options=array(),$addHtml='',$id='') {
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
			
				// Daten formatieren
				$dataArray = Contentomat::safeUnserialize($data);
				$dataFormatted = array();
				
				if (is_array($dataArray)) {
					
					foreach($dataArray as $selectedRelation) {
						$dataFormatted[] = $selectedRelation[0] . '_' . $selectedRelation[1];
					} 
				}
				
				
				$values = $possible_values['values'];
				$aliases = $possible_values['aliases'];

				if (!is_array($possible_values['aliases'])) {
					$possible_values['aliases'] = $possible_values['values'];
				}
		
 				$formatted_data = $this->form->select( array (
					'name' => $field . '[]',	// Achtung! Aus Feld ein Array machen, da Mehrfachauswahl möglich!
					'id' => $field,
//					'addHtml' => $addHTML,
					'optionsOnly' => false,
					'values' => $possible_values['values'],
					'aliases' => $possible_values['aliases'],
					'selected' => $dataFormatted,
					'multiple' => true
				));
				
			break;
		
			case 'formatted':

// Das hier ermittelt die Anzahl der Relationen:
// 				if (!is_array($data)) {
// 					$data = Contentomat::safeUnserialize($data);
// 				}
				
// 				if (is_array($data)) {
// 					$formatted_data = count($data);
// 				} else {
// 					$formatted_data = '0';
// 				}
				
				// Daten formatieren
				$dataArray = Contentomat::safeUnserialize($data);
				$dataFormatted = array();

				if (is_array($dataArray)) {
					
					$keys = array_flip((array)$possible_values['values']);
									
					foreach($dataArray as $selectedRelation) {

						$key = $keys[$selectedRelation[0] . '_' . $selectedRelation[1]];
						$dataFormatted[] = (array)$possible_values['aliases'][$key];
					}
				} else {
					$dataArray = array();
				}
				
				$formatted_data = implode(', ', $dataFormatted);
				
				break;
			
			case 'save':
				
				$relationData = array();
				
				if (is_array($data)) {
					
						foreach ($data as $relation) {
						$relationData[] = explode('_', $relation);
					}
				}
//				var_dump($relationData);

				$formatted_data = Contentomat::safeSerialize($relationData);
			break;
		}
		return $formatted_data;
	}
		
	////////////////////////////////////////////
	// select (Auswahlliste)
	////////////////////////////////////////////
	public function format_select($data='', $action='edit', $field='', $possible_values=array(), $noselection='', $add_vars='', $options=array()) {

		$values = $possible_values['values'];
		$aliases = $possible_values['aliases'];

		if (!is_array($values)) $values = array();
		if (!is_array($aliases)) $aliases = array();

		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
				
				$value_select = array();
				$alias_select = array();
				
				$count_aliases = count($aliases);
				$size = 1;

//print_r($values);
//echo'<p>'.$count_aliases;
//print_r($aliases);
//				if (is_array($values)) {
					/*
					foreach ($values as $key => $value) {
						$value_select[] = $this->cmt_htmlentities($value);
						if ($count_aliases) {
							$alias_select[] = $this->cmt_htmlentities($aliases[$key]);
						}
					}
					*/
//				}
				
				// No Selection einbauen?
				if ($noselection) {
					array_unshift ($values, "");
					array_unshift ($aliases, trim($noselection));
					
					// Da es mit der "keine Auswahl"-Option auf jeden Fall Aliase gibt, n�mlich mind. den einen!
					$count_aliases = true;

					// Falls es keine Auswahloptionen gibt, Leerfeld am Ende l�schen NOCH N�TIG??
					if ($values[1] == '' && count($values) == 2) {
						unset ($values[1]);
						unset ($aliases[1]);
					}
				}

				//Sonderformatierungen holen
				// TODO: 2009-02-11: Prüfen, ob nicht outdated!
				extract ($this->get_special_formats($add_vars));
//print_r($value_select);
//print_r($alias_select);
				if (!$count_aliases) {
					$aliases = $values;
				}

				$addHTML = $add_vars['html'];
				if ($add_vars['class']) {
					$addHTML .= trim(' class="' . $add_vars['class'] .'"');
				}

				$id = $field;
//var_dump($add_vars);
				// Mehrfachauswahl
				if ($options['multiple']) {
					$field = $field.'[]';
					if ($size == 1) $size = 10;

					if (!$options['multiple_separator']) {
						$options['multiple_separator'] = ';';
					} else {
						$options['multiple_separator'] = trim($options['multiple_separator']);
					}

					$data = explode($options['multiple_separator'], $data);
					
					//$addHTML = 'class = "cmtFormSelectMultiple"';
				}
				
				// check addHTML
				if (preg_match('/id\s?=/i', $addHTML)) {
					$id = '';
				}

 				$formatted_data = $this->form->select( array (
					'name' => $field,
					'id' => $id,
					'size' => $size,
					'addHtml' => $addHTML,
					'optionsOnly' => false,
					'values' => $values,
					'aliases' => $aliases,
					'selected' => $data,
					'multiple' => $options['multiple'],
					'replaceEntities' => $options['replaceEntities'],
 					'addRequestDummy' => true
				));
				// Feld ausgeben

				/*
				if ($count_aliases) {
					$formatted_data = $this->form->FormSelect($field, $value_select, $alias_select, $data, $size, $add_html);
				} else {
					$formatted_data = $this->form->FormSelect($field, $value_select, $value_select, $data, $size, $add_html);
				}
				*/
				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
		
		// Hier BUG: $key wird nicht oder falsch angegeben (Zeile 952, auch 914), wenn der Wert in $data nicht numerisch ist!?? Oder wenn es nur eiPassiert bei Tabelle
		// cmt_pages, Eintrag cmt_parentid = 'root'
			case 'formatted':
				
				if ($options['multiple']) {

					// Mehrfachauswahlfeld
					if (!$options['multiple_separator']) {
						$options['multiple_separator'] = ';';
					} else {
						$options['multiple_separator'] = trim($options['multiple_separator']);
					}

					$dataArray = explode($options['multiple_separator'], $data);
					$aliasArray = array();
					foreach ($dataArray as $value) {
						$keysFound = array_keys($values, $value);
						$aliasArray[] = $aliases[$keysFound[0]];
					}

					$formatted_data = implode(', ', $aliasArray);
				} else {

					// Einfache Auswahl
					$data = trim($data);
					$key = array_keys($values, $data);

					if ($aliases[$key[0]] || $aliases[$key[0]] === 0) {	//if ($key || $key === 0) {
						$formatted_data = $aliases[$key[0]]; //." ($data)";
					} else {
						$formatted_data = $data;
					}
				}
				break;
			
			case 'save':
				// Mehrfachauswahlfeld oder normal?
				if ($options['multiple'] && is_array($data)) {
					if (!$options['multiple_separator']) {
						$options['multiple_separator'] = ';';
					} else {
						$options['multiple_separator'] = trim($options['multiple_separator']);
					}

					if (($key = array_search('_cmtRequestEmpty', $data)) !== false) {
						unset($data[$key]);
					}
					
					$formatted_data = implode($options['multiple_separator'], $data);

				} else {
					$formatted_data = $data;
				}
			break;
		}

		return $formatted_data;
	}
	
	////////////////////////////////////////////////
	// link (Verzeichnis-Inhalt in Auswahlliste)
	////////////////////////////////////////////////
	public function format_link($link, $action="edit", $field="", $dir="all", $rootpath="", $show = "", $dont_show = "", $absolute=0, $noselection = "", $depth='', $add_vars="") {
		if (!$dir) {
			$dir = "all";
		}
		
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
				
				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));

				// Veraltete Methode: Links in einem Selectfeld
				switch ($linkMode) {
					case 'dropdownmenu':
						$dir_entries = $this->get_files($rootpath, $dir, $depth);
						// S�ubern
						if (is_array($dir_entries)) {
							foreach ($dir_entries as $key => $value) {
								$file_info = pathinfo($value);
		
								if (trim($show[0]) != "") {
									if (in_array($file_info[extension], $show)) {
										$files[] = $value;
									}
								} else
									if (trim($dont_show[0]) != "") {
										if (!in_array($file_info[extension], $dont_show)) {
											$files[] = $value;
										}
									} else {
										 $files[] = $value;
									}
								
		
							}
							
							// Ist der ausgesuchte Ordner leer???
							if (!is_array($files)) {
								$files = array();
							}
							
							natsort($files);
		
						 	//echo "pathtowebroot: ".PATHTOWEBROOT.", Webroot: ".WEBROOT."<br>";
							foreach ($files as $key => $file) {
								//$file = preg_replace("/^\.\//", "", $file); // warum das??
								
								// Kompletten Dateipfad f�r die Anzeige merken
								$file_path = $file;
							
								// Value f�r Select-Box bearbeiten
								if ($absolute) {
									$file = preg_replace("/^".preg_quote(PATHTOWEBROOT, "/")."/", "", $file);
								} else {
									$file = preg_replace("/^".preg_quote($rootpath, "/")."/", "", $file);
								}
		
								// Aliase f�r Select-Box erstellen:
								// 1. Zeichen definieren: Ordner oder nicht?
								if (is_dir ($file_path)) {
									
									$pre = "[dir]&nbsp;&nbsp;";
									$pre_file = "";
									$post = "/";
									
									$levels = 0;						
								} else {
									$pre = "[file]&nbsp;&nbsp;";
									$pre_file = "";
									$post = "";
									
									$levels = 1;
								}
														
								$files[$key] = $file;
								
								if ($absolute) {
									$file_path = WEBROOT.$file; // Pfad f�r die Anzeige
								} else {
									$file_path = $file; // Pfad f�r die Anzeige
								}
								
								// 3. Anzeige-Aliase erstellen				
								$aliases[$key] = $pre.preg_replace('/^'.preg_quote(PATHTOWEBROOT.$rootpath, '/').'/', '', $file).$post;
							}
		
							if ($noselection) {
								if (count($files)) {
									array_unshift($files, "");
									array_unshift($aliases, $noselection);
								} else {
									$files[0] = "";
									$aliases[0] = "";
								}
							}
						}
						
						$add_vars['class'] = "select_link";
						//Sonderformatierungen holen
						extract ($this->get_special_formats($add_vars));
						
						$formatted_data = $this->form->FormSelect($field, $files, $aliases, $link, "", $add_html);
					break;
					
					// Neue / aktuelle Methode: Fileselector anzeigen!	
					default:
						$formatted_data = $this->form->FormInput($field, $link, '', '', $add_html);
					break;
				}		
				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;

			case 'formatted':
				//$formatted_data = $link;
				$formatted_data = preg_replace('/^'.preg_quote(PATHTOWEBROOT.$rootpath, '/').'/', '', $link);
			break;
			
			case 'save':

				if ($dir == 'onlydir') {
					// Ist der Link ggf. kein Verzeichnis?
					if (!is_dir($rootpath.$link)) {
						$link = trim(preg_replace('/'.preg_quote($rootpath.'/', '/').'/', '', dirname($rootpath.$link)));
					}
					
					// Ist das Verzeichnis richtig formatiert??
					if ($link != '' && !preg_match('/\/$/', $link)) {
						$link .= '/';
					}
				}
				// ?????
				$formatted_data = preg_replace("/^".preg_quote(ROOT,"/")."/", "", $link);
			break;
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// flag (ja/nein)
	////////////////////////////////////////////
	public function format_flag($data, $action="edit", $field="", $value="", $add_vars="") {
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
			
				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));
							
				$data = intval($data);
				$formatted_data = $this->form->FormCheckbox($field, $data, $value, $add_html);

				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'save':
				if ($data) {
					$data = 1;
				} else {
					$data = 0;
				}
				$formatted_data = $data;
			break;
			
			case 'formatted':
				// formatiert			
				if (!$value) {
					$checked = array ("nein", "ja");
				} else {
					$checked = array ("nicht", "");
				}
				$formatted_data = trim($checked[$data]." ".$value);			
			break;
			
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// html
	////////////////////////////////////////////
	public function format_html($data, $action="edit", $field="", $html_editor=false, $add_vars="") {
		if (!$field) {
			$field = $this->field;
		}
	
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':

				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));
				
				if ($html_editor) {
					$parserVars = array();
					$parserVars['editorNr'] = $this->htmlEditorCounter++; 
					$editorID = 'htmlEditor_'.$parserVars['editorNr'];
					$parserVars['editorID'] = $editorID;
					
					if ($add_vars['templateEditorConfig']) {
						$editorTemplate = $add_vars['templateEditorConfig'];
					} else {
						$editorTemplate = $this->parser->getTemplate('app_showtable/tiny_mce_config_editor_'. strtolower($add_vars['tinymce_theme']).'.tpl');
					}
					
					if (is_array($add_vars)) {
						$parserVars = array_merge($parserVars, $add_vars);
					}

					$this->parser->setMultipleParserVars($parserVars);
					$add_formatted_data = $this->parser->parse($editorTemplate);
					$add_html .= ' id="'.$editorID.'"';

				}

				$data = stripslashes($data);
				$formatted_data .= $this->form->FormTextarea($field, $data, $cols, $rows, $add_html);
				$formatted_data .= $add_formatted_data;

				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
		
			case 'save':
				//formatiert
				$formatted_data = addslashes($data);
			break;
			
			case 'formatted':
				//$formatted_data = html_entity_decode($data, ENT_COMPAT, CHARSET);
				$formatted_data = htmlentities($data, ENT_COMPAT, CHARSET);

			break;

		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// integer
	////////////////////////////////////////////

	public function format_integer($data, $action="edit", $field="", $add_vars="") {
		if (!$field) {
			$field = $this->field;
		}
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
			
				$size = 16;
				$maxlength = 0;
				
				//Sonderformatierungen holen
//				echo 'size war: '.$size.'<br>';
				extract ($this->get_special_formats($add_vars));
//				echo 'size ist jetzt: '.$size;
				
				$formatted_data = $this->form->FormInput($field, $data, $size, $maxlength, $add_html);
						
				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'save':
				if (preg_match("/[\+\-\*\/\(\)]{1,}/", $data)) {
					eval ("\$formatted_data = $data;");
				} else {
					/*
					if ($data == "") {
						$data = NULL;
					}*/
					$formatted_data = $data;
				}
			break;
			
			case 'formatted':
				$formatted_data = $data; 
			break;
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// position
	////////////////////////////////////////////

	public function format_position($data, $action="edit", $field="", $table="", $id="", $parent_field="", $parent_value="", $add_vars="", $icon_up="icon_arrow_up_24px.png", $icon_down="icon_arrow_down_24px.png", $icon_up_disabled="icon_arrow_up_disabled_24px.png", $icon_down_disabled="icon_arrow_down_disabled_24px.png") {
		
		if (!$field) {
			$field = $this->field;
		}
		
		if (!$table) {
			$table = $this->table;
		}		

		//Sonderformatierungen holen
		extract ($this->get_special_formats($add_vars));

		switch ($action) {
			case 'duplicate':
			case 'new':

				if (!$parent_field) {
					$query = "SELECT COUNT(".$field.") as positions_counted FROM ".$table." ORDER BY ".$field." ASC";
					$this->db->Query($query);
					$r = $this->db->Get();
					$max_pos = $r['positions_counted'] + 1;
					for ($c = 1; $c<= $max_pos; $c++) {
						$pos_array[] = $c;
					}
					$data = $max_pos;
					$formatted_data = $this->form->FormSelect($field, $pos_array, "", $data, 1, $add_html);
				} else {
					$formatted_data = $this->form->FormInput($field, $data, "", "", $add_html);
					// Zus�tzlich in verstecktem Feld den Parent mitgeben
					$formatted_data .= $this->form->FormHidden($field."_parent", $parent_field);

				}
				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'edit':
				// Positionen holen
				if ($parent_field) {
					$query = "SELECT ".$parent_field." FROM ".$table." WHERE id = '".$id."'"; // Hier stand mal $GLOBALS['id']
					$this->db->Query($query);
					$r = $this->db->Get();
					$parent_value = $r[$parent_field];
					
					$query = "SELECT ".$field." FROM ".$table." WHERE ".$parent_field." = '".$parent_value."' ORDER BY ".$field." ASC";
				} else {
					$query = "SELECT ".$field." FROM ".$table." ORDER BY ".$field." ASC";
				}
				$this->db->Query($query);
				$pos_counter = 1;
				
				while ($r = $this->db->Get()) {
					if ($r[$field] == $data) {
						$data = $pos_counter;
					}
					$pos_array[$pos_counter] = $pos_counter;
					$pos_counter++;
				}
				
				if ($action == "duplicate") {
					$pos_counter++;
					$pos_array[$pos_counter] = $pos_counter;
				}					

				$formatted_data = $this->form->FormSelect($field, $pos_array, "", $data, 1, $add_html);
				
				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'save':
				//echo "Parent ist: $parent_field = $parent_value<br>";
				// H�chste Position raussuchen
				$query = "SELECT COUNT(".$field.") AS positions_counted FROM ".$table;
				if ($parent_field) {
					$query .= " WHERE ".$parent_field." = '".$parent_value."'";			
				}				
//echo "Neu: ".$query;
				$this->db->Query($query);
				$r = $this->db->Get();
				$max_position = $r['positions_counted'] + 1;
				if (!$max_position) {
					$max_position = 1;
				}
				// Falls ein Wert eingegeben wird, der gr��er ist als der Maximalwert,
				// oder der 0 ist, dann ans Ende h�ngen
				if ($data > $max_position || $data < 1 || !isset($data)) {
					$data = $max_position;
				}
				// -> Max-Position fertig

				// Alte Position und event altes Parent-Feld ermitteln
				if ($parent_field) {
					$query1 = "SELECT $field, $parent_field FROM $table WHERE id = '$id'";
				} else {
					$query1 = "SELECT $field FROM $table WHERE id = '$id'";
				}
//echo "<br>Alt: ".$query1;				
				$this->db->Query($query1);
				$r = $this->db->Get();
				$old_position = $r[$field];
				$old_parent_value = $r[$parent_field];
				$stored_position = $old_position;
				
				if (!$old_position || $parent_value != $old_parent_value) {
					$old_position = $max_position;
				}
				
				
				// -> ... alte Position fertig
				unset ($query);

				// Alles h�herer Position bewegen
				if ($old_position >= $data) {
					$query = "UPDATE ".$table." SET ".$field." = ".$field." + 1 WHERE ".$field." >= '".$data."' AND ".$field." <= '".$old_position."'";
				} else {
					$query = "UPDATE ".$table." SET ".$field." = ".$field." - 1 WHERE ".$field." <= '".$data."' AND ".$field." > '".$old_position."'"; 
				}
				// Gibt es ein Parent-Feld?
				if ($parent_field) {
					$query .= " AND ".$parent_field." = '".$parent_value."'";
				}

				// Update ausf�hren
				if ($query) {
					$this->db->Query($query);
				}

				if (!$this->db->last_errorNr()) {
					$formatted_data = $data;
				} else {
					$formatted_data = false;
				}
				
				// Aufr�umen, falls parent_value ge�ndert wurde
				if ($parent_field && ($parent_value != $old_parent_value)) {
					$query = "UPDATE $table SET $field = $field-1 WHERE $field >= '$stored_position' AND $parent_field = '$old_parent_value'";
					$this->db->Query($query);
				}
			break;
			
			case "delete":
				
				$query = "UPDATE ".$table." SET ".$field." = ".$field." - 1 WHERE ".$field." > '".$data."'";
								
				if ($parent_field) {
					$query .= " AND ".$parent_field." = '".$parent_value."'";
				}
				//echo $query.'<br>';
				$this->db->Query($query);
				
				if ($this->db->last_errorNr()) {
					return true;
				} else {
					return false;
				}
			break;
			
			case 'formatted':
				// H�chste Position herausfinden
				$query = "SELECT MAX(".$field.") AS ".$field." FROM ".$table;
				if ($parent_field) {
					//Parent-Value ermitteln
					$query1 = "SELECT ".$parent_field." FROM ".$table." WHERE id = '".$id."'";
					$this->db->Query($query1);
					$r = $this->db->Get();
					$parent_value = $r[$parent_field];
					$query .= " WHERE ".$parent_field." = '".$parent_value."'";				
				}
				//echo "<br>$query";
				$this->db->Query($query);
				$r = $this->db->Get();
				$max_position = $r[$field];

				$formatted_data .= $data."&nbsp;&nbsp;";
				if ($data > 1) {
					$formatted_data .= '<a href="'.SELFURL.'&action=move&cmt_newpos='.($data-1).'&cmt_field='.$field.'&id[]='.$id.'">';
					$formatted_data .= '<img src="'.CMT_TEMPLATE.'general/img/'.$icon_up.'" border="0" alt="hoch">';
					$formatted_data .= '</a>';
				} else {
					$formatted_data .= '<img src="'.CMT_TEMPLATE.'general/img/'.$icon_up_disabled.'">';
				}
				$formatted_data .= '&nbsp;';
				if ($data < $max_position) {
					$formatted_data .= '<a href="'.SELFURL.'&action=move&cmt_newpos='.($data+1).'&cmt_field='.$field.'&id[]='.$id.'">';
					$formatted_data .= '<img src="'.CMT_TEMPLATE.'general/img/'.$icon_down.'" border="0" alt="runter">';
					$formatted_data .= '</a>';
				} else {
					$formatted_data .= '<img src="'.CMT_TEMPLATE.'general/img/'.$icon_down_disabled.'">';
				}
			break;
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// 12. float
	////////////////////////////////////////////

	public function format_float($data, $action="edit", $field="", $round="", $add_vars="") {
		if (!$field) {
			$field = $this->field;
		}
		
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
			
				$size = 16;
				$maxlength = '';
				
				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));
			
				if ($data === NULL) {
					$data = '';
				}
				$formatted_data = $this->form->FormInput($field, $data, $size, $maxlength, $add_html);
						
				//Feldbeschreibung
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'save':
				$data = str_replace (',', '.', $data);
				if (trim($data) == "") {
					$data = NULL;
				}
				$formatted_data = $data;
				
			break;
			
			case 'formatted':
				
				//echo "data: $data";
					
				if ($round && $data != "") {
					$data = round($data, $round);
				}
	
				$formatted_data = $data;
				
			break;
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// 13. system_var
	////////////////////////////////////////////
	public function format_systemvar($data, $action="edit", $field="", $sysdata="", $user_edit=false, $add_vars="") {
		
		if (!$field) {
			$field = $this->field;
		}
		
		switch ($action) {
			case 'edit':
				$sysdata = stripslashes($data);
			case 'duplicate':
			case 'new':
				$size = 40;
				$maxlength = 0;
				
				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));
				
				// Outdated, Verhalten ge�ndert: Feld wird nur f�r Admins bearbeitbar.
				if (!$user_edit && CMT_USERTYPE != 'admin') {
					$add_html .= ' readonly="readonly"';
				}
				$formatted_data = $this->form->FormInput($field, $sysdata, $size, $maxlength, $add_html);
				// $formatted_data['description'] = $this->fielddesc;
			break;
			
			case 'save':
				if (!$user_edit && CMT_USERTYPE != 'admin') {
					$formatted_data = addslashes(stripslashes(trim($sysdata)));
				} else {
					$formatted_data = addslashes(stripslashes(trim($data)));
				}
			break;
			
			case 'formatted':
				$formatted_data = $data;
				/*
				if (CMT_USERTYPE == 'admin') {
					$formatted_data = $data;
				} else {
					$formatted_data = false;
				}*/
			break;
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	// 14. upload
	////////////////////////////////////////////
	public function format_upload($data, $action="edit", $field="", $dir="",$add_vars='', $addHtml='') {
		if (!$field) {
			$field = $this->field;
		}
		switch ($action) {
			case 'duplicate':
			case 'new':
			case 'edit':
				$size = 40;
				$maxlength = 0;
				
				//Sonderformatierungen holen
				extract ($this->get_special_formats($add_vars));
				$data = stripslashes($data);
				
				/* Alte Variante: Popup-Upload
				$formatted_data .= $this->form->FormInput($field, $data, $size);
				$formatted_data .= '&nbsp;<a href="#" onClick="startUpload(\''.urlencode($this->table).'\', \''.CMT_APPID.'\', \''.urlencode($field).'\', document.getElementsByName(\''.$field.'\')[0].value, \''.urlencode(format_directory($dir)).'\', \''.SID.'\')"><img src="'.CMT_TEMPLATE.'img/icon_upload.gif" border="0" class="icon_upload"></a>';
				*/
				
				$formatted_data .= $this->form->FormInput($field, $data, '20','', ' class="cmtFormFile" ').$addHtml;
				//$formatted_data .= $this->form->FormUpload($field.'_newfile', 'class="form_upload" style="margin-left: 16px;"');
				$formatted_data .= $this->form->FormUpload($field.'_newfile', ' class="cmtFormCustomFileInput" ');
				//$formatted_data .= $this->form->FormHidden($field.'_path', urlencode(format_directory($dir)));
				
			break;
			
			case 'save':
				$formatted_data = addslashes(stripslashes(trim($data)));
			break;
			
			case 'formatted':
				$formatted_data = $data;
			break;
		}
		return $formatted_data;
	}

	////////////////////////////////////////////
	//
	// Hilfsfunktionen
	//
	////////////////////////////////////////////

	 
	 
	/**
	 * public function createRelatedLinksselectorHtml()
	 * 
	 * Erzeugt das n�tige HTML f�r die Anzeige eines Dateiauswahlbrowsers
	 * 
	 * @param $table string Name der aktuellen Datenbanktabelle
	 * @param  $field string Name des Link-Feldes
	 * @param $data string Linkadresse
	 * @return string Gibt HTML zur�ck
	 */
	 
	 public function createRelatedLinksSelectorHtml($table, $field, $data) {
//	 	return '<b>Insgesamt:</b> <label id="count'.$field.'" name="count'.$field.'">{REL_COUNT}</label> <img src="'.CMT_TEMPLATE.'general/appinc_relationselector/img/icon_edit.png" id="RelationsSelector'.ucfirst($field).'" ' .
//	 			'class="showAddAppButton" title="Datei / Verzeichnis ausw&auml;hlen" '.
//	 			'onClick="windowOpen(\''.PATHTOADMIN.'cmt_applauncher.php?rel={REL_ID}&sid='.SID.'&launch='.CMT_APPID.
//	 			'&action='.$this->action.'&cmt_extapp=relations&my_id={MY_ID}&cmt_field='.$field.'\', \'\', \''.$field.'\');">';
	 	
		return '<b>Insgesamt:</b> <span id="count'.$field.'">{REL_COUNT}</span> ' . 
			'<a class="cmtDialog cmtDialogLoadContent cmtFileSelector" href="'.PATHTOADMIN.'cmt_applauncher.php?rel={REL_ID}&sid='.SID.'&launch='.CMT_APPID. 
 			'&action='.$this->action.'&cmt_extapp=relations&my_id={MY_ID}&cmt_field='.$field.'" data-field="'. $field .'">' .
			'<img src="'.CMT_TEMPLATE.'general/appinc_relationselector/img/icon_edit.png" ' .
 			'class="showAddAppButton" title="Relations ausw&auml;hlen"></a>';
	 }

	 
	 
	 
	 
	////////////////////////////////////////////
	// Feldbeschreibung zur�ckgeben
	////////////////////////////////////////////
	public function fieldDescription () {
		return $this->fielddesc;
	}
	
	public function getDescription() {
		return $this->fielddesc;
	}
	
	public function getAlias() {
		return $this->fieldAlias;
	}
	////////////////////////////////////////////
	// Filestruktur auslesen
	////////////////////////////////////////////
	//braucht man das noch?? Funktion in cmt_functions.inc auch vorhanden!
	public function format_directory ($directory) {
		$directory = preg_replace ("/^\.\//", "", $directory);
		$directory = preg_replace ("/^\//", "", $directory);
		$directory = preg_replace ("/\/{2,}/", "/", $directory);
		if (!preg_match("/\/$/", $directory)) {
			$directory .= "/";
		}
		
		return $directory;
	}
	
	public function get_files($dir_name, $dir, $depth, $act_depth=0) {
		//$dir_name = $this->format_directory($dir_name);
		//echo $dir_name;
		
		// Tiefe checken
		$act_depth++;
		if ($depth && $act_depth > $depth) {
			return $files;
		}
		
		if (!$dir_name) {
			$dir_name = $this->format_directory(PATHTOWEBROOT); //ROOT.WEBROOT;
		}
		
		$folder = @dir($dir_name);
		if (!isset($files)) {
			$files = array();
		}
		
		// einlesen
		if (!$folder) {
			return;
		}
		while (($entry = $folder->read()) !== false) {
			if ($entry != "." && $entry != "..") {
				$dir_struct[] = $entry;
			}
		}
		$folder->close();

		// eintr�ge erzeugen
		if (!is_array($dir_struct)) {
			//return $dir_name;
			return;
		}

		foreach ($dir_struct as $entry) {
			$entry_fullpath = $dir_name.$entry;
			
			if (is_dir($entry_fullpath)) {
				$files[] = $entry_fullpath;
				$files_in_dir = $this->get_files($this->format_directory($entry_fullpath), $dir, $depth, $act_depth);
				if (is_array($files_in_dir)) {
					$files = array_merge($files, $files_in_dir);
				}
			} else {
				/*
				if (!in_array($dir_name, $files)) {
					$files[] = $dir_name;
				}*/
				if ($dir == "all") {
					//$files[] = $dir_name;
					$files[] = $entry_fullpath;
				} /*else {
					if (!in_array($dir_name, $files)) {
						$files[] = $dir_name;
					}
				}*/
			}
		}
		return $files;
	}

	////////////////////////////////////////////
	// Kommandos und Paramter parsen
	////////////////////////////////////////////
	public function get_commands($line) {
		preg_match("/\{(.*)\}/Uis", $line, $match);
		return $match[1];
	}

	public function get_params($command, $line) {
		return trim(str_replace("{".$command."}", "", $line));
	}

    ///////////////////////////////////////////////////////////
    // Zeichenumwandlungsfunktionen
    ///////////////////////////////////////////////////////////
	public function cmt_htmlentities ($string) {
        /*$convert_special_chars['"'] = "&quot;";
        $convert_special_chars['<'] = "&lt;";
        $convert_special_chars['>'] = "&gt;";
        return strtr ($string, $convert_special_chars); */
		return htmlspecialchars($string, ENT_COMPAT, CHARSET);
	}

	////////////////////////////////////////////
	// HTML-Entities zur�ckwandeln
	////////////////////////////////////////////
	public function unhtmlentities($string) {
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}

	public function get_special_formats($add_vars) {
		$possible_settings = array ("cols", "rows", "width", "height", "class", "style", "readonly", "size", "maxlength", "disabled", "html");
		$style_vars = array();
		$return_vars = array();
		
		foreach ($possible_settings as $setting) {
			if (isset($add_vars[$setting])) {
				switch ($setting) {
					case "size":
						$return_vars['size'] = $add_vars['size'];
					break;
					
					case "maxlength":
						$return_vars['maxlength'] = $add_vars['maxlength'];
					break;
					
					case "cols":
						$return_vars['cols'] = $add_vars['cols'];
					break;
					
					case "rows":
						$return_vars['rows'] = $add_vars['rows'];
					break;

					case "width":
						$style_vars['width'] = "width: ".$add_vars['width'];						
					break;

					case "height":
						$style_vars['height'] = "height: ".$add_vars['height'];
					break;
					
					case "class":
						$return_vars['add_html'] = " class=\"".$add_vars['class']."\"";
					break;
					
					case "style":
						$add_vars['style'] = preg_replace("/^\"/", "", trim($add_vars['style']));
						$add_vars['style'] = preg_replace("/\"$/", "", trim($add_vars['style']));
						$style_vars['style'] = $add_vars['style'];
					break;
					
					case "readonly":
						$return_vars['add_html'] .= ' readonly="readonly"';
					break;

					case "disabled":
						$return_vars['add_html'] .= " disabled";
					break;

					case "html":
						$return_vars['add_html'] .= $add_vars['html'];
					break;
				}
			}
		}
		$return_style_vars = trim(implode("; ", $style_vars));
		if ($return_style_vars) {
			$return_vars['add_html'] .= " style=\"".$return_style_vars."\"";
		}
	
		return $return_vars;
	}

	public function get_tabledata_recursive($table, $parent, $parent_field, $parent_value, $alias_field, $level, $add_query, $pages, $path='', $multiple=false) {

		$db = new DBCex();
		$query = "SELECT $parent, $parent_field";
		if ($alias_field) {
			$query .= ", $alias_field";
		}

		$query .= " FROM $table WHERE $parent_field = '$parent_value' ".$add_query;
		$db->query($query);

		if (!is_array($pages)) {
			$pages = array();
		}
		while ($r = $db->get()) {
			$pages['values'][] = $r[$parent];

			if (!$multiple && $this->action != 'formatted') {
				$pages['aliases'][] = str_pad ("", $level*21, '│&nbsp;&nbsp;&nbsp;', STR_PAD_LEFT).'└─&nbsp;'.$this->cmt_htmlentities($r[$alias_field]);
			} else {
				$pages['aliases'][] = $path . $this->cmt_htmlentities($r[$alias_field]);
			}
			
			$suffix =  $path . $this->cmt_htmlentities($r[$alias_field]) .'&nbsp;&rarr; ';
			$pages = $this->get_tabledata_recursive($table, $parent, $parent_field, $r[$parent], $alias_field, $level+1, $add_query, $pages, $suffix, $multiple);
		}
		unset ($db);
		
		return $pages;
	}

// Klasse-Ende
}
?>
