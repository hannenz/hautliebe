<?php
/**
 * class_fieldhandler.inc 
 * Kurzkommentar
 *
 * Langkommentar
 * 
 * @version 2017-03-30
 * @author J.Hahn <info@buero-hahn.de>
 */

namespace Contentomat;

class FieldHandler {

 	private $dbTable;
 	private $db;
 	private $errorNr;
 	private $errorMessage;
 	
 	private $cacheIndexByFields;
 	private $cacheIndexByNames;
 	private $cmt;

	private $fieldTypeWrapper;
	
	protected $fieldsTable;

 	/**
 	 * Konstruktor
 	 */
 	public function __construct($params=array()) {
 		
 		$this->db = new DBCex();
 		$this->cmt = Contentomat::getContentomat();
 		
 		$this->cacheIndexByNames = array();
 		$this->cacheIndexByFields = array();
 		
 		$this->dbTable = '';
 		
 		$this->fieldsTable = 'cmt_fields';

		$this->fieldTypeWrapper = array (
			'string' => 'VARCHAR(255)',
			'text' => 'LONGTEXT',
			'integer' => 'INT',
			'float' => 'FLOAT',
			'flag' => 'TINYINT',
			'date' => 'DATE',
			'datetime' => 'DATETIME',
			'time' => 'TIME',
			'relation' => 'TEXT',
			'select' => 'VARCHAR(255)',
			'select_recursive' => 'VARCHAR(255)',
			'link' => 'VARCHAR(255)',
			'html' => 'LONGTEXT',
			'position' => 'INT NOT NULL',
			'system_var' => 'VARCHAR(255)',
			'upload' => 'VARCHAR(255)'
		);
 	}

