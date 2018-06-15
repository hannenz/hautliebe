<?php

/**
 * class_Importer.inc
 * Content o mat Export Class 
 * 
 * @version 2013-02-08
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */
class Importer {

	protected $db;   // DB Object, DBCex instance
	protected $importPath; // Path to locate import files
	protected $data;  // Timestamp of now
	protected $errorMessages;
	protected $reader;
	protected $doc;
	protected $fieldHandler;
	protected $appHandler;

	/**
	 * public function __construct()
	 *
	 * Constructor
	 */
	public function __construct() {
		$this->db = new DBCex();
		$this->reader = new XMLReader(); // XML Dom, DOMDocument instance
		$this->doc = new DOMDocument(); // XML Reader, XMLReader instance
		$this->errorMessages = array();
		$this->importPath = INCLUDEPATH . 'export/'; // default export path
		$this->date = date('Y-m-d_His');
		$this->fieldHandler = new FieldHandler();
		$this->appHandler = new ApplicationHandler();
	}

	/**
	 *
	 * @param type $params
	 * @return boolean 
	 */
	protected function readCmtFile($params) {

		$dataParts = array('tables', 'fields', 'data');

		foreach ($dataParts as $dataPart) {
			//	var_dump($dataPart);
			$this->reader->open($params['filePath'] . $params['fileName']);
			$this->data[$dataPart] = $this->getCmtTable($dataPart);
			//var_dump($this->data[$dataPart]);
		}
		return true;
	}

	/**
	 *
	 * @param type $tablesRoot
	 * @return type 
	 */
	protected function getCmtTable($tablesRoot) {

		$tableRootData = array();
		while ($this->reader->read() && $this->reader->name !== $tablesRoot);

		while ($this->reader->name === $tablesRoot) {
			$node = simplexml_import_dom($this->doc->importNode($this->reader->expand(), true));
			foreach ($node->children() as $table) {
				$tableRootData[(string) $table->attributes()->name] = $this->getCmtRows($table);
			}
			$this->reader->next($tablesRoot);
		}

		return $tableRootData;
	}

	/**
	 *
	 * @param type $table
	 * @return type 
	 */
	protected function getCmtRows($table) {
		$tableData = array();
		foreach ($table->children() as $row) {
			$rowData = array();
			foreach ($row->children() as $field) {
				//var_dump((string)$field->attributes()->name);
				$rowData[(string) $field->attributes()->name] = (string) $field;
			}
			$tableData[] = $rowData;
		}
		return $tableData;
	}

	protected function readCsvFile($params) {
		
	}

	/**
	 * public function import()
	 * 
	 * the main import function
	 * 
	 * @param array $params
	 * 
	 * return boolean 
	 */
	public function import($params = array()) {
		$defaultParams = array();
		$params = array_merge($defaultParams, $params);

		$checkFile = $this->checkFile($params['filePath'] . $params['fileName']);
		if (!$checkFile) {
			return false;
		} else {
			switch ($params['fileType']) {
				case 'sql':
					break;
				case 'csv':
					break;
				case 'cmt':
				default:
					$this->readCmtFile($params);
					break;
			}
		}

		if ($this->data) {
			$this->importTables($params);
		}
	}

	/**
	 * protected function getTablesListToImport()
	 * 
	 * @return type 
	 */
	protected function getTablesListToImport() {
		if (!($this->data['tables']) || empty($this->data['tables'])) {
			return array();
		}

		return array_keys($this->data['tables']);
	}

	/**
	 * protected function fieldDataWrapper()
	 * @param type $fieldData
	 * @return type 
	 */
	protected function fieldDataWrapper($fieldData) {
		$newFieldData = array(
			'tableName' => $fieldData['cmt_tablename'],
			'fieldName' => $fieldData['cmt_fieldname'],
			'fieldType' => $fieldData['cmt_fieldtype'],
			'fieldAlias' => $fieldData['cmt_fieldalias'],
			'fieldDefaultValue' => $fieldData['cmt_default'],
			'fieldDescription' => $fieldData['cmt_fielddesc'],
			//'fieldQuery' => $fieldData['cmt_fieldquery'],
			//'fieldIndex' => $fieldData['cmt_index'],
			'fieldOptions' => $fieldData['cmt_options']
		);

		return $newFieldData;
	}

