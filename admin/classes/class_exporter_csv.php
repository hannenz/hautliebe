<?php

/**
 * class_Export_csv.inc
 * Content o mat Export CSV Class 
 * 
 * @version 2013-01-25
 * @author A.Alkaissi <info@buero-hahn.de>
 *
 */

class CsvExporter extends Exporter{
	
	/**
	 * protected function getTable()
	 *
	 * get the data of given Table as XML string
	 *
	 * @param string $params

	 * @return string
	 */
	protected function getTable($params) {
		if (!$params['customQuery']) {
			$query = $this->buildQuery($params);
		} else {
			$query = $params['customQuery'];
		}

		$this->db->query($query);

		$rows = array();
		while($res = $this->db->get()){
			$rows[] = $res;
		}
		$params['rows']=$rows;
		
		return $this->arrayToCsv($params);
		
	}
		
	
	/**
	 * protected function arrayToCsv()
	 * convert an array to csv
	 * 
	 * @param array $array, data
	 * @param string $header_row, first row for columens names
	 * @param string $col_sep, columens seperator
	 * @param string $row_sep, row lines seperator
	 * @param string $qut
	 * @return string, csv string
	 */
	protected function arrayToCsv($params, $col_sep = ",", $row_sep = "\n", $qut = '"') {
		if (!is_array($params['rows']) or !is_array($params['rows'][0]))
			return '';
	
		//Header row.
		if ($params['exportStructure']) {
			foreach ($params['rows'][0] as $key => $val) {
				//Escaping quotes.
				$key = str_replace($qut, "$qut$qut", $key);
				$output .= "$col_sep$qut$key$qut";
			}
			$output = substr($output, 1) . "\n";
		}
		//Data rows.

		if ($params['exportData']) {
			foreach ($params['rows'] as $key => $val) {
				$tmp = '';
				foreach ($val as $cell_key => $cell_val) {
					//Escaping quotes.
					$cell_val = str_replace($qut, "$qut$qut", $cell_val);
					$cell_val = preg_replace("/\r/", "\\r", $cell_val);
					$cell_val = preg_replace("/\n/", "\\n", $cell_val);
					$tmp .= "$col_sep$qut$cell_val$qut";
				}
				$output .= substr($tmp, 1) . $row_sep;
			}
		}
	
		return $output;
	}
	
	/**
	 * protected function prepareData()
	 *
	 * build export tasks
	 * 
	 * @return array
	 */
	protected function prepareData($params=array()){
		$defaultParams = array(
			'filePrefix' => '',
			'fileName' => $this->date,
			'fileExtension' => 'csv',
		);

		$params = array_merge($defaultParams, $params);

		$exportTasks = array();
		
		$exportString = join("\n\n",$this->data['data']);
	
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