	/**
	 * public function editField()
	 * Speichert Daten für ein bereits existierendes Feld. Erwartet Parameter in einem Array
	 *
	 * @param number id Eindeutige ID des Feldes in cmt_fields
	 * @param string tableName Tabellenname. Falls id nicht vorhanden, wird Tabellen- und Feldname benötigt
	 * @param string fieldName Datenbankfeldname
	 * @param string fieldType Feldtyp (CMS)
	 * @param string fieldAlias Optional: Aliasname des Feldes
	 * @param string fieldDefaultValue Optional: Defaultwert des Feldes bei Neueintrag
	 * @param string fieldDescription Optional: Feldbeschreibung
	 * @param string fieldOptions Optional: Feldoptionen
	 *
	 * @return bool True oder false
	 */
	public function editField($params=array()) {
//$debug = new Debug();
		$defaultParams = array(
			'action' => 'edit'
		);
		$params = array_merge($defaultParams, $params);

		$params['id'] = intval($params['id']);
//$debug->log($params);
		// Alte Daten holen
		$fieldData = $this->getField(array(
			'id' => $params['id'],
			'tableName' => $params['tableName'],
			'fieldName' => $params['fieldName']
		));
//$debug->log($fieldData);
		if (!$params['fieldType']) $params['fieldType'] = $fieldData['cmt_fieldtype'];
		if (!$params['fieldName']) $params['fieldName'] = $fieldData['cmt_fieldname'];
		if (!$params['tableName']) $params['tableName'] = $fieldData['cmt_tablename'];

		// Muss Feldtyp oder Feldname geändert werden?
		if ($params['action'] == 'edit' && (($params['fieldName'] && $params['fieldName'] != $fieldData['cmt_fieldname']) || ($params['fieldType'] && $params['fieldType'] != $fieldData['cmt_fieldtype']))) {
			$query = 'ALTER TABLE '.$this->db->dbQuote($params['tableName']).' '.
					 'CHANGE '.$fieldData['cmt_fieldname'].' '.$this->db->dbQuote($params['fieldName']).
					 ' '.$this->fieldTypeWrapper[$params['fieldType']];

			$this->db->query($query);

			if (!$this->checkError(5200)) {
//$debug->log('Rename field failed!');
				return false;
			}
		}
//$debug->log('Rename field passed!');
		// Muss Feld neu erzeugt werden?
		$fieldInfo = $this->db->getFieldinfo($params['tableName']);
		
		if (($params['action'] == 'new' || $params['action'] == 'duplicate') && !$fieldInfo['name'][$params['fieldName']]) {
// var_dump('erzeuge '. $params['fieldName']);
			
			$query = 'ALTER TABLE '.$this->db->dbQuote($params['tableName']).' '.
					 'ADD '.$this->db->dbQuote($params['fieldName']).
					 ' '.$this->fieldTypeWrapper[$params['fieldType']];
			$this->db->query($query);

			if (!$this->checkError(5201)) {
//$debug->log('New field failed!');
				return false;
			}
		}
//$debug->log('New field passed!');
		// Diverse Werte für Feldtypen vorbereiten
		
		switch ($params['fieldType']) {

			case 'datetime':
				
				if (!is_array($params['fieldDefaultValue'])) {
					$params['fieldDefaultValue'] = array();
				}
				
				$params['fieldDefaultValue'] = $this->validateFormData(array(
					'fieldType' => 'datetime',
					'valueYear' => $params['fieldDefaultValue']['year'],
					'valueMonth' => $params['fieldDefaultValue']['month'],
					'valueDay' => $params['fieldDefaultValue']['day'],
					'valueHour' => $params['fieldDefaultValue']['hour'],
					'valueMinute' => $params['fieldDefaultValue']['minute'],
					'valueSecond' => $params['fieldDefaultValue']['second']
				));
				break;

			case 'date':
				
				if (!is_array($params['fieldDefaultValue'])) {
					$params['fieldDefaultValue'] = array();
				}
				
				$params['fieldDefaultValue'] = $this->validateFormData(array(
					'fieldType' => 'date',
					'valueYear' => $params['fieldDefaultValue']['year'],
					'valueMonth' => $params['fieldDefaultValue']['month'],
					'valueDay' => $params['fieldDefaultValue']['day']
				));
				break;

			case 'time':
				
				if (!is_array($params['fieldDefaultValue'])) {
					$params['fieldDefaultValue'] = array();
				}
				
				$params['fieldDefaultValue'] = $this->validateFormData(array(
					'fieldType' => 'time',
					'valueHour' => $params['fieldDefaultValue']['hour'],
					'valueMinute' => $params['fieldDefaultValue']['minute'],
					'valueSecond' => $params['fieldDefaultValue']['second']
				));
				break;
		}

		// Daten speichern
		if ($params['action'] == 'new' || $params['action'] == 'duplicate') {
			$queryStart = 'INSERT INTO cmt_fields ';
			$queryEnd = '';
		} else {
			$queryStart = 'UPDATE cmt_fields ';
			$queryEnd = " WHERE id = '".$params['id']."'";
		}
// TODO: So müssen immer alle Werte übergeben werden, statt nur eines, wenn z.B. nur ein Wert geändert werden soll!
		$query = $queryStart.
			" SET ".
 			"cmt_tablename = '".$this->db->dbQuote($params['tableName'])."', " .
			"cmt_fieldname = '".$this->db->dbQuote($params['fieldName'])."', " .
			"cmt_fieldtype = '".$this->db->dbQuote($params['fieldType'])."', " .
			"cmt_fieldalias = '".$this->db->dbQuote($params['fieldAlias'])."', " .
			"cmt_default = '".$this->db->dbQuote($params['fieldDefaultValue'])."', " .
			"cmt_fielddesc = '".$this->db->dbQuote($params['fieldDescription'])."', " .
			"cmt_options = '".$this->db->dbQuote($this->cmt->safeSerialize($params['fieldOptions']))."', " .

			// --> Nicht mehr benötigte Felder leeren
			"cmt_index = NULL, ".
			"cmt_fieldquery = NULL ".
			// <--
			$queryEnd;
//$debug->log($query);
		// Speichern
		$this->db->query($query);

		//return (boolean) !$this->db->getLastErrorNr();
		if ($this->db->getLastErrorNr()) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * public  function changeFieldsTable()
	 * Changes the tablename of the field in internal table "cmt_fields"
	 *
	 * @param string $oldTableName Name of the old table
	 * @param string $newTableName New table name
	 *
	 * @return boolean
	 */
	public function changeFieldsTable($oldTableName, $newTableName) {
		
		$fields = $this->getAllFields(array(
			'tableName' => 	$oldTableName,
			'getAll' => true
		));
		
		foreach ($fields as $fieldName => $fieldData) {
			$check = $this->db->query("UPDATE " . $this->fieldsTable . " SET cmt_tablename = '" . $this->db->dbQuote($newTableName) . "' WHERE id='" . $fieldData['id'] ."'");
			
			if ($check) {
				return false;
			}
		}
		
		return true;
	}

	/**
	 * public function deleteField() 
	 * Löscht ein Feld in der Datenbanktabelle und analog in der Systemtabelle 'cmt_fields'.
	 * Erwartet Parameter in einem Array.
	 * 
	 * @param number id	Fields entry id in cmt_fields
	 * @param string tableName Optional: If id isn't known the names of the table and the field can be given.
	 * @param string fieldName Optional
	 * 
	 * @return bool
	 */	
	public function deleteField($params=array()) {

 		// Default Einstellungen
		$defaultParams = array (
			'id' => 0,
			'tableName' => '',
			'fieldName' => ''
		);
 		$params = array_merge ($defaultParams, $params);
 		
 		// Wurde eine ID übergeben?
//  		$fieldId = intval($params['id']);
//  		if (!$fieldId) {
//  			$this->setError(5203);
//  			return false;
//  		}
 		
 		// relevante Eintragsinformationen auslesen
		$r = $this->getField($params);
		$fieldName = $r['cmt_fieldname'];
		$tableName = $r['cmt_tablename'];
		$fieldId = $r['id'];
 		
 		if (!$fieldName || !$tableName) return false;
 		
 		// 1. Löschen des Feldes in Systemtabelle
 		$this->db->query("DELETE FROM cmt_fields WHERE id = '".$fieldId."'");
 		
 		if ($this->checkError(5102)) {

			// 2. Entfernen der Spalte aus der Datenbanktabelle
			$this->db->query("ALTER TABLE ".$tableName." DROP COLUMN ".$fieldName);
			return $this->checkError(5103);
			
 		} else {
 			return false;
 		}
	}

	/**
	 * public function deleteAllFields()
	 * Deletes all fields in a table (also all Content-o-mat meta data for the fields)
	 *
	 * @param string $tableName Name of the database table
	 *
	 * @return boolean
	 */
	public function deleteAllFields($tableName) {
	
		$fieldNames = $this->getFieldNames($tableName);
// 		$debug = new Debug();
// 		$debug->info($fieldNames);	
		foreach ($fieldNames as $fieldName) {
			$check = $this->deleteField(array(
				'tableName' => $tableName,
				'fieldName' => $fieldName
			));
			
			if (!$check) {
				return false;
			}
		}
		
		return true;
	
	}

	/**
	 * public function getField() 
	 * Gibt die Daten eines Eintrags in der Systemtabelle 'cmt_fields' zurück. Erwartet Parameter in einem Array
	 * 
	 * @param number id			Entweder: Eindeutige ID des Eintrags
	 * @param string tableName	Oder: Tabellenname und ...
	 * @param string fieldName	... Feldname werden benötigt
	 * 
	 * @return array Array mit den Felddaten oder leeres Array
	 */	
	public function getField($params=array()) {

 		$defaultParams = array (
			'id' => 0,
			'tableName' => $this->dbTable,
			'fieldName' => ''
		);
 		$params = array_merge ($defaultParams, $params);
 		
 		
		$fieldId = intval($params['id']);
		
		// Fehlen Daten?
		if (!$fieldId && (!$params['tableName'] || !$params['fieldName'])) return array();
		
		
		if (!$fieldId) {
			// Daten ermitteln ohne id
			$this->db->query(
				"SELECT * FROM cmt_fields " .
				"WHERE cmt_tablename = '".$this->db->mysqlQuote($params['tableName'])."' " .
				"AND cmt_fieldname = '".$this->db->mysqlQuote($params['fieldName'])."' " .
				"LIMIT 1"
			);
			$fieldData = $this->db->get();
		} else {
			// Daten ermitteln mit id
			$this->db->query("SELECT * FROM cmt_fields WHERE id = '".$fieldId."'");
			$fieldData = $this->db->get();
		}

		// Daten verarbeiten
		$fieldOptions = $this->cmt->safeUnserialize($fieldData['cmt_options']);
		if (is_array($fieldOptions)) {
			//print_r($fieldOptions);
			foreach ($fieldOptions as $optionName => $optionValue) {

				// Optionstitel sind zusammengesetzt: cmt_option_{Feldtyp}_{Option}
				$optionTitle = 'cmt_option_'.$fieldData['cmt_fieldtype'].'_'.$optionName;

				// Manche Feldtypen (z.B. Select, speichern Daten in einem Unterarray
				if (is_array($optionValue)) {
					// Fieldtype Relation Sub-Array
					if ($fieldData['cmt_fieldtype']=='relation'){
						//Relation Tables (Multi Tables Select)
						$tableCount = count($optionValue);
						$tableNr = 1;
						foreach($optionValue as $table => $fields){
							if(!$fields['name']) {
								continue;
							}
							$optionTitleTable = $optionTitle. "_".$tableNr;			
							foreach($fields as $fieldname => $value){
								//Field name is like cmt_option_relation_tables_1_value_field
								$optionTitleField = $optionTitleTable."_".$fieldname;
								$fieldData[$optionTitleField] = $value;
							}
							$tableNr++;
						}
						$fieldData[$optionTitle. "_count"]=$tableCount;
						continue;	
					}
					else
					{
						$optionValue = implode("\n", $optionValue);
					}
				}

				$fieldData[$optionTitle] = $optionValue;
			}
		}

		// Default-Wert muss ebenfalls dem Feldtyp angepasst werden
		$fieldData['cmt_default_'.$fieldData['cmt_fieldtype']] = $fieldData['cmt_default'];

		// Manche Feldtypen müssen gesondert bearbeitet werden
		$addData = array();
		switch($fieldData['cmt_fieldtype']) {

			case 'datetime':
				preg_match('/^(.{4})-(.{2})-(.{2}) (.{2}):(.{2}):(.{2})$/', $fieldData['cmt_default'], $parts);

				$addData = array (
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_year' => $parts[1],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_month' => $parts[2],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_day' => $parts[3],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_hour' => $parts[4],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_minute' => $parts[5],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_second' => $parts[6]
				);
				break;

			case 'date':
				preg_match('/^(.{4})-(.{2})-(.{2})$/', $fieldData['cmt_default'], $parts);

				$addData = array (
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_year' => $parts[1],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_month' => $parts[2],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_day' => $parts[3],
				);
				break;

			case 'time':
				preg_match('/^(.{2}):(.{2}):(.{2})$/', $fieldData['cmt_default'], $parts);

				$addData = array (
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_hour' => $parts[4],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_minute' => $parts[5],
					'cmt_default_'.$fieldData['cmt_fieldtype'].'_second' => $parts[6]
				);
				break;
		}

		return array_merge($fieldData, $addData);
	}
	
	/**
	 * public function getAllFields() 
	 * Gibt alle Felddaten einer Tabelle aus der Systemtabelle 'cmt_fields' zur�ck. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName		Tabellenname
	 * @param number itemsPerPage	Anzahl der Eintr�ge, die pro Seite angezeigt werden
	 * @param string currentPage	Aktuelle Seite
	 * 
	 * @return array Multidimensionales Array mit den Felddaten
	 */	
	public function getAllFields($params=array()) {
// TODO: Paging hier ist doch Quatsch, bzw. per default sollte getAll 'true' sein!
 		$defaultParams = array (
			'tableName' => $this->dbTable,
			'itemsPerPage' => 10,
			'currentPage' => 1,
			'getAll' => false
		);
 		$params = array_merge ($defaultParams, $params);
 		
 		$params['itemsPerPage'] = intval($params['itemsPerPage']);
 		$startIndex = (intval($params['currentPage']) - 1) * $params['itemsPerPage'];
 		
		$fieldsData = array();
				
		// Daten ermitteln ohne id
		$query = 	"SELECT * FROM cmt_fields " .
					"WHERE cmt_tablename = '".$this->db->dbQuote($params['tableName'])."' " .
					"ORDER BY cmt_fieldname ASC ";
		if (!$params['getAll']) {
			$query .= "LIMIT ".$startIndex.",".$params['itemsPerPage'];
		}
		
		$this->db->query($query);

		while($r = $this->db->get()) {
			$fieldsData[$r['cmt_fieldname']] = $r;
		}
		return $fieldsData;	
	}
	

	public function duplicateField() {
		
	}

	/**
	 * 
	 * public protected function addField()
	 * Adds a new (Content-o-mat) field to a table. Params must be the same as in method FieldHandler::editField() 
	 *
	 * @param array $params Associative array with field params.
	 *
	 * @return boolean
	 */
	public function addField($params) {
		$params['action'] = 'new';
		unset($params['id']);
		
		return $this->editField($params);
	}

	/**
	 * public function getFieldNames()
	 * Returns the real/ database field names of a table 
	 *
	 * @param string $tableName Name of the a database table
	 *
	 * @return return_type
	 */
	public function getFieldNames($tableName, $nameAsKey=true) {
		
		$fieldsData = $this->getAllFields(array(
			'tableName' => $tableName,
			'getAll' => true
		));

		// add primary field id
//		$fieldsData['id'] = array('cmt_fieldalias' => 'ID');
		
		if ($nameAsKey) {
			return array_combine(array_keys($fieldsData), array_keys($fieldsData));
		} else {
			return array_keys($fieldsData);
		}
	}
	
 	public function getFieldAliases($tableName, $nameAsKey=true) {
 		$fieldsData = $this->getAllFields(array(
 			'tableName' => $tableName,
 			'getAll' => true
 		));	

 		$fieldAliases = array();
 		foreach($fieldsData as $fieldName => $field) {
 			$fieldAliases[$fieldName] = $field['cmt_fieldalias'];
 		}
 		
 		// add primary field 'id'
// 		$fieldAliases['id'] = 'ID';
 		
 		if ($nameAsKey) {
 			return $fieldAliases;
 		} else {
 			array_values($fieldAliases);
 		}
	}

	public function getFieldTypes($tableName) {
		$fieldsData = $this->getAllFields(array(
			'tableName' => $tableName,
			'getAll' => true
		));
	
		$fieldTypes = array();
		foreach($fieldsData as $fieldName => $field) {
			$fieldTypes[$fieldName] = $field['cmt_fieldtype'];
		}
			
		return $fieldTypes;
	}	

	/**
	 * public function addIndex() 
	 * Fügt einen INDEX über eine oder mehrere Spalten hinweg ein. Erwartet Parameter in einem Array
	 * 
	 * http://dev.mysql.com/doc/refman/5.1/de/multiple-column-indexes.html
	 * 
	 * @param string tableName	Tabellenname
	 * @param mixed fieldName	Name des oder der Felder - entweder String oder Array
	 * @param array indexFieldLength	Optionale L�ngen der Felder im Index (array[Feldname=> Feldl�nge in Bytes)
	 * 
	 * @return bool True oder false, je nach Erfolg
	 */
	public function addIndex($params=array()) {
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'indexField' => array(),
							'indexFieldLength' => array(),
							'indexType' => 'INDEX'
						);
 		$params = array_merge ($defaultParams, $params);
 		
 		if (!is_array($params['indexField'])) {
 			$params['indexField'][0] = $params['indexField'];
 		}
 		
 		if (empty($params['indexField'])) {
 			$this->setError(5120);
 			return false;
 		}
 		
 		// Wurde ein Name �bergeben
 		$indexName = '';
 		if ($params['indexName']) $indexName = trim($params['indexName']);

 		// Wurden Index-Feldl�ngen �bergeben
 		foreach ($params['indexField'] as $key => $fieldName) {
 			$fieldLength = intval($params['indexFieldLength'][$fieldName]);

 			if ($fieldLength) {
 				$params['indexField'][$key] = $params['indexField'][$key].'('.$fieldLength.')';
 			}
 		}

 		$query = "ALTER TABLE ".$params['tableName']." ADD ".$params['indexType']." ".$indexName." (".implode(',', $params['indexField']).")";
 		$this->db->query($query);

		// Zwischengespeicherte Daten l�schen
		$this->deleteCacheIndex($params);

 		return $this->checkError(5121); 
	}

