<?php

/**
 * class_Export_sql.inc
 * Content o mat Export SQL Class 
 * 
 * @version 2013-01-24
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */
class SqlExporter extends Exporter {

	
	/**
	 * public function work()
	 *
	 * called by export method
	 * used data in $this->tablesToExport to create export content
	 * save created data in $this->data array
	 *
	 * @return bollen
	 */
	public function work(){
		
		$this->data = array();
		
		// custom Query
		if ($this->settings['customQuery']) {
			
			$this->data[] = "using global custom query to export sql string is not supported!";

			//  get sql query by creating it
		} else {
			foreach ($this->tablesToExport as $table => $params) {
				$this->data['data'][] = $this->getTable($params);
			}
		}
		
		if (!empty($this->data)) {
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * protected function getTable()
	 *
	 * get the data of given Table as SQL string
	 *
	 * @param string $params

	 * @return string
	 */
	protected function getTable($params) {

		$sql = '';

		// create table structure and header comments
		if ($params['exportStructure']) {
			$sql .= "\n\n--\n-- Tabelle `" . $params['tableName'] . "`\n--\n\n";
			$sql.= 'DROP TABLE IF EXISTS ' . $this->db->dbQuote($params['tableName']) . ';';

			$this->db->Query('SHOW CREATE TABLE ' . $params['tableName']);
			$row2 = $this->db->get(MYSQLI_NUM);
			$sql.= "\n\n" . $row2[1] . ";\n\n\n";
		}
		if ($params['exportData']) {
			if($params['customQuery']){
				$query = $params['customQuery'];
			}else{
				$query = $this->buildQuery($params);
			}

			$this->db->Query($query);

			$currentFieldInfo = $this->db->getCurrentFieldInfo();

			$num_fields = count($currentFieldInfo['type']);

			for ($i = 0; $i < $num_fields; $i++) {
				while ($row = $this->db->get(MYSQLI_NUM)) {
					$sql.= 'INSERT INTO ' . $this->db->dbQuote($params['tableName']) . ' VALUES(';
					for ($j = 0; $j < $num_fields; $j++) {

						$row[$j] = addslashes($row[$j]);
						$row[$j] = preg_replace("/\r/", "\\r", $row[$j]);
						$row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
						if (isset($row[$j])) {
							$sql.= '"' . $row[$j] . '"';
						} else {
							$sql.= '""';
						}
						if ($j < ($num_fields - 1)) {
							$sql.= ',';
						}
					}
					$sql.= ");\n";
				}
			}
		}

		return $sql;
	}

	/**
	 * protected function prepareData()
	 *
	 * build export tasks
	 * 
	 * @return array
	 */
	protected function prepareData($params = array()) {
		$defaultParams = array(
			'filePrefix' => '',
			'fileName' => $this->date,
			'fileExtension' => 'sql',
		);

		$params = array_merge($defaultParams, $params);

		$exportTasks = array();

		$exportString = join("\n\n", $this->data['data']);


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