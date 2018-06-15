<?php

/**
 * class_Export_cmt.inc
 * Content o mat Export Contentomat Class 
 * 
 * @version 2013-02-05
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */
class CmtExporter extends XmlExporter {

	/**
	 * public function work()
	 *
	 * called by export method
	 * used data in $this->tablesToExport to create export content
	 * save created data in $this->data array
	 *
	 * @return bollen
	 */
	public function work() {

		$this->data = array();


		// custom Query
		if ($this->settings['customQuery']) {
			$this->data['data'][] = "using global custom query to export cmt string is not supported!";

			//  get sql query by creating it
		} else {
			foreach ($this->tablesToExport as $table => $params) {

				// table rows in cmt_tables
				if ($params['exportCmtTables']) {
					$params['customQuery'] = "SELECT * FROM cmt_tables WHERE cmt_tablename='" . $this->db->dbQuote($table) . "' ORDER BY id";
					$this->data['tables'][] = $this->getTable($params);
				}

				// table rows in cmt_fields
				if ($params['exportCmtFields']) {
					$params['customQuery'] = "SELECT * FROM cmt_fields WHERE cmt_tablename='" . $this->db->dbQuote($table) . "' ORDER BY id";
					$this->data['fields'][] = $this->getTable($params);
				}

				// table data
				if ($params['exportData']) {
					$params['customQuery'] = "SELECT * FROM ".$params['tableName']." ORDER BY id";
					$this->data['data'][] = $this->getTable($params);
				}
			}
		}
		
		return true;
	}

	/**
	 * protected function prepareData()
	 *
	 * collect & format export data in $this->data to
	 * prepare it to the next step of export (save or download)
	 *
	 * @return string
	 */
	protected function prepareData($params = array()) {
		$defaultParams = array(
			'filePrefix' => '',
			'fileName' => $this->date,
			'fileExtension' => 'cmt.xml',
		);

		$params = array_merge($defaultParams, $params);


		$exportTasks = array();

		// XML Frame Template
		$xmlSections = array('tables'=>'{{TABLES}}', 'fields'=>'{{FIELDS}}', 'data'=>'{{DATA}}');
		foreach($xmlSections as $section=>$placeHolder){
			
		}
		$exportXml = new SimpleXMLElement('<xml/>');
		$exportXml->addChild('tables', '{{TABLES}}');
		$exportXml->addChild('fields', '{{FIELDS}}');
		$exportXml->addChild('data', '{{DATA}}');
		$exportString = $exportXml->asXML();

		// tables string
		if ($this->data['tables']) {
			$tablesXmlString = join("\n\n", $this->data['tables']);
			$exportString = str_replace('{{TABLES}}', $tablesXmlString, $exportString);
		}else{
			$exportString = str_replace('{{TABLES}}', '', $exportString);
		}

		// fields string
		if ($this->data['fields']) {
			$fieldsXmlString = join("\n\n", $this->data['fields']);
			$exportString = str_replace('{{FIELDS}}', $fieldsXmlString, $exportString);
		}else{
			$exportString = str_replace('{{FIELDS}}','', $exportString);
		}


		// tables data string
		if ($this->data['data']) {
			$dataXmlString = join("\n\n", $this->data['data']);
			$exportString = str_replace('{{DATA}}', $dataXmlString, $exportString);
		}else{
			$exportString = str_replace('{{DATA}}','', $exportString);
		}



		if ($params['save']) {
			$exportTasks[] = array(
				'type' => 'save',
				'data' => $exportString,
				'fileName' => $params['filePrefix'] . $params['fileName'] . '.' . $params['fileExtension'],
				'filePath' => ''
			);
		}

		if ($params['download']) {
			$exportTasks[] = array(
				'type' => 'download',
				'data' => $exportString,
				'fileName' => $params['filePrefix'] . $params['fileName'] . '.' . $params['fileExtension']
			);
		}

		return $exportTasks;
	}

}