	/**
	 * public function updateIndex() 
	 * Speichert einen Index (INDEX oder FULLTEXT) über eine oder mehrere Spalten hinweg ein. Erwartet Parameter in einem Array
	 * 
	 * http://dev.mysql.com/doc/refman/5.1/de/multiple-column-indexes.html
	 * 
	 * @param string tableName	Tabellenname
	 * @param string indexName	Name des Indexes
	 * @param mixed indexField	Name (string) oder Namen (array) des oder der Felder des Indexes
	 * @param array indexFieldLength	Optionale L�ngen der Felder im Index (array[Feldname=> Feldl�nge in Bytes)
	 * 
	 * @return bool True oder false, je nach Erfolg
	 */
	public function updateIndex($params=array()) {
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'indexName' => '',
							'indexNewName' => '',
							'indexField' => array(),
							'indexFieldLength' => array(),
							'indexType' => 'INDEX'
						);
 		$params = array_merge ($defaultParams, $params);
	
 		if (!is_array($params['indexField'])) {
 			$params['indexField'][0] = $params['indexField'];
 		}

		// Ge�nderter Index-Name �bergeben?
		if (!$params['indexNewName']) $params['indexNewName'] = $params['indexName'];
		
 		if (empty($params['indexField'])) {
 			return $this->removeIndex($params);
 		}