	/**
	 * protected function tableDataWrapper()
	 * @param type $tableData
	 * @return type 
	 */
	protected function tableDataWrapper($tableData) {
		$newTableData = array(
			'cmt_tablename' => $tableDataHlp['tableName'],
			'cmt_showname' => $tableDataHlp['displayName'],
			'cmt_charset' => $tableDataHlp['charSet'],
			'cmt_collation' => $tableDataHlp['collation'],
			'cmt_include' => $tableDataHlp['applicationFile'],
			'cmt_itempos' => $tableDataHlp['position'],
			'cmt_addvars' => $tableDataHlp['addVars'],		// OUTDATED????
			'cmt_showfields' => $tableDataHlp['fieldsOverview'],
			'cmt_editstruct' => $tableDataHlp['fieldsEditMode'],
			'cmt_group' => $tableDataHlp['group'],
			'cmt_ownservice' => $tableDataHlp['ownServiceInclude'],
			'cmt_type' => $tableDataHlp['type'],
			'cmt_templates' => $tableDataHlp['templates'],
			'cmt_itemvisible' => $tableDataHlp['itemVisible'],
			'cmt_target' => $tableDataHlp['target'],		// OUTDATED????
			'cmt_queryvars' => $tableDataHlp['queryVars'],
			'cmt_systemtable' => $tableDataHlp['isSystemTable'],
			'cmt_tablesettings' => $tableDataHlp['tableSettings']
		);

		return $newTableData;
	}

	/**
	 * protected function importTables()
	 * 
	 * 
	 * @param array $params 
	 */
	protected function importTables($params = array()) {

		$defaultParams = array(
			'importType' => 'add' // delete exists application before importing new one
		);

		$params = array_merge($defaultParams, $params);

		// if no table/s selected to import from tables in xml import file, 
		// import all tables in xml file
		if (!$params['tables'] || empty($params['tables'])) {
			$params['tables'] = $this->getTablesListToImport();
		}

		foreach ($params['tables'] as $table) {

			// table settings in cmt_tables
			$tableData = $this->tableDataWrapper($this->data['tables'][$table][0]);

			// table fields settings in cmt_fields
			$tableFields = array();
			foreach ($this->data['fields'][$table] as $fieldData) {
				$tableFields[$fieldData['cmt_fieldname']] = $this->fieldDataWrapper($fieldData);
			}

			// content of table
			$tableContent = $this->data['data'][$table];

			// git application data if application is exists
			$applicationData = $this->appHandler->getApplicationByTablename($table);


			switch ($params['importType']) {
				case 'add':

					// delete Application from DB, cmt_tables, cmt_fields
					if (is_array($applicationData) && !empty($applicationData)) {
						$this->appHandler->deleteApplication($applicationData['id']);
					}

					// get pre-import Application group to insert application in it
					$tableData['group'] = $this->appHandler->getImportApplicationGroupId();

					// create application
					$data = $this->appHandler->createApplication(
							array(
								'tableName' => $table,
								'tableData' => $tableData,
								'tableFields' => $tableFields,
							)
					);

					break;

				case 'update':
					// merge import data in DB with priority of import data (overwrite)
					$data = $this->appHandler->editApplication(
							array(
								'force' => 'source',
								'tableName' => $table,
								'tableData' => $tableData,
								'tableFields' => $tableFields,
							)
					);

					break;
			}

			if ($data) {
				// 1.5 import contents
				if (!empty($tableContent)) {
					$check = $this->importTableContent(array('tableName' => $table, 'tableContent' => $tableContent));
					if (!$check) {
						return 0;
					}
				}
			}
		}
	}

	/**
	 * public function addTableContent
	 * 
	 * import table rows content
	 * 
	 * @param array $params
	 * @return boolean 
	 */
	public function importTableContent($params = array()) {
		$defaultParams = array(
		);
		$params = array_merge($defaultParams, $params);

		foreach ($params['tableContent'] as $row) {

			$check = $this->db->query("INSERT INTO " . $params['tableName'] . " SET " . $this->db->makeSetQuery($row));
		}

		return true;
	}


	/**
	 * function checkFile()
	 * Prüft, ob eine Datei vorhanden ist
	 *
	 * @param string $file Dateiname (inkl. Pfad)
	 * @return boolean true oder false, je nach Ergebnis der Prüfung
	 */
	public function checkFile($file) {
		if (!$file) {
			$this->errorMessages[] = 'Missing Filename and Path!';
			return false;
		}

		if (file_exists($file)) {
			return true;
		} else {
			$this->errorMessages[] = "File does not exist!";
			return false;
		}
	}

}