 		if (!$params['indexName']) {
 			$this->setError(5125);
 			return false;
 		} 

 		
 		// Wurden Index-Feldl�ngen �bergeben
 		foreach ($params['indexField'] as $key => $fieldName) {
 			$fieldLength = intval($params['indexFieldLength'][$fieldName]);

 			if ($fieldLength) {
 				$params['indexField'][$key] = $params['indexField'][$key].'('.$fieldLength.')';
 			}
 		}

 		$query = "ALTER TABLE ".$params['tableName']." DROP INDEX ".$params['indexName'].", " .
 				 "ADD ".$params['indexType']." ".$params['indexNewName']." (".implode(',', $params['indexField']).")";
 		$this->db->query($query);

		// Zwischengespeicherte Daten l�schen
		$this->deleteCacheIndex($params);
//echo $query;
 		return $this->checkError(5126); 
	}

	/**
	 * public function deleteIndex() 
	 * Entfernt einen INDEX. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * @param string indexName	Name des Index'
	 * 
	 * @return bool True oder false, je nach Erfolg
	 */	
	public function deleteIndex($params=array()) {
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'indexName' => ''
						);
 		$params = array_merge ($defaultParams, $params);
 		
 		if (!$params['indexName']) {
 			$this->setError(5122);
 			return false;
 		}

 		$query = "ALTER TABLE ".$params['tableName']." DROP INDEX ".$params['indexName'];
 		$this->db->query($query);

		// Zwischengespeicherte Daten l�schen
		$this->deleteCacheIndex($params);

 		return $this->checkError(5123); 
		
	}

	/**
	 * public function isIndex() 
	 * Pr�ft ob ein Feld oder ein Index vom Typ INDEX ist (BTREE,...)
	 * 
	 * @param string tableName	Tabellenname
	 * @param string fieldName	Feldname oder
	 * @param string indexName	Name des Index
	 * 
	 * @return bool True oder false, je nach Erfolg
	 */	
	public function isIndex($params=array()) {
		$indexType = $this->getIndexType($params);

		if (in_array('BTREE', $indexType) || in_array('RTREE', $indexType) || in_array('HASH', $indexType)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function isFulltext() 
	 * Prüft ob ein Feld oder ein Index vom Typ FULLTEXT ist. Erwartet Parameter in einem Array.
	 * 
	 * @param string tableName	Tabellenname
	 * @param string fieldName	Feldname oder
	 * @param string indexName	Name des Index
	 * 
	 * @return bool True oder false, je nach Erfolg
	 */	
	public function isFulltext($params=array()) {
		$indexType = $this->getIndexType($params);

		if (in_array('FULLTEXT', $indexType)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function getIndexType() 
	 * Ermittelt den MySQL-Typ eines Index anhand eines Feld- oder Indexnamens
	 * 
	 * @param string tableName	Tabellenname
	 * @param string fieldName	Feldname oder
	 * @param string indexName	Name des Index
	 * 
	 * @return array Da bei der auch nach Feldnamen gesucht werden kann und diese in mehreren Indizes vorkommen k�nnen, wird ein Array mit dem oder den Index-Type zur�ckgegeben (FULLTEXT, BTREE, etc...)
	 */	
	public function getIndexType($params=array()) {
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'fieldName' => '',
							'indexName' => ''
						);
 		$params = array_merge ($defaultParams, $params);

		if ($params['indexName']) {

			// Indextyp ermitteln
			$tableIndex = $this->getIndexByNames(array('tableName' => $params['tableName']));

			return array($tableIndex[$params['indexName']]['indexType']);
		
		} else if ($params['fieldName']) {
			// Indextyp aus Feldnamen ermitteln
			$indexTypes = array();
			$tableIndex = $this->getIndexByFields(array('tableName' => $params['tableName']));
		
			if (is_array($tableIndex[$params['fieldName']])) {
				
				foreach ($tableIndex[$params['fieldName']] as $indexName => $indexData) {
					$indexTypes[] = $indexData['indexType'];
				}
			}
		
			return $indexTypes;
		} else {
			return array();
		}
	}

	/**
	 * public function addFulltext() 
	 * F�gt einen FULLTEXT-Index �ber eine oder mehrere Spalten hinweg ein. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * @param mixed fieldName	Name des oder der Felder - entweder String oder Array
	 * 
	 * @return bool True oder false, je nach Erfolg
	 */
	public function addFulltext($params=array()) {
		$params['indexType'] = 'FULLTEXT';
		return $this->addIndex($params);
	}
	
	/**
	 * public function getIndex() 
	 * Liefert das von der Datenbank zur�ckgegebene Array mit Index-Informationen. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * @param mixed fieldName	Name des oder der Felder - entweder String oder Array
	 * @param array except		Optionales Array: Enth�lt Feldernamen, die nicht im Ergebniss auftauchen sollen
	 * 
	 * @return bool True oder false, je nach Erfolg
	 */
	public function getIndex($params) {
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'databaseName' => '',
							'except' => array()
						);
 		$params = array_merge ($defaultParams, $params);

 		$query = "SHOW INDEX FROM ".$params['tableName'];
 		
 		if ($params['databaseName']) $query .= " FROM ".$params['databaseName'];

 		$this->db->query($query);

 		if (!$this->checkError(5110)) {
 			return false;
 		} else {
 			$index = array();
 			while ($r = $this->db->get()) {
 				if (!in_array($r['Column_name'], $params['except'])) {
 					$index[] = $r;
 				}	
 			}
 			
 			return $index;
 		}  		
	}

	/**
	 * public function getIndexByNames() 
	 * Sortiert das von der Datenbank zur�ckgegebene Array mit Index-Informationen um: Schl�ssel sind die INDEX-Namen. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * @param mixed fieldName	Name des oder der Felder - entweder String oder Array
	 * @param array except		Optionales Array: Enth�lt Feldernamen, die nicht im Ergebniss auftauchen sollen
	 * 
	 * @return mixed False oder Array mit Daten
	 */
	public function getIndexByNames($params) {
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'databaseName' => '',
							'except' => array()
						);
 		$params = array_merge ($defaultParams, $params);

		// Inhalte gecacht?
		if (is_array($this->cacheIndexByNames[$params['tableName']])) {
			return $this->cacheIndexByNames[$params['tableName']];
		} else {
			$index = $this->getIndex($params);
		
			if (!is_array($index)) {
	 			$this->setError(5111);
	 			return false;			
			}
			
			// Daten umsortieren
			$indexData = array();
	
			foreach ($index as $key => $iA) {
				$indexName = $iA['Key_name'];
				$indexData[$indexName]['indexType'] = $iA['Index_type'];
				$indexData[$indexName]['indexLength'] = $iA['Sub_part'];
				$indexData[$indexName]['indexField'][] = $iA['Column_name'];
			}
			
			// Felder den Indizes zuordnen
			foreach ($indexData as $indexName => $iA) {
				$indexData[$indexName]['indexField'] = implode(',', $iA['indexField']);
			}
	
			// Ergebnis zwischenspeichern
			$this->cacheIndexByNames[$params['tableName']] = $indexData;
	
			return $indexData;
		}
	}
 	

	/**
	 * public function getIndexByFields() 
	 * Sortiert das von der Datenbank zur�ckgegebene Array mit Index-Informationen um: Schl�ssel sind die Feld-Namen. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * @param string databaseName	Optional: Datenbankname (nur der Vollst�ndigkeit halber)
	 * 
	 * @return mixed False oder Multidimensionales Array mit Daten
	 */
	public function getIndexByFields($params) {
 		$defaultParams = array (
							'tableName' => $this->dbTable,
							'databaseName' => ''
						);
 		$params = array_merge ($defaultParams, $params);

		// Inhalte gecacht?
		if (is_array($this->cacheIndexByFields[$params['tableName']])) {
			return $this->cacheIndexByFields[$params['tableName']];
		} else {
			$index = $this->getIndex($params);
		
			if (!is_array($index)) {
	 			$this->setError(5112);
	 			return false;			
			}
			
			// Daten umsortieren
			$indexData = array();
	
			foreach ($index as $key => $iA) {
				$fieldName = $iA['Column_name'];
				$indexData[$fieldName][$iA['Key_name']]['indexType'] = $iA['Index_type'];
				$indexData[$fieldName][$iA['Key_name']]['indexLength'] = $iA['Sub_part'];
				$indexData[$fieldName][$iA['Key_name']]['indexName'] = $iA['Key_name'];
			}
			
			// Ergebnis zwischenspeichern
			$this->cacheIndexByFields[$params['tableName']] = $indexData;
			
			return $indexData;
		}
	}

	/**
	 * private function deleteCacheIndex() 
	 * L�scht alle zwischengespeicherten Index-Informationen. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * 
	 * @return void
	 */
	private function deleteCacheIndex($params=array()) {
		if ($params['tableName']) {
			$this->deleteCacheIndexByFields($params);
			$this->deleteCacheIndexByNames($params);	
		}
	}

	/**
	 * private function deleteCacheIndexByNames( 
	 * Löscht die zwischengespeicherten Index-Informationen f�r IndexByNames einer Tabelle. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * 
	 * @return void
	 */
	private function deleteCacheIndexByNames($params=array()) {
		if ($params['tableName']) unset($this->cacheIndexByNames[$params['tableName']]);
	}

	/**
	 * private function deleteCacheIndexByFields() 
	 * L�scht die zwischengespeicherten Index-Informationen f�r IndexByFieldss einer Tabelle. Erwartet Parameter in einem Array
	 * 
	 * @param string tableName	Tabellenname
	 * 
	 * @return void
	 */
	private function deleteCacheIndexByFields($params=array()) {
		if ($params['tableName']) unset($this->cacheIndexByFields[$params['tableName']]);
	}

 	public function validateFormData($params=array()) {

		switch ($params['fieldType']) {

			case 'integer':
				return intval($params['value']);
				break;

			case 'float':
				return floatval($params['value']);
				break;

			case 'datetime':
				return sprintf("%04d-%02d-%02d %02d:%02d:%02d", $params['valueYear'], $params['valueMonth'], $params['valueDay'], $params['valueHour'], $params['valueMinute'], $params['valueSecond']);
				break;

			case 'date':
				return sprintf("%04d-%02d-%02d", $params['valueYear'], $params['valueMonth'], $params['valueDay']);
				break;

			case 'time':
				return sprintf("%02d:%02d:%02d", $params['valueHour'], $params['valueMinute'], $params['valueSecond']);
				break;

		}

	}
	
	
 	private function checkError($errorNr) {
 		if ($this->db->getLastErrorNr()) {
 			$this->setError($errorNr);
 			return false;
 		} else {
 			return true;
 		}
 	}
 	
 	private function setError($errorNr) {
 		$this->errorMessage = $this->db->getLastError();
 		$this->errorNr = $errorNr;
 	}
 	
 	public function getErrorNr() {
 		return $this->errorNr;
 	}

 	public function getError() {
 		return $this->errorMessage;
 	}

	/**
	 * public function setTable() 
	 * Setzt den Tabellenname als Klassenvariable. Dieser muss dann nicht mehr in den Methoden übergeben werden.
	 * 
	 * @param string $dbTable	Datenbankname der Tabelle
	 * 
	 * @return void
	 */		

 	public function setTable($dbTable) {
 		$this->dbTable = $dbTable;
 	}


 	/**
 	 * Destructor
 	 */
 	function __destruct() {
 		//print_r($this->cacheIndexByFields);
 	}
 }
?>
